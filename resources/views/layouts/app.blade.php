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

    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">


    @stack('scripts')
</body>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


<!-- <script>
document.addEventListener('DOMContentLoaded', function () {

    const sidebar  = document.getElementById('layout-menu'); 
    const openBtn  = document.getElementById('menuToggle');
    const closeBtn = document.getElementById('sidebarClose');

    if (!sidebar || !openBtn) {
        console.error('Sidebar or menuToggle missing');
        return;
    }
console.log("hiii");

    openBtn.addEventListener('click', function (e) {
        console.log("hiii");
        
        e.preventDefault();
        sidebar.classList.toggle('show');
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            sidebar.classList.remove('show');
        });
    }

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1200) {
            sidebar.classList.remove('show');
        }
    });

});
</script> -->



</html>