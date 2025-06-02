<div x-data="{ showLogin: false, showRegister: false, userType: 'buyer' }">
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
            class="relative w-[1312px] wrap h-[1024px] bg-cover bg-center flex justify-center items-center p-[96px] no-repeat overflow-visible">
            <button @click="showLogin = false"
                class="absolute top-3 right-3 z-50 bg-white p-1 rounded-full text-gray-500 hover:text-gray-700 focus:outline-none z-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="relative bg-white w-[588px] h-[832px] p-[60px] rounded-[12px] shadow-xl">
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
                            <a href="#" class="ml-2 text-[#212121] text-[16px] hover:underline">
                                {{ __('messages.login_with_google') }}
                            </a>
                        </div>
                        <div class="flex items-center justify-center h-[48px] w-full bg-[#F8F9FA] rounded-[12px]">
                            <img class="ml-3" src="{{ asset('images/Facebook.svg') }}" alt="">
                            <a href="#" class="ml-2 text-[#212121] text-[16px] hover:underline">
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

















    {{-- register section --}}
    <div x-show="showRegister" x-transition
        class="fixed inset-0 z-[50] flex items-center justify-center bg-black bg-opacity-50 "
        style="backdrop-filter: blur(2px);" @click.self="showRegister = false">
        <div style="background-image: url('{{ asset('images/4d2a165c129977b25a433b916ddfa33f089dcf9f.jpg') }}');"
            class="relative w-[1312px] wrap h-[1024px] bg-cover bg-center flex justify-center items-center p-[96px] no-repeat">
            <button @click="showRegister = false"
                class="absolute top-3 right-3 bg-white p-1 rounded-full text-gray-500 hover:text-gray-700 focus:outline-none z-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="relative bg-white w-[588px] h-[832px] p-[60px] rounded-[12px] shadow-xl overflow-y-auto">

                <p class="text-[32px] text-[#212121] font-bold">{{ __('messages.register') }}</p>
                <p class="text-[20px] text-[#767676] mb-4">{{ __('messages.registerMSG') }}</p>

                
                <div class="entity_type_selector mb-4">
                    <input type="radio" id="customer" name="user_type" value="customer" x-model="userType"
                        class="hidden peer" checked>
                    <label for="customer">{{ __('messages.customer') }}</label>
                    <input type="radio" id="supplier" name="user_type" value="supplier" x-model="userType"
                        class="hidden peer">
                    <label for="supplier">{{ __('messages.supplier') }}</label>
                </div>
                


                {{-- supplier section --}}
                    <template x-if="userType === 'supplier'">
                              <form method="POST" action="{{ route('register') }}" x-show="userType === 'supplier'">
                    @csrf
                        <p class="text-[24px] font-bold mb-4 mt-3"> {{ __('messages.account_data') }}
                        </p>
                        <input type="hidden" name="account_type" value="supplier">
                        <div class="mb-3">
                            <label for="full_name" class="block font-bold text-[20px] text-[#212121]">
                                {{ __('messages.full_name') }}
                            </label>
                            <div
                                class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                                <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                    src="{{ asset('images/interface-user-circle--circle-geometric-human-person-single-user--Streamline-Core.svg') }}"
                                    alt="">
                                <input type="text" name="full_name" required
                                    placeholder="{{ __('messages.nameMSG') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            @error('full_name')
                                <div class="error">{{ $message }}</div>
                            @enderror

                        </div>
                        <div class="mb-3">
                            <label for="company_name" class="block font-bold text-[20px] text-[#212121]">
                                {{ __('messages.company_name') }}
                            </label>
                            <div
                                class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                                <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                    src="{{ asset('images/shopping-bag-suitcase-1--product-business-briefcase--Streamline-Core.svg') }}"
                                    alt="">
                                <input type="text" name="company_name" required
                                    placeholder="{{ __('messages.company_nameMSG') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            @error('company_name')
                                <div class="error">{{ $message }}</div>
                            @enderror

                        </div>

                        <div class="mb-3">
                            <label for="email" class="block font-bold text-[20px] text-[#212121]">
                                {{ __('messages.email') }}
                            </label>
                            <div
                                class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                                <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                    src="{{ asset('images/mail-send-envelope--envelope-email-message-unopened-sealed-close--Streamline-Core.svg') }}"
                                    alt="">
                                <input type="email" name="email" required placeholder= "example@gmail.com"
                                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            @error('email')
                                <div class="error">{{ $message }}</div>
                            @enderror

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
                                <input type="tel" name="phone" required
                                    placeholder="{{ __('messages.phoneMSG') }}"
                                    class="ml-[-1px] block w-full px-3 py-2 border-[1px] border-[#767676] rounded-l-[12px] h-[56px] text-right">
                                @error('phone_number')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                   <div class="mb-3" x-data="{ password: '', confirmPassword: '', showPassword: false }">
                            <!-- Password Label -->
                            <label for="password" class="block font-bold text-[20px] text-[#212121]">
                                {{ __('messages.password') }}
                            </label>

                            <!-- Password Input -->
                            <div
                                class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                                <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                    src="{{ asset('images/interface-lock--combination-combo-lock-locked-padlock-secure-security-shield-keyhole--Streamline-Core.svg') }}"
                                    alt="">
                                <input :type="showPassword ? 'text' : 'password'" name="password" required
                                    placeholder="{{ __('messages.passwordMSG') }}" x-model="password"
                                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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

                            <!-- validation messages -->
                            <ul class="text-[14px] space-y-1 text-[#767676] mt-2" x-show="password.length >= 0">
                                <li x-show="!/[0-9]/.test(password)">
                                    • {{ __('messages.passwordNumber') }}
                                </li>
                                <li x-show="password.length <8">
                                    • {{ __('messages.passwordMin') }}
                                </li>
                                <li x-show="!/[A-Z]/.test(password)">
                                    • {{ __('messages.passwordString') }}
                                </li>
                            </ul>

                            @error('password')
                                <div class="text-[#d33] mt-1">{{ $message }}</div>
                            @enderror

                            <!-- Confirm Password Label -->
                            <label for="confirm_password" class="block font-bold text-[20px] mt-2 text-[#212121]">
                                {{ __('messages.confirm_password') }}
                            </label>

                            <!-- Confirm Password Input -->
                            <div
                                class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                                <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                    src="{{ asset('images/interface-lock--combination-combo-lock-locked-padlock-secure-security-shield-keyhole--Streamline-Core.svg') }}"
                                    alt="">
                                <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" required
                                    placeholder="{{ __('messages.confirm_passwordMSG') }}" x-model="confirmPassword"
                                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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

                            <!-- Password match validation -->
                            <div class="mt-2 text-sm text-[#d33]"
                                x-show="password && confirmPassword && password !== confirmPassword">
                                {{ __('messages.passwordConfirm') }}
                            </div>

                        </div>

                        <button type="button" @click="showSupplierForm = true"
                            class="w-full bg-[#185D31] text-white p-[12px] h-[48px] rounded-[12px] hover:bg-green-800">
                            {{ __('messages.complete_data') }}
                        </button>
                        <div x-show="showSupplierForm" class="mt-4">

                        </div>
                </form>
