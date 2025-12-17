@include('layouts.header')

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <h4 class="mb-4">Reset Password</h4>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

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
                <button type="submit" class="btn btn-primary">Reset Password</button>

                <button type="button" class="btn btn-primary">Cancel</button>
            </form>

        </div>
    </div>
</div>
