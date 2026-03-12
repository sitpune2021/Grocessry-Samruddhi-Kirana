<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Exports\SubCategorySampleExport;
use Maatwebsite\Excel\Facades\Excel;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subCategories = SubCategory::with('category')->latest()->paginate(20);
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
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {

            $file = $request->file('excel_file');

            // Excel read using maatwebsite
            $data = Excel::toArray([], $file);
            $rows = $data[0];

            // remove header row
            array_shift($rows);

            $success = 0;
            $skipped = 0;

            foreach ($rows as $row) {

                $categoryName = trim($row[0] ?? '');
                $name = trim($row[1] ?? '');

                if (!$categoryName || !$name) {
                    $skipped++;
                    continue;
                }

                // Case insensitive category match
                $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();

                if (!$category) {
                    $skipped++;
                    continue;
                }

                $slug = Str::slug($name);

                // Duplicate check
                if (SubCategory::where('name', $name)
                    ->where('category_id', $category->id)
                    ->exists()
                ) {

                    $skipped++;
                    continue;
                }

                SubCategory::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'slug' => $slug,
                    'created_by' => auth()->id(),
                ]);

                $success++;
            }

            return redirect()->route('sub-category.index')
                ->with('success', "$success sub categories imported, $skipped skipped");
        } catch (\Exception $e) {

            Log::error('SubCategory Bulk Upload Error', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function downloadSampleExcel()
    {
        return Excel::download(new SubCategorySampleExport, 'subcategory_sample.xlsx');
    }
}
