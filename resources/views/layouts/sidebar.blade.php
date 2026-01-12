@include('layouts.header')

<style>
    .sidebar-text {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .list-outer {
        margin-bottom: 10px;
    }

    .dropdowns-menus {
        display: flex;
        justify-content: start;
        align-items: center;
        
        width: 100%;
        cursor: pointer;
    }

    .submenu {
        display: none;
        margin-left: 35px;
        list-style: none;
    }

    .arrow.rotate {
        transform: rotate(90deg);
        transition: 0.2s;
    }
    #sidebar {
    height: 100vh;            
    overflow-y: auto;             
     overflow-x: hidden; 
     }
     .dropdown-outer{
        display: flex;
        flex-direction: row;
        justify-content: start;
        align-items: center;
     }
     .sidebar::-webkit-scrollbar {
    display: none;
}               
</style>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">

    <!-- Close -->
    <div class="sidebar-close">
        <i class="bx bx-x" style="color:red" id="sidebarClose"></i>
    </div>

    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('admin/assets/img/logo/samrudhi-kirana-logo1.png') }}"
                 alt="Samruddhi Kirana">
        </a>
    </div>

    <ul>

        @foreach(config('menu.sidebar') as $menu)

            {{-- ROLE CHECK --}}
            @if(isset($menu['roles']) && auth()->check() && !in_array(auth()->user()->role_id, $menu['roles']))
                @continue
            @endif

            {{-- SINGLE MENU --}}
            @if($menu['type'] === 'single')
                <li class="list-outer mt-4 ">
                    <a href="{{ isset($menu['route']) ? route($menu['route']) : url($menu['url']) }}" class="text-white">
                        <i class="{{ $menu['icon'] }}"></i>
                        <span style="margin-left: 6px;">{{ $menu['title'] }}</span>
                    </a>
                </li>
            @endif

            {{-- DROPDOWN MENU --}}
            @if($menu['type'] === 'dropdown')
                <li>
                   <div class="dropdown-outer"  onclick="toggleMenu('{{ $menu['key'] }}','{{ $menu['key'] }}Arrow')">
                     <div class="dropdowns-menus text-white mt-4"  >
                      <div class="">
                         <i class="{{ $menu['icon'] }}"></i>
                      </div>
                        <div style="margin-left: 10px;">               
                            {{ $menu['title'] }}
                        </div>
                       
                    </div>
                     <div class="text-end  mt-4  " style="text-align: right !important; margin-left: 5px;">
                            <i class="bx bx-chevron-right arrow text-white"
                           id="{{ $menu['key'] }}Arrow"></i>
                   </div>
                   </div>

                    <ul class="submenu" id="{{ $menu['key'] }}">
                        @foreach($menu['children'] as $child)

                            {{-- CHILD ROLE CHECK --}}
                            @if(isset($child['roles']) && auth()->check() && !in_array(auth()->user()->role_id, $child['roles']))
                                @continue
                            @endif

                            <li>
                                <a href="{{ isset($child['route']) ? route($child['route']) : url($child['url']) }}">
                                    {{ $child['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endif

        @endforeach

    </ul>

</div>

<script>
    function toggleMenu(menuId, arrowId) {

        const currentMenu  = document.getElementById(menuId);
        const currentArrow = document.getElementById(arrowId);
        const isOpen = currentMenu.style.display === 'block';

        document.querySelectorAll('.submenu').forEach(menu => {
            menu.style.display = 'none';
        });

        document.querySelectorAll('.arrow').forEach(arrow => {
            arrow.classList.remove('rotate');
        });

        if (!isOpen) {
            currentMenu.style.display = 'block';
            currentArrow.classList.add('rotate');
        }
    }
</script>
