@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="flex h-screen px-4 md:px-[64px] flex-col md:flex-row"
     x-data="chatApp({{ $conversations->toJson() }}, {{ $quickReplies->toJson() }}, {{ $openConversationId ?? 'null' }}, '{{ auth()->user()->account_type }},  isBlocked: false, otherUserIsSupplier: false')"
     x-init="init()">

    {{-- Sidebar --}}
    <div class="w-full md:w-1/3 border-b md:border-b-0 md:border-l border-gray-200 py-6 overflow-y-auto"
         :class="{'hidden md:block': currentConversation}"
    >
        <div class="flex justify-between items-center px-2">
            <p class="mb-4 text-[24px] font-bold flex gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
                {{ __('messages.messages') }}
            </p>
        </div>
        <input type="text" placeholder="{{ __('messages.search') }}" x-model="searchTerm"
               class="w-full p-2 border bg-[#EDEDED] rounded mb-4">


        <template x-if="conversations.length > 0">
            <template x-for="conv in filteredConversations()" :key="conv.id">
              <div class="flex items-center justify-between p-2 cursor-pointer hover:bg-gray-100"
     :class="{'bg-gray-200': conv.id === currentConversation}"
     @click="loadConversation(conv.id)">
    <!-- Left: avatar + info -->
    <div class="flex items-center">
   <img :src="conv.profile_picture ? '/storage/' + conv.profile_picture : '/default.png'"
     class="w-10 h-10 rounded-full ml-2">
        <div>
<p class="font-bold" x-text="conv.full_name"></p>
<span class="text-gray-500" x-text="conv.company_name"></span>
     <p class="text-sm text-gray-500 truncate"
               x-text="conv.last_message_text ?? ((conv.messages?.length ? conv.messages[conv.messages.length - 1]?.message : ''))"></p>
        </div>
    </div>

    <!-- Right: timestamp / mute / pin / unread -->
    <div class="flex flex-col items-end ml-2">
        <!-- Show time if not active -->
        <span class="text-xs text-gray-400"
              x-show="conv.id != currentConversation"
              x-text="formatDate(conv.updated_at || (conv.messages?.length ? conv.messages[conv.messages.length - 1]?.created_at : null))">
        </span>

        <!-- Show pin + mute if active -->
        <div x-show="conv.id === currentConversation" class="flex items-center gap-2">
            <button @click.stop="toggleFix(conv.id)" class="text-gray-500 hover:text-gray-700">
                <svg x-bind:class="{'text-[#185D31]': fixedConversations.includes(conv.id)}"
                     xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-pin-fill" viewBox="0 0 16 16">
                    <path d="M4.146.146A.5.5 0 0 1 4.5 0h7a.5.5 0 0 1 .5.5c0 .68-.342 1.174-.646 1.479-.126.125-.25.224-.354.298v4.431l.078.048c.203.127.476.314.751.555C12.36 7.775 13 8.527 13 9.5a.5.5 0 0 1-.5.5h-4v4.5c0 .276-.224 1.5-.5 1.5s-.5-1.224-.5-1.5V10h-4a.5.5 0 0 1-.5-.5c0-.973.64-1.725 1.17-2.189A6 6 0 0 1 5 6.708V2.277a3 3 0 0 1-.354-.298C4.342 1.674 4 1.179 4 .5a.5.5 0 0 1 .146-.354"/>
                </svg>
            </button>
            <button @click.stop="toggleMute(conv.id)" class="text-gray-500 hover:text-gray-700">
                <svg x-bind:class="{'text-[#185D31]': mutedConversations.includes(conv.id)}"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.143 17.082a24.248 24.248 0 0 0 3.844.148m-3.844-.148a23.856 
                             23.856 0 0 1-5.455-1.31 8.964 8.964 0 0 0 2.3-5.542m3.155 
                             6.852a3 3 0 0 0 5.667 1.97m1.965-2.277L21 
                             21m-4.225-4.225a23.81 23.81 0 0 0 
                             3.536-1.003A8.967 8.967 0 0 1 18 
                             9.75V9A6 6 0 0 0 6.53 
                             6.53m10.245 10.245L6.53 6.53M3 3l3.53 3.53" />
                </svg>
            </button>
        </div>

        <!-- Unread count -->
        <span x-show="getUnreadCount(conv) > 0"
              x-text="getUnreadCount(conv)"
              class="bg-[#185D31] text-white text-xs font-bold px-2 py-0.5 rounded-full mt-1">
        </span>
    </div>
