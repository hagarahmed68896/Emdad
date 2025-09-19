<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم  - @yield('page_title', 'الرئيسية')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
 <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <style>
    

        /* Custom scrollbar for better aesthetics */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Specific styling for the active sidebar link */
        .sidebar-link.active {
            background-color: #185D31;
            /* Darker green for active */
            color: white;
            border-radius: 0.75rem;
            /* rounded-xl */
        }

        .sidebar-link.active svg,
        .sidebar-link.active span {
            color: white !important;
        }

        /* Sidebar Collapse Styles */
        .sidebar {
            transition: width 0.3s ease-in-out, transform 0.3s ease-in-out;
            width: 16rem;
            /* Default width (w-64) */
            flex-shrink: 0;
            /* Prevent sidebar from shrinking */
        }

        .sidebar.collapsed {
            width: 4rem;
            /* Collapsed width (w-16) */
        }

        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .fa-chevron-down,
        .sidebar.collapsed .profile-info {
            display: none;
        }

        .sidebar.collapsed .sidebar-link {
            justify-content: center;
            /* Center icons when collapsed */
        }

        .sidebar.collapsed .sidebar-link .fa-th-large,
        .sidebar.collapsed .sidebar-link .fa-users,
        .sidebar.collapsed .sidebar-link .fa-file-invoice,
        .sidebar.collapsed .sidebar-link .fa-file-alt,
        .sidebar.collapsed .sidebar-link .fa-box,
        .sidebar.collapsed .sidebar-link .fa-shopping-cart,
        .sidebar.collapsed .sidebar-link .fa-comments,
        .sidebar.collapsed .sidebar-link .fa-star,
        .sidebar.collapsed .sidebar-link .fa-chart-line,
        .sidebar.collapsed .sidebar-link .fa-tags,
        .sidebar.collapsed .sidebar-link .fa-cog,
        .sidebar.collapsed .fa-question-circle {
            margin-right: 0 !important;
            /* Remove right margin for icons */
        }

        .sidebar.collapsed .profile-picture {
            margin-left: 0 !important;
            /* Center profile picture */
        }

        .sidebar.collapsed .sidebar-sub-menu {
            display: none;
            /* Hide sub-menus when collapsed */
        }

        /* Mobile Responsiveness */
        @media (max-width: 767px) {
            .sidebar {
                position: fixed;
                top: 0;
                right: 0;
                height: 100vh;
                transform: translateX(100%);
                /* Hidden by default */
                z-index: 50;
                /* Ensure it's above other content */
                width: 75%;
                /* Adjust width for mobile if needed */
                box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
                /* Add shadow for overlay effect */
            }

            .sidebar.active {
                transform: translateX(0);
                /* Visible when active */
            }

            .main-content-area {
                width: 100%;
                /* Take full width on mobile */
            }

            .mobile-menu-button {
                display: block;
                /* Show hamburger menu on mobile */
            }

            .desktop-collapse-button {
                display: none;
                /* Hide desktop collapse button on mobile */
            }
        }

        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0);
                /* Always visible on desktop */
            }

            .mobile-menu-button {
                display: none;
                /* Hide hamburger menu on desktop */
            }

            .desktop-collapse-button {
                display: block;
                /* Show desktop collapse button on desktop */
            }
        }
        [x-cloak] {
    display: none !important;
}

    </style>

</head>

<body class="flex h-screen overflow-hidden bg-gray-100" style="font-family: 'Cairo', sans-serif;">

    <!-- Sidebar -->
    <aside id="sidebar"  x-data="{ activeItem: null }" class="sidebar bg-white shadow-lg flex flex-col p-4 overflow-y-auto">
        <div class="flex items-center justify-center py-4 border-b border-gray-200">
            <!-- Placeholder for logo -->
            <div class="w-[100px] h-[100px]  flex items-center justify-center overflow-hidden">
                <img src="/images/image-picture-landscape-1--photos-photo-landscape-picture-photography-camera-pictures--Streamline-Core.png"
                    alt="">
            </div>
        </div>

        <nav class="mt-6 flex-grow">
            <ul>
      <li class="mb-2">
  <a href="{{ route('admin.dashboard') }}"
     class="sidebar-link flex items-center p-3  transition-colors duration-200
        {{ Request::is('admin/dashboard') ? 'bg-[#185D31] text-white rounded-xl': 'text-gray-700 hover:bg-[#185D31] hover:text-white rounded-xl' }}">
      <i class="fas fa-th-large ml-3 "></i>
      <span class="sidebar-text">لوحة التحكم</span>
  </a>
