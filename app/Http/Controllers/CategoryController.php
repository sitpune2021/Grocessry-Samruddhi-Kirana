<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories=Category::paginate(10);
        return view('menus.category.index',compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('menus.category.add-category');
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        Log::info('Category store request received', [
            'request_data' => $request->all(),
            'ip'           => $request->ip(),
            'user_id'      => auth()->id(),
        ]);

        try {
            // Validation
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'slug' => 'required|string|max:255',
            ]);

            // Create category
            $category = Category::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
            ]);

            Log::info('Category created successfully', [
                'category_id' => $category->id,
                'name'        => $category->name,
                'slug'        => $category->slug,
            ]);

            return redirect()
                ->back()
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
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Log request
            Log::info('Category Show Request Received', ['id' => $id]);

            // Fetch category
            $category = Category::find($id);

            if (!$category) {
                Log::warning("Category Not Found", ['id' => $id]);

                return response()->json([
                    'status'  => false,
                    'message' => 'Category not found'
                ], 404);
            }

            Log::info('Category Found', ['category' => $category]);

            // Return response
            return response()->json([
                'status'  => true,
                'message' => 'Category fetched successfully',
                'data'    => $category
            ], 200);
        } catch (\Throwable $e) {

            Log::error('Category Show Error', ['error' => $e->getMessage()]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Log::info('Update Category Request', ['id' => $id, 'request' => $request->all()]);

        $category = Category::find($id);

        if (!$category) {
            Log::warning("Category not found for ID: $id");
            return response()->json([
                'status'  => false,
                'message' => 'Category not found'
            ], 404);
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        Log::info('Category Updated Successfully', ['category' => $category]);

        return response()->json([
            'status'  => true,
            'message' => 'Category updated successfully',
            'data'    => $category
        ], 200);
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
        return response()->json([
            'status'  => true,
            'message' => 'Category deleted successfully (soft deleted)'
        ], 200);
    }
}