</div>

            </template>
        </template>
        <template x-if="conversations.length === 0">
            <div class="flex flex-1 flex-col items-center justify-center text-gray-400 py-20">
                <img src="{{ asset('/images/Illustrations (5).svg') }}" alt="" class="mb-4 max-w-[200px]">
                <p class="text-[20px] text-[#696969] mb-1">{{ __('messages.no_conversations_yet') }}</p>
                <p class="text-[20px] text-[#696969] mb-3">{{ __('messages.browse_and_contact') }}</p>
                <a href="{{ route('products.index') }}"
                   class="bg-[#185D31] p-3 text-white rounded-lg">{{ __('messages.browse_products') }}</a>
            </div>
        </template>
    </div>

    {{-- Main area --}}
    <div class="w-full h-full md:w-2/3 flex flex-col relative"
         :class="{'hidden md:flex': !currentConversation}">

        {{-- Header (only if conversation is open) --}}
        <template x-if="currentConversation">
            <div class="p-4 border-b flex items-center justify-between relative" x-show="!isSearching && product" x-cloak>
                <div class="flex items-center gap-4">
                    <button type="button" @click="currentConversation = null" class="md:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                    <img :src="product?.image ? '{{ asset('storage') }}/' + product.image : '/placeholder.png'"
                         class="w-20 h-20 rounded-lg border bg-[#EDEDED] object-cover" />
                    <div>
                        <p class="font-bold text-lg" x-text="product?.name ?? ''"></p>
                        <p class="text-gray-600">
                            <span x-text="product ? product.price : ''"></span>
                            <span x-show="product ? product.price : ''">{{ __('messages.riyal') }}</span>
                            | {{ __('messages.min_order_quantity') }}
                            <span x-text="product ? product.min_order_quantity : ''"></span> {{ __('messages.piece') }}
                        </p>
                        <p class="text-[#007405]"
                           x-text="product?.shipping_days
                                ? `{{ __('messages.delivery_by') }} ${new Date(Date.now() + product.shipping_days * 24*60*60*1000)
                                    .toLocaleDateString('ar-EG', { day: 'numeric', month: 'long' })}`
                                : ''">
                        </p>
                    </div>
                </div>
                {{-- Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="size-7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                    </button>

                    <div x-cloak x-show="open" @click.away="open = false"
                         class="absolute left-0 mt-2 py-2 w-64 bg-white border rounded-lg shadow-lg z-50">
                        <button @click="startSearch"
                                class="flex gap-2 items-center w-full px-4 py-2 text-right hover:bg-[#185D31] hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            {{ __('messages.search_in_conversation') }}
                        </button>


                        <meta name="csrf-token" content="{{ csrf_token() }}">

<button
    type="button"
    x-data="{ isBlocked: false, label: 'حظر' }"
    x-init="
        $watch('currentConversation', (newId) => {
            if (newId) {
                let conv = conversations.find(c => c.id === newId);
                if (conv) {
                    isBlocked = conv.is_blocked_by_me;
                    label = isBlocked ? 'إلغاء الحظر' : 'حظر';
                }
            }
        });
    "
    @click="
        let conv = conversations.find(c => c.id === currentConversation);
        if (!conv) {
            console.error('Conversation not found');
            return;
        }

        let accountType = '{{ Auth::user()->account_type }}';
        let targetId;

        if (accountType === 'supplier') {
            targetId = conv.user?.id;
        } else {
            targetId = conv.product?.id;
        }

        if (!targetId) {
            console.error('No valid ID to block');
            return;
        }

        fetch(`/users/${targetId}/toggle-block`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                // Update the local state
                isBlocked = d.action === 'blocked';
                label = isBlocked ? 'إلغاء الحظر' : 'حظر';

                // Update the conversation object in the main data array
                conv.is_blocked_by_me = isBlocked;
            }
            console.log('response', d);
        })
        .catch(e => console.error('error', e));
    "
    class="flex gap-1 hover:bg-[#185D31] w-full text-end hover:text-white px-4 py-2 rounded"
>
    <svg x-show="!isBlocked" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M18.364 5.636l-12.728 12.728" />
        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1"/>
    </svg>
    <svg x-show="isBlocked" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 13l4 4L19 7" />
    </svg>

    <span x-text="label"></span>
