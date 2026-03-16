@extends('layouts.app')
@section('content')
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row g-6">

                            <!-- Form controls -->
                            <div class="col-12">
                                <div class="card shadow-sm border-0 rounded-3">

                                    <!-- Card Header -->
                                    <div class="card-header bg-white fw-semibold">
                                        @if ($mode === 'add')
                                        <h4>Add Sub Category</h4>
                                        @elseif($mode === 'edit')
                                        <h4>Edit Sub Category</h4>
                                        @else
                                        <h4>View Sub Category</h4>
                                        @endif
                                    </div>

                                    <div class="card-body">
                                        <form
                                            action="{{ isset($subCategory) ? route('sub-category.update', $subCategory->id) : route('sub-category.store') }}"
                                            method="POST">
                                            @csrf

                                            @if (isset($subCategory))
                                            @method('PUT')
                                            @endif

                                            <!-- Inputs side by side -->
                                            <div class="row g-3">

                                                <!-- Parent Category -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Parent Category <span class="text-danger">*</span>
                                                    </label>

                                                    <select name="category_id" id="category_id" class="form-select" {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="">Select Category</option>
                                                        @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}"
                                                            {{ old('category_id', $subCategory->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                                            {{ $cat->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>

                                                    @error('category_id')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Sub Category Name -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Sub Category Name <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text"
                                                        name="name"
                                                        id="name"
                                                        class="form-control"
                                                        value="{{ old('name', $subCategory->name ?? '') }}"
                                                        placeholder="Enter sub category name"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>

                                                    @error('name')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Sub Category Slug -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Sub Category Slug <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text"
                                                        name="slug"
                                                        id="slug"
                                                        class="form-control"
                                                        value="{{ old('slug', $subCategory->slug ?? '') }}"
                                                        placeholder="auto-generated or manual"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>

                                                    @error('slug')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                            </div>

                                            <!-- Buttons (Right Aligned) -->
                                            <div class="mt-4 d-flex justify-content-end gap-2 text-end">
                                                <a href="{{ route('sub-category.index') }}" class="btn btn-success">
                                                    Back
                                                </a>

                                                @if ($mode === 'add')
                                                <button type="submit" class="btn btn-success">
                                                    Save Sub Category
                                                </button>
                                                @elseif($mode === 'edit')
                                                <button type="submit" class="btn btn-success">
                                                    Update Sub Category
                                                </button>
                                                @endif
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->
                </div>
                <!-- Content wrapper -->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.querySelector('input[name="name"]');
        const slugInput = document.querySelector('input[name="slug"]');

        nameInput.addEventListener('keyup', function() {
            if (!slugInput.dataset.manual) {
                slugInput.value = generateSlug(this.value);
            }
        });

        slugInput.addEventListener('input', function() {
            this.dataset.manual = true;
        });

        function generateSlug(text) {
            return text
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    });
</script>
@endpush