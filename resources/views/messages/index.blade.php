@extends('layouts.app')

@section('content')

<div class="flex h-screen px-[64px]"
    x-data="chatApp({{ $conversations->toJson() }}, {{ $quickReplies->toJson() }}, {{ $openConversationId ?? 'null' }})">
    <div class="w-1/3 border-l border-gray-200 py-6 overflow-y-auto">
        <p class="mb-4 text-xxl font-bold flex gap-2">
           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
</svg>
الرسائل
        </p>

        <input type="text" placeholder="بحث"
            class="w-full p-2 border bg-[#EDEDED] rounded mb-4">

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
x-text="conv.last_message_text ?? ((conv.messages?.length ? conv.messages[conv.messages.length - 1]?.message : ''))">
</p>

                        </div>
                    </div>
                   <span class="text-xs text-gray-400"
x-text="formatDate(conv.updated_at || (conv.messages?.length ? conv.messages[conv.messages.length - 1]?.created_at : null))">
</span>

                </div>
            </template>
        </template>
        {{-- <p x-if="conversations.length === 0" class="text-gray-500 text-center mt-4">لا توجد محادثات حتى الآن.</p> --}}
    </div>

 <div class="w-2/3 flex flex-col relative">

    <!-- Header with product info -->
    <div class="p-4 border-b flex items-center justify-between relative"
        x-show="!isSearching && product" x-cloak>
        <div class="flex items-center gap-4">
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
            </div>
        </div>

        <!-- Dropdown menu -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="p-2 rounded-full hover:bg-gray-100">
                <!-- dots icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-7">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
            </button>

            <!-- dropdown items -->
            <div x-show="open" @click.away="open = false"
                 class="absolute left-0 mt-2 py-2 w-48 bg-white border rounded-lg shadow-lg z-50">
                <button @click="startSearch"
                        class="flex gap-1 w-full px-4 py-2 text-right hover:bg-[#185D31] hover:text-white">
                    🔍 بحث في المحادثة
                </button>
                <button @click="openModal('ban')"
                        class="flex gap-1 w-full px-4 py-2 text-right hover:bg-[#185D31] hover:text-white">
                    🚫 حظر المورد
                </button>
                <button @click="openModal('report')"
                        class="flex gap-1 w-full px-4 py-2 text-right hover:bg-[#185D31] hover:text-white">
                    ⚠️ الإبلاغ عن المورد
                </button>
                <button @click="openModal('delete')"
                        class="flex gap-1 w-full px-4 py-2 text-right text-red-600 hover:bg-[#185D31] hover:text-white">
                    🗑️ حذف المحادثة
                </button>
            </div>
        </div>
    </div>

    <!-- Search input -->
    <div x-show="isSearching" class="p-4 border-b flex items-center gap-2">
        <input type="text" x-model="searchTerm" placeholder="اكتب كلمة للبحث..."
               class="flex-1 p-2 border rounded">
        <button @click="isSearching=false; searchTerm=''"
                class="bg-gray-200 px-3 py-2 rounded">✖</button>
    </div>

    <!-- Chat box -->
    <div class="flex-1 p-4 overflow-y-auto space-y-3" id="chat-box">

        <!-- Welcome message + quick replies as first bot message -->
        <div class="flex justify-end">
            <div class="bg-gray-100 text-right text-gray-800 px-4 py-3 rounded-2xl shadow max-w-lg">
                <p class="mb-3 font-medium">
                    أهلًا بك! 👋 تقدر تسأل عن المنتج، الشحن، الدفع أو أي تفاصيل مهمة بالنسبة لك.<br>
                    اختر من الأسئلة التالية أو اكتب سؤالك مباشرة:
                </p>
                <div class="space-y-2">
                    <template x-for="qr in quickReplies" :key="qr.id">
                        <button @click="sendQuickReply(qr)"
                                class="w-full text-right bg-white border border-gray-300 px-4 py-2 rounded-xl hover:bg-[#185D31] transition">
                            <span x-text="qr.text"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <template x-for="msg in messages" :key="msg.id">
            <div class="flex items-end"
                 :class="msg.sender_id === currentUserId ? 'justify-start' : 'justify-end'">

                <!-- Current user avatar -->
                <template x-if="msg.sender_id === currentUserId">
                    <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : '/default.png' }}"
                         class="w-10 h-10 rounded-full ml-2">
                </template>

                <!-- Message bubble -->
                <div class="max-w-[70%] p-2 rounded-lg shadow"
                     :class="msg.sender_id === currentUserId
                         ? 'bg-[#185D31] text-white text-left rounded-tl-none'
                         : 'bg-gray-200 text-black text-right rounded-tr-none'">
                    <p x-html="highlightText(msg.message)" class="break-words"></p>
                    <span class="text-xs block mt-1"
                          :class="msg.sender_id === currentUserId ? 'text-[#EDEDED]' : 'text-gray-600'"
                          x-text="formatDate(msg.created_at)">
                    </span>
                </div>

                <!-- Supplier avatar -->
                <template x-if="msg.sender_id !== currentUserId">
            <img :src="product?.supplier?.user?.profile_picture 
              ? '{{ asset('storage') }}/' + product.supplier.user.profile_picture 
              : '/default.png'"
     class="w-10 h-10 rounded-full mx-2">

                </template>
            </div>
        </template>
    </div>

    <!-- Input box -->
    <form @submit.prevent="sendMessage" class="p-4 flex border-t">
        <input type="text" x-model="newMessage" placeholder="اكتب رسالتك"
               class="flex-1 p-2 border border-gray-300 rounded-l-none rounded-r-lg">
        <button type="submit"
                class="bg-[#185D31] text-white px-4 rounded-r-none rounded-l-lg">أرسل</button>
    </form>
