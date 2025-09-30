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
        padding: 12px 20px;
        font-size: 16px;
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

    [x-cloak] { display: none !important; }
</style>

<div 
    x-data="{
        showLogin: {{ session('showLoginModal', false) ? 'true' : 'false' }},
        showRegister: {{ session('showRegisterModal', false) ? 'true' : 'false' }},
        showOTP: false,
        userType: 'customer',
accountData: true,
bussnissdata: false,
        hasLoginErrors: {{ json_encode($errors->has('phone_number')) }}
    }"
    @show-login-modal.window="showLogin = true"
    @open-otp.window="showOTP = true; showLogin = false; showRegister = false"
    @click.self="!hasLoginErrors && (showLogin = false)"
    @set-show-login.window="showLogin = $event.detail"
    @set-show-register.window="showRegister = $event.detail"
>
    <div class="flex items-center space-x-4">
        <button @click="showLogin = true"
            class="w-[130px] h-[40px] bg-[#185D31] px-4 py-2 rounded-[12px] text-white flex items-center justify-center font-semibold text-sm">
            {{ __('messages.register') }}
        </button>
    </div>

    <div x-show="showLogin" x-transition x-cloak
        class="fixed inset-0 z-[50] flex items-center justify-center bg-black bg-opacity-50 p-6">
        <div    class="relative bg-cover bg-center flex flex-col justify-center items-center p-[24px] no-repeat overflow-visible rounded-lg shadow-lg
               w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl 
               h-auto sm:h-auto md:h-auto mx-auto my-auto"
            style="background-image: url('{{ asset('images/4d2a165c129977b25a433b916ddfa33f089dcf9f.jpg') }}');">

            <button @click="showLogin = false"
                class="absolute top-3 right-3 bg-white p-1 rounded-full text-gray-500 hover:text-gray-700">
                ✕
            </button>

<form method="POST" action="{{ route('sendOtp') }}"
      x-data="{
          formData: { email: '', password:'', remember: false },
          errors: {},
          loading: false,
          submitForm() {
              this.loading = true;
              this.errors = {};

              axios.post(this.$el.action, {
                  email: this.formData.email,  
                  password: this.formData.password,   
                  auth_method: 'login'
              })
              .then(response => {
                  this.loading = false;

                  if (response.data.status) {
                      // ✅ Store data for OTP resend
                      localStorage.setItem('otp_auth_method', 'login'); // login flow
                      localStorage.setItem('otp_email', this.formData.email || ''); // user email
localStorage.setItem('otp_phone', response.data.phone_number || '');

                      this.$dispatch('open-otp');  // 🔔 show OTP modal
                  }
              })
              .catch(error => {
                  this.loading = false;
                  if (error.response && error.response.status === 422) {
                      this.errors = error.response.data.errors;
                  }
              });
          }
      }"
      @submit.prevent="submitForm"
      class="relative bg-white w-full max-w-[588px] mx-auto p-6 rounded-[12px] shadow-xl max-h-[90vh] overflow-y-auto"