</button>

                        <div x-data="reportModal()">

                       <button
    @click="
        let conv = conversations.find(c => c.id === currentConversation);
        
        if (!conv || !conv.user || !conv.product || !conv.product.supplier) {
            console.error('Conversation data is incomplete.');
            return;
        }

        const authUserId = {{ auth()->user()->id }};
        let userToReport = null;

        // Check if the authenticated user is the supplier
        if (conv.product.supplier.user.id === authUserId) {
            userToReport = conv.user; // The buyer is the one to report
        } 
        // Check if the authenticated user is the buyer
        else if (conv.user.id === authUserId) {
            userToReport = conv.product.supplier.user; // The supplier is the one to report
        } else {
            console.error('{{ __('messages.no_user_found_to_report') }}');
            return;
        }

        selectedUserId = userToReport.id;
        selectedUserName = userToReport.full_name ?? '{{ __('messages.unknown_user') }}';
        selectedUserImage = userToReport.profile_picture
            ? '{{ asset('storage') }}/' + userToReport.profile_picture
            : '/images/default.png';

        openReportModal = true;
    "
    class="flex gap-2 items-center w-full px-4 py-2 text-right hover:bg-[#185D31] hover:text-white"
>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
    </svg>
    {{ __('messages.report') }}
</button>

                            <div
                                x-show="openReportModal"
                                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                                x-cloak
                            >
                                <div class="bg-white rounded-xl shadow-lg p-6 w-11/12 max-w-lg relative">

                                    <button @click="closeModal" class="absolute top-3 right-3 text-gray-600 hover:text-black">&times;</button>

                                    <p class="text-[20px] font-bold">{{ __('messages.user') }}</p>
                              <div class="flex items-center py-2 mb-4 mt-4 bg-[#EDEDED]">
    <img
        :src="selectedUserImage || '{{ asset('storage/images/default.png') }}'"
        alt="Supplier Image"
        class="w-[55px] h-[55px] mx-3 rounded-full object-cover">
    <h3 class="text-lg font-semibold" x-text="selectedUserName"></h3>
