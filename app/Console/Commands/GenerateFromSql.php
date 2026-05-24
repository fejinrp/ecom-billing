<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateFromSql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:from-sql {sql_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse SQL dump and generate Laravel migrations and models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sqlPath = $this->argument('sql_path');
        if (!File::exists($sqlPath)) {
            $this->error("SQL file not found at: {$sqlPath}");
            return 1;
        }

        $content = File::get($sqlPath);
        
        // Find all CREATE TABLE blocks
        preg_match_all('/CREATE TABLE `([^`]+)` \((.*?)\) ENGINE=/s', $content, $matches);
        
        if (empty($matches[1])) {
            $this->error("No CREATE TABLE blocks found.");
            return 1;
        }

        $tableCount = count($matches[1]);
        $this->info("Found {$tableCount} tables in SQL file. Generating...");

        foreach ($matches[1] as $index => $tableName) {
            $tableDefinition = $matches[2][$index];
            $this->generateTable($tableName, $tableDefinition);
        }

        $this->info("All migrations and models generated successfully!");
        return 0;
    }

    protected function generateTable($tableName, $definition)
    {
        $lines = explode("\n", $definition);
        $columns = [];
        $primaryKey = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Handle primary key definition: PRIMARY KEY (`id`)
            if (preg_match('/^PRIMARY KEY \(`([^`]+)`\)/i', $line, $pkMatch)) {
                $primaryKey = $pkMatch[1];
                continue;
            }

            // Skip other indexes/keys for now to keep migrations clean
            if (preg_match('/^(KEY|UNIQUE KEY|CONSTRAINT|FOREIGN KEY)/i', $line)) {
                continue;
            }

            // Parse column definition: `column_name` type ...
            if (preg_match('/^`([^`]+)`\s+([a-z]+)(?:\(([^)]+)\))?\s*(.*)$/i', $line, $colMatch)) {
                $colName = $colMatch[1];
                $colType = strtolower($colMatch[2]);
                $colLength = $colMatch[3] ?? null;
                $colMeta = strtolower($colMatch[4] ?? '');

                $isNullable = !str_contains($colMeta, 'not null');
                $isAutoIncrement = str_contains($colMeta, 'auto_increment');
                
                $default = null;
                if (preg_match('/default\s+([^\s,]+)/i', $colMeta, $defMatch)) {
                    $default = trim($defMatch[1], "'\"");
                    if (strtolower($default) === 'null') $default = null;
                }

                $columns[$colName] = [
                    'type' => $colType,
                    'length' => $colLength,
                    'nullable' => $isNullable,
                    'auto_increment' => $isAutoIncrement,
                    'default' => $default
                ];
            }
        }

        // 1. Generate Migration File
        $this->createMigration($tableName, $columns, $primaryKey);

        // 2. Generate Model File
        $this->createModel($tableName, $columns, $primaryKey);
    }

    protected function createMigration($tableName, $columns, $primaryKey)
    {
        // Skip Laravel standard session & cache tables if they exist
        if (in_array($tableName, ['sessions', 'cache', 'password_reset_tokens'])) return;

        $fileName = date('Y_m_d_His') . '_create_' . $tableName . '_table.php';
        $filePath = database_path("migrations/{$fileName}");

        // Override default users migration to match our custom table
        if ($tableName === 'users') {
            // Find existing users migration and overwrite it
            $migrations = File::files(database_path('migrations'));
            foreach ($migrations as $file) {
                if (str_contains($file->getFilename(), 'create_users_table')) {
                    $filePath = $file->getRealPath();
                    break;
                }
            }
        }

        $fieldsStr = "";
        foreach ($columns as $name => $col) {
            $method = 'string';
            $args = "'{$name}'";

            if ($col['auto_increment']) {
                $method = 'id';
                $args = $primaryKey === $name ? '' : "'{$name}'";
            } else {
                switch ($col['type']) {
                    case 'int':
                    case 'tinyint':
                    case 'smallint':
                    case 'mediumint':
                    case 'bigint':
                        $method = 'integer';
                        break;
                    case 'decimal':
                    case 'float':
                    case 'double':
                        $method = 'decimal';
                        if ($col['length']) {
                            $args .= ", " . $col['length'];
                        } else {
                            $args .= ", 10, 2";
                        }
                        break;
                    case 'text':
                    case 'mediumtext':
                        $method = 'text';
                        break;
                    case 'longtext':
                        $method = 'longText';
                        break;
                    case 'timestamp':
                        $method = 'timestamp';
                        break;
                    case 'datetime':
                        $method = 'dateTime';
                        break;
                    case 'date':
                        $method = 'date';
                        break;
                    case 'binary':
                        $method = 'binary';
                        break;
                }
            }

            $chain = "\$table->{$method}({$args})";

            if ($col['nullable'] && !$col['auto_increment']) {
                $chain .= "->nullable()";
            }
            if ($col['default'] !== null) {
                if (is_numeric($col['default'])) {
                    $chain .= "->default({$col['default']})";
                } else if ($col['default'] === 'current_timestamp()') {
                    $chain .= "->useCurrent()";
                } else {
                    $chain .= "->default('{$col['default']}')";
                }
            }

            $fieldsStr .= "            {$chain};\n";
        }

        if ($tableName === 'users') {
            $fieldsStr .= "            \$table->rememberToken();\n";
            $fieldsStr .= "            \$table->timestamps();\n";
        }

        $migrationTemplate = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
{$fieldsStr}        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
EOT;

        File::put($filePath, $migrationTemplate);
        // Sleep briefly to ensure unique migration execution order timestamps
        usleep(250000); 
    }

    protected function createModel($tableName, $columns, $primaryKey)
    {
        $modelName = Str::studly(Str::singular($tableName));
        
        // Custom study name manual overrides
        if ($tableName === 'subcategory') $modelName = 'Subcategory';
        if ($tableName === 'kkpincode') $modelName = 'KkPincode';
        if ($tableName === 'purbal') $modelName = 'Purbal';
        if ($tableName === 'orderbal') $modelName = 'Orderbal';
        if ($tableName === 'sorderbal') $modelName = 'Sorderbal';
        if ($tableName === 'uorderbal') $modelName = 'Uorderbal';

        $filePath = app_path("Models/{$modelName}.php");

        $fillable = [];
        foreach (array_keys($columns) as $colName) {
            if ($colName !== $primaryKey) {
                $fillable[] = "'{$colName}'";
            }
        }

        $fillableStr = implode(",\n        ", $fillable);

        $primaryKeyConfig = "";
        if ($primaryKey && $primaryKey !== 'id') {
            $primaryKeyConfig = "    protected \$primaryKey = '{$primaryKey}';\n";
        }

        $timestampsConfig = "    public \$timestamps = false;";
        if (isset($columns['created_at']) || isset($columns['updated_at'])) {
            $timestampsConfig = "";
        }

        if ($modelName === 'User') {
            $modelTemplate = <<<EOT
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected \$table = 'users';
{$primaryKeyConfig}{$timestampsConfig}

    protected \$fillable = [
        {$fillableStr}
    ];

    protected \$hidden = [
        'password',
        'remember_token',
    ];
}
EOT;
        } else {
            $modelTemplate = <<<EOT
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
    use HasFactory;

    protected \$table = '{$tableName}';
{$primaryKeyConfig}{$timestampsConfig}

    protected \$fillable = [
        {$fillableStr}
    ];
}
EOT;
        }

        File::put($filePath, $modelTemplate);
    }
}
