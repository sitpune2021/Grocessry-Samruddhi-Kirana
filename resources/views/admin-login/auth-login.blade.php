@include('layouts.header')

<body class="login-body" >

<div class="container min-vh-100 d-flex align-items-center justify-content-center p-10 "> 
    <div class="login-card card">
        <div class="card-body p-4">

            <!-- Logo -->
            <div class="text-center mb-4">
                <a href="{{ route('login.form') }}">
                    <img src="{{ asset('admin/assets/img/logo/samrudhi-kirana-logo.png') }}"
                         alt="Samruddhi Kirana"
                         class="login-logo">
                </a>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Error Message -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('admin.login') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email Id</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           placeholder="Enter email"
                           value="{{ old('email') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control"
                               placeholder="Enter password"
                               required>
                        <span class="input-group-text" id="togglePassword" style="cursor:pointer">
                            <i class="bx bx-hide" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-3 text-end">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        Forgot Password?
                    </a>
                </div>

                <button class="btn btn-success w-100">Login</button>
            </form>

        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('reset.password') }}">
                    @csrf
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>New Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const password = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (password.type === 'password') {
        password.type = 'text';
        eyeIcon.classList.replace('bx-hide', 'bx-show');
    } else {
        password.type = 'password';
        eyeIcon.classList.replace('bx-show', 'bx-hide');
    }
});
</script>

</body>
</html>