</div>


                                    <h2 class="text-xl font-bold mb-4 text-center">{{ __('messages.submit_report') }}</h2>

                                    <template x-if="successMessage">
                                        <div class="bg-green-100 text-green-700 p-2 rounded mb-3" x-text="successMessage"></div>
                                    </template>

                                    <template x-if="errorMessage">
                                        <div class="bg-red-100 text-red-700 p-2 rounded mb-3" x-text="errorMessage"></div>
                                    </template>

                                    <form @submit.prevent="submitReport">

                                        <label class="block mb-2 font-semibold">{{ __('messages.report_type') }}</label>
                                        <select x-model="form.report_type" class="w-full border p-2 rounded mb-4">
                                            <option value="">{{ __('messages.select_report_type') }}</option>
                                            <option value="استخدام لغة غير لائقة">{{ __('messages.inappropriate_language') }}</option>
                                            <option value="سلوك غير مهني">{{ __('messages.unprofessional_behavior') }}</option>
                                            <option value="معلومات غير صحيحة">{{ __('messages.incorrect_information') }}</option>
                                            <option value="أخرى">{{ __('messages.other') }}</option>
                                        </select>

                                        <label class="block mb-2 font-semibold">{{ __('messages.report_reason') }}</label>
                                        <textarea x-model="form.reason" placeholder="{{ __('messages.please_explain_reason') }}"
                                                  class="w-full border p-2 rounded mb-4"></textarea>

                                        <button type="submit"
                                                class="w-full bg-[#185D31] text-white py-2 rounded hover:bg-[#154a2a]">
                                            {{ __('messages.send') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <script>
                            function reportModal() {
                                return {
                                    openReportModal: false,
                                    selectedUserId: null,
                                    selectedUserName: '',
                                    selectedUserImage: '',
                                    successMessage: '',
                                    errorMessage: '',
                                    form: {
                                        report_type: '',
                                        reason: ''
                                    },

                                    closeModal() {
                                        this.openReportModal = false;
                                        this.successMessage = '';
                                        this.errorMessage = '';
                                        this.form.reason = '';
                                        this.form.report_type = '';
                                        this.selectedUserId = null;
                                        this.selectedUserName = '';
                                        this.selectedUserImage = '';
                                    },

                                    submitReport() {
                                        if (!this.form.report_type) {
                                            this.errorMessage = '{{ __('messages.select_report_type_validation') }}';
                                            return;
                                        }
                                        if (!this.form.reason) {
                                            this.errorMessage = '{{ __('messages.explain_report_reason_validation') }}';
                                            return;
                                        }

                                        fetch(`/users/${this.selectedUserId}/report`, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                            },
                                            body: JSON.stringify({
                                                reason: this.form.reason,
                                                report_type: this.form.report_type
                                            })
                                        })
                                            .then(res => res.json())
                                            .then(data => {
                                                this.successMessage = data.message || '{{ __('messages.report_sent_success') }} 🚨';
                                                this.errorMessage = '';
                                                setTimeout(() => this.closeModal(), 1500);
                                            })
                                            .catch(err => {
                                                console.error(err);
                                                this.errorMessage = '{{ __('messages.report_send_error') }}';
                                            });
                                    }
                                }
                            }
                        </script>
                        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>



                        <button @click="openModal('delete')"
                                class="flex gap-2 items-center w-full px-4 py-2 text-right text-red-600 hover:bg-[#185D31] hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            {{ __('messages.delete_conversation') }}
                        </button>
                    </div>

                    {{-- Ban feedback message --}}
                    <div x-show="banMessage"
                         x-text="banMessage"
                         class="absolute left-0 -bottom-10 px-3 py-1 text-white rounded"
                         :class="product?.supplier?.user?.status === 'banned' ? 'bg-red-500' : 'bg-green-600'">
                    </div>
                </div>
            </div>
        </template>

        {{-- Search --}}
        <div x-show="isSearching" class="p-4 border-b flex items-center gap-2">
            <input type="text" x-model="searchTerm" placeholder="{{ __('messages.type_to_search') }}" class="flex-1 p-2 border rounded">
            <button @click="isSearching=false; searchTerm=''" class="bg-gray-200 px-3 py-2 rounded">✖</button>
        </div>

      {{-- Chat box --}}
        <div id="chat-box" class="flex-1 overflow-y-auto p-4 flex flex-col space-y-4 bg-gray-50">
            <template x-if="!currentConversation">
                <div class="flex flex-1 w-full flex-col items-center justify-center text-[#696969] py-[20%]">
                    <img src="{{ asset('/images/Illustrations (5).svg') }}" alt="" class="mb-4 max-w-[200px]">
                    <p class="text-lg">{{ __('messages.start_conversation_to_view_messages') }}</p>
                </div>
            </template>

            <template x-if="currentConversation">
                <div>
                    <div class="flex justify-start mb-2">
                        <div class="bg-gray-100 text-right text-gray-800 px-4 py-3 rounded-2xl shadow max-w-lg">
                            <p class="mb-3 font-medium">
                                {{ __('messages.welcome_message') }}<br>
                                {{ __('messages.quick_replies_instruction') }}
                            </p>

                            {{-- Quick Replies only for buyers --}}
                            @if (Auth::user()->account_type !== 'supplier')
                                <div class="space-y-2 mb-1">
                                    <template x-for="qr in quickReplies" :key="qr.id">
                                        <button @click="sendQuickReply(qr)"
                                                class="w-full text-right bg-white border border-gray-300 px-4 py-2 rounded-xl hover:bg-[#185D31] transition">
                                            <span x-text="qr.text"></span>
                                        </button>
                                    </template>
                                </div>
                            @endif
                        </div>
                    </div>

                    <template x-for="msg in messages" :key="msg.id">
                        <div class="flex items-end mb-3"
                            :class="msg.sender_id === currentUserId ? 'justify-start' : 'justify-end'">

                            {{-- Sender image (auth user) --}}
                            <template x-if="msg.sender_id === currentUserId">
                                <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : '/images/default.png' }}"
                                    class="w-10 h-10 rounded-full ml-2">
                            </template>

                            <div class="max-w-[70%] p-2 mb-1 rounded-lg shadow"
                                :class="msg.sender_id === currentUserId
                                    ? 'bg-[#185D31] text-white text-right rounded-tl-none'
                                    : 'bg-white text-black text-left rounded-tr-none'">

                                {{-- Attachment --}}
                                <template x-if="msg.attachment">
                                    <div>
                                        {{-- Image attachments --}}
                               <template x-if="msg.attachment.startsWith('blob:') || msg.attachment.match(/\.(jpg|jpeg|png|gif|webp)$/i)">
    <img
        :src="msg.attachment.startsWith('blob:') 
            ? msg.attachment 
            : '{{ asset('storage') }}/' + msg.attachment"
        alt="{{ __('messages.attachment') }}"
        class="max-w-full h-auto rounded-lg mb-2 cursor-pointer object-contain">
