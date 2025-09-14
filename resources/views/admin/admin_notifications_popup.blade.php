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
                       @if ($notification->type === App\Notifications\NewOrderReviewNotification::class)
     <div class="p-3 hover:bg-gray-50 border-b">
                <p class="text-sm text-gray-800">
                    {{ $notification->data['reviewer'] ?? 'مستخدم' }}
                    {{ __('قدم شكوى على الطلب رقم') }}
                    <span class="font-bold">#{{ $notification->data['order_id'] ?? 'N/A' }}</span>
                </p>

                @if(!empty($notification->data['comment']))
                    <p class="text-xs text-gray-500 mt-1">
                        "{{ Str::limit($notification->data['comment'], 50) }}"
                    </p>
                @endif

                <a href="{{ route('admin.reviews.show', $notification->data['review_id']) }}"
                   class="text-xs text-[#185D31] hover:underline mt-1 block">
                    {{ __('عرض التفاصيل') }}
                </a>
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
