<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;

class SubcategoryController extends Controller
{
    /**
     * Display listing of root subcategories with full tree structure for admin UI.
     */
    public function index()
    {
        // Root subcategories have parent_subcategory_id = NULL
        $subcategories = Subcategory::with('allChildren', 'category')
            ->where('status', 1)
            ->whereNull('parent_subcategory_id')
            ->orderBy('subcategoryname', 'asc')
            ->get();

        // Tree-formatted list of active subcategories for select dropdowns
        $allSubcategories = Subcategory::getTreeOptions();

        $categories = Category::where('status', 1)
            ->orderBy('cat_name', 'asc')
            ->get();

        return view('admin.subcategories.index', compact('subcategories', 'allSubcategories', 'categories'));
    }

    /**
     * API: Get nested recursive tree structure in JSON.
     */
    public function tree(Request $request)
    {
        $catId = $request->query('catid');

        $query = Subcategory::with('allChildren')
            ->where('status', 1)
            ->whereNull('parent_subcategory_id');

        if ($catId) {
            $query->where('catid', $catId);
        }

        $tree = $query->orderBy('subcategoryname', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $tree
        ]);
    }

    /**
     * API: Get direct children of a given subcategory node.
     */
    public function getChildren($id)
    {
        $children = Subcategory::where('status', 1)
            ->where('parent_subcategory_id', $id)
            ->orderBy('subcategoryname', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $children
        ]);
    }

    /**
     * Store a newly created subcategory node.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subcategoryname' => 'required|string|max:255',
            'catid' => 'required_without:parent_subcategory_id|nullable|integer',
            'parent_subcategory_id' => 'nullable|integer|exists:subcategory,id',
        ]);

        $parentId = $request->input('parent_subcategory_id');
        $catId = $request->input('catid');

        // If parent subcategory is provided, inherit catid from parent subcategory for backward compatibility
        if ($parentId) {
            $parentSubcat = Subcategory::find($parentId);
            if ($parentSubcat) {
                $catId = $parentSubcat->catid;
            }
        }

        $subcategory = Subcategory::create([
            'subcategoryname' => $request->input('subcategoryname'),
            'catid' => $catId,
            'parent_subcategory_id' => $parentId,
            'creationdate' => date('Y-m-d H:i:s'),
            'status' => 1,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'subcategory' => $subcategory
            ]);
        }

        return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory successfully created!');
    }

    /**
     * Update the specified subcategory node.
     */
    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        $request->validate([
            'subcategoryname' => 'required|string|max:255',
            'catid' => 'nullable|integer',
            'parent_subcategory_id' => 'nullable|integer|exists:subcategory,id',
        ]);

        $newParentId = $request->input('parent_subcategory_id');

        // Prevent self-referencing or circular dependency
        if ($newParentId && $subcategory->isDescendantOf($newParentId)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid move target: Cannot set a descendant as the parent node.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Invalid move target: Cannot set a descendant as the parent node.');
        }

        $catId = $request->input('catid', $subcategory->catid);

        if ($newParentId) {
            $parentSubcat = Subcategory::find($newParentId);
            if ($parentSubcat) {
                $catId = $parentSubcat->catid;
            }
        }

        $subcategory->update([
            'subcategoryname' => $request->input('subcategoryname'),
            'catid' => $catId,
            'parent_subcategory_id' => $newParentId,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Subcategory successfully updated!',
                'subcategory' => $subcategory
            ]);
        }

        return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory successfully updated!');
    }

    /**
     * Move node to a new parent with strict circular reference prevention.
     */
    public function move(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        $request->validate([
            'parent_subcategory_id' => 'nullable|integer|exists:subcategory,id',
        ]);

        $targetParentId = $request->input('parent_subcategory_id');

        if ($targetParentId && $subcategory->isDescendantOf($targetParentId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Circular dependency detected! A subcategory cannot be moved under one of its own sub-items.'
            ], 422);
        }

        $catId = $subcategory->catid;
        if ($targetParentId) {
            $parentSubcat = Subcategory::find($targetParentId);
            if ($parentSubcat) {
                $catId = $parentSubcat->catid;
            }
        }

        $subcategory->update([
            'parent_subcategory_id' => $targetParentId,
            'catid' => $catId
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Node successfully moved!',
            'subcategory' => $subcategory
        ]);
    }

    /**
     * Remove the specified subcategory node (Soft Delete).
     * Supports delete_mode: 'branch' (delete node + all children) or 'reparent' (move children to deleted node's parent).
     */
    public function destroy(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $deleteMode = $request->input('delete_mode', 'branch'); // 'branch' or 'reparent'

        DB::transaction(function () use ($subcategory, $deleteMode) {
            if ($deleteMode === 'reparent') {
                // Promote children to parent node of deleted subcategory
                Subcategory::where('parent_subcategory_id', $subcategory->id)
                    ->update(['parent_subcategory_id' => $subcategory->parent_subcategory_id]);

                $subcategory->update(['status' => 2]);
            } else {
                // Cascade delete entire branch (set status = 2)
                $descendantIds = $subcategory->getAllDescendantIds();
                Subcategory::whereIn('id', $descendantIds)->update(['status' => 2]);
            }
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Subcategory successfully deleted.'
            ]);
        }

        return redirect()->route('admin.subcategories.index')->with('success', 'Subcategory successfully removed!');
    }
}