</li>
<li class="mb-2"
    x-data="{ open: {{
        (
            Request::is('admin/users*') ||
            Request::is('admin/suppliers*') ||
            Request::is('admin/banned-users*')
        ) ? 'true' : 'false' }} }">

    <a href="#"
       @click.prevent="open = !open"
       class="sidebar-link flex items-center p-3 transition-colors duration-200 rounded-xl text-gray-700">
        <i class="fas fa-users ml-3"></i>
        <span class="sidebar-text">إدارة الحسابات</span>
        <i :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"
           class="fas mr-auto text-gray-500 text-xs sidebar-text"></i>
    </a>

    <ul x-show="open" x-transition class="mt-2 space-y-2 pr-6 sidebar-sub-menu">

        {{-- العملاء --}}
        <li>
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center p-2 transition-colors duration-200
               {{ (Request::is('admin/users*') || request('account_type') === 'customer') ? 'bg-[#185D31] text-white rounded-xl' : 'text-gray-600 hover:bg-[#185D31] hover:text-white rounded-xl' }}">
               العملاء
            </a>
        </li>

        {{-- الموردين --}}
        <li>
            <a href="{{ route('admin.suppliers.index') }}"
               class="flex items-center p-2 transition-colors duration-200
               {{ Request::is('admin/suppliers*') ? 'bg-[#185D31] text-white rounded-xl' : 'text-gray-600 hover:bg-[#185D31] hover:text-white rounded-xl' }}">
               الموردين
            </a>
        </li>

        {{-- المحظورون --}}
        <li>
            <a href="{{ route('admin.banned.index') }}"
               class="flex items-center p-2 transition-colors duration-200
               {{ Request::is('admin/banned-users*') ? 'bg-[#185D31] text-white rounded-xl' : 'text-gray-600 hover:bg-[#185D31] hover:text-white rounded-xl' }}">
               المحظورون
            </a>
        </li>

    </ul>
</li>





                <li class="mb-2">
                    <a href="{{route('invoices.index')}}"
     class="sidebar-link flex items-center p-3  transition-colors duration-200
        {{ Request::is(patterns: 'admin/invoices*') ? 'bg-[#185D31] text-white rounded-xl': 'text-gray-700 hover:bg-[#185D31] hover:text-white rounded-xl' }}">                        <i class="fas fa-file-invoice ml-3 text-gray-500"></i>
                        <span class="sidebar-text">الفواتير</span>
                    </a>
                </li>


                <li class="mb-2">
                    <a href="{{ route('admin.documents.index') }}"
     class="sidebar-link flex items-center p-3  transition-colors duration-200
        {{ Request::is(patterns: 'admin/documents*') ? 'bg-[#185D31] text-white rounded-xl': 'text-gray-700 hover:bg-[#185D31] hover:text-white rounded-xl' }}">   
                               <i class="fas fa-file-alt ml-3 text-gray-500"></i>
                        <span class="sidebar-text">مراجعة الوثائق</span>
                    </a>
                </li>
           <li class="mb-2"
    x-data="{ open: {{
        (
            Request::is('admin/products*') 
                    ) ? 'true' : 'false' }} }">

    <a href="#"
       @click.prevent="open = !open"
       class="sidebar-link flex items-center p-3 text-gray-700 hover:bg-[#185D31] hover:text-white rounded-xl transition-colors duration-200">
        <i class="fas fa-box ml-3"></i>
        <span class="sidebar-text">إدارة المنتجات</span>
        <i :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"
           class="fas mr-auto text-gray-500 text-xs sidebar-text"></i>
    </a>

    <ul x-show="open" x-transition class="mt-2 space-y-2 pr-6 sidebar-sub-menu">

        {{-- المنتجات --}}
        <li>
            <a href="{{ route('admin.products.index') }}"
               class="flex items-center p-2 transition-colors duration-200
               {{ Request::is('admin/products*') ? 'bg-[#185D31] text-white rounded-xl' : 'text-gray-600 hover:bg-[#185D31] hover:text-white rounded-xl' }}">
               المنتجات
            </a>
        </li>

        {{-- الفئات --}}
        <li>
            <a href="{{ route('admin.categories.index') }}"
               class="flex items-center p-2 transition-colors duration-200
               {{ Request::is('admin/categories*') ? 'bg-[#185D31] text-white rounded-xl' : 'text-gray-600 hover:bg-[#185D31] hover:text-white rounded-xl' }}">
               الفئات
            </a>
        </li>

    </ul>
