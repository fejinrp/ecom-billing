<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'category';
    protected $primaryKey = 'cat_id';
    public $timestamps = false;

    protected $fillable = [
        'cat_id',
        'cat_name',
        'creation_date',
        'status'
    ];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'catid', 'cat_id')->where('status', 1);
    }

    public function rootSubcategories()
    {
        return $this->hasMany(Subcategory::class, 'catid', 'cat_id')
            ->where('status', 1)
            ->whereNull('parent_subcategory_id')
            ->orderBy('subcategoryname', 'asc')
            ->with('allChildren');
    }

    /**
     * Get combined tree options of top-level categories and nested subcategories.
     */
    public static function getCombinedTreeOptions(): array
    {
        $categories = static::where('status', 1)->orderBy('cat_name', 'asc')->get();
        $options = [];

        foreach ($categories as $cat) {
            $catPath = $cat->cat_name;
            $options[] = [
                'value' => 'cat_' . $cat->cat_id,
                'catid' => $cat->cat_id,
                'subcatid' => null,
                'name' => $cat->cat_name,
                'label' => '📁 ' . $cat->cat_name,
                'path' => $catPath,
                'type' => 'category',
                'depth' => 0,
            ];

            $rootSubcategories = Subcategory::with('allChildren')
                ->where('status', 1)
                ->where('catid', $cat->cat_id)
                ->whereNull('parent_subcategory_id')
                ->orderBy('subcategoryname', 'asc')
                ->get();

            $flattenSub = function ($nodes, $depth = 1, $parentPath = '') use (&$options, &$flattenSub, $cat) {
                foreach ($nodes as $node) {
                    $currentPath = $parentPath ? $parentPath . ' > ' . $node->subcategoryname : $node->subcategoryname;
                    $indent = str_repeat("\u{00A0}\u{00A0}\u{00A0}\u{00A0}", $depth);
                    $options[] = [
                        'value' => 'sub_' . $node->id,
                        'catid' => $cat->cat_id,
                        'subcatid' => $node->id,
                        'name' => $node->subcategoryname,
                        'label' => $indent . '└─ ' . $node->subcategoryname,
                        'path' => $currentPath,
                        'type' => 'subcategory',
                        'depth' => $depth,
                    ];

                    if ($node->allChildren && $node->allChildren->count() > 0) {
                        $flattenSub($node->allChildren, $depth + 1, $currentPath);
                    }
                }
            };

            $flattenSub($rootSubcategories, 1, $catPath);
        }

        return $options;
    }
}