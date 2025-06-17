{{-- supplier section --}}
<template x-if="userType === 'supplier'">
    <form method="POST" action="{{ route('register.supplier') }}" x-show="userType === 'supplier'"
        enctype="multipart/form-data">
        @csrf
        <div x-show="accountData" x-cloak>
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
                    <input type="email" name="email" required
                        placeholder= "example@gmail.com"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror

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
                    <input type="tel" id="phone_number" name="phone_number" required
                        placeholder="{{ __('messages.phoneMSG') }}" pattern="[0-9]*" inputmode="numeric"
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

                <!-- validation messages -->
                <ul class="text-[14px] space-y-1 text-[#767676] mt-2"
                    x-show="password.length >= 0">
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
                <label for="confirm_password"
                    class="block font-bold text-[20px] mt-2 text-[#212121]">
                    {{ __('messages.confirm_password') }}
                </label>

                <!-- Confirm Password Input -->
                <div
                    class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
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

                <!-- Password match validation -->
                <div class="mt-2 text-sm text-[#d33]"
                    x-show="password && confirmPassword && password !== confirmPassword">
                    {{ __('messages.passwordConfirm') }}
                </div>

            </div>

            <button type="button" @click="bussnissdata = true, accountData = false"
                class="w-full bg-[#185D31] text-white p-[12px] h-[48px] rounded-[12px] hover:bg-green-800">
                {{ __('messages.complete_data') }}
            </button>
        </div>


        {{-- bussness data --}}
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
                    class="flex item-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                    <input type="text" name="national_id" required
                        placeholder="{{ __('messages.national_idMSG') }}"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">
                </div>
                <div x-data="{ fileUpload: null }" class=" mt-2">

                    <!-- Readonly input showing file name -->
                    <div
                        class="flex item-center mt-2 h-[56px] border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                        <input type="text" id="fileNameDisplay" readonly
                            :value="fileUpload ? fileUpload.name : ''"
                            placeholder="{{ __('messages.national_id') }}"
                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">

                        <div
                            class="file-input-container item-center justify-center w-[90px] h-[32px] mt-[11px] ml-2 bg-[#185D31] rounded-[12px] px-[12px] py-[8px]">
                            <label for="fileUpload"
                                class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                    alt="" class="h-[16px] w-[16px] ml-2">
                                {{ __('messages.attach') }}
                            </label>
                            <!-- Hidden file input - This is the one that sends the file -->
                            <input type="file" id="fileUpload" class="hidden"
                                name="national_id_attach" accept="image/*,application/pdf"
                                @change="fileUpload = $event.target.files[0]">
                        </div>
                    </div>
                    <!-- File not uploaded or invalid format -->
                    <span class="text-[14px] space-y-1 text-[#767676] mt-3"
                        x-show="!fileUpload || !fileUpload.name.match(/\.(jpg|jpeg|png|pdf)$/i)">
                        {{ __('messages.file_format') }}
                    </span>
                </div>
            </div>




            <div class="mb-4">
                <label for="commercial_registration"
                    class="block font-bold text-[20px] text-[#212121] rounded-[12px] overflow-hidden">
                    {{ __('messages.commercial_registration') }}
                </label>
                <div
                    class="flex item-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                    <input type="text" name="commercial_registration" required
                        placeholder="{{ __('messages.commercial_registration') }}"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">
                </div>
                <div x-data="{ fileUploadCom: null }" class=" mt-2">

                    <!-- Readonly input showing file name -->
                    <div
                        class="flex item-center mt-2 h-[56px] border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                        <input type="text" id="fileUploadComDisplay" readonly
                            :value="fileUploadCom ? fileUploadCom.name : ''"
                            placeholder="{{ __('messages.commercial_reg_or_certificate') }}"
                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">

                        <div
                            class="file-input-container item-center justify-center w-[90px] h-[32px] mt-[11px] ml-2 bg-[#185D31] rounded-[12px] px-[12px] py-[8px]">
                            <label for="fileUploadCom"
                                class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                    alt="" class="h-[16px] w-[16px] ml-2">
                                {{ __('messages.attach') }}
                            </label>
                            <!-- Hidden file input - This is the one that sends the file -->
                            <input type="file" id="fileUploadCom" class="hidden"
                                name="commercial_registration_attach"
                                accept="image/*,application/pdf"
                                @change="fileUploadCom = $event.target.files[0]">
                        </div>
                    </div>
                    <!-- File not uploaded or invalid format -->
                    <span class="text-[14px] space-y-1 text-[#767676] mt-3"
                        x-show="!fileUploadCom || !fileUploadCom.name.match(/\.(jpg|jpeg|png|pdf)$/i)">
                        {{ __('messages.file_format') }}
                    </span>
                </div>
            </div>


            <div class="mb-4">
                <label for="national_address"
                    class="block font-bold text-[20px] text-[#212121] rounded-[12px] overflow-hidden">
                    {{ __('messages.national_address') }}
                </label>
                <div
                    class="flex item-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                    <input type="text" name="national_address" required
                        placeholder="{{ __('messages.national_addressMSG') }}"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">
                </div>
                <div x-data="{ fileUploadadd: null }" class=" mt-2">

                    <!-- Readonly input showing file name -->
                    <div
                        class="flex item-center mt-2 h-[56px] border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                        <input type="text" id="fileUploadaddDisplay" readonly
                            :value="fileUploadadd ? fileUploadadd.name : ''"
                            placeholder="{{ __('messages.national_address_attach') }}"
                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">

                        <div
                            class="file-input-container item-center justify-center w-[90px] h-[32px] mt-[11px] ml-2 bg-[#185D31] rounded-[12px] px-[12px] py-[8px]">
                            <label for="fileUploadadd"
                                class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                    alt="" class="h-[16px] w-[16px] ml-2">
                                {{ __('messages.attach') }}
                            </label>
                            <!-- Hidden file input - This is the one that sends the file -->
                            <input type="file" id="fileUploadadd" class="hidden"
                                name="national_address_attach"
                                accept="image/*,application/pdf"
                                @change="fileUploadadd = $event.target.files[0]">
                        </div>
                    </div>
                    <!-- File not uploaded or invalid format -->
                    <span class="text-[14px] space-y-1 text-[#767676] mt-3"
                        x-show="!fileUploadadd || !fileUploadadd.name.match(/\.(jpg|jpeg|png|pdf)$/i)">
                        {{ __('messages.file_format') }}
                    </span>
                </div>
            </div>


            <div class="mb-4">
                <label for="iban"
                    class="block font-bold text-[20px] text-[#212121] rounded-[12px] overflow-hidden">
                    {{ __('messages.iban') }}
                </label>
                <div
                    class="flex item-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                    <input type="text" name="iban" required
                        placeholder="{{ __('messages.ibanMSG') }}"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">
                </div>
                <div x-data="{ fileUploadIBAN: null }" class=" mt-2">

                    <!-- Readonly input showing file name -->
                    <div
                        class="flex item-center mt-2 h-[56px] border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                        <input type="text" id="fileUploadIBANDisplay" readonly
                            :value="fileUploadIBAN ? fileUploadIBAN.name : ''"
                            placeholder="{{ __('messages.iban_attach') }}"
                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">

                        <div
                            class="file-input-container item-center justify-center w-[90px] h-[32px] mt-[11px] ml-2 bg-[#185D31] rounded-[12px] px-[12px] py-[8px]">
                            <label for="fileUploadIBAN"
                                class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                    alt="" class="h-[16px] w-[16px] ml-2">
                                {{ __('messages.attach') }}
                            </label>
                            <!-- Hidden file input - This is the one that sends the file -->
                            <input type="file" id="fileUploadIBAN" class="hidden"
                                name="iban_attach" accept="image/*,application/pdf"
                                @change="fileUploadIBAN = $event.target.files[0]">
                        </div>
                    </div>
                    <!-- File not uploaded or invalid format -->
                    <span class="text-[14px] space-y-1 text-[#767676] mt-3"
                        x-show="!fileUploadIBAN || !fileUploadIBAN.name.match(/\.(jpg|jpeg|png|pdf)$/i)">
                        {{ __('messages.file_format') }}
                    </span>
                </div>
            </div>


            <div class="mb-4">
                <label for="tax_certificate"
                    class="block font-bold text-[20px] text-[#212121] rounded-[12px] overflow-hidden">
                    {{ __('messages.tax_certificate') }}
                </label>
                <div
                    class="flex item-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                    <input type="text" name="tax_certificate" required
                        placeholder="{{ __('messages.tax_certificateMSG') }}"
                        class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">
                </div>
                <div x-data="{ fileUploadTax: null }" class=" mt-2">

                    <!-- Readonly input showing file name -->
                    <div
                        class="flex item-center mt-2 h-[56px] border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                        <input type="text" id="fileUploadTaxDisplay" readonly
                            :value="fileUploadTax ? fileUploadTax.name : ''"
                            placeholder="{{ __('messages.tax_certificate_attach') }}"
                            class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-[16px]">

                        <div
                            class="file-input-container item-center justify-center w-[90px] h-[32px] mt-[11px] ml-2 bg-[#185D31] rounded-[12px] px-[12px] py-[8px]">
                            <label for="fileUploadTax"
                                class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                    alt="" class="h-[16px] w-[16px] ml-2">
                                {{ __('messages.attach') }}
                            </label>
                            <!-- Hidden file input - This is the one that sends the file -->
                            <input type="file" id="fileUploadTax" class="hidden"
                                name="tax_certificate_attach" accept="image/*,application/pdf"
                                @change="fileUploadTax = $event.target.files[0]">
                        </div>
                    </div>
                    <!-- File not uploaded or invalid format -->
                    <span class="text-[14px] space-y-1 text-[#767676] mt-3"
                        x-show="!fileUploadTax || !fileUploadTax.name.match(/\.(jpg|jpeg|png|pdf)$/i)">
                        {{ __('messages.file_format') }}
                    </span>
                </div>
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
            <button type="submit"
                class="w-full bg-[#185D31] text-white p-[12px] h-[48px] rounded-[12px] hover:bg-green-800">
                {{ __('messages.register') }}
            </button>
            <button type="button" @click="bussnissdata = false, accountData = true"
                class="w-full bg-[#185D31] text-white p-[12px] h-[48px] rounded-[12px] hover:bg-green-800 mt-2">
                {{ __('messages.previous') }}
            </button>
        </div>
    </form>
</template>