</template>


                                        {{-- File attachments --}}
                                        <template x-if="!msg.attachment.startsWith('blob:') && !msg.attachment.match(/\.(jpg|jpeg|png|gif|webp)$/i)">
                                            <a :href="'/storage/' + msg.attachment"
                                            target="_blank"
                                            class="flex items-center gap-2 text-sm"
                                            :class="msg.sender_id === currentUserId ? 'text-gray-100 hover:text-white' : 'text-blue-500 hover:underline'">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 14.25v-2.125a3.75 3.75 0 0 0-7.5 0v2.125m10.5-5.625a3.75 3.75 0 0 1-7.5 0m7.5 0a3.75 3.75 0 0 0-7.5 0M18 12.75a6 6 0 0 1-12 0v-2.125a6 6 0 0 1 12 0v2.125Z"/>
                                                </svg>
                                                <span x-text="msg.attachment.split('/').pop()"></span>
                                            </a>
                                        </template>
                                    </div>
                                </template>

                                {{-- Message text --}}
                                <template x-if="msg.message">
                                    <p x-html="highlightText(msg.message)" class="break-words"></p>
                                </template>

                                {{-- Timestamp --}}
                                <span class="text-xs block mt-1"
                                    :class="msg.sender_id === currentUserId ? 'text-[#EDEDED]' : 'text-gray-600'"
                                    x-text="formatDate(msg.created_at)"></span>
                            </div>

                            {{-- Receiver image (other user) --}}
                            <template x-if="msg.sender_id !== currentUserId">
                                <img
                                    :src="(() => {
                                        let conv = conversations.find(c => c.id === currentConversation);
                                        if (!conv) return '/images/default.png';

                                        if ('{{ Auth::user()->account_type }}' === 'supplier') {
                                            // Supplier is chatting with buyer
                                            return conv.user?.profile_picture ? '/storage/' + conv.user.profile_picture : '/images/default.png';
                                        } else {
                                            // Buyer is chatting with supplier
                                            return conv.product?.supplier?.user?.profile_picture ? '/storage/' + conv.product.supplier.user.profile_picture : '/images/default.png';
                                        }
                                    })()"
                                    class="w-10 h-10 rounded-full mr-2">
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>






        {{-- Input (only if conversation exists) --}}
        {{-- <template x-show="activeConversation"> --}}
            <div x-show="activeConversation">
  {{-- Warning when conversation is blocked --}}
        <div id="blocked-warning" x-cloak
             x-show="activeConversation?.is_blocked_by_me"
             class="p-4 bg-gray-200 text-center text-red-700 font-bold rounded-b-lg">
            <span>{{ __('messages.cannot_send_messages') }}</span>
        </div>
          
          {{-- Warning if conversation is closed or under review --}}
        <div  x-cloak
            x-show="
                activeConversation.status === 'closed' || 
                (activeConversation.status === 'under_review' && activeConversation.block_until && new Date(activeConversation.block_until) > new Date())
            "
            class="p-4 bg-gray-200 text-center text-red-700 font-bold rounded-b-lg"
        >
            <template x-if="activeConversation.status === 'closed'">
            <span>{{ __('messages.conversation_closed') }}</span>
            </template>

            <template x-if="activeConversation.status === 'under_review'">
                <span>
{{ __('messages.conversation_under_review') }}
                    <span x-text="new Date(activeConversation.block_until).toLocaleString()"></span>
                </span>
            </template>
        </div>
       {{-- Chat form (hidden if blocked) --}}
<form @submit.prevent="sendMessage"
              id="chat-form"
