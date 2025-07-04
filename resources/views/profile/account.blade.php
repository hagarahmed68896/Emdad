@extends('layouts.app')

@section('content')
    <style>
        /* Modal specific styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1000;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 30px;
            border-radius: 1rem;
            /* Tailwind's rounded-xl equivalent */
            width: 90%;
            /* Responsive width */
            max-width: 500px;
            /* Max width */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            /* Tailwind's shadow-lg equivalent */
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Added for notification toggles */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 28px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #185D31; /* Your green color */
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #185D31;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(22px);
            -ms-transform: translateX(22px);
            transform: translateX(22px);
        }
    </style>

    <div class="min-h-screen flex flex-col items-center py-8">
        <div class="w-full bg-white rounded-xl shadow-lg flex flex-col md:flex-row overflow-hidden">
            <aside class="w-full md:w-1/4 bg-gray-50 p-6 border-b md:border-b-0 md:border-l border-gray-200">
                <div class="flex flex-col items-center pb-6 border-b border-gray-200 mb-6">
                    <div class="relative w-28 h-28 mb-4">
                        <img id="profilePageImage"
                            src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/Unknown_person.jpg') }}"
                            class="w-full h-full rounded-full object-cover shadow-md border-2 border-gray-300">
                        <button id="openProfileModalBtn"
                            class="absolute bottom-0 left-0 bg-[#185D31] rounded-full p-2 shadow-md hover:bg-green-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-400">
                            {{-- SVG Pen Icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-7.65 7.65a2 2 0 01-1.287.587L5.5 15.5l.004-.004a2 2 0 01.587-1.287l7.65-7.65zM10.742 7.003L14 3.745l2.255 2.255-3.258 3.258a1 1 0 01-.482.261L10 9l-.004-.004a1 1 0 01-.261-.482l-3.258-3.258z" />
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <input type="file" id="profilePictureInput" accept="image/*" class="hidden">
                    </div>
                    <button id="saveProfilePhotoBtn"
                        class="mt-2 px-4 py-2 bg-[#185D31] text-white rounded-lg shadow-md hover:bg-[#185D31] transition-colors duration-200 hidden">
                        {{ __('messages.save_image') }} </button>
                    <div id="uploadLoading" class="hidden mt-2 text-sm text-gray-600">{{ __('messages.loading') }} </div>
                    <div id="uploadMessage" class="mt-2 text-sm text-center"></div>

                    {{-- User's full name displayed --}}
                    <h2 class="text-xl font-semibold text-gray-800">{{ Auth::user()->full_name }} </h2>
                </div>

                <nav>
                    <ul class="space-y-2">
                        <li>
                            <a href="#" id="myAccountLink"
                                class="flex items-center p-3 text-lg font-medium text-white bg-[#185D31] rounded-lg hover:text-white hover:bg-[#185D31] transition-colors duration-200 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('messages.MyAccount') }}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="myOrdersLink"
                                class="flex items-center p-3 text-lg text-gray-700 rounded-lg hover:text-white hover:bg-[#185D31] transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                {{ __('messages.MyOrders') }}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="messagesLink"
                                class="flex items-center p-3 text-lg text-gray-700 rounded-lg  hover:text-white hover:bg-[#185D31] transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                {{ __('messages.messages') }}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="favLink"
                                class="flex items-center p-3 text-lg text-gray-700 rounded-lg hover:text-white hover:bg-[#185D31] transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                {{ __('messages.Fav') }}
                            </a>
                        </li>
                        <li>
                            <a href="#" id="notificationsLink"
                                class="flex items-center p-3 text-lg text-gray-700 rounded-lg hover:text-white hover:bg-[#185D31] transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                {{ __('messages.settings_notifications') }}
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center p-3 text-lg text-gray-700 rounded-lg hover:bg-red-100 transition-colors duration-200 w-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-3" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    {{ __('messages.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </aside>

            <main class="w-full md:w-3/4 p-6 bg-white">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2" id="mainContentTitle">{{ __('messages.MyAccount') }}</h1>
                    <p class="text-sm text-gray-500" id="breadcrumbs">{{ __('messages.home') }} <span class="mx-1">&gt;</span> {{ __('messages.MyAccount') }}</p>
                </div>

                {{-- My Account and Password Section --}}
<section id="myAccountContentSection"
    class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-200 {{ request('section') === 'favoritesSection' ? 'hidden' : '' }}">
          <h2 class="text-2xl font-semibold text-gray-800 mb-6">{{ __('messages.account_details') }}</h2>

                    {{-- START: Account Details Form (Now powered by Alpine.js AJAX) --}}
                    {{-- Pass the initial user data to the Alpine component --}}
                    <div x-data="accountDetailsForm({{ json_encode($user) }})"> 
                        
                        {{-- Success Message for account details form--}}
                        <div x-show="success" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2"
                            class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4" 
                            x-text="success"
                            x-init="setTimeout(() => success = '', 5000)"></div>

                        {{-- Error List for account details form--}}
                        <template x-if="Object.keys(errors).length">
                            <ul class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm list-disc list-inside"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 transform translate-y-0"
                                x-transition:leave-end="opacity-0 transform -translate-y-2">
                                <template x-for="[key, messages] of Object.entries(errors)" :key="key">
                                    <li x-text="messages[0]"></li>
                                </template>
                            </ul>
                        </template>

                        <form @submit.prevent="submitDetailsForm" action="{{ route('profile.updateDetails') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="first_name" class="block text-gray-700 text-sm font-medium mb-2">{{ __('messages.first_name') }}</label>
                                    <input type="text" id="first_name" name="first_name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                        placeholder="أدخل اسمك الأول" x-model="formData.first_name">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-gray-700 text-sm font-medium mb-2">
                                        {{ __('messages.last_name') }}</label>
                                    <input type="text" id="last_name" name="last_name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                        placeholder="{{ __('messages.enter_family_name') }}" x-model="formData.last_name">
                                </div>
                                <div>
                                    <label for="email" class="block text-gray-700 text-sm font-medium mb-2">
                                        {{ __('messages.email') }}</label>
                                    <input type="email" id="email" name="email"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                        placeholder="{{ __('messages.emailMSG') }}"
                                        x-model="formData.email">
                                </div>
                                <div>
                                    <label for="phone_number" class="block text-gray-700 text-sm font-medium mb-2">
                                        {{ __('messages.phone_number') }}</label>
                                    <input type="text" id="phone_number" name="phone_number" maxlength="9"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                        placeholder=" {{ __('messages.phoneMSG') }}" x-model="formData.phone_number">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="address" class="block text-gray-700 text-sm font-medium mb-2">{{ __('messages.address') }}</label>
                                    <input type="text" id="address" name="address"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                        placeholder="{{ __('messages.addressMSG') }}" x-model="formData.address">
                                </div>
                            </div>
                            <div class="flex justify-start gap-4 mb-8">
                                <button type="submit"
                                    class="px-6 py-2 text-white rounded-lg bg-[#185D31] transition-colors duration-200 shadow-md">{{ __('messages.save') }}</button>
                            </div>
                        </form>
                    </div>
                    {{-- END: Account Details Form --}}

                    {{-- Password Form (Your existing Alpine.js form) --}}
                    <div x-data="passwordForm()" class=" bg-white mx-auto">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6">{{__('messages.change_password')}}</h2>
                        {{-- Success Message for password form--}}
                        <div x-show="success" x-cloak
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2"
                            class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4" 
                            x-text="success"
                            x-init="setTimeout(() => success = '', 5000)"></div>

                        {{-- Error List for password form--}}
                        <template x-if="Object.keys(errors).length">
                            <ul class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm list-disc list-inside"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 transform translate-y-0"
                                x-transition:leave-end="opacity-0 transform -translate-y-2">
                                <template x-for="[key, messages] of Object.entries(errors)" :key="key">
                                    <li x-text="messages[0]"></li>
                                </template>
                            </ul>
                        </template>

                        <form @submit.prevent="submitPasswordForm" action="{{ route('profile.updatePassword') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div class="md:col-span-2">
                                    <label for="current_password" class="block text-gray-700 text-sm font-medium mb-2">{{__('messages.last_password')}}</label>
                                    <input type="password" id="current_password" name="current_password" x-model="formData.current_password"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                        placeholder="{{__('messages.last_password_msg')}}">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="password" class="block text-gray-700 text-sm font-medium mb-2"> 
                                        {{__('messages.new_password')}}</label>
                                    <input type="password" id="password" name="password" x-model="formData.password"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                        placeholder="{{__('messages.new_password_msg')}}">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="password_confirmation"
                                        class="block text-gray-700 text-sm font-medium mb-2">{{__('messages.confirm_passwordMSG')}}</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" x-model="formData.password_confirmation"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                        placeholder="{{__('messages.confirm_passwordMSG')}}">
                                </div>
                            </div>
                            <div class="flex justify-start gap-4">
                                <button type="submit"
                                    class="px-6 py-2 text-white rounded-lg bg-[#185D31] transition-colors duration-200 shadow-md">{{__('messages.save')}}</button>
                            </div>
                        </form>
                    </div>
                </section>

                {{-- Favorites Section --}}
<section id="favoritesSection"
    class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-200 {{ request('section') === 'favoritesSection' ? '' : 'hidden' }}">
    @include('partials.favorites', ['favorites' => $favorites])
</section>


                {{-- Notifications Section (New!) --}}
 <section id="notificationsSection" class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-200 {{ request('section') === 'notificationsSection' ? '' : 'hidden' }}">

    {{-- Pass notificationSettings to Alpine.js --}}
    <div x-data="notificationsForm({{ json_encode($notificationSettings) }})"> 
        {{-- Success/Error Messages for notifications --}}
        <div x-show="success" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4" 
            x-text="success"
            x-init="setTimeout(() => success = '', 5000)"></div>

        <template x-if="Object.keys(errors).length">
            <ul class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm list-disc list-inside"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2">
                <template x-for="[key, messages] of Object.entries(errors)" :key="key">
                    <li x-text="messages[0]"></li>
                </template>
            </ul>
        </template>

        <form @submit.prevent="submitNotificationsForm" action="{{ route('profile.updateNotifications') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <label for="receive_in_app" class="text-gray-700">{{ __('messages.receive_in_app') }}</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="receive_in_app" name="receive_in_app" x-model="formData.receive_in_app">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <label for="receive_chat" class="text-gray-700">{{ __('messages.receive_chat') }}</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="receive_chat" name="receive_chat" x-model="formData.receive_chat">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <label for="order_status_updates" class="text-gray-700">{{ __('messages.order_status_updates') }}</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="order_status_updates" name="order_status_updates" x-model="formData.order_status_updates">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <label for="offers_discounts" class="text-gray-700">{{ __('messages.offers_discounts') }}</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="offers_discounts" name="offers_discounts" x-model="formData.offers_discounts">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between py-2">
                    <label for="viewed_products_offers" class="text-gray-700">{{ __('messages.viewed_products_offers') }}</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="viewed_products_offers" name="viewed_products_offers" x-model="formData.viewed_products_offers">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-start gap-4 mt-8">
                <button type="submit"
                    class="px-6 py-2 text-white rounded-lg bg-[#185D31] transition-colors duration-200 shadow-md">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</section>
                {{-- End Notifications Section --}}


                @if (session('success'))
                    <div class="mt-4 p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mt-4 p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
            </main>
        </div>
    </div>

    <div id="profileModal" class="modal">
        <div class="modal-content text-center">
            <h3 class="text-2xl font-bold mb-6 text-gray-800">{{ __('messages.profile_image') }}</h3>
            {{-- Image displayed within the modal, showing current profile picture or your specified placeholder --}}
            <img id="modalProfileImage"
                src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/Unknown_person.jpg') }}"
                alt="Current Profile"
                class="w-48 h-48 rounded-full object-cover mx-auto mb-6 border-4 border-gray-200 shadow-lg">

            <div class="flex flex-col space-y-4">
                {{-- Button to trigger the hidden file input for changing the photo --}}
                <button id="changePhotoBtn"
                    class="flex items-center justify-center px-6 py-3 bg-[#185D31] text-white rounded-lg shadow-md hover:bg-[#185D31] transition-colors duration-200 text-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L20 16m-2-6a2 2 0 100-4 2 2 0 000 4z" />
                    </svg>
                    {{ __('messages.change_image') }}
                </button>
                {{-- Button to remove the current profile picture. Only shown if a profile picture exists. --}}
                @if (Auth::user()->profile_picture)
                    <button id="removePhotoBtn"
                        class="flex items-center justify-center px-6 py-3 bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 transition-colors duration-200 text-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ __('messages.remove_image') }}
                    </button>
                @endif
                {{-- Button to close the modal --}}
                <button id="closeModalBtn"
                    class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors duration-200 shadow-sm text-lg">
                    {{ __('messages.cancel') }}
                </button>
            </div>
            <div id="modalMessage" class="mt-4 text-sm text-center"></div>
        </div>
    </div>
 <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get references to main page elements
            const profileImage = document.getElementById('profilePageImage');
            const openProfileModalBtn = document.getElementById('openProfileModalBtn');
            const profilePictureInput = document.getElementById('profilePictureInput');
            const saveProfilePhotoBtn = document.getElementById('saveProfilePhotoBtn');
            const uploadLoading = document.getElementById('uploadLoading');
            const uploadMessage = document.getElementById('uploadMessage');

            // Get references to modal elements
            const profileModal = document.getElementById('profileModal');
            const modalProfileImage = document.getElementById('modalProfileImage');
            const changePhotoBtn = document.getElementById('changePhotoBtn');
            const removePhotoBtn = document.getElementById('removePhotoBtn');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const modalMessage = document.getElementById('modalMessage');

            let selectedFile = null;

            // Get references to content sections and navigation links
            const myAccountContentSection = document.getElementById('myAccountContentSection');
            const favoritesSection = document.getElementById('favoritesSection');
            const notificationsSection = document.getElementById('notificationsSection'); // NEW!

            const myAccountLink = document.getElementById('myAccountLink');
            const favLink = document.getElementById('favLink');
            const notificationsLink = document.getElementById('notificationsLink'); // NEW!

            const mainContentTitle = document.getElementById('mainContentTitle');
            const breadcrumbs = document.getElementById('breadcrumbs');

            // Pass translations from PHP to JavaScript
            const translations = {
                MyAccount: "{{ __('messages.MyAccount') }}",
                home: "{{ __('messages.home') }}",
                Fav: "{{ __('messages.Fav') }}",
                settings_notifications: "{{ __('messages.settings_notifications') }}" // NEW!
                // Add any other translations you might need dynamically here
            };

            // Function to manage active link styling
            function setActiveLink(activeLinkElement) {
                const navLinks = document.querySelectorAll('aside nav ul li a');
                navLinks.forEach(link => {
                    link.classList.remove('bg-[#185D31]', 'text-white', 'font-medium', 'shadow-sm');
                    link.classList.add('text-gray-700');
                });
                activeLinkElement.classList.add('bg-[#185D31]', 'text-white', 'font-medium', 'shadow-sm');
                activeLinkElement.classList.remove('text-gray-700');
            }

            // Function to show/hide content sections and update title/breadcrumbs
            function showContent(sectionId, titleKey, breadcrumbKey) {
                // Hide all main content sections
                myAccountContentSection.classList.add('hidden');
                favoritesSection.classList.add('hidden');
                notificationsSection.classList.add('hidden'); // NEW!

                // Show the requested section
                document.getElementById(sectionId).classList.remove('hidden');

                // Update title and breadcrumbs
                mainContentTitle.textContent = translations[titleKey];
                breadcrumbs.innerHTML = translations['home'] + ' <span class="mx-1">&gt;</span> ' + translations[breadcrumbKey];
            }

            // Determine section from URL query string
const urlParams = new URLSearchParams(window.location.search);
const section = urlParams.get('section');

switch (section) {
    case 'favoritesSection':
        setActiveLink(favLink);
        showContent('favoritesSection', 'Fav', 'Fav');
        break;
    case 'notificationsSection':
        setActiveLink(notificationsLink);
        showContent('notificationsSection', 'settings_notifications', 'settings_notifications');
        break;
    default:
        setActiveLink(myAccountLink);
        showContent('myAccountContentSection', 'MyAccount', 'MyAccount');
}



            // Event listener for "My Account" link
            myAccountLink.addEventListener('click', function(e) {
                e.preventDefault();
                setActiveLink(myAccountLink);
                showContent('myAccountContentSection', 'MyAccount', 'MyAccount');
            });

            // Event listener for "Fav" link
            favLink.addEventListener('click', function(e) {
                e.preventDefault();
                setActiveLink(favLink);
                showContent('favoritesSection', 'Fav', 'Fav');
            });

            // Event listener for "Notifications" link (NEW!)
            notificationsLink.addEventListener('click', function(e) {
                e.preventDefault();
                setActiveLink(notificationsLink);
                showContent('notificationsSection', 'settings_notifications', 'settings_notifications');
            });

            // --- Modal Open/Close Logic ---
            openProfileModalBtn.addEventListener('click', function() {
                modalProfileImage.src = profileImage.src;
                profileModal.style.display = 'flex';
                modalMessage.textContent = '';
            });

            closeModalBtn.addEventListener('click', function() {
                profileModal.style.display = 'none';
            });

            window.addEventListener('click', function(event) {
                if (event.target == profileModal) {
                    profileModal.style.display = 'none';
                }
            });

            // --- Change Photo (via hidden input triggered from modal) ---
            changePhotoBtn.addEventListener('click', function() {
                profilePictureInput.click();
                profileModal.style.display = 'none';
            });

            // --- Handle File Selection (from hidden input after modal closes) ---
            profilePictureInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    selectedFile = file;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profileImage.src = e.target.result;
                        saveProfilePhotoBtn.classList.remove('hidden');
                        uploadMessage.textContent = '';
                    };
                    reader.readAsDataURL(file);
                } else {
                    selectedFile = null;
                    saveProfilePhotoBtn.classList.add('hidden');
                }
                event.target.value = '';
            });

            // --- Handle Save Profile Photo Button Click (AJAX upload) ---
            saveProfilePhotoBtn.addEventListener('click', async function() {
                if (!selectedFile) {
                    uploadMessage.className = 'mt-2 text-sm text-red-500 text-center';
                    uploadMessage.textContent = 'الرجاء تحديد صورة لتحميلها.';
                    return;
                }

                uploadLoading.classList.remove('hidden');
                saveProfilePhotoBtn.classList.add('hidden');
                uploadMessage.textContent = '';

                const formData = new FormData();
                formData.append('profile_picture', selectedFile);
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const response = await fetch('{{ route('profile.updateProfilePicture') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    uploadLoading.classList.add('hidden');

                    if (response.ok) {
                        const newImageUrl = data.profile_picture_url + '?' + new Date().getTime();
                        profileImage.src = newImageUrl;
                        modalProfileImage.src = newImageUrl;

                        uploadMessage.className = 'mt-2 text-sm text-green-500 text-center';
                        uploadMessage.textContent = data.message || 'تم تحديث صورة الملف الشخصي بنجاح!';
                        selectedFile = null;
                        profilePictureInput.value = '';

                        if (removePhotoBtn) {
                            removePhotoBtn.classList.remove('hidden');
                        }
                    } else {
                        let errorMessage = 'حدث خطأ أثناء تحميل الصورة.';
                        if (data.message) {
                            errorMessage = data.message;
                        } else if (data.errors && data.errors.profile_picture) {
                            errorMessage = data.errors.profile_picture[0];
                        }
                        uploadMessage.className = 'mt-2 text-sm text-red-500 text-center';
                        uploadMessage.textContent = errorMessage;
                        saveProfilePhotoBtn.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error uploading profile picture:', error);
                    uploadLoading.classList.add('hidden');
                    uploadMessage.className = 'mt-2 text-sm text-red-500 text-center';
                    uploadMessage.textContent = 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.';
                    saveProfilePhotoBtn.classList.remove('hidden');
                }
            });

            // --- Handle Remove Photo Button Click (AJAX removal) ---
            @if (Auth::user()->profile_picture)
                removePhotoBtn.addEventListener('click', async function() {
                    if (!confirm('{{ __('messages.confirm_delete_image') }}')) {
                        return;
                    }

                    modalMessage.textContent = '';
                    uploadLoading.classList.remove('hidden'); // Use the same loading indicator or add a new one

                    try {
                        const response = await fetch('{{ route('profile.removeProfilePicture') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        uploadLoading.classList.add('hidden');

                        if (response.ok) {
                            const defaultImageUrl = '{{ asset('images/Unknown_person.jpg') }}';
                            profileImage.src = defaultImageUrl;
                            modalProfileImage.src = defaultImageUrl;
                            modalMessage.className = 'mt-4 text-sm text-green-500 text-center';
                            modalMessage.textContent = data.message || 'تم حذف الصورة الشخصية بنجاح.';
                            removePhotoBtn.classList.add('hidden'); // Hide remove button
                            saveProfilePhotoBtn.classList.add('hidden'); // Hide save button as no file selected
                            profilePictureInput.value = ''; // Clear file input
                            selectedFile = null; // Clear selected file
                        } else {
                            modalMessage.className = 'mt-4 text-sm text-red-500 text-center';
                            modalMessage.textContent = data.message || 'حدث خطأ أثناء حذف الصورة.';
                        }
                    } catch (error) {
                        console.error('Error removing profile picture:', error);
                        uploadLoading.classList.add('hidden');
                        modalMessage.className = 'mt-4 text-sm text-red-500 text-center';
                        modalMessage.textContent = 'حدث خطأ غير متوقع أثناء الحذف.';
                    }
                });
            @endif
        });

                // This typically goes into your app.js or a script tag before </body>
