
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
    $loginErrors = $errors->has('email') || $errors->has('password');
    $registerErrors = $errors->has('full_name') || $errors->has('phone_number') || $errors->has('confirm_password');
@endphp
@php
    $showLogin = session('showLoginModal') || $errors->has('email') || $errors->has('password');
@endphp

<div x-data="{
   showLogin: {{ session('showLoginModal', false) ? 'true' : 'false' }},
    showRegister: {{ session('showRegisterModal', false) ? 'true' : 'false' }},
    showOTP: false,
    showPassword: false,
    userType: 'customer',
    accountData: true,
    bussnissdata: false,
    // Add a state to check if there are login errors
    hasLoginErrors: {{ json_encode($loginErrors) }}
}"
{{-- Only allow closing if there are no login errors --}}
@click.self="!hasLoginErrors && (showLogin = false)">


    <div class="flex items-center space-x-4">
        <button @click="showLogin = true"
            class="w-[120px] h-[40px] bg-[#185D31] px-4 py-2 rounded-[12px] cursor-pointer text-white flex items-center justify-center font-semibold text-sm">
            {{ __('messages.register') }}
        </button>
    </div>


<div x-show="showLogin" x-transition x-cloak
    class="fixed inset-0 z-[50] flex items-center justify-center bg-black bg-opacity-50">

    <div style="background-image: url('{{ asset('images/4d2a165c129977b25a433b916ddfa33f089dcf9f.jpg') }}');"
        class="relative w-[90%] h-auto bg-cover bg-center flex flex-col justify-center items-center p-[24px] no-repeat overflow-visible rounded-lg shadow-lg">

        <button @click="showLogin = false"
            class="absolute top-3 right-3 z-50 bg-white p-1 rounded-full text-gray-500 hover:text-gray-700 focus:outline-none z-10">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- The global error display at the top is REMOVED as AJAX handles specific field errors --}}
        {{-- You can add a general alert here if needed for non-validation errors --}}

        {{-- IMPORTANT: Add x-data, @submit.prevent, and the submitForm method here --}}
        <form method="POST" action="{{ route('login') }}"
            x-data="{
                formData: { email: '', password: '', remember: false },
                errors: {},
                loading: false,
                submitForm() {
                    this.loading = true;
                    this.errors = {}; // Clear previous errors

                    axios.post(this.$el.action, this.formData)
                        .then(response => {
                            this.loading = false;
                            // Check for a redirect URL from the server
                            if (response.data.redirect) {
                                window.location.href = response.data.redirect;
                            } else {
                                // Handle other success cases if any, e.g., show a success message
                                // For login, typically it's always a redirect on success
                                console.log('Login successful, but no redirect URL provided.', response.data);
                            }
                        })
                        .catch(error => {
                            this.loading = false;
                            if (error.response) {
                                if (error.response.status === 422) {
                                    // Validation errors from Laravel
                                    this.errors = error.response.data.errors;
                                } else {
                                    // Other HTTP errors (e.g., 500 server error)
                                    console.error('Server error:', error.response.data);
                                    // You might want a general error message for the user here
                                    alert('An unexpected server error occurred. Please try again.');
                                }
                            } else {
                                // Network error or other unexpected issues
                                console.error('Network or other error:', error);
                                alert('Could not connect to the server. Please check your internet connection.');
                            }
                        });
                }
            }"
            @submit.prevent="submitForm"> {{-- Prevent default form submission for AJAX --}}

            <div class="relative bg-white w-full w-[588px] h-auto p-[24px] rounded-[12px] shadow-xl">

                @csrf {{-- CSRF token is still required for AJAX POST requests --}}
                <p class="text-[32px] text-[#212121] font-bold">{{ __('messages.login') }}</p>
                <p class="text-[20px] text-[#767676] mb-4">{{ __('messages.loginMSG') }}</p>

                <div class="mb-4">
                    <label for="email" class="block font-bold text-[20px] text-[#212121]">
                        {{ __('messages.email') }}
                    </label>
                    <div class="flex items-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                        :class="{'border-[#d33]': errors.email, 'border-[#767676]': !errors.email}">
                        <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                            src="{{ asset('images/mail-send-envelope--envelope-email-message-unopened-sealed-close--Streamline-Core.svg') }}"
                            alt="">
                        {{-- Use x-model to bind input to formData --}}
                        <input type="email" name="email" id="email" required
                            placeholder="example@gmail.com" x-model="formData.email"
                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    {{-- Display specific email error using Alpine.js --}}
                    <template x-if="errors.email">
                        <p class="text-red-500 text-xs mt-1" x-text="errors.email"></p>
                    </template>
                </div>

                <div class="mb-4" x-data="{showPassword: false}">
                    <label for="password" class="block font-bold text-[20px] text-[#212121]">
                        {{ __('messages.password') }}
                    </label>
                    <div class="flex items-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                        :class="{'border-[#d33]': errors.password, 'border-[#767676]': !errors.password}">
                        <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                            src="{{ asset('images/interface-lock--combination-combo-lock-locked-padlock-secure-security-shield-keyhole--Streamline-Core.svg') }}"
                            alt="">
                        {{-- Use x-model to bind input to formData --}}
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                            placeholder="{{ __('messages.passwordMSG') }}" x-model="formData.password"
                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        {{-- Password visibility toggle --}}
                                <button @click.prevent="showPassword = !showPassword"
                                    class="ml-2 px-2 text-sm text-[#212121]">
                                    <span x-show="!showPassword"><img
                                            src="{{ asset('images/interface-edit-view-off--disable-eye-eyeball-hide-off-view--Streamline-Core.svg') }}"
                                            alt=""></span>
                                    <span x-show="showPassword">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                            <path
                                                d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                            <path
                                                d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                        </svg>
                                    </span>
                                </button>
                    </div>
                    {{-- Display specific password error using Alpine.js --}}
                    <template x-if="errors.password">
                        <p class="text-red-500 text-xs mt-1" x-text="errors.password"></p>
                    </template>
                </div>

                <div class="flex items-center mb-4 justify-between">
                    <div class="flex items-center">
                        {{-- Use x-model for remember checkbox --}}
                        <input type="checkbox" id="remember" name="remember" x-model="formData.remember"
                            class="h-4 w-4 ml-2 text-[#185D31] focus:ring-[#185D31] border-[#185D31] rounded">
                        <label for="remember" class="ml-2 text-[16px] text-[#212121]">
                            {{ __('messages.remember_me') }}
                        </label>
                    </div>
                    <a href="#" class="text-[16px] text-[#185D31] hover:underline">
                        {{ __('messages.forgot_password') }}
                    </a>
                </div>

                {{-- Submit button with loading state --}}
                <button type="submit" :disabled="loading"
                    class="w-full bg-[#185D31] text-white p-[12px] h-[48px] rounded-md hover:bg-green-800 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading">{{ __('messages.login') }}</span>
                    <span x-show="loading">
                        <div class="animate-spin inline-block w-6 h-6 border-[3px] border-current border-t-transparent text-white rounded-full" role="status" aria-label="loading">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </span>
                </button>

    
                <div class="flex items-center justify-center my-4 text-[#EDEDED]">
                    <hr class="flex-grow border-t border-gray-400">
                    <span class="text-sm text-gray-500 font-medium mx-4">{{ __('messages.or') }}</span>
                    <hr class="flex-grow border-t border-gray-400">
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-center h-[48px] w-full bg-[#F8F9FA] rounded-[12px]">
                        <img class="ml-3" src="{{ asset('images/Google.svg') }}" alt="">
                        <a href="{{ route('login.google') }}"
                            class="ml-2 text-[#212121] text-[16px] hover:underline">
                            {{ __('messages.login_with_google') }}
                        </a>
                    </div>
                    <div class="flex items-center justify-center h-[48px] w-full bg-[#F8F9FA] rounded-[12px]">
                        <img class="ml-3" src="{{ asset('images/Facebook.svg') }}" alt="">
                        <a href="{{ route('login.facebook') }}"
                            class="ml-2 text-[#212121] text-[16px] hover:underline">
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
            </div>
        </form>

    </div>
</div>


    @include('partials.customerReg')






</div>