x-show="activeConversation && !activeConversation.is_blocked_by_me && activeConversation.status === 'open'"
              class="p-4 flex flex-col border-t relative gap-2 bg-white">
                {{-- Attachment preview --}}
                <div x-show="attachmentName" class="flex items-center justify-between gap-2 text-sm text-gray-600 border p-2 rounded-lg bg-gray-50">
                    <span class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.75c-.381.18-.814.285-1.25.285-.92 0-1.74-.484-2.22-1.21-.482-.725-.65-1.55-.49-2.317.16-.767.592-1.42 1.15-1.957.558-.538.995-.918 1.455-1.34.46-.422.84-.93 1.09-1.503.25-.573.34-1.21.25-1.85-.09-.64-.32-1.25-.67-1.76-.35-.51-.81-.92-1.35-1.22-.54-.3-1.12-.45-1.69-.45-.57 0-1.14.15-1.68.45-.54.3-.99.71-1.34 1.22-.35.51-.58.99-.67 1.55-.09.64-.09 1.28.09 1.95.18.67.5 1.3.89 1.83.39.53.64 1.14.73 1.8.09.66-.03 1.32-.34 1.93-.31.61-.79 1.14-1.37 1.59-1.34 1.02-2.92.21-3.69-.74-.77-.95-1.12-2.14-1.05-3.32.07-1.18.42-2.34 1.05-3.32.63-1.02 1.48-1.84 2.45-2.45.97-.61 2.06-.97 3.19-.97 1.13 0 2.22.36 3.19.97.97.61 1.82 1.43 2.45 2.45.63.98.98 2.14 1.05 3.32.07 1.18-.28 2.37-1.05 3.32-.77.95-2.35 1.76-3.69.74" />
                        </svg>
                        <span x-text="attachmentName"></span>
                    </span>
                    <button type="button" @click="removeAttachment" class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Main input area --}}
                <div class="flex items-center gap-2">
                    {{-- Attachments and Emoji buttons --}}
                    <div class="relative flex items-center">
                        <button type="button" @click="$refs.attachmentInput.click()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                            </svg>

                        </button>
                        <input type="file" x-ref="attachmentInput" accept="image/*,video/*,application/pdf" class="hidden" @change="handleAttachmentChange($event)">

                        <button type="button" @click="showEmojiPicker = !showEmojiPicker" class="p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                            </svg>

                        </button>

                        {{-- Emoji Picker --}}
                        <div x-show="showEmojiPicker" @click.away="showEmojiPicker = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute bottom-full right-0 mb-2 bg-white border border-gray-200 rounded-lg shadow-xl p-2 z-10 w-64">
                            <div class="grid grid-cols-8 gap-1">
                                <template x-for="emoji in ['😀','😂','😍','👍','🙏','❤️','🔥','🎉', '👀', '💯', '🤔', '😊', '😭', '🤯', '🥳', '😎']">
                                    <button type="button" @click="insertEmoji(emoji)" class="text-xl p-1 hover:bg-gray-200 rounded-md transition-colors">
                                        <span x-text="emoji"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Text Input Field --}}
                    <input type="text" x-model="newMessage" x-ref="textInput" placeholder="{{ __('messages.type_your_message') }}"
                           class="flex-1 p-3 border border-gray-300 bg-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-[#185D31]/50 transition-all">

                    {{-- Send Button --}}
                    <button type="submit" class="bg-[#185D31] text-white p-3 rounded-full hover:bg-[#154a2a] transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                             class="size-6" style="transform: scaleX(-1);">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.768 59.768 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                    </button>

                </div>

            </form>
            </div>
        {{-- </template> --}}





    </div>

    <div x-cloak x-show="modalType"
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
         x-transition>
        <div class="bg-white rounded-xl shadow-lg p-6 w-11/12 max-w-lg">
            <h2 class="text-xl font-bold mb-4 text-center">
                <span x-show="modalType==='delete'">{{ __('messages.confirm_delete_conversation') }}</span>
            </h2>

            <div x-show="modalType==='report'">
                <textarea x-model="reportReason" class="w-full border rounded p-2" placeholder="{{ __('messages.write_reason') }}"></textarea>
            </div>

            <div class="flex justify-center gap-4 mt-6">
                <button @click="closeModal" class="px-4 py-2 bg-gray-300 rounded">{{ __('messages.cancel') }}</button>
                <button @click="confirmModal" class="px-4 py-2 bg-red-600 text-white rounded">{{ __('messages.confirm') }}</button>
            </div>
        </div>
    </div>


</div>



<script>
function chatApp(initialConversations, initialQuickReplies, initialConversationId = null) {
    return {
        conversations: initialConversations || [],
        quickReplies: initialQuickReplies || [],
        currentConversation: null,
        messages: [],
        product: null,
        currentUserId: @json(auth()->id()),
        searchTerm: '',
        isSearching: false,
        modalType: null,
        reportReason: '',
        showEmojiPicker: false,
        newMessage: '',
        banMessage: '',
        attachmentFile: null, // New: Holds the file object
        attachmentName: '',   // New: Holds the file name for display
        fixedConversations: [], // To store IDs of fixed conversations
        mutedConversations: [], // To store IDs of muted conversations
        modalId: null, // New variable to store the ID



        init() {
            if (initialConversationId) {
                this.loadConversation(initialConversationId);
            } else if (this.conversations.length > 0) {
                // If no specific conversation is requested, load the first one
                this.loadConversation(this.conversations[0].id);
            }
        },
filteredConversations() {
    let filtered = this.conversations;

    // Apply search filter
    if (this.searchTerm) {
        const term = this.searchTerm.toLowerCase();
        filtered = filtered.filter(c =>
            c.product?.supplier?.user?.full_name?.toLowerCase().includes(term) ||
            c.product?.supplier?.company_name?.toLowerCase().includes(term) ||
            c.last_message_text?.toLowerCase().includes(term) ||
            (c.messages?.length && c.messages[c.messages.length - 1].message?.toLowerCase().includes(term))
        );
    }

    // Sort: fixed first → normal → muted last
    return filtered.sort((a, b) => {
        const aFixed = this.fixedConversations.includes(a.id);
        const bFixed = this.fixedConversations.includes(b.id);
        const aMuted = this.mutedConversations.includes(a.id);
        const bMuted = this.mutedConversations.includes(b.id);

        // 1. Fixed chats always go first
        if (aFixed && !bFixed) return -1;
        if (!aFixed && bFixed) return 1;

        // 2. Muted chats always go last
        if (aMuted && !bMuted) return 1;
        if (!aMuted && bMuted) return -1;

        // 3. Otherwise sort by latest activity
        const aDate = new Date(a.updated_at || (a.messages?.length ? a.messages[a.messages.length - 1]?.created_at : 0));
        const bDate = new Date(b.updated_at || (b.messages?.length ? b.messages[b.messages.length - 1]?.created_at : 0));
        return bDate - aDate;
    });
}

,
  get activeConversation() {
        return this.conversations.find(c => c.id === this.currentConversation);
    },
  // New computed property to handle sorting
        get sortedConversations() {
            const fixed = this.conversations.filter(c => this.fixedConversations.includes(c.id));
            const nonFixed = this.conversations.filter(c => !this.fixedConversations.includes(c.id));

            nonFixed.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));

            return [...fixed, ...nonFixed];
        },
        
    
        startSearch() { this.isSearching = true; },

        highlightText(text) {
            if (!this.searchTerm) return text;
            const safe = this.searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            return text.
            replace(new RegExp(`(${safe})`, 'gi'), '<mark class="bg-yellow-300">$1</mark>');
        },

