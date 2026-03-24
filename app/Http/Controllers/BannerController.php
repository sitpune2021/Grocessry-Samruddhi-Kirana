<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use App\Models\ContactDetail;
use App\Models\AboutPage;
use App\Models\Product;
use Illuminate\Support\Facades\Log;


class BannerController extends Controller
{

    public function index()
    {
        $banners = Banner::latest()->get();
        return view('banners.index', compact('banners'));
    }

    public function create()
    {

        return view('banners.form', [
            'banner' => null,
            'products' => Product::select('id', 'name')->orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Banner Store Started', [
            'request_data' => $request->all(),
            'has_file' => $request->hasFile('image')
        ]);

        try {

            // 🔹 VALIDATION
            $validated = $request->validate([
                'name'       => 'required|string|max:255',
                'product_id' => 'nullable|exists:products,id',
                'image'      => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            Log::info('Validation Passed', $validated);

            // 🔹 CHECK FILE
            if (!$request->hasFile('image')) {
                Log::error('Image file not found in request');
                return back()->with('error', 'Image not found')->withInput();
            }

            $file = $request->file('image');

            Log::info('Image File Details', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
            ]);

            // 🔹 STORE IMAGE
            $imagePath = $file->store('banner', 'public');

            Log::info('Image Stored Successfully', [
                'path' => $imagePath
            ]);

            // 🔹 SAVE DATA
            Banner::create([
                'name'       => $validated['name'],
                'product_id' => $validated['product_id'] ?? null,
                'image'      => $imagePath,
            ]);

            Log::info('Banner Created Successfully');

            return redirect()
                ->route('banners.index')
                ->with('success', 'Banner added successfully');

        } catch (\Throwable $e) {

            Log::error('Banner Store Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Image upload failed: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        $products = Product::select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('banners.form', compact('banner', 'products'));
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:255',
            'product_id' => 'nullable|exists:products,id', // 👈 added
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // image update
        if ($request->hasFile('image')) {

            // delete old image
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }

            $banner->image = $request->file('image')->store('banner', 'public');
        }

        // update fields
        $banner->name       = $request->name;
        $banner->product_id = $request->product_id; // 👈 added
        $banner->save();

        return redirect()
            ->route('banners.index')
            ->with('success', 'Banner updated successfully');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('banners.index')->with('success', 'Banner deleted successfully');
    }

    public function contactList()
    {
        $contacts = ContactDetail::latest()->paginate(10);

        return view('contacts.index', compact('contacts'));
    }

    public function aboutus()
    {
        return view('website.about');
    }

    public function storeAboutUs(Request $request)
    {
        $request->validate([
            'content' => 'required',
        ]);

        AboutPage::updateOrCreate(
            ['id' => 1], // ek hi About Us page rahe
            ['content' => $request->content]
        );

        return back()->with('success', 'About Us updated successfully');
    }
}
