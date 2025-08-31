@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="flex h-screen px-4 md:px-[64px] flex-col md:flex-row"
     x-data="chatApp({{ $conversations->toJson() }}, {{ $quickReplies->toJson() }}, {{ $openConversationId ?? 'null' }})"
     x-init="init()">

    {{-- Sidebar --}}
    <div class="w-full md:w-1/3 border-b md:border-b-0 md:border-l border-gray-200 py-6 overflow-y-auto"
         :class="{'hidden md:block': currentConversation}">
        <p class="mb-4 text-[24px] font-bold flex gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
            </svg>
            الرسائل
        </p>

        <input type="text" placeholder="بحث" class="w-full p-2 border bg-[#EDEDED] rounded mb-4">

        <template x-if="conversations.length > 0">
            <template x-for="conv in conversations" :key="conv.id">
                <div class="flex items-center justify-between p-2 cursor-pointer hover:bg-gray-100"
                     @click="loadConversation(conv.id)">
                    <div class="flex items-center">
                        <img :src="conv.product?.supplier?.user?.profile_picture
                                        ? '/storage/' + conv.product.supplier.user.profile_picture
                                        : '/default.png'"
                             class="w-10 h-10 rounded-full ml-2">
                        <div>
                            <p class="font-bold" x-text="conv.product.supplier.user.full_name"></p>
                            <span class="text-gray-500" x-text="conv.product.supplier.company_name"></span>
                            <p class="text-sm text-gray-500 truncate"
                               x-text="conv.last_message_text ?? ((conv.messages?.length ? conv.messages[conv.messages.length - 1]?.message : ''))"></p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400"
                          x-text="formatDate(conv.updated_at || (conv.messages?.length ? conv.messages[conv.messages.length - 1]?.created_at : null))"></span>
                </div>
            </template>
        </template>

        <template x-if="conversations.length === 0">
            <div class="flex flex-1 flex-col items-center justify-center text-gray-400 py-20">
                <img src="{{ asset('/images/Illustrations (5).svg') }}" alt="" class="mb-4 max-w-[200px]">
                <p class="text-[20px] text-[#696969] mb-1">لم تبدأ أي محادثة بعد</p>
                <p class="text-[20px] text-[#696969] mb-3">تصفح المنتجات وتواصل مع الموردين.</p>
                <a href="{{ route('products.index') }}"
                   class="bg-[#185D31] p-3 text-white rounded-lg">تصفح المنتجات</a>
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
                            <span x-show="product ? product.price : ''">ريال</span>
                            | الحد الأدني للطلب
                            <span x-text="product ? product.min_order_quantity : ''"></span> قطعة
                        </p>