</li>
          <li class="mb-2"
    x-data="{ open: {{
        (
            Request::is('admin/orders*')
            ) ? 'true' : 'false' }} }">

    <a href="#"
       @click.prevent="open = !open"
       class="sidebar-link flex items-center p-3 text-gray-700 hover:bg-[#185D31] hover:text-white rounded-xl transition-colors duration-200">
                        <i class="fas fa-shopping-cart ml-3 text-gray-500"></i>
        <span class="sidebar-text">إدارة الطلبات</span>
        <i :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"
           class="fas mr-auto text-gray-500 text-xs sidebar-text"></i>
    </a>

    <ul x-show="open" x-transition class="mt-2 space-y-2 pr-6 sidebar-sub-menu">

        {{-- الطلبات --}}
        <li>
            <a href="{{ route('admin.orders.index') }}"
               class="flex items-center p-2 transition-colors duration-200
               {{ Request::is('admin/orders*') ? 'bg-[#185D31] text-white rounded-xl' : 'text-gray-600 hover:bg-[#185D31] hover:text-white rounded-xl' }}">
               الطلبات
            </a>
        </li>

        {{-- التسويات --}}
     <li>
    <a href="{{ route('settlements.index') }}"
       class="flex items-center p-2 transition-colors duration-200
       {{ Request::is('admin/settlements*') 
            ? 'bg-[#185D31] text-white rounded-xl' 
            : 'text-gray-600 hover:bg-[#185D31] hover:text-white rounded-xl' }}">
        التسويات
    </a>
</li>


    </ul>
