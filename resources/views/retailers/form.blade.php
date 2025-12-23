@extends('layouts.app')

@section('content')

<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-xl font-semibold mb-4">
        {{ isset($retailer) ? 'Edit Retailer' : 'Create Retailer' }}
    </h2>

    <form
        method="POST"
        action="{{ isset($retailer) 
            ? route('retailers.update', $retailer->id) 
            : route('retailers.store') }}"
    >
        @csrf

        @if(isset($retailer))
            @method('PUT')
        @endif

        <!-- Name -->
        <div class="mb-3">
            <label class="block mb-1">Name</label>
            <input type="text"
                   name="name"
                   class="w-full border rounded px-3 py-2"
                   value="{{ old('name', $retailer->name ?? '') }}"
                   required>
        </div>

        <!-- Mobile -->
        <div class="mb-3">
            <label class="block mb-1">Mobile</label>
            <input type="text"
                   name="mobile"
                   class="w-full border rounded px-3 py-2"
                   value="{{ old('mobile', $retailer->mobile ?? '') }}"
                   required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="block mb-1">Email</label>
            <input type="email"
                   name="email"
                   class="w-full border rounded px-3 py-2"
                   value="{{ old('email', $retailer->email ?? '') }}">
        </div>

        <!-- Address -->
        <div class="mb-4">
            <label class="block mb-1">Address</label>
            <textarea name="address"
                      class="w-full border rounded px-3 py-2"
                      rows="3">{{ old('address', $retailer->address ?? '') }}</textarea>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded">
                {{ isset($retailer) ? 'Update' : 'Save' }}
            </button>

            <a href="{{ route('retailers.index') }}"
               class="bg-gray-500 text-white px-4 py-2 rounded">
                Cancel
            </a>
        </div>

    </form>
</div>

@endsection