<p class="text-[#007405]"
x-text="product?.shipping_days
? `التوصيل بحلول ${new Date(Date.now() + product.shipping_days * 24*60*60*1000)
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

                    <div x-show="open" @click.away="open = false"
                         class="absolute left-0 mt-2 py-2 w-64 bg-white border rounded-lg shadow-lg z-50">
                        <button @click="startSearch"
                                class="flex gap-2 items-center w-full px-4 py-2 text-right hover:bg-[#185D31] hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            بحث في المحادثة
                        </button>

                        <button @click="toggleStatus(product?.supplier?.user?.id)"
                                class="flex gap-2 items-center w-full px-4 py-2 text-right hover:bg-[#185D31] hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path x-show="product?.supplier?.user?.status === 'banned'" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75m-9 11.25H2.25a2.25 2.25 0 0 1-2.25-2.25v-10.5a2.25 2.25 0 0 1 2.25-2.25h11.25a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25Z" />
                                <path x-show="product?.supplier?.user?.status !== 'banned'" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            <span x-text="product?.supplier?.user?.status === 'banned' ? 'إلغاء الحظر' : 'حظر المورد'"></span>
                        </button>


                        <button @click="openModal('report')"
                                class="flex gap-2 items-center w-full px-4 py-2 text-right hover:bg-[#185D31] hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            الإبلاغ عن المورد
                        </button>

                        <button @click="openModal('delete')"
                                class="flex gap-2 items-center w-full px-4 py-2 text-right text-red-600 hover:bg-[#185D31] hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            حذف المحادثة
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
            <input type="text" x-model="searchTerm" placeholder="اكتب كلمة للبحث..." class="flex-1 p-2 border rounded">
            <button @click="isSearching=false; searchTerm=''" class="bg-gray-200 px-3 py-2 rounded">✖</button>
        </div>

        {{-- Chat box --}}
        <div class="flex-1 p-4 overflow-y-auto space-y-3" id="chat-box">
            <template x-if="!currentConversation">
                <div class="flex flex-1 w-full flex-col items-center justify-center text-[#696969] py-[20%]">
                    <img src="{{ asset('/images/Illustrations (5).svg') }}" alt="" class="mb-4 max-w-[200px]">
                    <p class="text-lg">ابدأ بالتواصل لعرض الرسائل هنا.</p>
                </div>
            </template>

            <template x-if="currentConversation">
                <div>
                    <div class="flex justify-end">
                        <div class="bg-gray-100 text-right text-gray-800 px-4 py-3 rounded-2xl shadow max-w-lg">
                            <p class="mb-3 font-medium">
                                أهلًا بك! 👋 تقدر تسأل عن المنتج، الشحن، الدفع أو أي تفاصيل مهمة بالنسبة لك.<br>
                                اختر من الأسئلة التالية أو اكتب سؤالك مباشرة:
                            </p>
                            <div class="space-y-2 mb-1">
                                <template x-for="qr in quickReplies" :key="qr.id">
                                    <button @click="sendQuickReply(qr)"
                                            class="w-full text-right bg-white border border-gray-300 px-4 py-2 rounded-xl hover:bg-[#185D31] transition">
                                        <span x-text="qr.text"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
<template x-for="msg in messages" :key="msg.id">
    <div class="flex items-end mb-3" :class="msg.sender_id === currentUserId ? 'justify-start' : 'justify-end'">

        <template x-if="msg.sender_id === currentUserId">
            <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : '/default.png' }}"
                 class="w-10 h-10 rounded-full ml-2">
        </template>

        <div class="max-w-[70%] p-2 mb-1 rounded-lg shadow"
             :class="msg.sender_id === currentUserId
                 ? 'bg-[#185D31] text-white text-left rounded-tl-none'
                 : 'bg-gray-200 text-black text-right rounded-tr-none'">
            <p x-html="highlightText(msg.message)" class="break-words"></p>
            <span class="text-xs block mt-1"
                  :class="msg.sender_id === currentUserId ? 'text-[#EDEDED]' : 'text-gray-600'"
                  x-text="formatDate(msg.created_at)"></span>
        </div>

        <template x-if="msg.sender_id !== currentUserId">
            <img :src="product?.supplier?.user?.profile_picture
                         ? '/storage/' + product.supplier.user.profile_picture
                         : '/default.png'"
                 class="w-10 h-10 rounded-full ml-2">
        </template>
    </div>
</template>
                </div>
            </template>
        </div>

        {{-- Input (only if conversation exists) --}}
<template x-if="currentConversation">
    <form @submit.prevent="sendMessage" class="p-4 flex flex-col border-t relative gap-2 bg-white">

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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.75c-.381.18-.814.285-1.25.285-.92 0-1.74-.484-2.22-1.21-.482-.725-.65-1.55-.49-2.317.16-.767.592-1.42 1.15-1.957.558-.538.995-.918 1.455-1.34.46-.422.84-.93 1.09-1.503.25-.573.34-1.21.25-1.85-.09-.64-.32-1.25-.67-1.76-.35-.51-.81-.92-1.35-1.22-.54-.3-1.12-.45-1.69-.45-.57 0-1.14.15-1.68.45-.54.3-.99.71-1.34 1.22-.35.51-.58.99-.67 1.55-.09.64-.09 1.28.09 1.95.18.67.5 1.3.89 1.83.39.53.64 1.14.73 1.8.09.66-.03 1.32-.34 1.93-.31.61-.79 1.14-1.37 1.59-1.34 1.02-2.92.21-3.69-.74-.77-.95-1.12-2.14-1.05-3.32.07-1.18.42-2.34 1.05-3.32.63-1.02 1.48-1.84 2.45-2.45.97-.61 2.06-.97 3.19-.97 1.13 0 2.22.36 3.19.97.97.61 1.82 1.43 2.45 2.45.63.98.98 2.14 1.05 3.32.07 1.18-.28 2.37-1.05 3.32-.77.95-2.35 1.76-3.69.74" />
                    </svg>
                </button>
                <input type="file" x-ref="attachmentInput" accept="image/*,video/*,application/pdf" class="hidden" @change="uploadAttachment($event, 'file')">

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
            <input type="text" x-model="newMessage" x-ref="textInput" placeholder="اكتب رسالتك"
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
</template>


    </div>

    {{-- Modal --}}
    <template x-if="modalType">
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-xl shadow-lg p-6 w-11/12 max-w-lg">
                <h2 class="text-xl font-bold mb-4 text-center">
                    <span x-show="modalType==='delete'">تأكيد حذف المحادثة؟</span>
                    <span x-show="modalType==='ban'">تأكيد حظر المورد؟</span>
                    <span x-show="modalType==='report'">سبب الإبلاغ عن المورد</span>
                </h2>

                <template x-if="modalType==='report'">
                    <textarea x-model="reportReason" class="w-full border rounded p-2" placeholder="اكتب السبب..."></textarea>
                </template>

                <div class="flex justify-center gap-4 mt-6">
                    <button @click="closeModal" class="px-4 py-2 bg-gray-300 rounded">إلغاء</button>
                    <button @click="confirmModal" class="px-4 py-2 bg-red-600 text-white rounded">تأكيد</button>
                </div>
            </div>
        </div>
    </template>

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

        init() {
            if (initialConversationId) {
                this.loadConversation(initialConversationId);
            } else if (this.conversations.length > 0) {
                // If no specific conversation is requested, load the first one
                this.loadConversation(this.conversations[0].id);
            }
        },

        startSearch() { this.isSearching = true; },

        highlightText(text) {
            if (!this.searchTerm) return text;
            const safe = this.searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            return text.replace(new RegExp(`(${safe})`, 'gi'), '<mark class="bg-yellow-300">$1</mark>');
        },

        openModal(type) { this.modalType = type; },
        closeModal() { this.modalType = null; this.reportReason = ''; },
        confirmModal() {
            if (this.modalType === 'delete') this.deleteConversation();
            else if (this.modalType === 'ban') this.toggleStatus(this.product?.supplier?.user?.id);
            else if (this.modalType === 'report') this.reportSupplier();
            this.closeModal();
        },

        toggleStatus(id) {
            if (!id) return;

            fetch(`/suppliers/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.product.supplier.user.status = data.status;

                    this.banMessage = data.status === 'banned' ? 'تم حظر المورد بنجاح' : 'تم إلغاء الحظر بنجاح';

                    setTimeout(() => this.banMessage = '', 3000);

                    console.log('User status updated to:', data.status);
                } else {
                    console.error('Toggle failed:', data);
                }
            })
            .catch(err => {
                console.error('Error:', err);
            });
        },
        reportSupplier() {
            if (!this.product) return;
            fetch(`/suppliers/${this.product.supplier_user_id}/report`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ reason: this.reportReason })
            }).then(() => console.log('تم إرسال البلاغ 🚨'));
        },

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

