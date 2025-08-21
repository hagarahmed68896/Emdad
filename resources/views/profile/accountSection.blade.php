<section id="myAccountContentSection" 
    class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-200 {{ $section === 'myAccount' ? '' : 'hidden' }}">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">{{ __('messages.account_details') }}</h2>

    {{--
      START: Account Details Form (Now powered by Alpine.js AJAX)
      Pass the initial user data, including related supplier and documents, to the Alpine component.
      Example: json_encode(['user' => $user->toArray(), 'business' => $user->business->toArray()])
    --}}
    <div x-data="accountDetailsForm({{ json_encode($user) }}, {{ json_encode($businessData) }})">

        {{-- Success Message for account details form --}}
        <div x-show="success" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4" x-text="success"
            x-init="setTimeout(() => success = '', 5000)"></div>

        {{-- Error List for account details form --}}
        <template x-if="Object.keys(errors).length">
            <ul class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-[18px]list-disc list-inside"
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

        <form @submit.prevent="submitDetailsForm" action="{{ route('profile.updateDetails') }}"
            enctype="multipart/form-data" method="POST">
            @csrf
            {{-- All form fields are now correctly nested in this single grid div --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="first_name"
                        class="block text-gray-700 text-[18px]  font-bold mb-2">{{ __('messages.first_name') }}</label>
                    <input type="text" id="first_name" name="first_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder="{{__('messages.enter_first_name')}}" x-model="formData.first_name">
                </div>
                <div>
                    <label for="last_name" class="block text-gray-700 text-[18px]  font-bold mb-2">
                        {{ __('messages.last_name') }}</label>
                    <input type="text" id="last_name" name="last_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder="{{ __('messages.enter_family_name') }}" x-model="formData.last_name">
                </div>
                <div>
                    <label for="email" class="block text-gray-700 text-[18px]  font-bold mb-2">
                        {{ __('messages.email') }}</label>
                    <input type="email" id="email" name="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder="{{ __('messages.emailMSG') }}" x-model="formData.email">
                </div>
                <div>
                    <label for="phone_number" class="block text-gray-700 text-[18px]  font-bold mb-2">
                        {{ __('messages.phone_number') }}</label>
                    <input type="text" id="phone_number" name="phone_number" maxlength="9"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder=" {{ __('messages.phoneMSG') }}" x-model="formData.phone_number">
                </div>
                <div class="md:col-span-2">
                    <label for="address"
                        class="block text-gray-700 text-[18px]  font-bold mb-2">{{ __('messages.address') }}</label>
                    <input type="text" id="address" name="address"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder="{{ __('messages.addressMSG') }}" x-model="formData.address">
                </div>

                {{-- supplier data --}}
                @if (!Auth::check() || Auth::user()->account_type == 'supplier')
                <div class="md:col-span-2">
                    <p class="md:col-span-2 text-[24px]  font-bold text-gray-800 mt-4">
                        {{__('messages.supplier_label')}}
                    </p>
                    <p class="md:col-span-2 text-[#696969] text-[16px]">
                        {{__('messages.supplier_info')}}
                    </p>
                    </div>
                    <div>
                        <label for="company_name" class="block text-gray-700 text-[18px]  font-bold mb-2">
                            {{__('messages.supplier_name')}}</label>
                        <input type="text" id="company_name" name="company_name" required
                            class="w-full px-4 py-2 border rounded-lg" placeholder="{{__('messages.enter_supplier_name')}} "
                            x-model="formData.business.company_name">
                    </div>
                    <div>
                        <label for="  start_date" class="block text-gray-700 text-[18px]  font-bold mb-2"> 
                             {{__('messages.company_created_at')}}</label>
                        <input type="date" id="start_date" name="start_date"
                            class="w-full px-4 py-2 border rounded-lg" x-model="formData.business.start_date">
                    </div>
                    <div class=" md:col-span-2">
                        <label for="experience_years" class="block text-gray-700 text-[18px] font-bold mb-2"> 
                            {{__('messages.exp_years')}}</label>
                        <input type="number" id="experience_years" name="experience_years"
                            class="w-full px-4 py-2 border rounded-lg" x-model="formData.business.experience_years">
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block text-gray-700 text-[18px]  font-bold mb-2">وصف عام عن
                            المورد</label>
                        <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border rounded-lg"
                            x-model="formData.business.description"></textarea>
                    </div>

                    {{-- Certificates --}}
                    <div class="md:col-span-2">
                        <label for="certificate_input" class="block text-gray-700 text-[18px] font-bold mb-2">
                            {{ __('messages.certificates') }}
                        </label>
                        <p class="mb-1 text-[#696969] text-[16px]">{{__('messages.certificates_notes')}}</p>
                        <div class="flex items-center border-[1px] rounded-[12px] overflow-hidden">
                            <input type="text" readonly
                                :value="formData.certificate ? formData.certificate.name :
                                    (() => {
                                        let doc = formData.business.documents.find(d => d.document_name === 'Certificate');
                                        return doc ? doc.original_name : '';
                                    })()"
                                placeholder="{{ __('messages.select_file') }}"
                                class="block w-full px-3 py-2 border-none h-[56px]">

                            <div class="file-input-container flex items-center justify-center w-[90px] h-[32px] mx-2 bg-[#185D31] rounded-[12px]">
                                <label for="certificate_attach_input"
                                    class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                    <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                        alt="" class="h-[16px] w-[16px] ml-2">
                                    {{ __('messages.attach') }}
                                </label>
                                <!-- CHANGE THE NAME ATTRIBUTE HERE -->
                                <input type="file" id="certificate_attach_input" class="hidden"
                                    name="documents[certificate]" accept="image/*,application/pdf"
                                    @change="formData.certificate = $event.target.files[0]">
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- National ID -->
                        <div>
                            <label for="national_id_number" class="block text-gray-700 text-[18px]  font-bold mb-2">
                                {{ __('messages.national_id') }}
                            </label>
                            <div class="flex items-center border-[1px] rounded-[12px] overflow-hidden">
                                <input type="text" id="national_id_number" name="business[national_id]"
                                    x-model="formData.business.national_id" required
                                    placeholder="{{ __('messages.national_id_number') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px]">
                            </div>
                        </div>
                        <div>
                            <label for="national_id_attach" class="block text-gray-700 text-[18px]  font-bold mb-2">
                                {{ __('messages.national_id_attach') }}
                            </label>
                            <div class="flex items-center border-[1px] rounded-[12px] overflow-hidden">
                                <!-- Show old or new file name -->
                                <input type="text" readonly
                                    :value="formData.national_id_attach ?
                                        formData.national_id_attach.name :
                                        (() => {
                                            let doc = formData.business.documents.find(d => d
                                                .document_name === 'National ID');
                                            // Change this line to use the new 'original_name' column
                                            return doc ? doc.original_name : '';
                                        })()"
                                    placeholder="{{ __('messages.select_file') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px]">

                                <!-- Attach button -->
                                <div
                                    class="file-input-container flex items-center justify-center w-[90px] h-[32px] mx-2 bg-[#185D31] rounded-[12px]">
                                    <label for="national_id_attach_input"
                                        class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                        <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                            alt="" class="h-[16px] w-[16px] ml-2">
                                        {{ __('messages.attach') }}
                                    </label>
                                    <input type="file" id="national_id_attach_input" class="hidden"
                                        name="documents[national_id]" accept="image/*,application/pdf"
                                        @change="formData.national_id_attach = $event.target.files[0]">
                                </div>
                            </div>
                        </div>

                        <!-- Commercial Registration -->
                        <div>
                            <label for="commercial_registration_number"
                                class="block text-gray-700 text-[18px]  font-bold mb-2">
                                {{ __('messages.commercial_registration') }}
                            </label>
                            <div class="flex items-center border-[1px] rounded-[12px] overflow-hidden">
                                <input type="text" id="commercial_registration_number"
                                    name="business[commercial_registration]"
                                    x-model="formData.business.commercial_registration" required
                                    placeholder="{{ __('messages.commercial_registration_number') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px]">
                            </div>
                        </div>
                        <div>
                            <label for="commercial_registration_attach"
                                class="block text-gray-700 text-[18px]  font-bold mb-2">
                                {{ __('messages.commercial_registration_attach') }}
                            </label>
                            <div class="flex items-center border-[1px] rounded-[12px] overflow-hidden">

                                <input type="text" readonly
                                    :value="formData.commercial_registration_attach ?
                                        formData.commercial_registration_attach.name :
                                        (() => {
                                            let doc = formData.business.documents.find(d => d
                                                .document_name === 'Commercial Registration');
                                            return doc ? doc.original_name : '';
                                        })()"
                                    placeholder="{{ __('messages.select_file') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px]">

                                <div
                                    class="file-input-container flex items-center justify-center w-[90px] h-[32px] mx-2 bg-[#185D31] rounded-[12px]">
                                    <label for="commercial_registration_attach_input"
                                        class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                        <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                            alt="" class="h-[16px] w-[16px] ml-2">
                                        {{ __('messages.attach') }}
                                    </label>
                                    <input type="file" id="commercial_registration_attach_input" class="hidden"
                                        name="documents[commercial_registration]" accept="image/*,application/pdf"
                                        @change="formData.commercial_registration_attach = $event.target.files[0]">
                                </div>
                            </div>
                        </div>

                        <!-- National Address -->
                        <div>
                            <label for="national_address_number" class="block text-gray-700 text-[18px]  font-bold mb-2">
                                {{ __('messages.national_address') }}
                            </label>
                            <div class="flex items-center border-[1px] rounded-[12px] overflow-hidden">
                                <input type="text" id="national_address_number" name="business[national_address]"
                                    x-model="formData.business.national_address" required
                                    placeholder="{{ __('messages.national_address_number') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px]">
                            </div>
                        </div>
                        <div>
                            <label for="national_address_attach" class="block text-gray-700 text-[18px]  font-bold mb-2">
                                {{ __('messages.national_address_attach') }}
                            </label>
                            <div class="flex items-center border-[1px] rounded-[12px] overflow-hidden">
                                <input type="text" readonly
                                    :value="formData.national_address_attach ?
                                        formData.national_address_attach.name :
                                        (() => {
                                            let doc = formData.business.documents.find(d => d
                                                .document_name === 'National Address');
                                            return doc ? doc.original_name : '';
                                        })()"
                                    placeholder="{{ __('messages.select_file') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px]">
                                <div
                                    class="file-input-container flex items-center justify-center w-[90px] h-[32px] mx-2 bg-[#185D31] rounded-[12px]">
                                    <label for="national_address_attach_input"
                                        class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                        <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                            alt="" class="h-[16px] w-[16px] ml-2">
                                        {{ __('messages.attach') }}
                                    </label>
                                    <input type="file" id="national_address_attach_input" class="hidden"
                                        name="documents[national_address]" accept="image/*,application/pdf"
                                        @change="formData.national_address_attach = $event.target.files[0]">
                                </div>
                            </div>
                        </div>

                        <!-- IBAN -->
                        <div>
                            <label for="iban_number" class="block text-gray-700 text-[18px]  font-bold mb-2">
                                {{ __('messages.iban') }}
                            </label>
                            <div class="flex items-center border-[1px] rounded-[12px] overflow-hidden">
                                <input type="text" id="iban_number" name="business[iban]"
                                    x-model="formData.business.iban" required
                                    placeholder="{{ __('messages.iban_number') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px]">
                            </div>
                        </div>
                        <div>
                            <label for="iban_attach" class="block text-gray-700 text-[18px]  font-bold mb-2">
                                {{ __('messages.iban_attach') }}
                            </label>
                            <div class="flex items-center border-[1px] rounded-[12px] overflow-hidden">
                                <input type="text" readonly
                                    :value="formData.iban_attach ?
                                        formData.iban_attach.name :
                                        (() => {
                                            let doc = formData.business.documents.find(d => d
                                                .document_name === 'IBAN');
                                            return doc ? doc.original_name : '';
                                        })()"
                                    placeholder="{{ __('messages.select_file') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px]">
                                <div
                                    class="file-input-container flex items-center justify-center w-[90px] h-[32px] mx-2 bg-[#185D31] rounded-[12px]">
                                    <label for="iban_attach_input"
                                        class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                        <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                            alt="" class="h-[16px] w-[16px] ml-2">
                                        {{ __('messages.attach') }}
                                    </label>
                                    <input type="file" id="iban_attach_input" class="hidden"
                                        name="documents[iban]" accept="image/*,application/pdf"
                                        @change="formData.iban_attach = $event.target.files[0]">
                                </div>
                            </div>
                        </div>

                        <!-- Tax Certificate -->
                        <div>
                            <label for="tax_certificate_number" class="block text-gray-700 text-[18px]  font-bold mb-2">
                                {{ __('messages.tax_certificate') }}
                            </label>
                            <div class="flex items-center border-[1px] rounded-[12px] overflow-hidden">
                                <input type="text" id="tax_certificate_number" name="business[tax_certificate]"
                                    x-model="formData.business.tax_certificate" required
                                    placeholder="{{ __('messages.tax_certificate_number') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px]">
                            </div>
                        </div>
                        <div>
                            <label for="tax_certificate_attach" class="block text-gray-700 text-[18px]  font-bold mb-2">
                                {{ __('messages.tax_certificate_attach') }}
                            </label>
                            <div class="flex items-center border-[1px] rounded-[12px] overflow-hidden">
                                <input type="text" readonly
                                    :value="formData.tax_certificate_attach ?
                                        formData.tax_certificate_attach.name :
                                        (() => {
                                            let doc = formData.business.documents.find(d => d
                                                .document_name === 'Tax Certificate');
                                            return doc ? doc.original_name : '';
                                        })()"
                                    placeholder="{{ __('messages.select_file') }}"
                                    class="block w-full px-3 py-2 border-none h-[56px]">
                                <div
                                    class="file-input-container flex items-center justify-center w-[90px] h-[32px] mx-2 bg-[#185D31] rounded-[12px]">
                                    <label for="tax_certificate_attach_input"
                                        class="flex justify-center items-center text-[12px] text-white cursor-pointer w-full h-full">
                                        <img src="{{ asset('images/image-camera-1--photos-picture-camera-photography-photo-pictures--Streamline-Core.svg') }}"
                                            alt="" class="h-[16px] w-[16px] ml-2">
                                        {{ __('messages.attach') }}
                                    </label>
                                    <input type="file" id="tax_certificate_attach_input" class="hidden"
                                        name="documents[tax_certificate]" accept="image/*,application/pdf"
                                        @change="formData.tax_certificate_attach = $event.target.files[0]">
                                </div>
                            </div>
                        </div>

                    </div>
                @endif
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
        <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('messages.change_password') }}</h2>
        {{-- Success Message for password form --}}
        <div x-show="success" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4" x-text="success"
            x-init="setTimeout(() => success = '', 5000)"></div>

        {{-- Error List for password form --}}
        <template x-if="Object.keys(errors).length">
            <ul class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-[18px]list-disc list-inside"
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
                    <label for="current_password"
                        class="block text-gray-700 text-[18px]  font-bold mb-2">{{ __('messages.last_password') }}</label>
                    <input type="password" id="current_password" name="current_password"
                        x-model="formData.current_password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder="{{ __('messages.last_password_msg') }}">
                </div>
                <div class="md:col-span-2">
                    <label for="password" class="block text-gray-700 text-[18px]  font-bold mb-2">
                        {{ __('messages.new_password') }}</label>
                    <input type="password" id="password" name="password" x-model="formData.password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder="{{ __('messages.new_password_msg') }}">
                </div>
                <div class="md:col-span-2">
                    <label for="password_confirmation"
                        class="block text-gray-700 text-[18px]  font-bold mb-2">{{ __('messages.confirm_passwordMSG') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        x-model="formData.password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder="{{ __('messages.confirm_passwordMSG') }}">
                </div>
            </div>
            <div class="flex justify-start gap-4">
                <button type="submit"
                    class="px-6 py-2 text-white rounded-lg bg-[#185D31] transition-colors duration-200 shadow-md">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</section>