openModal(type, id = null) {
    this.modalType = type;
    this.modalId = id; // Store the ID here
},
        closeModal() { this.modalType = null; this.reportReason = ''; },
  confirmModal() {
    if (this.modalType === 'delete') {
        this.deleteConversation();
    }
    this.closeModal();
},


reportSupplier(id) {
    // Access the ID from the state variable
    if (!id) {
        console.error('❌ Cannot report: Supplier ID is missing.');
        return;
    }

    fetch(`/suppliers/${id}/report`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ reason: this.reportReason })
    })
    .then(res => res.json())
    .then(data => {
        console.log('✅ تم إرسال البلاغ:', data);
    })
    .catch(err => {
        console.error('❌ خطأ في البلاغ:', err);
    });
}
,

        deleteConversation() {
            if (!this.currentConversation) return;
            fetch(`/conversations/${this.currentConversation}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => {
                if (!r.ok) throw new Error('Delete failed');
                this.conversations = this.conversations.filter(c => c.id !== this.currentConversation);
                this.currentConversation = null;
                this.messages = [];
                this.product = null;
            })
            .catch(e => console.error(e));
        },

       sendMessage() {
    if (!this.newMessage && !this.attachmentFile) return;
    if (!this.currentConversation) return;

    // Prepare a temporary local message
    const tempMessage = {
        id: 'local-' + Date.now(),
        message: this.newMessage || '',
        attachment: this.attachmentFile ? URL.createObjectURL(this.attachmentFile) : null,
        sender_id: this.currentUserId,
        created_at: new Date().toISOString()
    };

    // Optimistically add it to the chat
    this.messages.push(tempMessage);

    // Save message content and attachment
    const messageText = this.newMessage;
    const attachmentFile = this.attachmentFile;

    // Clear input fields immediately
    this.newMessage = '';
    this.attachmentFile = null;
    this.attachmentName = '';
    if (this.$refs.attachmentInput) this.$refs.attachmentInput.value = '';

    this.scrollToBottom();
    this.updateConversationPreviewFromMessages();

    // Send to server
    const formData = new FormData();
    formData.append('conversation_id', this.currentConversation);
    if (messageText) formData.append('message', messageText);
    if (attachmentFile) formData.append('attachment', attachmentFile);

    axios.post(`/messages/${this.currentConversation}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
    .then(res => {
        // Replace temporary message with server-saved message
        const idx = this.messages.findIndex(m => m.id === tempMessage.id);
        if (idx !== -1 && res.data.message) this.messages.splice(idx, 1, res.data.message);
        this.scrollToBottom();
    })
    .catch(err => {
        console.error('Error sending message:', err);
        // Remove temporary message if failed
        this.messages = this.messages.filter(m => m.id !== tempMessage.id);
        this.scrollToBottom();
    });
},


        // This function sends only the text message
// This function sends only the text message
sendTextMessage() {
    axios.post(`/messages/${this.currentConversation}`, {
        message: this.newMessage
    })
    .then(res => {
            console.log(res.data); // check what you actually receive

        // Corrected: Reassign the entire array to force a re-render
        this.messages = [...this.messages, res.data.message];
        this.newMessage = '';
        this.updateConversationPreviewFromMessages();
        this.scrollToBottom();
    })
    .catch(err => {
        console.error(err);
    });
},

        handleAttachmentChange(event) {
    let file = event.target.files[0];
    if (!file) return;

    this.attachmentFile = file;
    this.attachmentName = file.name;
},

        // This function sends the attachment (and optional text)
    // This function sends the attachment (and optional text)
uploadAttachment() {
    let formData = new FormData();
    formData.append('attachment', this.attachmentFile);
    formData.append('conversation_id', this.currentConversation);
    formData.append('message', this.newMessage || '');

    axios.post(`/messages/${this.currentConversation}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
    .then(res => {
        // Corrected: Reassign the entire array to force a re-render
        this.messages = [...this.messages, res.data.message];
        this.attachmentFile = null;
        this.attachmentName = '';
        this.newMessage = '';
        if (this.$refs.attachmentInput) this.$refs.attachmentInput.value = '';
        this.updateConversationPreviewFromMessages();
        this.scrollToBottom();
    })
    .catch(err => {
        console.error(err);
    });
},

        removeAttachment() {
            this.attachmentFile = null;
            this.attachmentName = '';
            if (this.$refs.attachmentInput) this.$refs.attachmentInput.value = '';
        },

        insertEmoji(emoji) {
            const input = this.$refs.textInput;
            if (!input) { this.newMessage += emoji; return; }
            const start = input.selectionStart ?? this.newMessage.length;
            const end = input.selectionEnd ?? this.newMessage.length;
            this.newMessage = this.newMessage.slice(0, start) + emoji + this.newMessage.slice(end);
            this.$nextTick(() => {
                input.focus();
                const pos = start + emoji.length;
                input.setSelectionRange(pos, pos);
            });
        },

        updateConversationPreviewFromMessages() {
            if (!this.currentConversation || !this.messages?.length) return;
            const idx = this.conversations.findIndex(c => c.id === this.currentConversation);
            if (idx === -1) return;
            const last = this.messages[this.messages.length - 1];
            this.conversations[idx] = {
                ...this.conversations[idx],
                last_message_text: last.message,
                updated_at: last.created_at || new Date().toISOString(),
            };
        },
loadConversation(id) {
    this.currentConversation = id;
    fetch(`/messages/${id}`)
        .then(res => res.json())
        .then(data => {
            this.messages = data.messages || [];
            this.product = data.product || null;

            // ✅ Mark unread messages as read
            this.messages.forEach(msg => {
                if (!msg.is_read && msg.sender_id !== this.currentUserId) {
                    msg.is_read = true;
                }
            });

            // ✅ Sync updated messages into conversations array
            let idx = this.conversations.findIndex(c => c.id === id);
            if (idx !== -1) {
                this.conversations[idx].messages = this.messages;
                this.conversations = [...this.conversations]; // force Alpine reactivity
            }

            // ✅ Tell backend to mark them as read
            fetch(`/conversations/${id}/mark-read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });

            this.updateConversationPreviewFromMessages();
            this.scrollToBottom();
        });
},
getUnreadCount(conv) {
    if (!conv.messages) return 0;
    return conv.messages.filter(m => !m.is_read && m.sender_id !== this.currentUserId).length;
},


        sendQuickReply(qr) {
            if (!qr || !this.currentConversation) return;
            fetch(`/messages/${this.currentConversation}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ message: qr.text, quick_reply_id: qr.id })
            })
            .then(res => res.json())
            .then(data => {
                this.messages = data.messages;
                this.updateConversationPreviewFromMessages();
                this.scrollToBottom();
            })
            .catch(err => console.error('Error sending quick reply message:', err));
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = document.getElementById('chat-box');
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

 formatDate(date) {
    if (!date) return '';
    const d = new Date(date);
    let hours = d.getHours();
    const minutes = d.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    return `${hours}:${minutes} ${ampm}`;
}


        ,
        // ...inside chatApp return object
toggleFix(conversationId) {
    const index = this.fixedConversations.indexOf(conversationId);
    if (index > -1) {
        this.fixedConversations.splice(index, 1);
    } else {
        this.fixedConversations.push(conversationId);
    }
    // Optional: Persist this state to the server
    // fetch('/conversations/' + conversationId + '/toggle-fix', { method: 'POST', ... });
},

toggleMute(conversationId) {
    const index = this.mutedConversations.indexOf(conversationId);
    if (index > -1) {
        this.mutedConversations.splice(index, 1);
    } else {
        this.mutedConversations.push(conversationId);
    }
    // Optional: Persist this state to the server
    // fetch('/conversations/' + conversationId + '/toggle-mute', { method: 'POST', ... });
},
// ...
    };
}
</script>
<script src="//unpkg.com/alpinejs" defer></script>

@endsection