function passwordForm() {
    return {
        success: '',
        errors: {},
        formData: {
            current_password: '',
            password: '',
            password_confirmation: ''
        },

        async submitPasswordForm() {
            this.success = ''; // Clear previous messages
            this.errors = {}; // Clear previous errors

            try {
                // Get CSRF token from meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                const response = await fetch('{{ route('profile.updatePassword') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken // Send CSRF token
                    },
                    body: JSON.stringify(this.formData) // Send form data as JSON
                });

                const data = await response.json();

                if (!response.ok) {
                    // Handle HTTP errors (e.g., 422 Unprocessable Entity)
                    if (response.status === 422) {
                        this.errors = data.errors; // Laravel validation errors
                    } else {
                        // Generic error or server error
                        this.errors = { general: [data.message || 'An unexpected error occurred.'] };
                    }
                    this.success = ''; // Clear success if there was an error
                } else {
                    // Success!
                    this.success = data.message;
                    this.errors = {}; // Clear any previous errors
                    // Clear form fields on success
                    this.formData.current_password = '';
                    this.formData.password = '';
                    this.formData.password_confirmation = '';
                }

            } catch (error) {
                console.error('Error submitting password form:', error);
                this.errors = { general: ['Network error or something went wrong.'] };
                this.success = '';
            }
        }
    }
}
// Add this function to your JavaScript file (e.g., resources/js/app.js)
function accountDetailsForm(initialUser) { // Pass initial user data
    return {
        success: '',
        errors: {},
        formData: {
            first_name: initialUser.first_name || '',
            last_name: initialUser.last_name || '',
            email: initialUser.email || '',
            phone_number: initialUser.phone_number || '',
            address: initialUser.address || '',
        },

        async submitDetailsForm(event) {
            this.success = ''; // Clear previous messages
            this.errors = {}; // Clear previous errors

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const form = event.target; // Get the form element

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(this.formData) // Send form data as JSON
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors; // Laravel validation errors
                    } else {
                        this.errors = { general: [data.message || 'An unexpected error occurred.'] };
                    }
                    this.success = ''; // Clear success if there was an error
                } else {
                    // Success!
                    this.success = data.message;
                    this.errors = {}; // Clear any previous errors

                    // Optional: You might want to update the displayed user data
                    // if the controller returns the new user object, or simply rely
                    // on the x-model bindings to keep the form data updated.
                    // If you fetch `user` object again, update initialUser.
                }

            } catch (error) {
                console.error('Error submitting account details form:', error);
                this.errors = { general: ['Network error or something went wrong.'] };
                this.success = '';
            }
        }
    }
}
// Make the function available globally
window.accountDetailsForm = accountDetailsForm;


        // Alpine.js component for notifications form (NEW!)
        document.addEventListener('alpine:init', () => {
            Alpine.data('notificationsForm', (initialSettings) => ({
                formData: {
                    receive_in_app: initialSettings.receive_in_app || false,
                    receive_chat: initialSettings.receive_chat || false,
                    order_status_updates: initialSettings.order_status_updates || false,
                    offers_discounts: initialSettings.offers_discounts || false,
                    viewed_products_offers: initialSettings.viewed_products_offers || false,
                },
                success: '',
                errors: {},

                async submitNotificationsForm() {
                    this.errors = {}; // Clear previous errors
                    this.success = ''; // Clear previous success message

                    try {
                        const response = await fetch(this.$el.action, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.success = data.message || 'تم تحديث إعدادات الإشعارات بنجاح!';
                            // Optionally, update the formData if the backend sends back canonical values
                            // this.formData = { ...data.settings };
                        } else {
                            if (response.status === 422) { // Validation errors
                                this.errors = data.errors;
                            } else {
                                this.errors = {
                                    general: [data.message || 'حدث خطأ أثناء تحديث إعدادات الإشعارات.']
                                };
                            }
                        }
                    } catch (error) {
                        console.error('Error submitting notification form:', error);
                        this.errors = {
                            general: ['حدث خطأ في الشبكة. يرجى المحاولة مرة أخرى.']
                        };
                    }
                }
            }));
        });
    </script>
@endsection