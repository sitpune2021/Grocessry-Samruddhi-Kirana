 @extends('website.layout')

 @section('title', 'Login')

 @section('content')

 <div class="container py-5 mt-5">
     <div class="row justify-content-center mt-5">
         <div class="col-lg-8 col-md-6 ">

             <div class="card shadow border-0 rounded-4 mt-5">
                 <div class="card-body">

                     <h4 class="text-center mb-4">Register</h4>

                     <form method="POST" action="{{ route('register') }}">
                         @csrf

                         <!-- Full Name + Email -->
                         <div class="row">
                             <div class="col-md-6 mb-3">
                                 <label class="form-label">Full Name</label>
                                 <input type="text"
                                     name="name"
                                     class="form-control"
                                     placeholder="Enter full name"
                                     value="{{ old('name') }}"
                                     required>
                                 @error('name')
                                 <div class="error-message">{{ $message }}</div>
                                 @enderror
                             </div>

                             <div class="col-md-6 mb-3">
                                 <label class="form-label">Email address</label>
                                 <input type="email"
                                     name="email"
                                     class="form-control"
                                     placeholder="Enter email"
                                     value="{{ old('email') }}"
                                     required>
                                 @error('email')
                                 <div class="error-message">{{ $message }}</div>
                                 @enderror
                             </div>
                         </div>

                         <!-- Mobile + Password -->
                         <div class="row">
                             <div class="col-md-6 mb-3">
                                 <label class="form-label">Mobile Number</label>
                                 <input type="text"
                                     name="mobile"
                                     class="form-control"
                                     placeholder="Enter mobile number"
                                     value="{{ old('mobile') }}"
                                     required>
                                 @error('mobile')
                                 <div class="error-message">{{ $message }}</div>
                                 @enderror
                             </div>

                             <div class="col-md-6 mb-3">
                                 <label class="form-label">Password</label>
                                 <input type="password"
                                     name="password"
                                     class="form-control"
                                     placeholder="Enter password"
                                     required>
                                 @error('password')
                                 <div class="error-message">{{ $message }}</div>
                                 @enderror
                             </div>
                         </div>

                         <!-- Submit -->
                         <button type="submit" class="btn btn-primary   mb-3">
                             Register
                         </button>

                         <!-- Login link -->
                         <p class="  mb-0">
                             Already have an account?
                             <a href="{{ route('login') }}" class="text-primary fw-semibold">
                                 Login
                             </a>
                         </p>

                     </form>

                 </div>
             </div>


         </div>
     </div>
 </div>

 @endsection