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
                        <h4>Add Unit</h4>
                        @elseif($mode === 'edit')
                        <h4>Edit Unit</h4>
                        @else
                        <h4>View Unit</h4>
                        @endif
                    </div>

                    <div class="card-body">
                        <form
                            action="{{ $mode === 'edit' ? route('units.update', $units->id) : route('units.store') }}"
                            method="POST">
                            @csrf

                            @if ($mode === 'edit')
                            @method('PUT')
                            @endif

                            <!-- Inputs side by side -->
                            <div class="row g-3">

                                <!-- Parent Category -->
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">
                                        Unit Name <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                        name="name"
                                        class="form-control"
                                        value="{{ old('name', $units->name ?? '') }}"
                                        {{ $mode === 'show' ? 'readonly' : '' }}>

                                    @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Sub Category Name -->
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">
                                        Short Name<span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                        name="short_name"
                                        class="form-control"
                                        value="{{ old('short_name', $units->short_name ?? '') }}"
                                        {{ $mode === 'show' ? 'readonly' : '' }}>

                                    @error('short_name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Buttons (Right Aligned) -->
                            <div class="mt-4 d-flex justify-content-end gap-2 text-end">
                                <a href="{{ route('units.index') }}" class="btn btn-success">
                                    Back
                                </a>

                                @if ($mode === 'add')
                                <button type="submit" class="btn btn-success">
                                    Save Unit
                                </button>
                                @elseif ($mode === 'edit')
                                <button type="submit" class="btn btn-success">
                                    Update Unit
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