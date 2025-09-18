@auth
<div 
    x-data="{ showNotificationPopup: false, buttonRect: null }" 
    x-init="$watch('showNotificationPopup', value => {
        if (value) {
            buttonRect = $el.querySelector('a').getBoundingClientRect();
        } else {
            buttonRect = null;
        }
    })" 
    class="relative inline-block">

    {{-- Notification Bell --}}
    <a href="#" @click.prevent="showNotificationPopup = !showNotificationPopup"
       class="relative w-[18px] h-[18px] z-10">
        <img src="{{ asset('images/interface-alert-alarm-bell-2--alert-bell-ring-notification-alarm--Streamline-Core.svg') }}"
             alt="Notification Icon">
        {{-- Notification Count Badge --}}
        @if ($unreadNotificationCount > 0)
            <span
                class="absolute -top-3 p-1 -right-4 bg-red-500 text-white rounded-full text-xs w-7 h-7 flex items-center justify-center">
                {{ $unreadNotificationCount }}
            </span>
        @endif
    </a>

    {{-- Notifications Popup --}}
<div x-show="showNotificationPopup" x-cloak 
     @click.away="showNotificationPopup = false"
     x-transition:enter="transition ease-out duration-300"
     class="bg-white shadow-lg rounded-lg p-4
            fixed top-[5%] left-0 w-[calc(100%-4rem)] max-w-[360px] z-20
            sm:absolute sm:top-full sm:mt-2 sm:w-[404px] sm:left-0 sm:mx-0
            max-h-[420px] overflow-y-auto">



        <h3 class="text-xl font-bold text-right text-gray-900 mb-4">{{ __('الإشعارات') }}</h3>

        <div id="notifications-content-area" class="w-full flex flex-col items-center">
            @if ($notifications->isEmpty())
                {{-- Empty state --}}
                <div class="flex flex-col justify-center items-center w-full py-10 text-gray-600">
                    <img src="{{ asset('images/Illustrations (3).svg') }}"
                         alt="No notifications illustration" class="w-[156px] h-[163px] mb-10 ">
                    <p class="text-[#696969] text-[20px] text-center">{{ __('لا توجد إشعارات حالياً') }}</p>
                </div>
            @else
                {{-- Notifications List --}}
                <div class="grid grid-cols-1 gap-2 w-full" id="notifications-grid">
                    @foreach ($notifications as $notification)
                        <div
                            class="p-3 rounded-lg border-b flex items-center justify-between
                            {{ $notification->read_at ? 'bg-white text-gray-600' : 'bg-[#F8F9FA] text-[#212121] font-medium' }}">

                            <div class="flex-grow rtl:pr-3 ltr:pl-3">
                                
                                {{-- Different notification types --}}
                       @if ($notification->type === App\Notifications\NewReviewNotification::class)
    <div class="flex items-start gap-3">
        {{-- Reviewer Avatar --}}
        <img src="{{ asset('storage/' . ($notification->data['reviewer_avatar'] ?? 'images/default_avatar.png')) }}"
             class="w-10 h-10 rounded-full object-cover"
             alt="Reviewer Avatar">

        <div>
            <p class="text-[16px] rtl:text-right ltr:text-left">
                <span class="font-bold">{{ __('إشعار جديد') }}: </span>
                {{ __(':name أضاف مراجعة جديدة على منتج :product_name', [
                    'name' => $notification->data['reviewer_name'] ?? 'مستخدم',
                    'product_name' => $notification->data['product_name'] ?? 'المنتج',
                ]) }}
            </p>
            <p class="text-sm text-gray-500 rtl:text-right ltr:text-left mt-1 truncate">
                "{{ Str::limit($notification->data['comment'] ?? '', 50) }}" -
                {{ $notification->data['rating'] ?? 0 }} / 5
            </p>
        </div>
    </div>


    @elseif($notification->type === App\Notifications\NewOrderNotification::class)
    <div class="flex items-start gap-3">
        {{-- Bill SVG --}}
    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                 viewBox="0 0 24 24" stroke-width="1.5"
                                                 stroke="currentColor" class="w-8 h-8 text-[#185D31] flex-shrink-0">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M14.857 17.082a23.848 23.848 0 0 0 
                                                         5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 
                                                         0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 
                                                         6.022c1.733.64 3.56 1.085 5.455 
                                                         1.31m5.714 0a24.255 24.255 0 0 
                                                         1-5.714 0m5.714 0a3 3 0 1 
                                                         1-5.714 0"/>
                                            </svg>

        <div>
