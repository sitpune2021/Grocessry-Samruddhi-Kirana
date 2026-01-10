<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subCategories = SubCategory::with('category')->latest()->paginate(10);
        return view('menus.sub-category.index', compact('subCategories'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mode = 'add';
        $categories = Category::all();
        return view('menus.sub-category.add-subcategory', compact('categories', 'mode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // Validation
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name'        => 'required|string|max:255 |unique:sub_categories,name',
                'slug'        => 'required|string|max:255|unique:sub_categories,slug',
            ]);

        try {
            // Create Sub Category
            SubCategory::create([
                'category_id' => $validated['category_id'],
                'name'        => $validated['name'],
                'slug'        => Str::slug($validated['slug']),
                'created_by'  => auth()->id(),
            ]);

            // Success log
            Log::info('Sub Category created successfully', [
                'category_id' => $validated['category_id'],
                'name'        => $validated['name'],
                'created_by'  => auth()->id(),
            ]);

            return redirect()->route('sub-category.index')
                ->with('success', 'Sub Category created successfully');
        } catch (\Throwable $e) {

            // Error log
            Log::error('Failed to create Sub Category', [
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
                'request'    => $request->all(),
                'user_id'    => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating Sub Category');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SubCategory $subCategory)
    {
        $mode = 'view';
        $categories = Category::all();
        return view('menus.sub-category.add-subcategory', compact('subCategory', 'categories', 'mode'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubCategory $subCategory)
    {
        $mode = 'edit';
        $categories = Category::all();
        return view('menus.sub-category.add-subcategory', compact('subCategory', 'categories', 'mode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:sub_categories,slug,' . $subCategory->id,
        ]);

        $subCategory->update([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'slug'        => Str::slug($request->slug),
            'updated_by'  => auth()->id(),
        ]);

        return redirect()->route('sub-category.index')
            ->with('success', 'Sub Category updated successfully');
    }

    /** DELETE */
    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();

        return redirect()->route('sub-category.index')
            ->with('success', 'Sub Category deleted successfully');
    }

    //get sub categories
    public function getSubCategories($categoryId)
    {
        $subCategories = SubCategory::where('category_id', $categoryId)
            ->get(['id', 'name']);

        return response()->json($subCategories);
    }
}