</template>
  












                {{-- customer section --}}
                    <div x-show="userType === 'customer'" class="mb-4">
                        
                <form method="POST" action="{{ route('register') }}" x-show="userType === 'customer'" x-cloak>
                    @csrf
                        <p class="text-[24px] font-bold mb-4 mt-3"> {{ __('messages.account_data') }}</p>

                        <input type="hidden" name="account_type" value="customer">

                        <div class="mb-3">
                            <label for="name" class="block font-bold text-[20px] text-[#212121]">
                                {{ __('messages.full_name') }}
                            </label>
                            <div
                                class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                                <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                    src="{{ asset('images/interface-user-circle--circle-geometric-human-person-single-user--Streamline-Core.svg') }}"
                                    alt="">
                                <input type="text" name="full_name" required
                                    placeholder="{{ __('messages.nameMSG') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="block font-bold text-[20px] text-[#212121]">
                                {{ __('messages.email') }}
                            </label>
                            <div
                                class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                                <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                    src="{{ asset('images/mail-send-envelope--envelope-email-message-unopened-sealed-close--Streamline-Core.svg') }}"
                                    alt="">
                                <input type="email" name="email" required placeholder= "example@gmail.com"
                                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                            </div>
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
                                <input type="tel" name="phone_number" required
                                    placeholder="{{ __('messages.phoneMSG') }}"
                                    class="ml-[-1px] block w-full px-3 py-2 border-[1px] border-[#767676] rounded-l-[12px] h-[56px] text-right">
                            </div>
                        </div>


                        <div class="mb-3" x-data="{ password: '', confirmPassword: '', showPassword: false }">
                            <!-- Password Label -->
                            <label for="password" class="block font-bold text-[20px] text-[#212121]">
                                {{ __('messages.password') }}
                            </label>

                            <!-- Password Input -->
                            <div
                                class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                                <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                    src="{{ asset('images/interface-lock--combination-combo-lock-locked-padlock-secure-security-shield-keyhole--Streamline-Core.svg') }}"
                                    alt="">
                                <input :type="showPassword ? 'text' : 'password'" name="password" required
                                    placeholder="{{ __('messages.passwordMSG') }}" x-model="password"
                                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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

                            <!-- validation messages -->
                            <ul class="text-[14px] space-y-1 text-[#767676] mt-2" x-show="password.length >= 0">
                                <li x-show="!/[0-9]/.test(password)">
                                    • {{ __('messages.passwordNumber') }}
                                </li>
                                <li x-show="password.length <8">
                                    • {{ __('messages.passwordMin') }}
                                </li>
                                <li x-show="!/[A-Z]/.test(password)">
                                    • {{ __('messages.passwordString') }}
                                </li>
                            </ul>

                            @error('password')
                                <div class="text-[#d33] mt-1">{{ $message }}</div>
                            @enderror

                            <!-- Confirm Password Label -->
                            <label for="confirm_password" class="block font-bold mt-2 text-[20px] text-[#212121]">
                                {{ __('messages.confirm_password') }}
                            </label>

                            <!-- Confirm Password Input -->
                            <div
                                class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                                <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                                    src="{{ asset('images/interface-lock--combination-combo-lock-locked-padlock-secure-security-shield-keyhole--Streamline-Core.svg') }}"
                                    alt="">
                                <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" required
                                    placeholder="{{ __('messages.confirm_passwordMSG') }}" x-model="confirmPassword"
                                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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

                            <!-- Password match validation -->
                            <div class="mt-2 text-sm text-[#d33]"
                                x-show="password && confirmPassword && password !== confirmPassword">
                                {{ __('messages.passwordConfirm') }}
                            </div>

                        </div>


                        <div class="mb-3">
                            <input type="checkbox" id="terms" name="terms" required
                                class="ml-1 h-[15px] w-[15px] text-[#185D31] bg-[#185D31] focus:ring-[#185D31] border-[#185D31] rounded">
                            <label for="terms" class="ml-2 text-[16px] text-[#212121]">
                                {{ __('messages.accept_terms') }}
                                <a href="#" @click="" class="text-[#185D31] underline">
                                    {{ __('messages.terms_and_conditions') }}
                                </a>
                                {{ __('messages.and') }}
                                <a href="#" @click="" class="text-[#185D31] underline">
                                    {{ __('messages.privacy_policy') }}
                                </a>
                            </label>

                        </div>
                        <button type="submit"
                            class="w-full bg-[#185D31] text-white p-[12px] h-[48px] rounded-[12px] hover:bg-green-800">
                            {{ __('messages.register') }}
                        </button>
                    </form>
                    </div>





                <div class="text-center mt-4 mb-4">
                    <p class="text-center mt-4 text-[16px] text-[#212121]">
                        {{ __('messages.already_have_account') }}
                        <a href="#" @click="showLogin = true; showRegister = false"
                            class="text-[#185D31] underline">
                            {{ __('messages.login') }}
                        </a>
                    </p>
                </div>


            </div>
        </div>


    </div>






</div>


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
</style>