</li>
                <li class="mb-2">
                    <a href="{{route('admin.messages.index')}}"
 class="flex items-center p-2 transition-colors duration-200
       {{ Request::is('admin/settlements*') 
            ? 'bg-[#185D31] text-white rounded-xl' 
            : 'text-gray-600 hover:bg-[#185D31] hover:text-white rounded-xl' }}">                        <i class="fas fa-comments ml-3 text-gray-500"></i>
                        <span class="sidebar-text">إدارة المحادثات</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a  href="{{ route('admin.reviews.index') }}"
                  class="sidebar-link flex items-center p-3  transition-colors duration-200
                  {{ Request::is(patterns: 'admin/reviews*') ? 'bg-[#185D31] text-white rounded-xl': 'text-gray-700 hover:bg-[#185D31] hover:text-white rounded-xl' }}">   
                                           <i class="fas fa-star ml-3 text-gray-500"></i>
                        <span class="sidebar-text">مراجعة التقييمات</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('admin.reports') }}"
                  class="sidebar-link flex items-center p-3  transition-colors duration-200
                  {{ Request::is(patterns: 'admin/reports*') ? 'bg-[#185D31] text-white rounded-xl': 'text-gray-700 hover:bg-[#185D31] hover:text-white rounded-xl' }}">              
                               <i class="fas fa-chart-line ml-3 text-gray-500"></i>
                        <span class="sidebar-text">التقارير والإحصائيات</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('admin.notifications.index') }}"
                  class="sidebar-link flex items-center p-3  transition-colors duration-200
                  {{ Request::is(patterns: 'admin/notifications*') ? 'bg-[#185D31] text-white rounded-xl': 'text-gray-700 hover:bg-[#185D31] hover:text-white rounded-xl' }}">   
                         <i class="fas fa-tags ml-3 text-gray-500"></i>
                        <span class="sidebar-text">العروض والإشعارات</span>
                    </a>
                </li>
                <li class="mb-2"
    x-data="{ open: {{
        (
            Request::is('admin/profile*') 
     
        ) ? 'true' : 'false' }} }">

    <a href= "#"
       @click.prevent="open = !open"
       class="sidebar-link flex items-center p-3 transition-colors duration-200 rounded-xl text-gray-700">
        <i class="fas fa-cog ml-3 text-gray-500"></i>
        <span class="sidebar-text"> الإعدادات</span>
        <i :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"
           class="fas mr-auto text-gray-500 text-xs sidebar-text"></i>
    </a>

    <ul x-show="open" x-transition class="mt-2 space-y-2 pr-6 sidebar-sub-menu">

        <li>
            <a href="{{ route('admin.profile.index') }}"
               class="flex items-center p-2 transition-colors duration-200
               {{ (Request::is('admin/profile*')) ? 'bg-[#185D31] text-white rounded-xl' : 'text-gray-600 hover:bg-[#185D31] hover:text-white rounded-xl' }}">
              الملف الشخصي
            </a>
        </li>

        <li>
            <a href="{{ route('admin.contact.settings') }}"
                             class="flex items-center p-2 transition-colors duration-200
               {{ (Request::is('admin/contact-settings*')) ? 'bg-[#185D31] text-white rounded-xl' : 'text-gray-600 hover:bg-[#185D31] hover:text-white rounded-xl' }}">
             إعدادات التواصل 
            </a>
        </li>

        <li>
            <a href="#"
               class="flex items-center p-2 transition-colors duration-200">
            الشروط و الاحكام
            </a>
        </li>
     <li>
            <a href="#"
               class="flex items-center p-2 transition-colors duration-200">
           نسبة الأرباح 
            </a>
        </li>
    </ul>
