<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.header')
</head>

<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

        <!-- Sidebar -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            @include('layouts.sidebar')
        </aside>

        <!-- Page -->
        <div class="layout-page">
            @include('layouts.navbar')

            <!-- Content -->
            <div class="content-wrapper">
                @yield('content')
            </div>

            @include('layouts.footer')
        </div>

    </div>
</div>

<!-- Core JS (ONLY ONCE) -->
<script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/js/bootstrap.js') }}"></script>

@stack('scripts')
</body>
</html>
