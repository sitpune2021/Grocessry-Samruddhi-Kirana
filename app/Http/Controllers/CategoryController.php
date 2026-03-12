<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $categories = Category::orderBy('id', 'desc')
            ->paginate(20);

        return view('menus.category.index', compact('categories'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mode = 'add';
        return view('menus.category.add-category', compact('mode'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        Log::info('Category store request received', [
            'request_data' => $request->all(),
            'ip'           => $request->ip(),
        ]);

        try {

            // Validation
            $validated = $request->validate([
                'name'     => 'required|string|max:255|unique:categories,name',
                'slug'     => 'required|string|max:255',
                'category_images'   => 'required|array',
                'category_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($request->hasFile('category_images')) {

                $imageNames = [];

                foreach ($request->file('category_images') as $image) {
                    $name = time() . '_' . $image->getClientOriginalName();
                    $image->storeAs('categories', $name, 'public');
                    $imageNames[] = $name;
                }

                // Save as ARRAY (Laravel will JSON encode)
                $validated['category_images'] = $imageNames;
            }

            // Create category
            $category = Category::create([
                'name'     => $validated['name'],
                'slug'     => $validated['slug'],
                'category_images' => $imageNames,

            ]);

            Log::info('Category created successfully', [
                'category_id' => $category->id,
                'name'        => $category->name,
                'slug'        => $category->slug,
                'category_images' => $imageNames,


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
                ->with('success', 'Category added successfully');
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

            if (!$category) {
                Log::warning("Category Not Found", ['id' => $id]);

                return redirect()->route('category.index')
                    ->with('error', 'Category not found');
            }

            Log::info('Category Found', ['category' => $category]);

            // Return response
            return view('menus.category.add-category', compact('category',  'mode'));
        } catch (\Throwable $e) {

            Log::error('Category Show Error', ['error' => $e->getMessage()]);

            return redirect()->route('menus.category.index')
                ->with('error', 'Category not found');
        }
    }

    public function edit($id)
    {
        abort_unless(hasPermission('category.edit'), 403);

        Log::info('Category Edit Request Received', ['id' => $id]);

        $category = Category::findOrFail($id);

        $mode = 'edit';

        return view('menus.category.add-category', compact('category', 'mode'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        Log::info('Category Update Request Received', [
            'id' => $id,
            'request' => $request->all(),
        ]);

        try {

            $category = Category::find($id);

            if (!$category) {
                Log::warning('Category Not Found', ['id' => $id]);

                return redirect()
                    ->route('category.index')
                    ->with('error', 'Category not found');
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'slug' => 'required|string|max:255',
                'category_images' => 'nullable|array',
                'category_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $imageNames = $category->category_images ?? [];

            if ($request->hasFile('category_images')) {
                $imageNames = []; // Clear old images
                foreach ($request->file('category_images') as $image) {
                    $name = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('categories', $name, 'public');
                    $imageNames[] = $name; // Only new images
                }
            }
            $category->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'category_images' => $imageNames,
            ]);

            Log::info('Category Updated Successfully', [
                'category_id' => $category->id,
                'images' => $imageNames,
            ]);

            return redirect()
                ->route('category.index')
                ->with('success', 'Category updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Category Update Validation Failed', [
                'errors' => $e->errors(),
            ]);
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Category Update Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        Log::info('Delete Category Request', [
            'id' => $id
        ]);

        $category = Category::where('id', $id)
            ->first();

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
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Log::info('Bulk Upload Started', [
                'ip'         => $request->ip(),
                'excel_file' => $request->file('excel_file')->getClientOriginalName(),
            ]);

            $file = $request->file('excel_file');
            $data = array_map('str_getcsv', file($file->getRealPath()));
            array_shift($data); // header skip

            $successCount = 0;
            $skippedCount = 0;

            foreach ($data as $rowIndex => $row) {
                $name     = trim($row[0] ?? '');
                $slug     = !empty(trim($row[1] ?? '')) ? Str::slug(trim($row[1])) : Str::slug($name);
                $imageUrl = trim($row[2] ?? ''); // Column C = image URL

                Log::info("Bulk Upload: Processing Row", [
                    'row'   => $rowIndex + 2,
                    'name'  => $name,
                    'slug'  => $slug,
                    'image' => $imageUrl,
                ]);

                if (empty($name)) {
                    Log::warning("Bulk Upload: Skipped — Name Empty", ['row' => $rowIndex + 2]);
                    $skippedCount++;
                    continue;
                }

                if (Category::where('name', $name)->exists()) {
                    Log::warning("Bulk Upload: Skipped — Duplicate", ['name' => $name]);
                    $skippedCount++;
                    continue;
                }

                // Slug unique बनवा
                $original = $slug;
                $i = 1;
                while (Category::where('slug', $slug)->exists()) {
                    $slug = $original . '-' . $i++;
                }

                // ✅ Image URL वरून download करा
                $imageNames = [];
                if (!empty($imageUrl) && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    try {
                        $context = stream_context_create([
                            'http' => [
                                'timeout'    => 10,
                                'user_agent' => 'Mozilla/5.0',
                            ],
                            'ssl' => [
                                'verify_peer'      => false,
                                'verify_peer_name' => false,
                            ],
                        ]);

                        $imageContents = file_get_contents($imageUrl, false, $context);

                        if ($imageContents !== false) {
                            $imageName = time() . '_' . basename(parse_url($imageUrl, PHP_URL_PATH));
                            Storage::disk('public')->put('categories/' . $imageName, $imageContents);
                            $imageNames[] = $imageName;
                            Log::info("Bulk Upload: Image Saved", ['name' => $imageName, 'category' => $name]);
                        } else {
                            Log::warning("Bulk Upload: Image Download Failed", ['url' => $imageUrl]);
                        }
                    } catch (\Exception $e) {
                        Log::warning("Bulk Upload: Image Error", [
                            'url'   => $imageUrl,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                Category::create([
                    'name'            => $name,
                    'slug'            => $slug,
                    'category_images' => $imageNames,
                ]);

                Log::info("Bulk Upload: Category Created", [
                    'name'   => $name,
                    'slug'   => $slug,
                    'images' => $imageNames,
                ]);

                $successCount++;
            }

            Log::info('Bulk Upload Completed', [
                'success' => $successCount,
                'skipped' => $skippedCount,
            ]);

            return redirect()->route('category.index')
                ->with('success', "{$successCount} categories imported. {$skippedCount} skipped.");
        } catch (\Exception $e) {
            Log::error('Bulk Upload Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    // =============================================
    // SAMPLE CSV DOWNLOAD
    // =============================================
    public function downloadSampleExcel()
    {
        Log::info('Sample CSV Download Request');

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="category_sample.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'slug', 'image_url']);
            fputcsv($file, ['Dairy Products', 'dairy-products', 'https://picsum.photos/200']);
            fputcsv($file, ['Grains', 'grains', 'https://picsum.photos/201']);
            fputcsv($file, ['Home & Garden', '', '']); // slug + image रिकामं
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