</div>


    <template x-if="modalType">
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-xl shadow-lg p-6 w-96">
                <h2 class="text-xl font-bold mb-4 text-center">
                    <span x-show="modalType==='delete'">تأكيد حذف المحادثة؟</span>
                    <span x-show="modalType==='ban'">تأكيد حظر المورد؟</span>
                    <span x-show="modalType==='report'">سبب الإبلاغ عن المورد</span>
                </h2>

                <template x-if="modalType==='report'">
                    <textarea x-model="reportReason" class="w-full border rounded p-2" placeholder="اكتب السبب..."></textarea>
                </template>

                <div class="flex justify-center gap-4 mt-6">
                    <button @click="closeModal"
                        class="px-4 py-2 bg-gray-300 rounded">إلغاء</button>
                    <button @click="confirmModal"
                        class="px-4 py-2 bg-red-600 text-white rounded">تأكيد</button>
                </div>
            </div>
        </div>
    </template>

</div>

<script>
function chatApp(initialConversations, initialQuickReplies, initialConversationId = null) {
    return {
        conversations: initialConversations,
        quickReplies: initialQuickReplies,
        currentConversation: null,
        messages: [],
        product: null,
        newMessage: '',
        currentUserId: @json(auth()->id()),
        searchTerm: '',
        isSearching: false,
        modalType: null,
        reportReason: '',

        init() {
            if (initialConversationId) {
                this.loadConversation(initialConversationId);
            }
        },

        // New function to add a supplier's reply locally
        addSupplierReply(answer) {
            const supplierReply = {
                id: 'local-' + Date.now(), // Use a temporary local ID
                message: answer,
                sender_id: this.product?.supplier_user_id ?? -1,
                created_at: new Date().toISOString()
            };
            this.messages.push(supplierReply);
            this.updateConversationPreviewFromMessages();
            this.scrollToBottom();
        },

        // This method will now correctly send the user's message and then add the supplier's answer
// This method will now correctly send the user's message and then add the supplier's answer
sendQuickReply(qr) {
    if (!qr || !this.currentConversation) return;

    // We only need to send one request to the backend.
    // The backend will handle the creation of both the user's message and the reply.
    fetch(`/messages/${this.currentConversation}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            message: qr.text,
            quick_reply_id: qr.id, // Send the quick reply ID
        })
    })
    .then(res => res.json())
    .then(data => {
        // The backend returns the full, updated list of messages.
        this.messages = data.messages;
        this.updateConversationPreviewFromMessages();
        this.scrollToBottom();
    })
    .catch(err => {
        console.error('Error sending quick reply message:', err);
    });
}, 

startSearch() {
            this.isSearching = true;
        },

        highlightText(text) {
            if (!this.searchTerm) return text;
            let regex = new RegExp(`(${this.searchTerm})`, "gi");
            return text.replace(regex, '<mark class="bg-yellow-300">$1</mark>');
        },

        openModal(type) {
            this.modalType = type;
        },

        closeModal() {
            this.modalType = null;
            this.reportReason = '';
        },

        confirmModal() {
            if (this.modalType === 'delete') {
                this.deleteConversation();
            } else if (this.modalType === 'ban') {
                this.banSupplier();
            } else if (this.modalType === 'report') {
                this.reportSupplier();
            }
            this.closeModal();
        },

        banSupplier() {
            if (!this.product) return;
            fetch(`/suppliers/${this.product.supplier_user_id}/ban`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => {
                console.log("تم حظر المورد 🚫");
            });
        },

        reportSupplier() {
            if (!this.product) return;
            fetch(`/suppliers/${this.product.supplier_user_id}/report`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ reason: this.reportReason })
            }).then(() => {
                console.log("تم إرسال البلاغ 🚨");
            });
        },

        deleteConversation() {
            if (!this.currentConversation) return;
            fetch(`/conversations/${this.currentConversation}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => {
                this.conversations = this.conversations.filter(c => c.id !== this.currentConversation);
                this.currentConversation = null;
                this.messages = [];
                this.product = null;
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

        // This method now returns the fetch Promise, which is crucial for async flow
        sendMessage() {
            if (!this.newMessage || !this.currentConversation) return Promise.resolve();

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

            return fetch(`/messages/${this.currentConversation}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: messageText })
                })
                .then(res => res.json())
                .then(data => {
                    // Update the local message with the real one from the server
                    const idx = this.messages.findIndex(m => m.id === tempMessage.id);
                    if (idx !== -1 && data.message) {
                        this.messages.splice(idx, 1, data.message);
                    }
                    this.scrollToBottom();
                })
                .catch(err => {
                    console.error('Error sending message:', err);
                    // On error, remove the temporary message
                    this.messages = this.messages.filter(m => m.id !== tempMessage.id);
                    this.scrollToBottom();
                    throw err; // Re-throw the error to be caught by the sendQuickReply
                });
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                const el = document.getElementById('chat-box');
                if (el) {
                    el.scrollTop = el.scrollHeight;
                }
            });
        },

        formatDate(dt) {
            try {
                return new Date(dt).toLocaleString();
            } catch(e) {
                return dt;
            }
        }
    };
}
</script>

@endsection