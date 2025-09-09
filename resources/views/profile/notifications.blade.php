 <section id="notificationsSection" class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-200 {{ request('section') === 'notificationsSection' ? '' : 'hidden' }}">

    {{-- Pass notificationSettings to Alpine.js --}}
    <div x-data="notificationsForm({{ json_encode($notificationSettings) }})"> 
        {{-- Success/Error Messages for notifications --}}
        <div x-show="success" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4" 
            x-text="success"
            x-init="setTimeout(() => success = '', 5000)"></div>

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

        <form @submit.prevent="submitNotificationsForm" action="{{ route('profile.updateNotifications') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <label for="receive_in_app" class="text-gray-700">{{ __('messages.receive_in_app') }}</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="receive_in_app" name="receive_in_app" x-model="formData.receive_in_app">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <label for="receive_chat" class="text-gray-700">{{ __('messages.receive_chat') }}</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="receive_chat" name="receive_chat" x-model="formData.receive_chat">
                        <span class="slider"></span>
                    </label>
                </div>

            @if(auth()->user()->account_type === 'customer')

                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <label for="order_status_updates" class="text-gray-700">{{ __('messages.order_status_updates') }}</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="order_status_updates" name="order_status_updates" x-model="formData.order_status_updates">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <label for="offers_discounts" class="text-gray-700">{{ __('messages.offers_discounts') }}</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="offers_discounts" name="offers_discounts" x-model="formData.offers_discounts">
                        <span class="slider"></span>
                    </label>
                </div>

                {{-- <div class="flex items-center justify-between py-2">
                    <label for="viewed_products_offers" class="text-gray-700">{{ __('messages.viewed_products_offers') }}</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="viewed_products_offers" name="viewed_products_offers" x-model="formData.viewed_products_offers">
                        <span class="slider"></span>
                    </label>
                </div> --}}
            @else 
            <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <label for="receive_new_order" class="text-gray-700">{{ __('messages.receive_new_order') }}</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="receive_new_order" name="receive_new_order" x-model="formData.receive_new_order">
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <label for="receive_new_review" class="text-gray-700">{{ __('messages.receive_new_review') }}</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="receive_new_review" name="receive_new_review" x-model="formData.receive_new_review">
                        <span class="slider"></span>
                    </label>
                </div>
                {{-- <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <label for="receive_complain" class="text-gray-700">{{ __('messages.receive_complain') }}</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="receive_complain" name="receive_complain" x-model="formData.receive_complain">
                        <span class="slider"></span>
                    </label>
                </div> --}}
                @endif
            </div>
            
            <div class="flex justify-start gap-4 mt-8">
                <button type="submit"
                    class="px-6 py-2 text-white rounded-lg bg-[#185D31] transition-colors duration-200 shadow-md">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</section>