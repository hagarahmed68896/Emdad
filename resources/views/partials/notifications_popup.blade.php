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
                                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                        <div class="w-11 h-11 flex items-center justify-center text-gray-500 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                 viewBox="0 0 24 24" stroke-width="1.5"
                                                 stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M14.857 17.082a23.848 23.848 0 0 0 
                                                         5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 
                                                         0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 
                                                         6.022c1.733.64 3.56 1.085 5.455 
                                                         1.31m5.714 0a24.255 24.255 0 0 
                                                         1-5.714 0m5.714 0a3 3 0 1 
                                                         1-5.714 0"/>
                                            </svg>
                                        </div>
                                        <p class="text-[16px] rtl:text-right ltr:text-left">
                                            {{ __('تم تحديث حالة طلبك رقم :order_id إلى :status', [
                                                'order_id' => $notification->data['order_id'],
                                                'status' => $notification->data['status'],
                                            ]) }}
                                        </p>
                                    </div>

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
            loadAllNotifications();
        });
    }

    // function loadAllNotifications() {
    //     fetch('{{ route("notifications.all") }}')
    //         .then(res => res.json())
    //         .then(data => {
    //             document.getElementById('notifications-content-area').innerHTML = data.html;
    //         });
    // }
</script>
@endauth
