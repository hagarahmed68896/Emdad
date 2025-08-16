<section id="myAccountContentSection"
    class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-200 {{ request('section') === 'favoritesSection' ? 'hidden' : '' }}">
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
            <ul class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm list-disc list-inside"
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

        <form @submit.prevent="submitDetailsForm" action="{{ route('profile.updateDetails') }}" method="POST">
            @csrf
            {{-- All form fields are now correctly nested in this single grid div --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="first_name"
                        class="block text-gray-700 text-sm font-medium font-bold mb-2">{{ __('messages.first_name') }}</label>
                    <input type="text" id="first_name" name="first_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder="أدخل اسمك الأول" x-model="formData.first_name">
                </div>
                <div>
                    <label for="last_name" class="block text-gray-700 text-sm font-medium font-bold mb-2">
                        {{ __('messages.last_name') }}</label>
                    <input type="text" id="last_name" name="last_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder="{{ __('messages.enter_family_name') }}" x-model="formData.last_name">
                </div>
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-medium font-bold mb-2">
                        {{ __('messages.email') }}</label>
                    <input type="email" id="email" name="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder="{{ __('messages.emailMSG') }}" x-model="formData.email">
                </div>
                <div>
                    <label for="phone_number" class="block text-gray-700 text-sm font-medium font-bold mb-2">
                        {{ __('messages.phone_number') }}</label>
                    <input type="text" id="phone_number" name="phone_number" maxlength="9"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder=" {{ __('messages.phoneMSG') }}" x-model="formData.phone_number">
                </div>
                <div class="md:col-span-2">
                    <label for="address"
                        class="block text-gray-700 text-sm font-medium font-bold mb-2">{{ __('messages.address') }}</label>
                    <input type="text" id="address" name="address"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder="{{ __('messages.addressMSG') }}" x-model="formData.address">
                </div>

                {{-- supplier data --}}
                @if (!Auth::check() || Auth::user()->account_type == 'supplier')
                    <h2 class="md:col-span-2 text-3xl text-gray-800 mb-1 mt-4 text-right">
                        بيانات المورد
                    </h2>
                    <div>
                        <label for="company_name" class="block text-gray-700 text-sm font-medium font-bold mb-2">اسم
                            المورد</label>
                        <input type="text" id="company_name" name="company_name"
                            class="w-full px-4 py-2 border rounded-lg" placeholder="أدخل اسم المورد"
                            x-model="formData.business.company_name">
                    </div>
                    <div>
                        <label for="created_at" class="block text-gray-700 text-sm font-medium font-bold mb-2">تاريخ بدء
                            النشاط التجاري</label>
                        <input type="date" id="created_at" name="created_at"
                            class="w-full px-4 py-2 border rounded-lg" x-model="formData.business.created_at">
                    </div>
                    <div>
                        <label for="experience_years" class="block text-gray-700 text-sm font-medium font-bold mb-2">عدد سنوات
                            الخبرة</label>
                        <input type="number" id="experience_years" name="experience_years"
                            class="w-full px-4 py-2 border rounded-lg" x-model="formData.business.exp_years">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-medium font-bold mb-2">فئة المنتجات</label>
                        <select name="product_category" x-model="formData.business.product_category"
                            class="w-full px-4 py-2 border rounded-lg">
                            <option value="">حدد فئة المنتجات</option>
                            <option value="clothes">الملابس</option>
                            <option value="electronics">الإلكترونيات</option>
                            <option value="food">الأطعمة</option>
                            <option value="medical">مستلزمات طبية</option>
                        </select>
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block text-gray-700 text-sm font-medium font-bold mb-2">وصف عام عن
                            المورد</label>
                        <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border rounded-lg"
                            x-model="formData.business.supplier_desc"></textarea>
                    </div>

                    {{-- Certificates --}}
                    <div class="md:col-span-2">
                        <label for="certificate" class="block text-gray-700 text-sm font-medium font-bold mb-2">شهادات</label>
                        <input type="file" id="certificate" name="certificate" accept=".jpg,.jpeg,.png,.pdf"
                            class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    {{-- Existing Attachments Display --}}
                    <div class="md:col-span-2 mt-4">
                        <label class="block text-gray-700 text-sm font-medium font-bold mb-2">المرفقات الحالية</label>
                        <template x-if="formData.business.documents && formData.business.documents.length > 0">
                            <ul class="list-disc list-inside space-y-2">
                                <template x-for="document in formData.business.documents" :key="document.id">
                                    <li class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 2h2v2H6V6zm4 0h4v2h-4V6z" clip-rule="evenodd" />
                                        </svg>
                                        <a :href="document.file_path" x-text="document.document_type" target="_blank" class="text-blue-600 hover:underline"></a>
                                    </li>
                                </template>
                            </ul>
                        </template>
                        <div x-if="!formData.business.documents || formData.business.documents.length === 0" class="text-gray-500 text-sm">
                            لا توجد مرفقات حالية.
                        </div>
                    </div>

                    {{-- Attachments Upload Inputs --}}
                    <div class="md:col-span-2 mt-4">
                        <label class="block text-gray-700 text-sm font-medium font-bold mb-2">تحميل مرفقات جديدة</label>
                        <div class="space-y-4">
                            <input type="file" name="national_id" accept=".jpg,.jpeg,.png" class="w-full">
                            <input type="file" name="commercial_register" accept=".jpg,.jpeg,.png" class="w-full">
                            <input type="file" name="national_address" accept=".jpg,.jpeg,.png" class="w-full">
                            <input type="file" name="iban" accept=".jpg,.jpeg,.png" class="w-full">
                            <input type="file" name="training_certificate" accept=".jpg,.jpeg,.png" class="w-full">
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
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">{{ __('messages.change_password') }}</h2>
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
            <ul class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm list-disc list-inside"
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
                        class="block text-gray-700 text-sm font-medium font-bold mb-2">{{ __('messages.last_password') }}</label>
                    <input type="password" id="current_password" name="current_password"
                        x-model="formData.current_password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder="{{ __('messages.last_password_msg') }}">
                </div>
                <div class="md:col-span-2">
                    <label for="password" class="block text-gray-700 text-sm font-medium font-bold mb-2">
                        {{ __('messages.new_password') }}</label>
                    <input type="password" id="password" name="password" x-model="formData.password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                        placeholder="{{ __('messages.new_password_msg') }}">
                </div>
                <div class="md:col-span-2">
                    <label for="password_confirmation"
                        class="block text-gray-700 text-sm font-medium font-bold mb-2">{{ __('messages.confirm_passwordMSG') }}</label>
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