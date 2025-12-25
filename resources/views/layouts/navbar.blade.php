@include('layouts.header')

<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme fixed-top"
     id="layout-navbar"
     style="display:flex !important; align-items:center !important;">

    <!-- LEFT : Menu Toggle -->
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
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
                        <img src="{{ asset('admin/assets/img/avatars/1.png') }}"
                             alt="User Avatar"
                             class="w-px-40 h-auto rounded-circle" />
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

                    <li><div class="dropdown-divider my-1"></div></li>

                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="icon-base bx bx-user icon-md me-3"></i>
                            <span>My Profile</span>
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="icon-base bx bx-cog icon-md me-3"></i>
                            <span>Settings</span>
                        </a>
                    </li>

                    <li><div class="dropdown-divider my-1"></div></li>

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