</li>
 
            </ul>
        </nav>

        <div class="mt-auto pt-4 border-t border-gray-200">
            <div class="flex items-center p-3">
                <i class="fas fa-question-circle ml-3 text-gray-500"></i>
                <span class="sidebar-text">الأسئلة الشائعة</span>
            </div>
            <div class="flex items-center p-3 mt-2">
                <img src="https://placehold.co/40x40/E0F2F1/004D40?text=A" alt="Admin Profile"
                    class="w-10 h-10 rounded-full ml-3 flex-shrink-0 profile-picture">
                <div class="profile-info">
                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->full_name ?? 'أحمد محمد' }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email ?? 'admin@gmail.com' }}</p>
                </div>
            </div>
            <div class="mt-4">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                        <span class="sidebar-text">تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div id="main-content-area" class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Navigation Bar -->
        <header class="flex items-center justify-between bg-white shadow-md p-4 z-10">
            <div class="flex items-center">
                <!-- Desktop Sidebar Toggle Button (Moved here) -->
                <button id="sidebar-toggle"
                    class="desktop-collapse-button p-2 rounded-full hover:bg-[#185D31] hover:text-white focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200 ml-4">
                    <i class="fas fa-chevron-right text-gray-600" id="collapse-icon"></i>
                </button>
                <!-- Mobile Menu Toggle Button (Hamburger) -->
                <button id="mobile-menu-toggle"
                    class="mobile-menu-button text-gray-500 hover:text-gray-700 focus:outline-none md:hidden ml-4">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div>
                <h1 class="text-xl font-semibold text-gray-800">مرحباً {{ Auth::user()->first_name}}</h1>
                <p class="text-gray-500 text-sm hidden md:block">مرحباً بك مرة أخرى في لوحة التحكم</p>
                </div>
            </div>
            <div class="flex items-center space-x-4 w-[271px] justify-between">

                <script src="//unpkg.com/alpinejs" defer></script>

                @php
                    $currentLang = app()->getLocale();
                @endphp

                <!-- Language Dropdown -->
                <div x-data="{ open: false }"  x-cloak class="language btn-group flex items-center relative"
                    style="color: #212121; width:90px; height:24px;">

                    <!-- Toggle -->
                    <div class="dropdown flex items-center cursor-pointer" @click="open = !open">
                        <img src="{{ asset('images/Vector (2).svg') }}" alt="Language Icon"
                            class="w-[16px] h-[16px] rtl:ml-2 ltr:mr-2">
                        <span class="text-[#212121] text-sm md:text-base">
                            {{ $currentLang == 'ar' ? 'العربية' : 'English' }}
                        </span>
                    </div>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute top-full mt-2 w-[180px] h-auto left-0 rounded-[12px] bg-[#FFFFFF] py-2 shadow-lg z-50">

                        <div class="flex items-center cursor-pointer py-3 px-4 text-base text-[#212121]"
                            onclick="window.location.href='{{ route('change.language', 'ar') }}'">
                            <input type="radio" value="arabic" {{ $currentLang == 'ar' ? 'checked' : '' }}
                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-6 h-6 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                                id="arabic">
                            <label for="arabic" class="text-neutral-700">{{ __('messages.arabic') }}</label>
                        </div>

                        <div class="flex items-center cursor-pointer py-3 px-4 text-base text-[#212121]"
                            onclick="window.location.href='{{ route('change.language', 'en') }}'">
                            <input type="radio" value="english" {{ $currentLang == 'en' ? 'checked' : '' }}
                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-6 h-6 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                                id="english">
                            <label for="english" class="text-neutral-700">{{ __('messages.english') }}</label>
                        </div>
                    </div>
                </div>

@include('admin.admin_notifications_popup')
                {{-- <button class="focus:outline-none">
                    <img src="{{ asset('images/interface-alert-alarm-bell-2--alert-bell-ring-notification-alarm--Streamline-Core.svg') }}"
                        alt="Notification Icon">
                </button> --}}


                <div class="flex items-center rtl:mr-6 ltr:ml-6">
                    <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/Unknown_person.jpg') }}"
                        class="w-10 h-10 rounded-full object-cover" id="profileImage" style="cursor: pointer;">
                </div>
            </div>
        </header>

      <!-- Dynamic Content Section -->
        @yield('content')
    </div>





    
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle'); // Desktop toggle button
        const collapseIcon = document.getElementById('collapse-icon');
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle'); // Mobile hamburger button

        // Function to toggle sidebar state for desktop
        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');

            // Toggle icon direction
            if (sidebar.classList.contains('collapsed')) {
                collapseIcon.classList.remove('fa-chevron-right');
                collapseIcon.classList.add('fa-chevron-left');
            } else {
                collapseIcon.classList.remove('fa-chevron-left');
                collapseIcon.classList.add('fa-chevron-right');
            }
        }

        // Function to handle mobile sidebar toggle (overlay)
        function toggleMobileSidebar() {
            sidebar.classList.toggle('active'); // 'active' class controls transform for mobile
        }

        // Event listeners
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', toggleMobileSidebar);
        }

        // Close mobile sidebar when clicking outside
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 768 && sidebar.classList.contains('active')) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnMobileToggle = mobileMenuToggle.contains(event.target);

                if (!isClickInsideSidebar && !isClickOnMobileToggle) {
                    sidebar.classList.remove('active');
                }
            }
        });

        // Optional: Close mobile sidebar if a link is clicked
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('active');
                }
            });
        });
    </script>
    <script>
        document.getElementById('language-icon').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default anchor behavior
            var dropdown = document.getElementById('language-dropdown');
            dropdown.classList.toggle('show'); // Toggle the dropdown visibility
        });
        // Optional: Close the dropdown if clicking outside of it
        window.onclick = function(event) {
            if (!event.target.matches('#language-icon') && !event.target.matches('#language-button')) {
                var dropdowns = document.getElementsByClassName("dropdown-menu");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