uploadAttachment(event, type) {
    let file = event.target.files[0];
    if (!file || !this.currentConversation) return;

    this.attachmentFile = file;
    this.attachmentName = file.name;

    let formData = new FormData();
    formData.append('attachment', file);
    formData.append('conversation_id', this.currentConversation);
    // Ensure `message` is an empty string if it's currently empty
    formData.append('message', this.newMessage || '');

    axios.post(`/messages/${this.currentConversation}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
    .then(res => {
        this.messages.push(res.data.message); // Updated to push the correct data
        this.attachmentFile = null;
        this.attachmentName = '';
        this.newMessage = '';
        if (this.$refs.attachmentInput) this.$refs.attachmentInput.value = '';
        if (this.$refs.cameraInput) this.$refs.cameraInput.value = '';
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
            if (this.$refs.cameraInput) this.$refs.cameraInput.value = '';
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
                    this.updateConversationPreviewFromMessages();
                    this.scrollToBottom();
                });
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

        sendMessage() {
            if (!this.newMessage || !this.currentConversation) return;
            const messageText = this.newMessage;

            const tempMessage = {
                id: 'local-' + Date.now(),
                message: messageText,
                sender_id: this.currentUserId,
                created_at: new Date().toISOString()
            };

            this.messages.push(tempMessage);
            this.newMessage = '';
            this.scrollToBottom();
            this.updateConversationPreviewFromMessages();

            fetch(`/messages/${this.currentConversation}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ message: messageText })
            })
            .then(res => res.json())
            .then(data => {
                const idx = this.messages.findIndex(m => m.id === tempMessage.id);
                if (idx !== -1 && data.message) this.messages.splice(idx, 1, data.message);
                this.scrollToBottom();
            })
            .catch(err => {
                console.error('Error sending message:', err);
                this.messages = this.messages.filter(m => m.id !== tempMessage.id);
                this.scrollToBottom();
            });
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = document.getElementById('chat-box');
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        formatDate(dt) {
            try { return new Date(dt).toLocaleString(); }
            catch { return dt; }
        }
    };
}
</script>

@endsection