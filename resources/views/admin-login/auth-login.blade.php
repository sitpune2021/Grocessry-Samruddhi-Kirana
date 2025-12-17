@include('layouts.header')

<body>
    <div class="container">
        <div class="authentication-wrapper authentication-basic ">
            <div class="authentication-inner">
                <div class="card px-sm-6 px-0">
                    <div class="login-page">
                        <div class="">
                            <div class="row justify-content-center">
                                <div class="col-md-5 col-lg-4">
                                    <div class="card login-card">
                                        <div class="card-body">
                                            <!-- Logo -->
                                            <div class="app-brand text-center mb-4  mx-auto" >
                                                <a href="{{ route('login.form') }}" class="text-center">
                                                    <img src="{{ asset('admin/assets/img/logo/samrudhi-kirana-logo.png') }}"
                                                        alt="Samruddhi Kirana" class="login-logo" style="height: 140px ; width:300px;">
                                                </a>
                                            </div>
                                            <!-- /Logo -->
                                            @if (session('success'))
                                                <div class="alert alert-success alert-dismissible fade show"
                                                    role="alert">
                                                    {{ session('success') }}
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="alert"></button>
                                                </div>
                                            @endif
                                            <form action="{{ route('admin.login') }}" method="post">
                                                @csrf

                                                {{-- ERROR MESSAGE --}}
                                                @if ($errors->any())
                                                    <div class="alert alert-danger alert-dismissible fade show"
                                                        role="alert">
                                                        {{ $errors->first() }}
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                            aria-label="Close"></button>
                                                    </div>
                                                @endif

                                                <div class="mb-3">
                                                    <label class="form-label">Email Id</label>
                                                    <input type="email" name="email" class="form-control"
                                                        placeholder="Enter email" value="{{ old('email') }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Password</label>
                                                    <div class="input-group">
                                                        <input type="password" name="password" id="password"
                                                            class="form-control" placeholder="Enter password" required>
                                                        <span class="input-group-text" id="togglePassword"
                                                            style="cursor:pointer">
                                                            <i class="bx bx-hide" id="eyeIcon"></i>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="mb-3 text-end">
                                                    <!-- Trigger modal -->
                                                    <a href="#" class="forgot-link" data-bs-toggle="modal"
                                                        data-bs-target="#resetPasswordModal">
                                                        Forgot Password?
                                                    </a>
                                                </div>

                                                <button class="btn btn-primary w-100">Login</button>

                                            </form>

                                            <!-- Reset Password Modal -->
                                            <div class="modal fade" id="resetPasswordModal" tabindex="-1"
                                                aria-labelledby="resetPasswordLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="resetPasswordLabel">Reset
                                                                Password</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="POST" action="{{ route('reset.password') }}">
                                                                @csrf
                                                                <div class="mb-3">
                                                                    <label>Email</label>
                                                                    <input type="email" name="email"
                                                                        class="form-control" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label>New Password</label>
                                                                    <input type="password" name="password"
                                                                        class="form-control" required>
                                                                </div>
                                                                <button type="submit"
                                                                    class="btn btn-primary w-100">Reset
                                                                    Password</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Modal -->

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bx-hide');
                eyeIcon.classList.add('bx-show');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bx-show');
                eyeIcon.classList.add('bx-hide');
            }
        });
    </script>

</body>

</html>
