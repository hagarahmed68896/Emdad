<div x-show="showRegister" x-transition x-cloak
    class="fixed inset-0 z-[50] flex items-center justify-center bg-black bg-opacity-50 p-4 sm:p-6 lg:p-8"
    style="backdrop-filter: blur(2px);" @click.self="showRegister = false">

    {{-- This div acts as the overall modal container, allowing its content (the white form) to dictate height --}}
    <div
           class="relative bg-cover bg-center flex flex-col justify-center items-center p-[24px] no-repeat overflow-visible rounded-lg shadow-lg
               w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl 
               h-auto sm:h-auto md:h-auto mx-auto my-auto"
        style="background-image: url('{{ asset('images/4d2a165c129977b25a433b916ddfa33f089dcf9f.jpg') }}'); background-size: cover; background-position: center;">

        <button @click="showRegister = false"
            class="absolute top-3 right-3 z-50 bg-white p-1 rounded-full text-gray-500 hover:text-gray-700 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

        {{-- Main Content Area - this is where the height responsiveness is crucial --}}
        <div class="relative bg-white w-full p-8 sm:p-10 md:p-12 rounded-lg shadow-xl
                    flex-grow overflow-y-auto max-h-[90vh] my-4 sm:my-8"
          <div x-data="{
    formData: {
        full_name: '',
        email: '',
        phone_number: '',
        password: '',
        password_confirmation: '',
        account_type: 'customer', // Default to customer
        company_name: '',
        national_id: '',
        national_id_attach: null,
        commercial_registration: '',
        commercial_registration_attach: null,
        national_address: '',
        national_address_attach: null,
        iban: '',
        iban_attach: null,
        tax_certificate: '',
        tax_certificate_attach: null,
        terms: false,
        showLogin: false,
        showRegister: true,
        showOTP: false,
    },
    errors: {},
    loading: false,
    showPassword: false, // For password toggle
    submitForm() {
        this.loading = true;
        this.errors = {}; // Clear previous errors

        const submitData = new FormData();

        // ------------------ 1️⃣ تحديد auth_method ------------------
        submitData.append('auth_method', this.formData.account_type === 'supplier' ? 'register_supplier' : 'register');

        // ------------------ 2️⃣ الحقول النصية ------------------
        const fields = [
            'full_name', 'email', 'phone_number', 'password', 'password_confirmation', 'account_type',
            'company_name', 'national_id', 'commercial_registration', 'national_address', 'iban', 'tax_certificate'
        ];
        fields.forEach(field => {
            submitData.append(field, this.formData[field]);
        });

        submitData.append('terms', this.formData.terms ? '1' : '0');

        // ------------------ 3️⃣ الملفات ------------------
        const fileFields = [
            'national_id_attach', 'commercial_registration_attach', 
            'national_address_attach', 'iban_attach', 'tax_certificate_attach'
        ];
        fileFields.forEach(field => {
            if (this.formData[field] instanceof File) {
                submitData.append(field, this.formData[field]);
            }
        });

        // ------------------ 4️⃣ الكابتشا ------------------
        let captchaResponse = this.formData.account_type === 'customer' 
            ? grecaptcha.getResponse(captchaCustomerId)
            : grecaptcha.getResponse(captchaSupplierId);

        if (!captchaResponse) {
            this.loading = false;
            this.errors['g-recaptcha-response'] = ['يرجى تأكيد أنك لست روبوت!'];
            return;
        }
        submitData.append('g-recaptcha-response', captchaResponse);

        // ------------------ 5️⃣ إرسال البيانات ------------------
        axios.post(this.$el.action, submitData, { headers: { 'Content-Type': 'multipart/form-data' } })
            .then(response => {
                this.loading = false;

                if (response.data.success) {
                    // فتح نافذة OTP
                    window.dispatchEvent(new CustomEvent('open-otp'));

                    // إعادة تعيين الكابتشا
                    if (this.formData.account_type === 'customer') grecaptcha.reset(captchaCustomerId);
                    else grecaptcha.reset(captchaSupplierId);

                    // إعادة تعيين الفورم
                    this.formData = {
                        full_name: '', email: '', phone_number: '',
                        password: '', password_confirmation: '',
                        company_name: '', national_id: '', national_id_attach: null,
                        commercial_registration: '', commercial_registration_attach: null,
                        national_address: '', national_address_attach: null,
                        iban: '', iban_attach: null,
                        tax_certificate: '', tax_certificate_attach: null,
                        terms: false, account_type: 'customer',
                        showLogin: false, showRegister: true, showOTP: false
                    };
                } else {
                    console.log('Registration not successful:', response.data);
                }
            })
            .catch(error => {
                this.loading = false;
                if (error.response) {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                        console.error('Validation errors:', this.errors);
                    } else {
                        console.error('Server error:', error.response.data);
                    }
                } else {
                    console.error('Network or other error:', error);
                }

                // إعادة تعيين الكابتشا بعد الفشل
                if (this.formData.account_type === 'customer') grecaptcha.reset(captchaCustomerId);
                else grecaptcha.reset(captchaSupplierId);
            });
    }
}"
 @set-show-otp.window="showOTP = $event.detail"
             @set-show-register.window="showRegister = $event.detail"
