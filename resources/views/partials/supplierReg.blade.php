{{-- supplier section --}}
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<div x-show="formData.account_type === 'supplier'">
    <form method="POST" action="{{ route('sendOtp') }}" x-show="userType === 'supplier'"
        enctype="multipart/form-data"
        @submit.prevent="submitForm" {{-- Prevent default form submission and call Alpine's submitForm --}}
    >
        @csrf
        <div x-show="accountData" x-cloak>
            <p class="text-[24px] font-bold mb-4 mt-3"> {{ __('messages.account_data') }}
            </p>

            {{-- Hidden input for account_type; formData.account_type will handle the value --}}
            <input type="hidden" name="account_type" x-model="formData.account_type">

            <div class="mb-3">
                <label for="full_name" class="block font-bold text-[20px] text-[#212121]">
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
                <label for="company_name" class="block font-bold text-[20px] text-[#212121]">
                    {{ __('messages.company_name') }}
                </label>
                <div
                    class="flex items-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                    :class="{'border-[#d33]': errors.company_name, 'border-[#767676]': !errors.company_name }">
                    <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                        src="{{ asset('images/shopping-bag-suitcase-1--product-business-briefcase--Streamline-Core.svg') }}"
                        alt="">
                    <input type="text" name="company_name" x-model="formData.company_name" required
                        placeholder="{{ __('messages.company_nameMSG') }}"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <template x-if="errors.company_name">
                    <div class="text-[#d33] mt-1 text-xs" x-text="errors.company_name[0]"></div>
                </template>
            </div>

            <div class="mb-3">
                <label for="email" class="block font-bold text-[20px] text-[#212121]">
                    {{ __('messages.email') }}
                </label>
                <div
                    class="flex items-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                    :class="{'border-[#d33]': errors.email, 'border-[#767676]': !errors.email }">
                    <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                        src="{{ asset('images/mail-send-envelope--envelope-email-message-unopened-sealed-close--Streamline-Core.svg') }}"
                        alt="">
                    <input type="email" name="email" x-model="formData.email" required
                        placeholder= "example@gmail.com"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <template x-if="errors.email">
                    <div class="text-[#d33] mt-1 text-xs" x-text="errors.email[0]"></div>
                </template>
            </div>

            <div class="mb-3">
                <label for="phone_number" class="block font-bold text-[20px] text-[#212121]">
                    {{ __('messages.phone_number') }}
                </label>
                <div class="flex items-center mt-2">
                    <span
                        class="border-[#767676] border-[1px] rounded-r-[12px] px-3 pt-[16px] pb-[8px] text-[16px] h-[56px] text-[#767676]">
                        966+
                    </span>
                    <input type="tel" id="phone_number" name="phone_number" x-model="formData.phone_number" required
                        placeholder="{{ __('messages.phoneMSG') }}" pattern="[0-9]*" inputmode="numeric" maxlength="9"
                        class="ml-[-1px] block w-full px-3 py-2 border-[1px] rounded-l-[12px] h-[56px] text-right"
                        :class="{'border-[#d33]': errors.phone_number, 'border-[#767676]': !errors.phone_number }">
                </div>
                <template x-if="errors.phone_number">
                    <div class="text-[#d33] mt-1 text-xs" x-text="errors.phone_number[0]"></div>
                </template>
            </div>

            <div class="mb-3" x-data="{ password: formData.password, confirmPassword: formData.password_confirmation, showPassword: false }"
                x-init="$watch('password', value => formData.password = value); $watch('confirmPassword', value => formData.password_confirmation = value);">
                <label for="password" class="block font-bold text-[20px] text-[#212121]">
                    {{ __('messages.password') }}
                </label>

                <div
                    class="flex items-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                    :class="{'border-[#d33]': errors.password, 'border-[#767676]': !errors.password }">
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                height="16" fill="currentColor" class="bi bi-eye"
                                viewBox="0 0 16 16">
                                <path
                                    d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                <path
                                    d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                            </svg>
                        </span>
                    </button>
                </div>
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

                <template x-if="errors.password">
                    <div class="text-[#d33] mt-1 text-xs" x-text="errors.password[0]"></div>
                </template>

                <label for="confirm_password"
                    class="block font-bold text-[20px] mt-2 text-[#212121]">
                    {{ __('messages.confirm_password') }}
                </label>

                <div
                    class="flex items-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                    :class="{'border-[#d33]': errors.password_confirmation, 'border-[#767676]': !errors.password_confirmation }">
                    <img class="h-[24px] w-[24px] object-cover text-[#767676] mr-4"
                        src="{{ asset('images/interface-lock--combination-combo-lock-locked-padlock-secure-security-shield-keyhole--Streamline-Core.svg') }}"
                        alt="">
                    <input :type="showPassword ? 'text' : 'password'"
                        name="password_confirmation" required
                        placeholder="{{ __('messages.confirm_passwordMSG') }}"
                        x-model="confirmPassword"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <button @click.prevent="showPassword = !showPassword"
                        class="ml-2 px-2 text-sm text-[#212121]">
                        <span x-show="!showPassword"><img
                                src="{{ asset('images/interface-edit-view-off--disable-eye-eyeball-hide-off-view--Streamline-Core.svg') }}"
                                alt=""></span>
                        <span x-show="showPassword">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                height="16" fill="currentColor" class="bi bi-eye"
                                viewBox="0 0 16 16">
                                <path
                                    d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                <path
                                    d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                            </svg>
                        </span>
                    </button>
                </div>

                <div class="mt-2 text-sm text-[#d33]"
                    x-show="password && confirmPassword && password !== confirmPassword">
                    {{ __('messages.passwordConfirm') }}
                </div>
                <template x-if="errors.password_confirmation">
                    <div class="text-[#d33] mt-1 text-xs" x-text="errors.password_confirmation[0]"></div>
                </template>
            </div>

            <button type="button" @click="bussnissdata = true; accountData = false; formData.account_type = 'supplier';"
                class="w-full bg-[#185D31] text-white p-[12px] h-[48px] rounded-[12px] hover:bg-green-800">
                {{ __('messages.complete_data') }}
            </button>
        </div>


        {{-- business data --}}
        <div x-show="bussnissdata" class="mt-4">
            <p class="text-[24px] font-bold text-[#212121] mb-4">
                {{ __('messages.bussniss_data') }}
            </p>

            <div class="mb-4">
                <label for="national_id"
                    class="block font-bold text-[20px] text-[#212121] rounded-[12px] overflow-hidden">
                    {{ __('messages.national_id') }}
                </label>
                <div
                    class="flex item-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                    :class="{'border-[#d33]': errors.national_id, 'border-[#767676]': !errors.national_id }">
                    <input type="text" name="national_id" x-model="formData.national_id" required
                        placeholder="{{ __('messages.national_idMSG') }}"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">
                </div>
                <template x-if="errors.national_id">
                    <div class="text-[#d33] mt-1 text-xs" x-text="errors.national_id[0]"></div>
                </template>

                <div class=" mt-2">
                    <div
                        class="flex item-center mt-2 h-[56px] border-[1px] rounded-[12px] overflow-hidden"
                        :class="{'border-[#d33]': errors.national_id_attach, 'border-[#767676]': !errors.national_id_attach }">
                        <input type="text" id="nationalIdAttachDisplay" readonly
                            :value="formData.national_id_attach ? formData.national_id_attach.name : ''"
                            placeholder="{{ __('messages.national_id_attach') }}"
                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">

                        <div
                            class="file-input-container item-center justify-center w-[90px] h-[32px] mt-[11px] ml-2 bg-[#185D31] rounded-[12px] px-[12px] py-[8px]">
                            <label for="national_id_attach"
                                class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                    alt="" class="h-[16px] w-[16px] ml-2">
                                {{ __('messages.attach') }}
                            </label>
                            <input type="file" id="national_id_attach" class="hidden"
                                name="national_id_attach" accept="image/*,application/pdf"
                                @change="formData.national_id_attach = $event.target.files[0]">
                        </div>
                    </div>
                    <span class="text-[14px] space-y-1 text-[#767676] mt-3"
                        x-show="!formData.national_id_attach || !formData.national_id_attach.name.match(/\.(jpg|jpeg|png|pdf)$/i)">
                        {{ __('messages.file_format') }}
                    </span>
                    <template x-if="errors.national_id_attach">
                        <div class="text-[#d33] mt-1 text-xs" x-text="errors.national_id_attach[0]"></div>
                    </template>
                </div>
            </div>

            <div class="mb-4">
                <label for="commercial_registration"
                    class="block font-bold text-[20px] text-[#212121] rounded-[12px] overflow-hidden">
                    {{ __('messages.commercial_registration') }}
                </label>
                <div
                    class="flex item-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                    :class="{'border-[#d33]': errors.commercial_registration, 'border-[#767676]': !errors.commercial_registration }">
                    <input type="text" name="commercial_registration" x-model="formData.commercial_registration" required
                        placeholder="{{ __('messages.commercial_registration') }}"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">
                </div>
                <template x-if="errors.commercial_registration">
                    <div class="text-[#d33] mt-1 text-xs" x-text="errors.commercial_registration[0]"></div>
                </template>

                <div class=" mt-2">
                    <div
                        class="flex item-center mt-2 h-[56px] border-[1px] rounded-[12px] overflow-hidden"
                        :class="{'border-[#d33]': errors.commercial_registration_attach, 'border-[#767676]': !errors.commercial_registration_attach }">
                        <input type="text" id="commercialRegistrationAttachDisplay" readonly
                            :value="formData.commercial_registration_attach ? formData.commercial_registration_attach.name : ''"
                            placeholder="{{ __('messages.commercial_reg_or_certificate') }}"
                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">

                        <div
                            class="file-input-container item-center justify-center w-[90px] h-[32px] mt-[11px] ml-2 bg-[#185D31] rounded-[12px] px-[12px] py-[8px]">
                            <label for="commercial_registration_attach"
                                class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                    alt="" class="h-[16px] w-[16px] ml-2">
                                {{ __('messages.attach') }}
                            </label>
                            <input type="file" id="commercial_registration_attach" class="hidden"
                                name="commercial_registration_attach"
                                accept="image/*,application/pdf"
                                @change="formData.commercial_registration_attach = $event.target.files[0]">
                        </div>
                    </div>
                    <span class="text-[14px] space-y-1 text-[#767676] mt-3"
                        x-show="!formData.commercial_registration_attach || !formData.commercial_registration_attach.name.match(/\.(jpg|jpeg|png|pdf)$/i)">
                        {{ __('messages.file_format') }}
                    </span>
                    <template x-if="errors.commercial_registration_attach">
                        <div class="text-[#d33] mt-1 text-xs" x-text="errors.commercial_registration_attach[0]"></div>
                    </template>
                </div>
            </div>

            <div class="mb-4">
                <label for="national_address"
                    class="block font-bold text-[20px] text-[#212121] rounded-[12px] overflow-hidden">
                    {{ __('messages.national_address') }}
                </label>
                <div
                    class="flex item-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                    :class="{'border-[#d33]': errors.national_address, 'border-[#767676]': !errors.national_address }">
                    <input type="text" name="national_address" x-model="formData.national_address" required
                        placeholder="{{ __('messages.national_addressMSG') }}"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">
                </div>
                <template x-if="errors.national_address">
                    <div class="text-[#d33] mt-1 text-xs" x-text="errors.national_address[0]"></div>
                </template>

                <div class=" mt-2">
                    <div
                        class="flex item-center mt-2 h-[56px] border-[1px] rounded-[12px] overflow-hidden"
                        :class="{'border-[#d33]': errors.national_address_attach, 'border-[#767676]': !errors.national_address_attach }">
                        <input type="text" id="nationalAddressAttachDisplay" readonly
                            :value="formData.national_address_attach ? formData.national_address_attach.name : ''"
                            placeholder="{{ __('messages.national_address_attach') }}"
                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">

                        <div
                            class="file-input-container item-center justify-center w-[90px] h-[32px] mt-[11px] ml-2 bg-[#185D31] rounded-[12px] px-[12px] py-[8px]">
                            <label for="national_address_attach"
                                class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                    alt="" class="h-[16px] w-[16px] ml-2">
                                {{ __('messages.attach') }}
                            </label>
                            <input type="file" id="national_address_attach" class="hidden"
                                name="national_address_attach"
                                accept="image/*,application/pdf"
                                @change="formData.national_address_attach = $event.target.files[0]">
                        </div>
                    </div>
                    <span class="text-[14px] space-y-1 text-[#767676] mt-3"
                        x-show="!formData.national_address_attach || !formData.national_address_attach.name.match(/\.(jpg|jpeg|png|pdf)$/i)">
                        {{ __('messages.file_format') }}
                    </span>
                    <template x-if="errors.national_address_attach">
                        <div class="text-[#d33] mt-1 text-xs" x-text="errors.national_address_attach[0]"></div>
                    </template>
                </div>
            </div>

            <div class="mb-4">
                <label for="iban"
                    class="block font-bold text-[20px] text-[#212121] rounded-[12px] overflow-hidden">
                    {{ __('messages.iban') }}
                </label>
                <div
                    class="flex item-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                    :class="{'border-[#d33]': errors.iban, 'border-[#767676]': !errors.iban }">
                    <input type="text" name="iban" x-model="formData.iban" required
                        placeholder="{{ __('messages.ibanMSG') }}"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">
                </div>
                <template x-if="errors.iban">
                    <div class="text-[#d33] mt-1 text-xs" x-text="errors.iban[0]"></div>
                </template>

                <div class=" mt-2">
                    <div
                        class="flex item-center mt-2 h-[56px] border-[1px] rounded-[12px] overflow-hidden"
                        :class="{'border-[#d33]': errors.iban_attach, 'border-[#767676]': !errors.iban_attach }">
                        <input type="text" id="ibanAttachDisplay" readonly
                            :value="formData.iban_attach ? formData.iban_attach.name : ''"
                            placeholder="{{ __('messages.iban_attach') }}"
                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">

                        <div
                            class="file-input-container item-center justify-center w-[90px] h-[32px] mt-[11px] ml-2 bg-[#185D31] rounded-[12px] px-[12px] py-[8px]">
                            <label for="iban_attach"
                                class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                    alt="" class="h-[16px] w-[16px] ml-2">
                                {{ __('messages.attach') }}
                            </label>
                            <input type="file" id="iban_attach" class="hidden"
                                name="iban_attach" accept="image/*,application/pdf"
                                @change="formData.iban_attach = $event.target.files[0]">
                        </div>
                    </div>
                    <span class="text-[14px] space-y-1 text-[#767676] mt-3"
                        x-show="!formData.iban_attach || !formData.iban_attach.name.match(/\.(jpg|jpeg|png|pdf)$/i)">
                        {{ __('messages.file_format') }}
                    </span>
                    <template x-if="errors.iban_attach">
                        <div class="text-[#d33] mt-1 text-xs" x-text="errors.iban_attach[0]"></div>
                    </template>
                </div>
            </div>


            <div class="mb-4">
                <label for="tax_certificate"
                    class="block font-bold text-[20px] text-[#212121] rounded-[12px] overflow-hidden">
                    {{ __('messages.tax_certificate') }}
                </label>
                <div
                    class="flex item-center mt-2 border-[1px] rounded-[12px] overflow-hidden"
                    :class="{'border-[#d33]': errors.tax_certificate, 'border-[#767676]': !errors.tax_certificate }">
                    <input type="text" name="tax_certificate" x-model="formData.tax_certificate" required
                        placeholder="{{ __('messages.tax_certificateMSG') }}"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">
                </div>
                <template x-if="errors.tax_certificate">
                    <div class="text-[#d33] mt-1 text-xs" x-text="errors.tax_certificate[0]"></div>
                </template>

                <div class=" mt-2">
                    <div
                        class="flex item-center mt-2 h-[56px] border-[1px] rounded-[12px] overflow-hidden"
                        :class="{'border-[#d33]': errors.tax_certificate_attach, 'border-[#767676]': !errors.tax_certificate_attach }">
                        <input type="text" id="taxCertificateAttachDisplay" readonly
                            :value="formData.tax_certificate_attach ? formData.tax_certificate_attach.name : ''"
                            placeholder="{{ __('messages.tax_certificate_attach') }}"
                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">

                        <div
                            class="file-input-container item-center justify-center w-[90px] h-[32px] mt-[11px] ml-2 bg-[#185D31] rounded-[12px] px-[12px] py-[8px]">
                            <label for="tax_certificate_attach"
                                class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                    alt="" class="h-[16px] w-[16px] ml-2">
                                {{ __('messages.attach') }}
                            </label>
                            <input type="file" id="tax_certificate_attach" class="hidden"
                                name="tax_certificate_attach" accept="image/*,application/pdf"
                                @change="formData.tax_certificate_attach = $event.target.files[0]">
                        </div>
                    </div>
                    <span class="text-[14px] space-y-1 text-[#767676] mt-3"
                        x-show="!formData.tax_certificate_attach || !formData.tax_certificate_attach.name.match(/\.(jpg|jpeg|png|pdf)$/i)">
                        {{ __('messages.file_format') }}
                    </span>
                    <template x-if="errors.tax_certificate_attach">
                        <div class="text-[#d33] mt-1 text-xs" x-text="errors.tax_certificate_attach[0]"></div>
                    </template>
                </div>
            </div>

            <div class="mb-3">
                <input type="checkbox" id="terms" name="terms" x-model="formData.terms" required
                    class="ml-1 h-[15px] w-[15px] text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded"
                    :class="{'border-[#d33]': errors.terms, 'border-[#185D31]': !errors.terms }">
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
                    <!-- ✅ reCAPTCHA -->
<div class="mb-3" id="recaptcha_supplier"></div>

            <button type="submit"
                class="w-full bg-[#185D31] text-white p-[12px] h-[48px] rounded-[12px] hover:bg-green-800"
                :disabled="loading">
                <span x-show="!loading">{{ __('messages.Register') }}</span>
                <span x-show="loading">
                    <svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
            <button type="button" @click="bussnissdata = false; accountData = true"
                class="w-full bg-[#185D31] text-white p-[12px] h-[48px] rounded-[12px] hover:bg-green-800 mt-2">
                {{ __('messages.previous') }}
            </button>
        </div>
    </form>
</div>
{{-- <script>
    // Make it global
    let captchaWidgetId;

    document.addEventListener('DOMContentLoaded', function() {
        function renderCaptcha() {
            if (window.grecaptcha && document.getElementById('recaptcha_register')) {
                captchaWidgetId = grecaptcha.render('recaptcha_register', {
                    'sitekey': '6LcTZuErAAAAAI-idNNNcQzsYW0Ca-t782dVsvWJ',
                    'theme': 'light'
                });
            } else {
                setTimeout(renderCaptcha, 1000);
            }
        }

        renderCaptcha();
    });
</script> --}}
