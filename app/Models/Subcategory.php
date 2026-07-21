<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Subcategory extends Model
{
    use HasFactory;

    protected $table = 'subcategory';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'catid',
        'parent_subcategory_id',
        'subcategoryname',
        'creationdate',
        'status'
    ];

    /**
     * Parent category relationship (top-level Category)
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'catid', 'cat_id');
    }

    /**
     * Immediate parent subcategory relationship
     */
    public function parent()
    {
        return $this->belongsTo(Subcategory::class, 'parent_subcategory_id', 'id');
    }

    /**
     * Direct children subcategories (active status)
     */
    public function children()
    {
        return $this->hasMany(Subcategory::class, 'parent_subcategory_id', 'id')
                    ->where('status', 1)
                    ->orderBy('subcategoryname', 'asc');
    }

    /**
     * Recursive eager loading of all child subcategories
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * Get all descendant IDs recursively for this subcategory using MySQL 8 CTE or Eloquent recursion fallback.
     */
    public function getAllDescendantIds(): array
    {
        $ids = [];

        try {
            // MySQL 8 Recursive CTE
            $results = DB::select("
                WITH RECURSIVE subcat_tree AS (
                    SELECT id FROM subcategory WHERE id = ? AND status = 1
                    UNION ALL
                    SELECT s.id FROM subcategory s
                    INNER JOIN subcat_tree st ON s.parent_subcategory_id = st.id
                    WHERE s.status = 1
                )
                SELECT id FROM subcat_tree
            ", [$this->id]);

            $ids = array_column($results, 'id');
        } catch (\Exception $e) {
            // Fallback for older engines / in-memory DBs
            $ids = [$this->id];
            $collectChildren = function ($subcategory) use (&$ids, &$collectChildren) {
                foreach ($subcategory->children as $child) {
                    $ids[] = $child->id;
                    $collectChildren($child);
                }
            };
            $collectChildren($this);
        }

        return array_values(array_unique($ids));
    }

    /**
     * Check if moving this node under targetParentId would cause a circular reference cycle.
     */
    public function isDescendantOf($targetParentId): bool
    {
        if (!$targetParentId) {
            return false;
        }

        if ((int)$targetParentId === (int)$this->id) {
            return true; // Node cannot be parent of itself
        }

        // Check if targetParentId is a descendant of $this->id
        $descendantIds = $this->getAllDescendantIds();
        return in_array((int)$targetParentId, array_map('intval', $descendantIds));
    }

    /**
     * Get a flattened list of subcategories formatted for dropdown selects with visual tree levels.
     */
    public static function getTreeOptions($catId = null): array
    {
        $query = static::with('allChildren')
            ->where('status', 1)
            ->whereNull('parent_subcategory_id');

        if ($catId) {
            $query->where('catid', $catId);
        }

        $roots = $query->orderBy('subcategoryname', 'asc')->get();

        $options = [];

        $flatten = function ($nodes, $depth = 0) use (&$options, &$flatten) {
            foreach ($nodes as $node) {
                $prefix = $depth > 0 ? str_repeat("\u{00A0}\u{00A0}\u{00A0}\u{00A0}", $depth) . '└─ ' : '';
                $options[] = [
                    'id' => $node->id,
                    'catid' => $node->catid,
                    'subcategoryname' => $node->subcategoryname,
                    'parent_subcategory_id' => $node->parent_subcategory_id,
                    'depth' => $depth,
                    'formatted_name' => $prefix . $node->subcategoryname,
                ];

                if ($node->allChildren && $node->allChildren->count() > 0) {
                    $flatten($node->allChildren, $depth + 1);
                }
            }
        };

        $flatten($roots);

        return $options;
    }
}