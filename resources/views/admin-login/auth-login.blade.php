@include('layouts.header')

<body>
  <!-- Content -->

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