>

    @csrf

    <p class="text-2xl font-bold text-[#212121]">{{ __('messages.login') }}</p>
    <p class="text-base text-[#767676] mb-4">{{ __('messages.loginMSG') }}</p>
         <div class="mb-3">
                        <label for="email" class="block font-bold text-lg sm:text-xl text-[#212121]">
                            {{ __('messages.email') }}
                        </label>
                        <div class="flex items-center mt-2 border-[1px] rounded-lg overflow-hidden h-14"
                            :class="{ 'border-[#d33]': errors.email, 'border-[#767676]': !errors.email }">
                            <img class="h-6 w-6 object-cover text-[#767676] mx-3"
                                src="{{ asset('images/mail-send-envelope--envelope-email-message-unopened-sealed-close--Streamline-Core.svg') }}"
                                alt="">
                            <input type="email" name="email" x-model="formData.email" required
                                placeholder="example@gmail.com"
                                class="block w-full px-3 py-2 border-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <template x-if="errors.email">
                            <div class="text-[#d33] mt-1 text-xs" x-text="errors.email[0]"></div>
                        </template>
                    </div>
                                        {{-- Password input field --}}
                    <div class="mb-3">
                        <label for="password" class="block font-bold text-lg sm:text-xl text-[#212121]">
                            {{ __('messages.password') }}
                        </label>
                        <div class="flex items-center mt-2 border-[1px] rounded-lg overflow-hidden h-14"
                            :class="{ 'border-[#d33]': errors.password, 'border-[#767676]': !errors.password }">
                            <img class="h-6 w-6 object-cover text-[#767676] mx-3"
                                src="{{ asset('images/interface-lock--combination-combo-lock-locked-padlock-secure-security-shield-keyhole--Streamline-Core.svg') }}"
                                alt="">
                            <input :type="showPassword ? 'text' : 'password'" name="password" required
                                placeholder="{{ __('messages.passwordMSG') }}" x-model="formData.password"
                                class="block w-full px-3 py-2 border-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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
                    </div>
{{-- <div class="mb-4">
    <label for="phone" class="block font-bold text-lg text-[#212121]">
        {{ __('messages.phone_number') }}
    </label>
    <div class="relative mt-2">
        <span class="absolute left-0 top-0 h-full flex items-center px-3 text-[#767676] border-r border-gray-300 rounded-l-[12px]">
            +966
        </span>
        <input type="text" name="phone_number" id="phone"
            maxlength="9" pattern="[0-9]*" inputmode="numeric"
            placeholder="{{__('messages.phoneMSG')}}"
            x-model="formData.phone_number"
            :class="{'border-red-500': errors.phone_number, 'border-gray-400': !errors.phone_number}"
            class="block w-full pl-20 pr-2 py-2 h-[48px] border rounded-[12px] focus:outline-none text-base text-left">
    </div>
    <template x-if="errors.phone_number">
        <p class="text-red-500 text-xs mt-1" x-text="errors.phone_number[0]"></p>
    </template>
</div> --}}


    <button type="submit" :disabled="loading"
     {{-- @click="$dispatch('open-otp')" --}}
        class="w-full bg-[#185D31] text-white py-3 h-[48px] rounded-md hover:bg-green-800 disabled:opacity-50">
        <span x-show="!loading">{{ __('messages.send_confirmation_code') }}</span>
        <span x-show="loading" class="flex justify-center">
            <div class="animate-spin w-6 h-6 border-2 border-white border-t-transparent rounded-full"></div>
        </span>
    </button>

    {{-- <div class="flex items-center justify-center my-4">
        <hr class="flex-grow border-gray-400">
        <span class="text-sm text-gray-500 font-medium mx-4">{{ __('messages.or') }}</span>
        <hr class="flex-grow border-gray-400">
    </div>

    <div class="space-y-3">
        <a href="{{ route('login.google') }}"
            class="flex items-center justify-center h-[48px] bg-[#F8F9FA] rounded-[12px]">
            <img src="{{ asset('images/Google.svg') }}" class="mr-2" alt="">
            <span class="text-[#212121] text-base">{{ __('messages.login_with_google') }}</span>
        </a>
        <a href="{{ route('login.facebook') }}"
            class="flex items-center justify-center h-[48px] bg-[#F8F9FA] rounded-[12px]">
            <img src="{{ asset('images/Facebook.svg') }}" class="mr-2" alt="">
            <span class="text-[#212121] text-base">{{ __('messages.login_with_facebook') }}</span>
        </a>
    </div> --}}

    <div class="text-center mt-4">
        <p class="text-sm text-[#212121]">
            {{ __('messages.dont_have_account') }}
     <a href="#" @click.prevent="$dispatch('set-show-register', true); $dispatch('set-show-login', false)"
    class="text-[#185D31] underline">
    {{ __('messages.create__new_account') }}
</a>
        </p>
    </div>
</form>

        </div>
    </div>
    @include('partials.customerReg')

    @include('partials.otp')

</div>