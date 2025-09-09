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
    @open-otp.window="showOTP = true; showLogin = false; showRegister = false"
    @click.self="!hasLoginErrors && (showLogin = false)"
    @set-show-login.window="showLogin = $event.detail"
    @set-show-register.window="showRegister = $event.detail"
>
    <div class="flex items-center space-x-4">
        <button @click="showLogin = true"
            class="w-[120px] h-[40px] bg-[#185D31] px-4 py-2 rounded-[12px] text-white flex items-center justify-center font-semibold text-sm">
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
        formData: { phone_number: '', remember: false },
        errors: {},
        loading: false,
        submitForm() {
            this.loading = true;
            this.errors = {};

            axios.post(this.$el.action, {
                phone_number: this.formData.phone_number,   // ✅ backend expects 'phone'
                auth_method: 'login'
            })
            .then(response => {
                this.loading = false;
                if (response.data.status) {
                       this.$dispatch('open-otp');  // 🔔 tell root to switch modals

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

    <div class="mb-4">
        <label for="phone" class="block font-bold text-lg text-[#212121]">
            {{ __('messages.phone_number') }}
        </label>
        <div class="flex items-center mt-2 border rounded-[12px] overflow-hidden"
            :class="{'border-red-500': errors.phone, 'border-gray-400': !errors.phone}">
            <span class="border-r px-3 py-2 text-[#767676]">966+</span>
            <input type="text" name="phone_number" id="phone"
                maxlength="9" pattern="[0-9]*" inputmode="numeric"
                placeholder="{{__('messages.phoneMSG')}}"
                x-model="formData.phone_number"
                class="block w-full px-2 py-2 h-[48px] border-none focus:outline-none text-base">
        </div>
        <template x-if="errors.phone_number">
            <p class="text-red-500 text-xs mt-1" x-text="errors.phone_number[0]"></p>
        </template>
    </div>

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