<style>
    .entity_type_selector {
        display: flex;
        background-color: #EDEDED;
        border-radius: 12px;
        margin-bottom: 16px;
        overflow: hidden;
        height: 48px;
    }

    .entity_type_selector input[type="radio"] {
        display: none;
    }

    .entity_type_selector label {
        flex-grow: 1;
        text-align: center;
        padding-top: 12px;
        padding-bottom: 12px;
        font-size: 16px;
        padding-left: 20px;
        padding-right: 20px;
        cursor: pointer;
        font-weight: bold;
        color: #212121;
        transition: background-color 0.3s ease;
        border-radius: 12px;
        background-color: transparent;
    }

    .entity_type_selector input[type="radio"]:checked+label {
        background-color: #185D31;
        color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .form-section.hidden {
        display: none;
    }

    [x-cloak] {
        display: none !important;
    }

    .file-input-container {
        position: relative;
        display: inline-block;
    }

    .file-input {
        display: none;
        /* Hide the default file input */
    }

    .custom-file-input {
        display: inline-block;
        padding: 8px 12px;
        background-color: #185D31;
        color: #FFFFFF;
        border-radius: 12px;
        cursor: pointer;
        text-align: center;
        width: 85px;
        height: 32px;
    }

    .file-name {
        margin-left: 10px;
        color: #000;
        /* Change as needed */
    }
</style>
@php
    $hasRegisterErrors = $errors->has('full_name') || $errors->has('email') || $errors->has('phone_number') || $errors->has('password') || $errors->has('confirm_password');
@endphp


<div x-data="{ showLogin:  {{ $errors->any() ? 'true' : 'false' }}, 
    showRegister:  {{ $errors->any() ? 'true' : 'false' }}, 
    showOTP:  {{ $errors->any() ? 'true' : 'false' }}, 
    userType: 'customer', accountData: true, bussnissdata: false }">
    <div class="flex items-center space-x-4">
        <button @click="showLogin = true"
            class="w-[120px] h-[40px] bg-[#185D31] px-4 py-2 rounded-[12px] cursor-pointer text-white flex items-center justify-center font-semibold text-sm">
            {{ __('messages.register') }}
        </button>
    </div>


    <div x-show="showLogin" x-transition x-cloak
        class="fixed inset-0 z-[50] flex items-center justify-center bg-black bg-opacity-50"
        style="backdrop-filter: blur(2px);" @click.self="showLogin = false">
        <div style="background-image: url('{{ asset('images/4d2a165c129977b25a433b916ddfa33f089dcf9f.jpg') }}');"
            class="relative w-[90%] h-auto bg-cover bg-center flex flex-col justify-center items-center p-[24px] no-repeat overflow-visible rounded-lg shadow-lg">
            <button @click="showLogin = false"
                class="absolute top-3 right-3 z-50 bg-white p-1 rounded-full text-gray-500 hover:text-gray-700 focus:outline-none z-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="relative bg-white w-full w-[588px] h-auto p-[24px] rounded-[12px] shadow-xl">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <p class="text-[32px] text-[#212121] font-bold">{{ __('messages.login') }}</p>
                    <p class="text-[20px] text-[#767676] mb-4">{{ __('messages.loginMSG') }}</p>
                    <div class="mb-4">
                        <label for="email" class="block font-bold text-[20px] text-[#212121]">
                            {{ __('messages.email') }}
                        </label>
                        <div
                            class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                            <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                src="{{ asset('images/mail-send-envelope--envelope-email-message-unopened-sealed-close--Streamline-Core.svg') }}"
                                alt="">
                            <input type="email" name="email" required placeholder="example@gmail.com"
                                class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block font-bold text-[20px] text-[#212121]">
                            {{ __('messages.password') }}
                        </label>
                        <div
                            class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                            <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                src="{{ asset('images/interface-lock--combination-combo-lock-locked-padlock-secure-security-shield-keyhole--Streamline-Core.svg') }}"
                                alt="">
                            <input type="password" name="password" required
                                placeholder="{{ __('messages.passwordMSG') }}"
                                class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="flex items-center mb-4 justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember"
                                class="h-4 w-4 ml-2 text-[#185D31] focus:ring-[#185D31] border-[#185D31] rounded">
                            <label for="remember" class="ml-2 text-[16px] text-[#212121]">
                                {{ __('messages.remember_me') }}
                            </label>
                        </div>
                        <a href="#" class="text-[16px] text-[#185D31] hover:underline">
                            {{ __('messages.forgot_password') }}
                        </a>
                    </div>
                    <button type="submit"
                        class="w-full bg-[#185D31] text-white p-[12px] h-[48px] rounded-md hover:bg-green-800">
                        {{ __('messages.login') }}
                    </button>
                    <div class="flex items-center justify-center my-4 text-[#EDEDED]">
                        <hr class="flex-grow border-t border-gray-400">
                        <span class="text-sm text-gray-500 font-medium mx-4">{{ __('messages.or') }}</span>
                        <hr class="flex-grow border-t border-gray-400">
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-center h-[48px] w-full bg-[#F8F9FA] rounded-[12px]">
                            <img class="ml-3" src="{{ asset('images/Google.svg') }}" alt="">
                            <a href="{{ route('login.google') }}"  class="ml-2 text-[#212121] text-[16px] hover:underline">
                                {{ __('messages.login_with_google') }}
                            </a>
                        </div>
                        <div class="flex items-center justify-center h-[48px] w-full bg-[#F8F9FA] rounded-[12px]">
                            <img class="ml-3" src="{{ asset('images/Facebook.svg') }}" alt="">
                            <a href="{{ route('login.facebook') }}" class="ml-2 text-[#212121] text-[16px] hover:underline">
                                {{ __('messages.login_with_facebook') }}
                            </a>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <p class="text-[16px] text-[#212121]">
                            {{ __('messages.dont_have_account') }}
                            <a href="#" @click="showRegister = true; showLogin = false"
                                class="text-[#185D31] underline">
                                {{ __('messages.create__new_account') }}
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>



@include('partials.customerReg')






</div>
