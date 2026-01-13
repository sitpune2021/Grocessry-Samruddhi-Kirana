<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
 

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::orderBy('created_at', 'desc')->paginate(20);
        return view('menus.brands.index', compact('brands'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('menus.brands.add-brands', ['mode' => 'add']);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|boolean',
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;

        while (Brand::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $validated['slug'] = $slug;

        if ($request->hasFile('logo')) {

            $originalName = $request->file('logo')->getClientOriginalName();

            $fileName = time() . '_' . $originalName;

            $request->file('logo')->storeAs('brands', $fileName, 'public');

            $validated['logo'] = $fileName;
        }


        Brand::create($validated);

        return redirect()->route('brands.index')
            ->with('success', 'Brand created successfully');
    }

    public function show($id)
    {
        $brand = Brand::findOrFail($id);

        $mode = 'view'; // 👈 important for readonly fields

        return view('menus.brands.add-brands', compact('brand', 'mode'));
    }


    /**
     * Display the specified resource.
     */
    public function edit(Brand $brand)
    {
        return view('menus.brands.add-brands', [
            'brand' => $brand,
            'mode' => 'edit'
        ]);
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|boolean',
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;

        while (
            Brand::where('slug', $slug)
            ->where('id', '!=', $brand->id)
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }

        $validated['slug'] = $slug;

        if ($request->hasFile('logo')) {

            if ($brand->logo && Storage::disk('public')->exists('brands/' . $brand->logo)) {
                Storage::disk('public')->delete('brands/' . $brand->logo);
            }

            $originalName = $request->file('logo')->getClientOriginalName();
            $fileName = time() . '_' . $originalName;

            $request->file('logo')->storeAs('brands', $fileName, 'public');
            $validated['logo'] = $fileName;
        }

        $brand->update($validated);

        return redirect()
            ->route('brands.index')
            ->with('success', 'Brand updated successfully');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('brands.index')
            ->with('success', 'Brand deleted successfully');
    }

    public function updateStatus(Request $request)
{
    // Find brand by ID or fail
    $brand = Brand::findOrFail($request->id);

    // Toggle status: if 1 -> 0, if 0 -> 1
    $brand->status = $brand->status ? 0 : 1;
    $brand->save();

    // Return JSON if called via AJAX, or back() if standard
   return back();
}

}
