    <div class="user-profile-section shrink-0 order-7">
        @auth
            <div class="p-[15px]">
                <div class="dropdown relative w-full sm:w-auto" x-data="{ profile: false }">
                    <a class="btn p-0 border-0 bg-transparent" @click="profile = !profile" aria-expanded="false"
                        id="dropdownButton">
                        <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/Unknown_person.jpg') }}"
                            class="w-10 h-10 rounded-full object-cover" id="profileImage" style="cursor: pointer;">
                    </a>


       <ul x-show="profile" @click.away="profile = false" x-cloak
    class=" shadow h-auto rounded-lg p-3 absolute
           top-[30px]   
           w-[calc(100vw-30px)] max-w-[296px]   
           rtl:left-0 ltr:right-0  sm:w-[296px] 
           mt-2 bg-white z-50"
    style="min-width: 220px;"> 
                     <style>
    @media (max-width: 640px) {

        /* Adjust this breakpoint as needed */
        .profile-menu {
            position: fixed !important;
            /* Change to fixed for mobile */
            top: 0 !important;
            /* Position it at the top of the viewport */
            **left: 0 !important;**
            /* FIX: Must be 0 to appear on screen */
            right: auto !important; 
            /* Ensures it doesn't conflict with any right-0 settings */
            width: 100% !important;
            /* Full width */
            max-width: none !important;
            /* Remove max-width for mobile */
            margin-top: 0 !important;
            /* Remove top margin */
        }
    }
</style>
                        <li class="flex items-center mb-2 border-b pb-3">
                            <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/Unknown_person.jpg') }}"
                                class="w-10 h-10 me-2 rounded-full object-cover">
                            <div>
                                <span class="text-base text-[#121212]">{{ Auth::user()->full_name }}</span><br>
                                <small class="sm:text-sm text-gray-500 text-[10px]">{{ Auth::user()->email }}</small>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded"
                                href="{{ route('profile.show', parameters: ['section' => 'myAccountContentSection']) }}#myAccountContentSection">
                                {{ __('messages.MyAccount') }}
                            </a>
                        </li>
                        
                        <li>
                            <a class="dropdown-item pb-4 block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded"
                                href="{{ route('profile.show', ['section' => 'myOrdersSection']) }}#myOrdersSection">
                                {{ __('messages.MyOrders') }}</a>
                        </li>

                        <li>
                            <a class="dropdown-item block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded"
                                href="{{ route('profile.show', ['section' => 'favoritesSection']) }}#favoritesSection">
                                {{ __('messages.Fav') }}
                            </a>
                        </li>
                        
                        <li>
                            <a class="dropdown-item pb-4 block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded"
                                href="{{ route('profile.show', ['section' => 'notificationsSection']) }}#notificationsSection">

                                {{ __('messages.settings_notifications') }}</a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="dropdown-item pb-4 w-full text-gray-700 hover:bg-gray-100 px-3 py-2 rounded">
                                    {{ __('messages.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        @else
            @include('partials.login')
        @endauth
    </div>