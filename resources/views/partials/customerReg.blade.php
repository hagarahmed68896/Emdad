 <div x-show="showRegister" x-transition x-cloak
         class="fixed inset-0 z-[50] flex items-center justify-center bg-black bg-opacity-50"
         style="backdrop-filter: blur(2px);" @click.self="showRegister = false">
        <div style="background-image: url('{{ asset('images/4d2a165c129977b25a433b916ddfa33f089dcf9f.jpg') }}');"
             class="relative w-[90%] h-auto bg-cover bg-center flex flex-col justify-center items-center no-repeat
                    overflow-visible rounded-lg shadow-lg mt-[60px]">
            <button @click="showRegister = false"
                    class="absolute top-3 right-3 z-50 bg-white p-1 rounded-full text-gray-500 hover:text-gray-700 focus:outline-none z-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- THIS IS THE KEY CHANGE: Move x-data to this div --}}
            <div class="relative bg-white w-[588px] h-[832px] p-[60px] rounded-[12px] shadow-xl overflow-y-auto"
                 x-data="{ // This x-data is fine for form-specific data and methods
                    formData: {
                        full_name: '',
                        email: '',
                        phone_number: '',
                        password: '',
                        password_confirmation: '',
                        terms: false,
                        account_type: 'customer' // Default to customer
                    },
                    errors: {},
                    loading: false,
                    showPassword: false, // For password visibility toggle
                    submitForm() {
                        this.loading = true;
                        this.errors = {}; // Clear previous errors
                        console.log('Submitting form with data:', this.formData); // Debugging
                        axios.post(this.$el.action, this.formData)
                            .then(response => {
                                this.loading = false;
                                if (response.data.success) {
                                    console.log('Registration successful! Setting showRegister to false and showOTP to true.'); // Debugging
                                    // CORRECTED: Access the parent component's state directly via $dispatch
                                    this.$dispatch('set-show-otp', true); // Dispatch a custom event
                                    this.$dispatch('set-show-register', false); // Dispatch a custom event

                                    // Clear form data
                                    this.formData = {
                                        full_name: '', email: '', phone_number: '',
                                        password: '', password_confirmation: '', terms: false,
                                        account_type: 'customer'
                                    };
                                } else {
                                    console.log('Registration not successful, but no 422 error:', response.data); // Debugging
                                }
                            })
                            .catch(error => {
                                this.loading = false;
                                if (error.response) {
                                    if (error.response.status === 422) {
                                        this.errors = error.response.data.errors;
                                        console.error('Validation errors:', this.errors); // Debugging
                                    } else {
                                        console.error('Server error:', error.response.data); // Debugging
                                    }
                                } else {
                                    console.error('Network or other error:', error); // Debugging
                                }
                            });
                    }
                }"
                @set-show-otp.window="showOTP = $event.detail" {{-- Listen for custom event --}}
                @set-show-register.window="showRegister = $event.detail" {{-- Listen for custom event --}}
            >

                <p class="text-[32px] text-[#212121] font-bold">{{ __('messages.register') }}</p>
                <p class="text-[20px] text-[#767676] mb-4">{{ __('messages.registerMSG') }}</p>

                <div class="entity_type_selector mb-4" x-modelable="userType">
                     <input type="radio" id="customer" name="account_type" value="customer" x-model="formData.account_type"
                           class="hidden peer" checked @change="userType = 'customer'">
                     <label for="customer">{{ __('messages.customer') }}</label>
                     <input type="radio" id="supplier" name="account_type" value="supplier" x-model="formData.account_type"
                           class="hidden peer" @change="userType = 'supplier'">
                     <label for="supplier">{{ __('messages.supplier') }}</label>
                </div>

                @include('partials.supplierReg')

                {{-- customer section --}}
                <div x-show="formData.account_type === 'customer'" class="mb-4">
                    <form method="POST" action="{{ route('register') }}" x-cloak @submit.prevent="submitForm">

                        @csrf
                        <p class="text-[24px] font-bold mb-4 mt-3"> {{ __('messages.account_data') }}</p>

                        <input type="hidden" name="account_type" x-model="formData.account_type">

                        <div class="mb-3">
                            <label for="name" class="block font-bold text-[20px] text-[#212121]">
                                {{ __('messages.full_name') }}
                            </label>
                            <div class="flex items-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                                :class="{ 'border-[#d33]': errors.full_name, 'border-[#767676]': !errors.full_name }">
                                <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                    src="{{ asset('images/interface-user-circle--circle-geometric-human-person-single-user--Streamline-Core.svg') }}"
                                    alt="">
                                <input type="text" name="full_name" x-model="formData.full_name" required
                                    placeholder="{{ __('messages.nameMSG') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <template x-if="errors.full_name">
                                <div class="text-[#d33] mt-1 text-xs" x-text="errors.full_name[0]"></div>
                            </template>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="block font-bold text-[20px] text-[#212121]">
                                {{ __('messages.email') }}
                            </label>
                            <div class="flex items-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                                :class="{ 'border-[#d33]': errors.email, 'border-[#767676]': !errors.email }">
                                <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                    src="{{ asset('images/mail-send-envelope--envelope-email-message-unopened-sealed-close--Streamline-Core.svg') }}"
                                    alt="">
                                <input type="email" name="email" x-model="formData.email" required
                                    placeholder="example@gmail.com"
                                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <template x-if="errors.email">
                                <div class="text-[#d33] mt-1 text-xs" x-text="errors.email[0]"></div>
                            </template>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="block font-bold text-[20px] text-[#212121]">
                                {{ __('messages.phone_number') }}
                            </label>
                            <div class="flex items-center mt-2">
                                <span
                                    class="border-[#767676] border-[1px] rounded-r-[12px] px-3 pt-[16px] pb-[8px] text-[16px] h-[56px] text-[#767676]">
                                    966+
                                </span>
                                <input type="tel" name="phone_number" x-model="formData.phone_number" required maxlength="9"
                                    pattern="[0-9]*" inputmode="numeric" placeholder="{{ __('messages.phoneMSG') }}"
                                    class="ml-[-1px] block w-full px-3 py-2 border-[1px] border-[#767676] rounded-l-[12px] h-[56px] text-right">
                            </div>
                            <template x-if="errors.phone_number">
                                <div class="text-[#d33] mt-1 text-xs" x-text="errors.phone_number[0]"></div>
                            </template>
                        </div>

                        {{-- Password input field --}}
                        <div class="mb-3">
                            <label for="password" class="block font-bold text-[20px] text-[#212121]">
                                {{ __('messages.password') }}
                            </label>
                            <div class="flex items-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                                :class="{ 'border-[#d33]': errors.password, 'border-[#767676]': !errors.password }">
                                <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                    src="{{ asset('images/interface-lock--combination-combo-lock-locked-padlock-secure-security-shield-keyhole--Streamline-Core.svg') }}"
                                    alt="">
                                <input :type="showPassword ? 'text' : 'password'" name="password" required
                                    placeholder="{{ __('messages.passwordMSG') }}" x-model="formData.password"
                                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <button @click.prevent="showPassword = !showPassword"
                                    class="ml-2 px-2 text-sm text-[#212121] flex items-center justify-center">
                                    <span x-show="!showPassword">
                                        <img class="h-6 w-6"
                                            src="{{ asset('images/interface-edit-view-off--disable-eye-eyeball-hide-off-view--Streamline-Core.svg') }}"
                                            alt="Show password">
                                    </span>
                                    <span x-show="showPassword">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                            <path
                                                d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                            <path
                                                d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            {{-- Server-side validation errors for password --}}
                            <template x-if="errors.password">
                                <div class="text-[#d33] mt-1 text-xs" x-text="errors.password[0]"></div>
                            </template>
                            {{-- Client-side password strength messages --}}
                            <ul class="text-[14px] space-y-1 text-[#767676] mt-2" x-show="formData.password.length >= 0">
                                <li x-show="!/[0-9]/.test(formData.password)">
                                    • {{ __('messages.passwordNumber') }}
                                </li>
                                <li x-show="formData.password.length <8">
                                    • {{ __('messages.passwordMin') }}
                                </li>
                                <li x-show="!/[A-Z]/.test(formData.password)">
                                    • {{ __('messages.passwordString') }}
                                </li>
                            </ul>
                        </div>

                        {{-- Confirm Password input field --}}
                        <div class="mb-3">
                            <label for="password_confirmation" class="block font-bold text-[20px] text-[#212121]">
                                {{ __('messages.confirm_password') }}
                            </label>
                            <div class="flex items-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                                :class="{ 'border-[#d33]': errors.password_confirmation || (formData.password_confirmation.length >
                                        0 && formData.password !== formData.password_confirmation), 'border-[#767676]': !
                                        errors.password_confirmation && (formData.password_confirmation.length === 0 ||
                                            formData.password === formData.password_confirmation) }">
                               <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                    src="{{ asset('images/interface-lock--combination-combo-lock-locked-padlock-secure-security-shield-keyhole--Streamline-Core.svg') }}"
                                    alt="">
                                <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" required
                                    placeholder="{{ __('messages.confirm_passwordMSG') }}"
                                    x-model="formData.password_confirmation"
                                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <button @click.prevent="showPassword = !showPassword"
                                    class="ml-2 px-2 text-sm text-[#212121] flex items-center justify-center">
                                    <span x-show="!showPassword">
                                        <img class="h-6 w-6"
                                            src="{{ asset('images/interface-edit-view-off--disable-eye-eyeball-hide-off-view--Streamline-Core.svg') }}"
                                            alt="Show password">
                                    </span>
                                    <span x-show="showPassword">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                            <path
                                                d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                            <path
                                                d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            {{-- Client-side password mismatch message --}}
                            <div class="mt-1 text-xs"
                                :class="formData.password_confirmation.length > 0 && formData.password !== formData
                                    .password_confirmation ? 'text-[#d33]' : 'text-[#767676]'"
                                x-show="formData.password_confirmation.length > 0 && formData.password !== formData.password_confirmation">
                                {{ __('messages.passwordConfirm') }}
                            </div>
                            {{-- Server-side validation error for password_confirmation --}}
                            <template x-if="errors.password_confirmation">
                                <div class="text-[#d33] mt-1 text-xs" x-text="errors.password_confirmation[0]"></div>
                            </template>
                        </div>

                        <div class="mb-3">
                            <input type="checkbox" id="terms" name="terms" x-model="formData.terms" required
                                class="ml-1 h-[15px] w-[15px] text-[#185D31] bg-[#185D31] focus:ring-[#185D31] border-[#185D31] rounded">
                            <label for="terms" class="ml-2 text-[16px] text-[#212121]">
                                {{ __('messages.accept_terms') }}
                                <a href="{{ route('terms') }}" class="text-[#185D31] underline">
                                    {{ __('messages.terms_and_conditions') }}
                                </a>
                                {{ __('messages.and') }}
                                <a href="{{ route('privacy') }}" class="text-[#185D31] underline">
                                    {{ __('messages.privacy_policy') }}
                                </a>
                            </label>
                            <template x-if="errors.terms">
                                <div class="text-[#d33] mt-1 text-xs" x-text="errors.terms[0]"></div>
                            </template>
                        </div>
                        <button type="submit" :disabled="loading"
                            class="w-full bg-[#185D31] text-white p-[12px] h-[48px] rounded-[12px] hover:bg-green-800 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!loading">{{ __('messages.register') }}</span>
                            <span x-show="loading">
                                <div class="animate-spin inline-block w-6 h-6 border-[3px] border-current border-t-transparent text-white rounded-full"
                                    role="status" aria-label="loading">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </span>
                        </button>
                    </form>
                </div>


                <div class="text-center mt-4 mb-4">
                    <p class="text-center mt-4 text-[16px] text-[#212121]">
                        {{ __('messages.already_have_account') }}
                        <a href="#" @click="$dispatch('set-show-login', true); $dispatch('set-show-register', false)"
                            class="text-[#185D31] underline">
                            {{ __('messages.login') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

@include('partials.otp')
