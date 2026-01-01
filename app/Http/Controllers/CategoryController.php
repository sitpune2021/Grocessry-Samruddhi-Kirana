<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $user = Auth::user();

        $categories = Category::with('brand')
            ->where('warehouse_id', $user->warehouse_id)
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('menus.category.index', compact('categories'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mode = 'add';
        $brands = Brand::all(); // ✅ Pass brands to view
        return view('menus.category.add-category', compact('brands', 'mode'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        Log::info('Category store request received', [
            'request_data' => $request->all(),
            'ip'           => $request->ip(),
            'user_id'      => Auth::id(),
        ]);

        try {
            $user = Auth::user();
            // Validation
            $validated = $request->validate([
                // 'name'     => 'required|string|max:255|unique:categories,name',
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories')->where(function ($q) use ($user) {
                        $q->where('warehouse_id', $user->warehouse_id);
                    }),
                ],
                'slug'     => 'required|string|max:255',
                'brand_id' => 'required|exists:brands,id',
            ]);

            // Create category
            $category = Category::create([
                'name'     => $validated['name'],
                'slug'     => $validated['slug'],
                'brand_id' => $validated['brand_id'],
                'warehouse_id' => $user->warehouse_id,
            ]);

            Log::info('Category created successfully', [
                'category_id' => $category->id,
                'name'        => $category->name,
                'slug'        => $category->slug,
                'brand_id'    => $category->brand_id,
                'warehouse_id' => $category->warehouse_id,
            ]);

            return redirect()
                ->route('menus.category.index')
                ->with('success', 'Category added successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {

            // Validation error log
            Log::warning('Category validation failed', [
                'errors' => $e->errors(),
            ]);

            throw $e; // Important: rethrow so Laravel can handle it

        } catch (\Exception $e) {

            // Any other error
            Log::error('Error while creating category', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return redirect()
                ->route('category.index')
                ->with('error', 'Category not stored.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Log request
            $mode = 'view';
            Log::info('Category Show Request Received', ['id' => $id]);

            // Fetch category
            $category = Category::find($id);
            $brands = Brand::all();

            if (!$category) {
                Log::warning("Category Not Found", ['id' => $id]);

                return redirect()->route('category.index')
                    ->with('error', 'Category not found');
            }

            Log::info('Category Found', ['category' => $category]);

            // Return response
            return view('menus.category.add-category', compact('category', 'brands', 'mode'));
        } catch (\Throwable $e) {

            Log::error('Category Show Error', ['error' => $e->getMessage()]);

            return redirect()->route('menus.category.index')
                ->with('error', 'Category not found');
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        Log::info('Category Edit Request Received', ['id' => $id]);

        $category = Category::with('brand')->find($id);

        if (!$category) {
            Log::warning('Category Not Found for Edit', ['id' => $id]);

            return redirect()->route('category.index')
                ->with('error', 'Category not found');
        }

        $brands = Brand::all();   // ✅ get all brands
        $mode   = 'edit';

        return view('menus.category.add-category', compact(
            'category',
            'brands',
            'mode'
        ));
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        Log::info('Category Update Request Received', [
            'id'      => $id,
            'request' => $request->all(),
            'user_id' => Auth::id(),
        ]);

        try {
            $user = Auth::user();

            // Fetch category ONLY from user's warehouse
            $category = Category::where('id', $id)
                ->where('warehouse_id', $user->warehouse_id)
                ->first();

            if (!$category) {
                Log::warning('Category Not Found or Unauthorized', [
                    'id' => $id,
                    'warehouse_id' => $user->warehouse_id,
                ]);

                return redirect()->route('menus.category.index')
                    ->with('error', 'Category not found or access denied');
            }

            // Warehouse-wise unique validation
            $validated = $request->validate([
                'brand_id' => 'required|exists:brands,id',
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories')
                        ->where(
                            fn($q) =>
                            $q->where('warehouse_id', $user->warehouse_id)
                        )
                        ->ignore($category->id),
                ],
                'slug' => 'required|string|max:255',
            ]);

            // Do NOT update warehouse_id here
            $category->update($validated);

            Log::info('Category Updated Successfully', [
                'category_id'  => $category->id,
                'name'         => $category->name,
                'brand_id'     => $category->brand_id,
                'warehouse_id' => $category->warehouse_id,
            ]);

            return redirect()->route('category.index')
                ->with('success', 'Category updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {

            Log::warning('Category Update Validation Failed', [
                'errors' => $e->errors(),
            ]);

            throw $e;
        } catch (\Throwable $e) {

            Log::error('Category Update Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Log request
        Log::info('Delete Category Request', [
            'id' => $id
        ]);

        // Find category
        $category = Category::find($id);

        if (!$category) {
            Log::warning("Category not found for delete", [
                'id' => $id
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Log before delete
        Log::info('Category Found for Delete', [
            'category' => $category
        ]);

        // Perform soft delete
        $category->delete();

        // Log after delete
        Log::info('Category Soft Deleted Successfully', [
            'id' => $id,
            'deleted_at' => now()->toDateTimeString()
        ]);

        // JSON response
        return redirect()->route('category.index')
            ->with('success', 'Category deleted successfully');
    }
}