@php
    // Ensure $data is an array
    $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);

    // Fallback: use order_number if exists, else pad order_id to 6 digits
    $orderNumber = isset($data['order_number']) 
        ? $data['order_number'] 
        : (isset($data['order_id']) ? str_pad($data['order_id'], 6, '0', STR_PAD_LEFT) : 'N/A');

    // Total amount (optional)
    $totalAmount = $data['order_total'] ?? null;
@endphp

<p class="text-[16px] rtl:text-right ltr:text-left">
    <span class="font-bold">{{ __('طلب جديد') }}: </span>
    {{ __('لديك طلب جديد برقم :order_number', ['order_number' => $orderNumber]) }}
    {{-- @if($totalAmount !== null)
        {{ __('بقيمة :total', ['total' => $totalAmount]) }}
    @endif --}}
</p>





        </div>
    </div>


@elseif ($notification->type === App\Notifications\OrderStatusUpdatedNotification::class)
<div
    x-data="reviewModal({{ $notification->data['order_id'] }}, @json($notification->data['products'] ?? []))"
    class="flex items-center space-x-3 rtl:space-x-reverse"
>
    <div class="w-11 h-11 flex items-center justify-center text-gray-500 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke-width="1.5"
             stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 
                  3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 
                  3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 
                  3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 
                  3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 
                  3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 
                  3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 
                  3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 
                  3.745 0 0 1 21 12Z" />
        </svg>
    </div>
    <div class="flex-1">
    <p class="text-[16px] rtl:text-right ltr:text-left">
        {{ __('تم تحديث حالة طلبك رقم :order_id إلى :status', [
            'order_id' => $notification->data['order_id'],
            'status' => $notification->data['status'],
        ]) }}
    </p>

    @if ($notification->data['status'] === 'delivered')
    <button 
        @click='
            fetch("{{ url('orders') }}/{{ $notification->data['order_id'] }}/products")
                .then(res => res.json())
                .then(data => {
                    products = data;   // update Alpine variable
                    open = true;       // ✅ open the modal
                })
        '
        class="px-3 py-1 mt-1 text-sm bg-[#185D31] text-white rounded-lg hover:bg-green-700">
        {{ __('تقييم الطلب') }}
    </button>
    @endif
    </div>
    <div 
        x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        x-cloak
    >
        <div @click.away="open = false"
             class="bg-white dark:bg-gray-800 w-full max-w-lg mx-4 rounded-xl shadow-lg p-6 rtl:text-right ltr:text-left space-y-4">

            <h2 class="text-lg font-semibold text-center">{{ __('كيف كانت تجربتك مع هذا الطلب؟') }}</h2>

            <div class="flex justify-center space-x-1 rtl:space-x-reverse">
                <template x-for="i in 5" :key="i">
                    <button type="button" @click="rating = i"
                            :class="i <= rating ? 'text-yellow-400' : 'text-gray-300'"
                            class="text-3xl">★</button>
                </template>
            </div>
            <p x-show="errors.rating" class="text-red-500 text-sm" x-text="errors.rating"></p>

            <div x-show="!successMessage">
                <div x-show="rating <= 3 && rating > 0" class="space-y-4">

                    <div>
                        <label class="block font-semibold mb-2">{{ __('هل واجهت مشكلة في المنتج أم في الطلب؟') }}</label>
                        <select x-model="issue_type" class="w-full border rounded-lg p-2">
                            <option value="">{{ __('حدد نوع المشكلة') }}</option>
                            <option value="product">{{ __('مشكلة في المنتج') }}</option>
                            <option value="order">{{ __('مشكلة في الطلب') }}</option>
                        </select>
                        <p x-show="errors.issue_type" class="text-red-500 text-sm" x-text="errors.issue_type"></p>
                    </div>

                    <template x-if="issue_type === 'product'">
                        <div>
                            <label class="block font-semibold mb-2">{{ __('حدد المنتج الذي واجهت فيه المشكلة') }}</label>
                            <select x-model="selected_product" class="w-full border rounded-lg p-2">
                                <option value="">{{ __('اختر المنتج') }}</option>
                                <template x-for="p in products" :key="p.id">
                                    <option :value="p.id" x-text="p.name"></option>
                                </template>
                            </select>
                        <p x-show="errors.product_id" class="text-red-500 text-sm" x-text="errors.product_id"></p>
                        </div>
                    </template>

                    <div>
                        <label class="block font-semibold mb-2">{{ __('ما المشكلة التي واجهتها؟') }}</label>
                        <textarea x-model="comment" rows="3"
                                 class="w-full border rounded-lg p-2"
                                 placeholder="{{ __('برجاء توضيح المشكلة التي واجهتها') }}"></textarea>
                        <p x-show="errors.comment" class="text-red-500 text-sm" x-text="errors.comment"></p>
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">{{ __('هل ترغب في تقديم شكوى حول هذه المشكلة؟') }}</label>
                        <select x-model="complaint" class="w-full border rounded-lg p-2">
                            <option value="">{{ __('اختر') }}</option>
                            <option value="yes">{{ __('نعم') }}</option>
                            <option value="no">{{ __('لا') }}</option>
                        </select>
                        <p x-show="errors.complaint" class="text-red-500 text-sm" x-text="errors.complaint"></p>
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="button" @click="submitReview()"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <span x-text="rating > 0 && rating <= 3 ? '{{ __('إرسال الشكوى') }}' : '{{ __('إرسال التقييم') }}'"></span>
                    </button>
                </div>
            </div>

            <div x-show="successMessage"
                 class="p-3 rounded-lg bg-green-100 text-green-700 text-center font-semibold">
                <span x-text="successMessage"></span>
            </div>
        </div>
    </div>
