 @include('layouts.header')

 <body>
     <!-- Layout wrapper -->
     <div class="layout-wrapper layout-content-navbar">
         <div class="layout-container">
             <!-- Menu -->
             <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                 @include('layouts.sidebar')
             </aside>
             <!-- / Menu -->

             <!-- Layout container -->
             <div class="layout-page">
                 <!-- Navbar -->

                 @include('layouts.navbar')
                 <!-- / Navbar -->

                 <!-- Content wrapper -->
                 <div class="content-wrapper">
                     <!-- Content -->
                     <div class="container-xxl flex-grow-1 container-p-y">
                         <div class="row g-6">

                             <div class="col-12">
                                 <div class="card shadow-sm border-0 rounded-3">

                                     {{-- Card Header --}}
                                     <div class="card-header bg-white fw-semibold">
                                         <h2 class="text-xl font-semibold mb-4">
                                             {{ isset($retailer) ? 'Edit Retailer' : 'Create Retailer' }}
                                         </h2>
                                     </div>

                                     <div class="card-body">
                                         <form
                                             method="POST"
                                             action="{{ isset($retailer) 
                                            ? route('retailers.update', $retailer->id) 
                                            : route('retailers.store') }}">
                                             @csrf

                                             @if(isset($retailer))
                                             @method('PUT')
                                             @endif


                                             <div class="col-md-4">
                                                 <label class="form-label fw-medium">Name</label>
                                                 <input type="text"
                                                     name="name"
                                                     class="w-full border rounded px-3 py-2"
                                                     value="{{ old('name', $retailer->name ?? '') }}">
                                                 @error('name')
                                                 <div class="invalid-feedback">{{ $message }}</div>
                                                 @enderror
                                             </div>

                                             <!-- Mobile -->
                                             <div class="col-md-4 mb-3">
                                                 <label class="form-label">
                                                     Mobile <span class="text-danger">*</span>
                                                 </label>

                                                 <input type="text" name="mobile" class="form-control"
                                                     value="{{ old('mobile', $user->mobile ?? '') }}"
                                                     oninput="validateMobile(this)"
                                                     onkeypress="return isNumber(event)"
                                                     {{ $mode === 'view' ? 'readonly' : '' }}>


                                                 {{-- Display error message without red border --}}
                                                 @error('mobile')
                                                 <div class="text-danger mt-1">{{ $message }}</div>
                                                 @enderror
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
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <!-- / Content -->
                 @include('layouts.footer')
             </div>
             <!-- Content wrapper -->
         </div>
         <!-- / Layout page -->
     </div>

     </div>
     <!-- / Layout wrapper -->
 </body>

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