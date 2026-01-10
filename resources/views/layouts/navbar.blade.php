@include('layouts.header')

<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme fixed-top"
    id="layout-navbar"
    style="display:flex !important; align-items:center !important;">

    <!-- LEFT : Menu Toggle -->
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)" id="menuToggle">
            <i class="icon-base bx bx-menu icon-md"></i>
        </a>
    </div>

    <!-- 🔥 FLEX SPACER (THIS IS THE KEY FIX) -->
    <div style="flex:1 !important;"></div>

    <!-- RIGHT : User Profile -->
    <div class="navbar-nav-right d-flex align-items-center"
        id="navbar-collapse"
        style="flex-basis:auto !important;">

        <ul class="navbar-nav flex-row align-items-center">

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown">

                    <div class="avatar avatar-online">
                      <img
        src="{{ Auth::user()->profile_photo
            ? asset('storage/' . Auth::user()->profile_photo)
            : asset('admin/assets/img/avatars/1.png') }}"
        class="rounded-circle"
        width="40"
        height="40"
        alt="User Avatar"
    >
                    </div>
                </a>

                <!-- Dropdown -->
                <ul class="dropdown-menu dropdown-menu-end">

                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('admin/assets/img/avatars/1.png') }}"
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>

                                <div class="flex-grow-1">
                                    <h6 class="mb-0">
                                        {{ Auth::user()?->first_name ?? 'Guest' }}
                                    </h6>
                                    <small class="text-body-secondary">{{ Auth::user()?->role?->name ?? 'Guest' }}</small>
                                </div>
                            </div>
                        </a>
                    </li>

                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>

                    <li>
                        <button type="button"
                            class="dropdown-item"
                            data-bs-toggle="modal"
                            data-bs-target="#profileModal">
                            <i class="icon-base bx bx-user icon-md me-3"></i>
                            <span>My Profile</span>
                        </button>
                    </li>

                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="icon-base bx bx-cog icon-md me-3"></i>
                            <span>Settings</span>
                        </a>
                    </li>

                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>

                    <li>
                        <a href="#" class="dropdown-item"
                            onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                            <i class="icon-base bx bx-power-off icon-md me-3"></i>
                            <span>Log Out</span>
                        </a>

                        <form id="logoutForm"
                            action="{{ route('logout') }}"
                            method="POST"
                            class="d-none">
                            @csrf
                        </form>
                    </li>

                </ul>
            </li>
            <!-- /User -->

        </ul>
    </div>
</nav>

<!-- profile Popup -->
<div class="modal fade" style="width:110%; margin:auto !important;" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">My Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label>First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control"
                                value="{{ Auth::user()->first_name }}">
                        </div>

                        <div class="col-md-6">
                            <label>Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control"
                                value="{{ Auth::user()->last_name }}">
                        </div>

                        <div class="col-md-6">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control"
                                value="{{ Auth::user()->email }}" disabled>
                        </div>

                        <div class="col-md-6">
                            <label>Mobile <span class="text-danger">*</span> </label>
                            <input type="text" name="mobile" class="form-control"
                                value="{{ Auth::user()->mobile }}">
                        </div>

                        <div class="col-md-6">
                            <label>Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                placeholder="Leave blank to keep old">
                            @error('password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="col-md-6">
                            <label>New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror"
                                placeholder="Leave blank to keep old password">
                            @error('new_password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="col-md-6">
                            <label>Profile Photo</label>
                            <input type="file" name="profile_photo" class="form-control">
                        </div>
                        <!-- 
                                        <div class="col-md-12">
                                            <label>Role</label>
                                            <input type="text" class="form-control"
                                                value="{{ Auth::user()->role->name ?? '' }}" disabled>
                                        </div> -->

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">Close</button>

                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Update Profile
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {

        const sidebar = document.getElementById('layout-menu');
        const openBtn = document.getElementById('menuToggle');
        const closeBtn = document.getElementById('sidebarClose');



        if (!sidebar || !openBtn) {
            console.error('Sidebar or menuToggle missing');
            return;
        }


        openBtn.addEventListener('click', function(e) {
            // console.log("hiii");

            e.preventDefault();
            sidebar.classList.toggle('show');
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                sidebar.classList.remove('show');
            });
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1200) {
                sidebar.classList.remove('show');
            }
        });


        if (newPassword.value.trim() !== '' && currentPassword.value.trim() === '') {
            e.preventDefault();
            currentPassword.classList.add('is-invalid');

            let error = document.createElement('div');
            error.className = 'text-danger mt-1';
            error.innerText = 'Current password is required';

            if (!currentPassword.nextElementSibling) {
                currentPassword.parentNode.appendChild(error);
            }

            currentPassword.focus();
        }

    });
</script>