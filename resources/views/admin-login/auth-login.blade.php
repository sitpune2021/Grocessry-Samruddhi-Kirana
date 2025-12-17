@include('layouts.header')

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card px-sm-6 px-0">
                    <div class="login-page">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-md-5 col-lg-4">
                                    <div class="card login-card">
                                        <div class="card-body">
                                            <!-- Logo -->
                                            <div class="app-brand text-center mb-4">
                                                <a href="{{ route('login.form') }}">
                                                    <img src="{{ asset('admin/assets/img/logo/samrudhi-kirana-logo.png') }}"
                                                        alt="Samruddhi Kirana" class="login-logo">
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

  <div class="authentication-inner d-flex justify-content-center align-items-center min-vh-100">

  <div class="card login-card">
    <div class="card-body p-4">

      <!-- Logo -->
      <div class="text-center mb-4">
        <img
          src="{{ asset('admin/assets/img/logo/samrudhi-kirana-logo.png') }}"
          alt="Samruddhi Kirana"
          class="login-logo">
      </div>
      <!-- /Logo -->

      <form action="{{ route('admin.login') }}" method="post">
        @csrf

        <div class="mb-3">
          <label class="form-label">Email Id</label>
          <input type="email" name="email" class="form-control" placeholder="Enter email" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            <span class="input-group-text">
              <i class="bx bx-hide"></i>
            </span>
          </div>
        </div>

        <div class="mb-3 text-end">
          <a href="#" class="text-primary small">Forgot Password?</a>
        </div>

        <button class="btn btn-primary w-100">Login</button>
      </form>

    </div>
  </div>

</div>

</body>

</html>