@open-otp.window="showOTP = true; showLogin = false; showRegister = false"             
        >

            <p class="text-2xl sm:text-3xl text-[#212121] font-bold">{{ __('messages.register') }}</p>
            <p class="text-base sm:text-lg text-[#767676] mb-4">{{ __('messages.registerMSG') }}</p>

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
                <form method="POST" action="{{ route('sendOtp') }}" x-cloak @submit.prevent="submitForm">

                    @csrf
                    <!-- 🧠 Honeypot (Bot Trap) -->
                    <input type="text" name="nickname" style="display:none" autocomplete="off">

                    <p class="text-xl sm:text-2xl font-bold mb-4 mt-3"> {{ __('messages.account_data') }}</p>

                    <input type="hidden" name="account_type" x-model="formData.account_type">

                    <div class="mb-3">
                        <label for="name" class="block font-bold text-lg sm:text-xl text-[#212121]">
                            {{ __('messages.full_name') }}
                        </label>
                        <div class="flex items-center mt-2 border-[1px] rounded-lg overflow-hidden h-14"
                            :class="{ 'border-[#d33]': errors.full_name, 'border-[#767676]': !errors.full_name }">
                            <img class="h-6 w-6 object-cover text-[#767676] mx-3"
                                src="{{ asset('images/interface-user-circle--circle-geometric-human-person-single-user--Streamline-Core.svg') }}"
                                alt="">
                            <input type="text" name="full_name" x-model="formData.full_name" required
                                placeholder="{{ __('messages.nameMSG') }}"
                                class="block w-full px-3 py-2 border-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <template x-if="errors.full_name">
                            <div class="text-[#d33] mt-1 text-xs" x-text="errors.full_name[0]"></div>
                        </template>
                    </div>

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

                    <div class="mb-3">
                        <label for="phone" class="block font-bold text-lg sm:text-xl text-[#212121]">
                            {{ __('messages.phone_number') }}
                        </label>
                        <div class="flex items-center mt-2">
                            <span
                                class="border-[#767676] border-[1px] rounded-r-lg px-3 py-2 text-base h-14 flex items-center text-[#767676]">
                                966+
                            </span>
                            <input type="tel" name="phone_number" x-model="formData.phone_number" required
                                maxlength="9" pattern="[0-9]*" inputmode="numeric"
                                placeholder="{{ __('messages.phoneMSG') }}"
                                class="ml-[-1px] block w-full px-3 py-2 border-[1px] border-[#767676] rounded-l-lg h-14 text-right">
                        </div>
                        <template x-if="errors.phone_number">
                            <div class="text-[#d33] mt-1 text-xs" x-text="errors.phone_number[0]"></div>
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
                        {{-- Server-side validation errors for password --}}
                        <template x-if="errors.password">
                            <div class="text-[#d33] mt-1 text-xs" x-text="errors.password[0]"></div>
                        </template>
                        {{-- Client-side password strength messages --}}
                        <ul class="text-sm space-y-1 text-[#767676] mt-2" x-show="formData.password.length >= 0">
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
                        <label for="password_confirmation" class="block font-bold text-lg sm:text-xl text-[#212121]">
                            {{ __('messages.confirm_password') }}
                        </label>
                        <div class="flex items-center mt-2 border-[1px] rounded-lg overflow-hidden h-14"
                            :class="{ 'border-[#d33]': errors.password_confirmation || (formData.password_confirmation.length >
                                    0 && formData.password !== formData.password_confirmation), 'border-[#767676]': !
                                    errors.password_confirmation && (formData.password_confirmation.length === 0 ||
                                        formData.password === formData.password_confirmation) }">
                            <img class="h-6 w-6 object-cover text-[#767676] mx-3"
                                src="{{ asset('images/interface-lock--combination-combo-lock-locked-padlock-secure-security-shield-keyhole--Streamline-Core.svg') }}"
                                alt="">
                            <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" required
                                placeholder="{{ __('messages.confirm_passwordMSG') }}"
                                x-model="formData.password_confirmation"
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
                            class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">
                        <label for="terms" class="ml-2 text-sm sm:text-base text-[#212121]">
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
                        <!-- ✅ reCAPTCHA -->
<div class="mb-3" id="recaptcha_customer"></div>

                    <button type="submit" :disabled="loading"
                        class="w-full bg-[#185D31] text-white py-3 px-4 rounded-lg hover:bg-green-800 disabled:opacity-50 disabled:cursor-not-allowed text-base">
                        <span x-show="!loading">{{ __('messages.Register') }}</span>
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
                <p class="text-center mt-4 text-sm sm:text-base text-[#212121]">
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
<script>
   let captchaCustomerId, captchaSupplierId;

function renderCaptcha() {
    if (window.grecaptcha) {
        const customerDiv = document.getElementById('recaptcha_customer');
        const supplierDiv = document.getElementById('recaptcha_supplier');

        if (customerDiv && !captchaCustomerId) {
            captchaCustomerId = grecaptcha.render('recaptcha_customer', {
                'sitekey': '6LcTZuErAAAAAI-idNNNcQzsYW0Ca-t782dVsvWJ',
                'theme': 'light'
            });
        }

        if (supplierDiv && !captchaSupplierId) {
            captchaSupplierId = grecaptcha.render('recaptcha_supplier', {
                'sitekey': '6LcTZuErAAAAAI-idNNNcQzsYW0Ca-t782dVsvWJ',
                'theme': 'light'
            });
        }
    } else {
        setTimeout(renderCaptcha, 1000);
    }
}

document.addEventListener('DOMContentLoaded', renderCaptcha);

</script>


@include('partials.otp')