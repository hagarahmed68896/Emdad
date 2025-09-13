@extends('layouts.admin')

@section('page_title', 'المحادثات')

@section('content')

<div class="p-6 overflow-y-auto">

    <p class="text-[32px] font-bold mb-4">المحادثات</p>

    {{-- MAIN ALPINE.JS SCOPE --}}
    <div x-data="tableScope()" class="rounded-xl shadow mx-2">

        <div class="bg-white p-6 rounded-xl">

            {{-- ✅ Bulk Action Bar --}}
            <div x-show="selectedConversations.length > 0" class="flex flex-col md:flex-row items-center justify-between mb-4 gap-4 md:gap-0">
                <span class="text-xl font-bold text-gray-800" x-text="selectedConversations.length + ' محدد'"></span>

                {{-- Conditional button based on selection --}}
                <button  x-cloak
                    x-show="isReportedConversationSelected" 
                    @click="openModal(selectedConversations[0])" 
                    class="px-4 py-2 rounded-xl bg-[#185D31] hover:bg-green-800 text-white flex items-center gap-2"
                >
                    <i class="fas fa-exclamation-triangle"></i>
                    مراجعة البلاغ
                </button>
            </div>

            {{-- ✅ Filter/Search Bar --}}
      <div x-show="selectedConversations.length === 0"
     class="flex flex-col md:flex-row w-full items-center justify-between mb-4 space-y-4 md:space-y-0">

    <form action="{{ route('admin.messages.index') }}" method="GET"
          class="flex flex-col md:flex-row md:items-center gap-4 w-full md:flex-1">

        <div x-data="{
            open: false,
            selectedSort: '{{ request('sort_option') ?? '' }}',
            selectedType: '{{ request('document_name') ?? '' }}'
        }" class="relative inline-block text-left">
            <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
                 class="cursor-pointer w-6 h-6" @click="open = !open" alt="Filter Icon">

            <div x-show="open" @click.away="open = false" x-transition.opacity x-cloak
                 class="absolute mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow z-50 p-4 rtl:right-0 md:rtl:left-0">

                <h3 class="font-bold text-gray-700 mb-2 rtl:text-right">الترتيب حسب:</h3>
                <ul class="space-y-1 mb-4">
                    <li>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sort_option" value="" x-model="selectedSort"
                                   class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                            <span class="text-gray-700">الكل</span>
                        </label>
                    </li>
                    <li>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sort_option" value="name_asc" x-model="selectedSort"
                                   class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                            <span class="text-gray-700">الاسم أ → ي</span>
                        </label>
                    </li>
                    <li>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sort_option" value="name_desc" x-model="selectedSort"
                                   class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                            <span class="text-gray-700">الاسم ي → أ</span>
                        </label>
                    </li>
                    <li>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sort_option" value="oldest" x-model="selectedSort"
                                   class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                            <span class="text-gray-700">الأقدم أولاً</span>
                        </label>
                    </li>
                    <li>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sort_option" value="latest" x-model="selectedSort"
                                   class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                            <span class="text-gray-700">الأحدث أولاً</span>
                        </label>
                    </li>
                </ul>

                <div class="flex justify-end gap-2">
                    <button type="submit" @click="open = false"
                            class="px-4 py-2 rounded-xl bg-[#185D31] text-white hover:bg-green-800">
                        تطبيق
                    </button>
                    <button type="button"
                            @click="selectedSort = ''; $el.closest('form').submit(); open = false;"
                            class="px-4 py-2 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300">
                        إعادة تعيين
                    </button>
                </div>
            </div>
            
            <input type="hidden" name="sort_option" :value="selectedSort">
            <input type="hidden" name="document_name" :value="selectedType">
        </div>

        <div class="relative max-w-md  flex-1">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>

            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="بحث باسم العميل أو المورد"
                   class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500">
            
            <button type="submit"
                    class="absolute inset-y-0 left-0 flex items-center px-4 text-white bg-[#185D31] hover:bg-green-800 rounded-l-xl">
                بحث
            </button>
        </div>
    </form>

    <div x-data="{ open: false }" class="relative inline-block text-left w-full md:w-auto md:flex-none">
        @php
            $status = request('status');
            $statusName = match ($status) {
                'open' => 'نشطة', 'reported' => 'تم الإبلاغ', 'closed' => 'مغلقة', default => 'كل الحالات',
            };
        @endphp
        <button type="button" @click="open = !open" class="bg-[#185D31] hover:bg-green-800 text-white py-2 px-4 rounded-xl flex items-center justify-center gap-2 w-full">
            <span>{{ $statusName }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 rtl:mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </button>
        <div x-show="open" @click.away="open = false" x-cloak 
             class="absolute mt-2 w-48 bg-white rtl:left-0 border border-gray-200 rounded-xl shadow z-50">
            <a href="{{ route('admin.messages.index', array_merge(request()->query(), ['status' => null])) }}" class="block px-4 py-2 text-center {{ $status == null ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">كل الحالات</a>
            <a href="{{ route('admin.messages.index', array_merge(request()->query(), ['status' => 'open'])) }}" class="block px-4 py-2 text-center {{ $status == 'open' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">نشطة</a>
            <a href="{{ route('admin.messages.index', array_merge(request()->query(), ['status' => 'reported'])) }}" class="block px-4 py-2 text-center {{ $status == 'reported' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">تم الإبلاغ</a>
            <a href="{{ route('admin.messages.index', array_merge(request()->query(), ['status' => 'closed'])) }}" class="block px-4 py-2 text-center {{ $status == 'closed' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">مغلقة</a>
        </div>
    </div>
</div>

            {{-- ✅ جدول المحادثات --}}
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th  class="px-4 py-2"  >
<input type="checkbox"                      class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded" x-model="selectAll" @click="toggleSelectAll"></th>
                        <th class="px-4 py-2">اسم العميل</th>
                        <th class="px-4 py-2">اسم المورد</th>
                        <th class="px-4 py-2">آخر رسالة</th>
                        <th class="px-4 py-2">الحالة</th>
                        <th class="px-4 py-2">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @foreach($conversations as $conv)
                    <tr>
                        <td class="px-4 py-2">
                            <input type="checkbox"                class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded"

                             value="{{ $conv->id }}" x-model="selectedConversations">
                        </td>
                        <td class="px-4 py-3">{{ $conv->user->full_name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $conv->product->supplier->user->full_name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ optional($conv->messages->last())->message ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($conv->status === 'reported')
                                <span class="text-[#B26404] bg-[#FFF3B3] py-1 px-3 rounded-full ">تم الإبلاغ</span>
                            @elseif($conv->status === 'open')
                                <span class="text-[#007405] bg-[#D4EDDA] py-1 px-3 rounded-full">نشطة</span>
                            @elseif($conv->status === 'closed')
                                <span class="text-[#C62525] bg-[#FAE1DF] py-1 px-3 rounded-full">مغلقة</span>
                            @else
                                <span class="text-[#696969] bg-[#EDEDED] py-1 px-3 rounded-full">قيد المراجعة</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $conv->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

{{-- Pagination Bottom --}}
<div class="flex justify-between items-center mt-4">
    <div class="text-gray-700">
        عرض
        <span class="font-semibold">{{ $conversations->firstItem() }}</span>
        -
        <span class="font-semibold">{{ $conversations->lastItem() }}</span>
        من
        <span class="font-semibold">{{ $conversations->total() }}</span>
        محادثة
    </div>
    <div>
        {{ $conversations->links('pagination::tailwind') }}
    </div>
</div>        </div>

        {{-- ✅ Report Review Modal --}}
        <div x-show="openModalFlag" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 p-4">
            <div @click.away="closeModal()" class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md md:max-w-2xl relative overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">مراجعة البلاغ</h2>
                    <button @click="closeModal()" class="text-gray-600 hover:text-gray-900 text-3xl font-bold">&times;</button>
                </div>

                <div x-show="selectedReportedConversation" class="space-y-4">
                    <p class="text-sm text-gray-600 bg-gray-100 p-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-info-circle  text-blue-500"></i>
                        هذه المحادثة للعرض فقط. يمكنك مراجعة الرسائل دون إمكانية التعديل
                    </p>
                    <div class="flex items-center gap-4 border-b pb-4">
                        <div>
                            <h4 class="text-lg font-semibold">العميل: <span x-text="selectedReportedConversation.user.full_name"></span></h4>
                            <h4 class="text-lg font-semibold">المورد: <span x-text="selectedReportedConversation.product.supplier.user.full_name"></span></h4>
                        <h4 class="text-lg font-semibold">
    تاريخ البلاغ: 
    <span x-text="selectedReportedConversation.reports?.[0]?.created_at 
        ? new Date(selectedReportedConversation.reports[0].created_at).toLocaleDateString() 
        : '-'"></span>
</h4>

<h4 class="text-lg font-semibold">
    سبب البلاغ: 
    <span x-text="selectedReportedConversation.reports?.[0] 
        ? selectedReportedConversation.reports[0].report_type + ': ' + selectedReportedConversation.reports[0].reason 
        : '-'"></span>
</h4>
</div>
                    </div>

<div class="max-h-96 overflow-y-auto p-4 bg-gray-100 rounded-lg flex flex-col-reverse">
    <template x-for="message in selectedReportedConversation.messages" :key="message.id">
        <div class="flex items-end mb-2" :class="message.sender_id === selectedReportedConversation.user_id ? 'justify-start' : 'justify-end'">
            
            <!-- Left side: sender -->
            <template x-if="message.sender_id === selectedReportedConversation.user_id">
                <div class="flex  gap-2">
                    <img :src="message.sender.profile_picture ? '/storage/' + message.sender.profile_picture : '/images/default.png'" 
                         class="w-8 h-8 rounded-full">
                    <div class="p-3 rounded-xl max-w-[70%] bg-gray-200 text-gray-800 break-words">
                        <p x-text="message.message"></p>
                        <span class="text-xs block mt-1 text-gray-500" x-text="new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                    </div>
                </div>
            </template>

            <!-- Right side: receiver -->
            <template x-if="message.sender_id !== selectedReportedConversation.user_id">
                <div class="flex items-end gap-2">
                    <div class="p-3 rounded-xl max-w-[70%] bg-[#185D31] text-white break-words">
                        <p x-text="message.message"></p>
                        <span class="text-xs block mt-1 text-gray-300" x-text="new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                    </div>
                    <img :src="message.sender.profile_picture ? '/storage/' + message.sender.profile_picture : '/images/default.png'" 
                         class="w-8 h-8 rounded-full">
                </div>
            </template>

        </div>
    </template>
</div>





<p>يرجى اختيار الإجراء المناسب بناءً على مراجعتك للمحادثة.</p>
<div class="flex flex-col md:flex-row justify-center gap-4 w-full">

    {{-- Close Conversation Button --}}
    <div x-data="closeConversationModal()" class="w-full md:w-1/2">
        <button 
            @click="open({id: 1, client_id: 2, supplier_id: 3, status: 'open'})"
            class="w-full py-3 text-white font-semibold rounded-xl bg-[#185D31] hover:bg-green-800 transition-colors duration-200 shadow-md flex items-center justify-center gap-2">
            إغلاق المحادثة
        </button>

        {{-- Modal --}}
        <div x-show="isOpen" x-cloak
             class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 p-4">
            <div @click.away="close()" class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md md:max-w-lg relative overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-center w-full">إغلاق المحادثة</h2>
                    <button @click="close()" class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 text-3xl font-bold">&times;</button>
                </div>
                <form @submit.prevent="submitForm" class="space-y-6">
                    {{-- Target Category --}}
                    <div>
                        <label class="block font-semibold mb-2">الفئة المستهدفة</label>
                        <div class="border rounded-xl overflow-hidden">
                            <label class="flex items-center justify-between px-4 py-2 cursor-pointer hover:bg-gray-100">
                                <span>عميل</span>
                                <input type="radio" name="target" value="client" x-model="form.target">
                            </label>
                            <label class="flex items-center justify-between px-4 py-2 cursor-pointer border-t hover:bg-gray-100">
                                <span>المورد</span>
                                <input type="radio" name="target" value="supplier" x-model="form.target">
                            </label>
                        </div>
                    </div>
<div x-show="message" class="mt-2 text-sm text-green-600" x-text="message"></div>

                    {{-- Action --}}
                    <div>
                        <label class="block font-semibold mb-2">الإجراء</label>
                        <div class="border rounded-xl overflow-hidden">
                            <label class="flex items-center justify-between px-4 py-2 cursor-pointer hover:bg-gray-100">
                                <span>إغلاق المحادثة فقط</span>
                                <input type="radio" name="action" value="close" x-model="form.action">
                            </label>
                            <label class="flex items-center justify-between px-4 py-2 cursor-pointer border-t hover:bg-gray-100">
                                <span>إغلاق المحادثة + تعليق الرسائل لمدة 3 أيام</span>
                                <input type="radio" name="action" value="under_review" x-model="form.action">
                            </label>
                            <label class="flex items-center justify-between px-4 py-2 cursor-pointer border-t hover:bg-gray-100">
                                <span>تحذير المستخدم فقط دون إغلاق المحادثة</span>
                                <input type="radio" name="action" value="warn_only" x-model="form.action">
                            </label>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block font-semibold mb-2">ملاحظات</label>
                        <textarea x-model="form.notes" placeholder="أدخل الملاحظات" class="w-full border border-gray-300 rounded-xl p-3 resize-none"></textarea>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="w-full bg-[#185D31] hover:bg-green-800 text-white font-semibold py-3 rounded-xl transition-all duration-200 shadow-md">
                        إرسال
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Review Button --}}
 <!-- Button -->
<form action="{{ route('admin.conversations.updateStatus', $conv->id) }}" method="POST" class="w-full md:w-1/2">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" value="under_review">
    <button type="submit"
        class="w-full text-center py-3 font-semibold rounded-xl bg-[#EDEDED] text-[#185D31] hover:bg-gray-300 hover:text-[#14532d] transition-all duration-200 shadow-md flex items-center justify-center gap-2">
        قيد المراجعة
    </button>
</form>


</div>

<script>
function closeConversationModal() {
    return {
        isOpen: false,
        conversation: null,
        message: '', // <-- new message property
        form: {
            target: 'client',
            action: '',
            notes: ''
        },

        open(conv) {
            this.conversation = conv;
            this.isOpen = true;
            this.form = { target: 'client', action: '', notes: '' };
            this.message = ''; // clear message on open
        },

        close() {
            this.isOpen = false;
            this.conversation = null;
            this.message = '';
        },

        async submitForm() {
            if (!this.form.action) {
                this.message = 'الرجاء اختيار الإجراء';
                return;
            }

            try {
                const res = await fetch(`/admin/conversations/${this.conversation.id}/action`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        target: this.form.target,
                        action: this.form.action,
                        notes: this.form.notes
                    })
                });

                const data = await res.json();

                if (data.success) {
                    // Update locally
                    this.conversation.status = this.form.action === 'close' ? 'closed' : this.form.action === 'under_review' ? 'under_review' : this.conversation.status;
                    this.message = 'تم تنفيذ الإجراء بنجاح';
                } else {
                    this.message = 'حدث خطأ، حاول مرة أخرى';
                }
            } catch (err) {
                console.error(err);
                this.message = 'حدث خطأ، حاول مرة أخرى';
            }
        }
    }
}
</script>



                </div>
            </div>
        </div>
    </div>
</div>

<script>
function tableScope() {
    return {
        selectedConversations: [],
        conversations: @json($conversations->items()),
        conversationsOnPage: @json($conversations->pluck('id')),
        selectAll: false,
        isReportedConversationSelected: false,

        // modal state
        openModalFlag: false,
        selectedReportedConversation: null,

        init() {
            this.$watch('selectedConversations', () => {
                const id = this.selectedConversations[0];
                const conv = this.conversations.find(c => c.id == id);
                this.isReportedConversationSelected = this.selectedConversations.length === 1 && conv?.status === 'reported';
            });
        },

        toggleSelectAll() {
            this.selectedConversations = this.selectAll ? [...this.conversationsOnPage] : [];
        },

        openModal(id) {
            this.selectedReportedConversation = this.conversations.find(c => c.id == id);
            this.openModalFlag = true;
        },

        closeModal() {
            this.openModalFlag = false;
            this.selectedReportedConversation = null;
        }
    }
}
</script>

@endsection