</div>



<script>
function reviewModal(orderId, initialProducts = []) {
    return {
        open: false,
        rating: 0,
        issue_type: '',
        selected_product: '',
        comment: '',
        complaint: '',
        products: initialProducts,
        errors: {},
        successMessage: '',

        async submitReview() {
            this.errors = {};

            try {
                const res = await fetch(`/reviews`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify({
                        product_id: this.selected_product || null,
                        rating: this.rating,
                        comment: this.comment,
                        issue_type: this.issue_type || null,
                        order_id: orderId || null,
                        issues: this.issue_type ? { type: this.issue_type, complaint: this.complaint } : null,

                    }),
                });

                if (res.status === 422) {
                    // Laravel validation errors
                    const errorData = await res.json();
                    this.errors = errorData.errors || {};
                    return;
                }

                const data = await res.json();

                if (data.success) {
                    if (this.rating <= 3) {
                        this.successMessage = "✅ تم إرسال الشكوى بنجاح";
                    } else {
                        this.successMessage = "✅ تم إرسال التقييم بنجاح";
                    }

                    setTimeout(() => {
                        this.open = false;
                        this.resetForm();
                    }, 2000);
                } else {
                    this.errors = data.errors || {};
                }
            } catch (e) {
                console.error("Submit failed", e);
            }
        },

        resetForm() {
            this.rating = 0;
            this.issue_type = '';
            this.selected_product = '';
            this.comment = '';
            this.complaint = '';
            this.errors = {};
            this.successMessage = '';
        }
    }
}
</script>


                                @elseif($notification->type === App\Notifications\NewOfferNotification::class)
                                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                        @if(isset($notification->data['product_image']))
                                            <img src="{{ Storage::url($notification->data['product_image']) }}"
                                                 class="w-12 h-12 object-cover rounded-md">
                                        @endif
                                        <div>
                                            <p class="text-[16px] rtl:text-right ltr:text-left">
                                                {{ __('تم إضافة عرض جديد على المنتج :product_name بخصم :discount%', [
                                                    'product_name' => $notification->data['product_name'] ?? 'المنتج',
                                                    'discount' => $notification->data['discount_percent'] ?? 0
                                                ]) }}
                                            </p>
                                            @if(isset($notification->data['url']))
                                                <a href="{{ $notification->data['url'] }}"
                                                   class="text-sm text-[#185D31] hover:underline block mt-1">
                                                    {{ __('عرض التفاصيل') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
@elseif ($notification->type === App\Notifications\AdminActionNotification::class)
    <div x-data="{ open: false }" class="border rounded-xl p-3 bg-gray-50">
        <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
            <p class="font-semibold text-gray-800">
                {{ data_get($notification->data, 'message', 'إجراء إداري') }}
            </p>
            <svg :class="{'rotate-180': open}" class="w-5 h-5 text-gray-600 transform transition-transform duration-200"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <div x-show="open" x-transition class="mt-3 space-y-2 text-sm text-gray-700">
            @if (data_get($notification->data, 'rating'))
                <p>⭐ التقييم: {{ $notification->data['rating'] }}</p>
            @endif

            @if (data_get($notification->data, 'comment'))
                <p>📝 التعليق: {{ $notification->data['comment'] }}</p>
            @endif

            @if (data_get($notification->data, 'issue_type') === 'order')
                <p>📦 رقم الطلب: {{ data_get($notification->data, 'order_number', '-') }}</p>
            @endif

            @if (data_get($notification->data, 'product_name'))
                <p>🛒 المنتج: {{ $notification->data['product_name'] }}</p>
            @endif
        </div>
    </div>
@elseif($notification->type === App\Notifications\AdminUserNotification::class)
  @php
    $data = $notification->data;

    $title = $data['title'] ?? 'بدون عنوان';
    $type  = $data['notification_type'] ?? 'info';
    $cat   = $data['category'] ?? 'غير محدد';
    $content = $data['content'] ?? 'بدون تفاصيل';
    
    // Default values for icon and display text
    $icon = '';
    $displayText = 'إشعار جديد';

    switch ($type) {
        case 'alert':
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" /></svg>';
            $displayText = "تنبيه";
            break;
        case 'offer':
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
</svg>
';
            $displayText = "عرض جديد";
            break;
        case 'info':
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
</svg>
';
            $displayText = "إشعار";
            break;
    }
@endphp

  <div x-data="{ open: false }" class="p-3 border rounded-lg">
    <button @click="open = !open"
            class="w-full text-left font-semibold flex justify-between items-center">
        
        <div class="flex items-center gap-2">
            <span class="inline-block">{!! $icon !!}</span>
            <span>{{ $displayText }}:</span>
            <span>{{ $title }}</span>
        </div>

        <svg :class="{'rotate-180': open}" class="w-5 h-5 text-gray-600 transform transition-transform duration-200"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7" />
        </svg>
    </button>
</div>




                               
                                    @else
                                    <p class="text-[16px] rtl:text-right ltr:text-left">
                                        <span class="font-bold">{{ __('إشعار جديد') }}: </span>
                                        {{ $notification->data['message'] ?? 'رسالة إشعار' }}
                                    </p>
                                @endif





                                

                                {{-- Time Ago --}}
                                <p class="text-xs text-gray-500 rtl:text-right ltr:text-left mt-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Mark as Read Button --}}
                            @if (!$notification->read_at)
                                <button type="button"
                                        onclick="markAsRead('{{ route('notifications.markAsRead', $notification->id) }}')"
                                        class="text-xs text-[#185D31] hover:underline flex-shrink-0 rtl:mr-2 ltr:ml-2">
                                    {{ __('قراءة') }}
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- "View All Notifications" Button --}}
                {{-- <div class="mt-6 text-center w-full">
                    <button type="button"
                            onclick="loadAllNotifications()"
                            class="mt-2 w-full px-[20px] py-[11px] bg-[#185D31] text-white rounded-[12px] text-[16px]">
                        {{ __('عرض كل الإشعارات') }}
                    </button>
                </div> --}}
            @endif
        </div>
    </div>
</div>

{{-- JS --}}
<script>
    function markAsRead(url) {
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(() => {
            // Force full page refresh
            window.location.reload();
        });
    }
</script>

@endauth
