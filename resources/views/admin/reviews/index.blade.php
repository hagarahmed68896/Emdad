@extends('layouts.admin')

@section('page_title', __('messages.review_ratings'))

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="p-6 overflow-y-auto">

    <p class="text-[32px] font-bold">{{ __('messages.review_ratings') }}</p>

    <div>
        @include('admin.total_reviews')
    </div>

    {{-- The single Alpine.js data scope --}}
    <div x-data="{
        selectedReviews: [],
        selectAll: false,
        reviewsOnPage: @json($reviews->pluck('id')),
        reviewsData: {{ json_encode(
            $reviews->map(fn($r) => [
                'id' => $r->id,
                'is_complaint' => $r->is_complaint,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'issue_type' => $r->issue_type,
                'status' => $r->status,
                'review_date' => $r->review_date?->format('Y-m-d') ?? null,
                'order' => $r->order ? [
                    'total_amount' => $r->order->total_amount,
                    'order_number' => $r->order->order_number,
                    'items' => $r->order->orderItems->map(fn($item) => [
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'product' => [
                            'name' => $item->product?->name ?? null,
                            'image' => $item->product?->image ? asset('storage/' . $item->product->image) : null,
                        ],
                    ]),
                ] : null,
                'user' => [
                    'full_name' => $r->user?->full_name ?? null,
                ],
                'product' => [
                    'name' => $r->product?->name ?? null,
                    'image' => $r->product?->image ? asset('storage/' . $r->product->image) : null,
                ],
            ]),
            JSON_HEX_APOS | JSON_HEX_QUOT
        ) }},

        showComplainModal: false,
        showTakeActionModal: false,
        currentReview: null,
        form: {
            action: '',
            notes: ''
        },
        message: '',

        init() {
            this.$watch('selectedReviews', () => {
                this.selectAll = this.selectedReviews.length === this.reviewsOnPage.length && this.reviewsOnPage.length > 0;
            });
        },

        toggleSelectAll() {
            this.selectedReviews = this.selectAll ? [...this.reviewsOnPage] : [];
        },

        openComplainModal(review) {
            this.currentReview = review;
            this.showComplainModal = true;
        },

        closeComplaint() {
            if (!this.currentReview) return;
            console.log('closeComplaint triggered');

            fetch(`/admin/reviews/${this.currentReview.id}/close`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.currentReview.status = 'rejected';
                    this.closeComplainModal();
                } else {
                    alert('{{ __("messages.error_try_again") }}');
                }
            })
            .catch(err => {
                console.error(err);
                alert('{{ __("messages.error_try_again") }}');
            });
        },

        closeComplainModal() {
            this.showComplainModal = false;
            this.currentReview = null;
        },

        hasComplaintSelected() {
            return this.selectedReviews.some(id => this.reviewsData.find(r => r.id == id)?.is_complaint == 1);
        },

        submitActionForm() {
            fetch(`/admin/reviews/${this.currentReview.id}/action`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(this.form)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.message = '{{ __("messages.action_success") }}';
                    this.form.action = '';
                    this.form.notes = '';
                    this.showTakeActionModal = false;
                    this.closeComplainModal();
                } else {
                    alert('{{ __("messages.error_try_again") }}');
                }
            })
            .catch(err => {
                console.error(err);
                alert('{{ __("messages.error_try_again") }}');
            });
        }
    }" class="bg-white p-3 rounded-xl">

        {{-- ✅ Action bar when selected --}}
        <div x-show="selectedReviews.length > 0" class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-gray-800" x-text="selectedReviews.length + ' {{ __('messages.selected') }}'"></span>

                {{-- Conditional button based on selection --}}
                <button x-show="hasComplaintSelected()"
                        @click="openComplainModal(reviewsData.find(r => r.id == selectedReviews[0]))"
                        class="px-4 py-2 rounded-xl bg-[#185D31] hover:bg-green-800 text-white flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ __('messages.review_complaint') }}
                </button>
            </div>
        </div>


      {{-- ✅ Filter and Search Bar --}}
<div x-show="selectedReviews.length === 0" x-cloak
     class="flex mt-2 mx-auto flex-col md:flex-row items-center justify-between mb-4 space-y-4 md:space-y-0">
    <form id="filterForm" action="{{ route('admin.reviews.index') }}" method="GET"
          class="flex flex-col md:flex-row md:items-center gap-4 w-full max-w-xl">
        <input type="hidden" name="ratingFilter" id="ratingFilter" value="{{ $ratingFilter ?? '' }}">
        <input type="hidden" name="sort" id="sortFilter" value="{{ $sortFilter ?? '' }}">

        <div x-data="{ open: false, selectedStatus: '{{ $ratingFilter ?? '' }}', selectedSort: '{{ $sortFilter ?? '' }}' }"
             class="relative inline-block text-left">
            <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
                 class="cursor-pointer w-6 h-6" @click="open = !open" alt="{{ __('messages.filter_icon') }}">
            <div x-show="open" @click.away="open = false" x-transition.opacity x-cloak
                 class="absolute mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow z-50 p-4 right-0 md:left-0">
                <h3 class="font-bold text-gray-700 mb-2 rtl:text-right">{{ __('messages.sort_by') }}:</h3>
                <ul class="space-y-1 mb-4">
                    <li><label class="flex items-center cursor-pointer">
                        <input type="radio" value="" x-model="selectedSort"
                               class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                        <span class="ml-2 text-gray-700">{{ __('messages.all') }}</span>
                    </label></li>
                    <li><label class="flex items-center cursor-pointer">
                        <input type="radio" value="latest" x-model="selectedSort"
                               class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                        <span class="ml-2 text-gray-700">{{ __('messages.latest') }}</span>
                    </label></li>
                    <li><label class="flex items-center cursor-pointer">
                        <input type="radio" value="oldest" x-model="selectedSort"
                               class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                        <span class="ml-2 text-gray-700">{{ __('messages.oldest') }}</span>
                    </label></li>
                </ul>

                <h3 class="font-bold text-gray-700 mb-2 rtl:text-right">{{ __('messages.rating') }}:</h3>
                <ul class="space-y-1 mb-4">
                    <li><label class="flex items-center cursor-pointer">
                        <input type="radio" value="" x-model="selectedStatus"
                               class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                        <span class="ml-2 text-gray-700">{{ __('messages.all') }}</span>
                    </label></li>
                    @for ($i = 5; $i >= 1; $i--)
                    <li><label class="flex items-center cursor-pointer">
                        <input type="radio" value="{{ $i }}" x-model="selectedStatus"
                               class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                        <span class="ml-2 text-yellow-400">
                            @for ($j = 1; $j <= $i; $j++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </span>
                    </label></li>
                    @endfor
                </ul>
                <div class="flex justify-center gap-2">
                    <button type="submit"
                            x-on:click="$event.preventDefault(); document.getElementById('ratingFilter').value = selectedStatus; document.getElementById('sortFilter').value = selectedSort; document.getElementById('filterForm').submit(); open = false;"
                            class="px-4 py-2 rounded-xl bg-[#185D31] text-white hover:bg-green-800">
                        {{ __('messages.apply') }}
                    </button>
                    <button type="button"
                            x-on:click="$event.preventDefault(); selectedStatus = ''; selectedSort = ''; document.getElementById('ratingFilter').value = ''; document.getElementById('sortFilter').value = ''; document.getElementById('filterForm').submit(); open = false;"
                            class="px-4 py-2 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300">
                        {{ __('messages.reset') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="relative w-full">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('messages.search_reviews') }}"
                   class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-xl focus:outline-none">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>
            <button type="submit"
                    class="absolute inset-y-0 left-0 flex items-center px-3 text-white bg-[#185D31] rounded-l-xl">
                {{ __('messages.search') }}
            </button>
        </div>
    </form>

    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.reviews.export.csv', array_merge(request()->query(), ['ratingFilter' => $ratingFilter ?? '', 'sort' => $sortFilter ?? '', 'search' => $search ?? ''])) }}"
           class="bg-gray-100 mx-2 hover:bg-gray-300 text-[#185D31] py-2 px-4 rounded-xl flex items-center">
            <i class="fas fa-download ml-2"></i>
            <span>{{ __('messages.download') }}</span>
        </a>

        <div x-data="{ open: false }" class="relative inline-block text-left">
            @php
                $ratingName = match ($ratingFilter ?? '') {
                    'positive' => __('messages.positive_reviews'),
                    'negative' => __('messages.negative_reviews'),
                    'complain' => __('messages.complaints'),
                    default     => __('messages.all_reviews'),
                };
            @endphp
            <button @click="open = !open"
                    class="bg-[#185D31] hover:bg-green-800 text-white py-2 px-4 rounded-xl flex items-center">
                <span>{{ $ratingName }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak
                 class="absolute mt-2 w-48 bg-white left-0 border border-gray-200 rounded-xl shadow z-50">
                <a href="#" x-on:click="$event.preventDefault(); document.getElementById('ratingFilter').value = ''; document.getElementById('filterForm').submit();"
                   class="block px-4 py-2 text-center {{ ($ratingFilter ?? '') == '' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">{{ __('messages.all_reviews') }}</a>
                <a href="#" x-on:click="$event.preventDefault(); document.getElementById('ratingFilter').value = 'positive'; document.getElementById('filterForm').submit();"
                   class="block px-4 py-2 text-center {{ $ratingFilter == 'positive' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">{{ __('messages.positive_reviews') }}</a>
                <a href="#" x-on:click="$event.preventDefault(); document.getElementById('ratingFilter').value = 'complain'; document.getElementById('filterForm').submit();"
                   class="block px-4 py-2 text-center {{ $ratingFilter == 'complain' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">{{ __('messages.complaints') }}</a>
                <a href="#" x-on:click="$event.preventDefault(); document.getElementById('ratingFilter').value = 'negative'; document.getElementById('filterForm').submit();"
                   class="block px-4 py-2 text-center {{ $ratingFilter == 'negative' ? 'bg-[#185D31] text-white' : 'text-gray-700 hover:bg-[#185D31] hover:text-white' }}">{{ __('messages.negative_reviews') }}</a>
            </div>
        </div>
    </div>
</div>


        {{-- Reviews Table --}}
        @include('admin.reviews.reviews_table')

  {{-- Complaint Modal --}}
<div x-show="showComplainModal" x-cloak
     @click.outside="closeComplainModal()"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 relative">
        <button @click="closeComplainModal()"
                class="absolute top-3 right-3 mb-4 text-gray-500 hover:text-gray-800 text-xl font-bold">
            ✕
        </button>
        <h2 class="text-xl text-center font-bold mb-4">{{ __('messages.review_complaint') }}</h2>

        <div class="space-y-2">
            <p x-show="currentReview?.user">
                <strong>{{ __('messages.customer_name') }}:</strong> <span x-text="currentReview?.user?.full_name"></span>
            </p>
            <p x-show="currentReview?.order">
                <strong>{{ __('messages.order_number') }}:</strong> <span x-text="currentReview?.order?.order_number"></span>#
            </p>
            <p><strong>{{ __('messages.review_date') }}:</strong> <span x-text="currentReview?.review_date"></span></p>
            <p>
                <strong>{{ __('messages.issue_type') }}:</strong>
                <span x-text="currentReview?.issue_type === 'product' ? '{{ __('messages.product_issue') }}' : (currentReview?.issue_type === 'order' ? '{{ __('messages.delivery_issue') }}' : currentReview?.issue_type)"></span>
            </p>
            <p><strong>{{ __('messages.issue_reason') }}:</strong> <span x-text="currentReview?.comment"></span></p>
            <strong class="mt-4 text-[20px]">{{ __('messages.product') }}</strong>

            <template x-if="currentReview?.issue_type === 'order' && currentReview?.order">
                <div class="space-y-2">
                    <template x-for="item in currentReview.order.items" :key="item.id">
                        <div class="flex items-center gap-4 p-2 bg-[#F8F9FA] rounded-lg">
                            <img :src="item.product?.image ?? '{{ asset('images/default-product.png') }}'" class="w-20 h-20 object-cover rounded-lg" alt="Product Image">
                            <div>
                                <p><strong>{{ __('messages.product') }}:</strong> <span x-text="item.product?.name"></span></p>
                                <p><strong>{{ __('messages.quantity') }}:</strong> <span x-text="item.quantity"></span></p>
                                <p><strong>{{ __('messages.unit_price') }}:</strong> <span x-text="item.unit_price"></span> ر.س</p>
                            </div>
                        </div>
                    </template>
                    <div class="mt-2">
                        <p><strong>{{ __('messages.total_amount') }}:</strong> <span x-text="currentReview?.order?.total_amount"></span> ر.س</p>
                    </div>
                </div>
            </template>

            <template x-if="currentReview?.issue_type === 'product' && currentReview?.product">
                <div class="flex items-center gap-4 p-2 bg-[#F8F9FA] rounded-lg">
                    <img :src="currentReview.product.image ?? '{{ asset('images/default-product.png') }}'" class="w-20 h-20 object-cover rounded-lg" alt="Product Image">
                    <div>
                        <p><strong>{{ __('messages.product') }}:</strong> <span x-text="currentReview.product.name"></span></p>
                        <p><strong>{{ __('messages.quantity') }}:</strong> <span x-text="currentReview?.order?.items[0]?.quantity ?? 1"></span></p>
                        <p><strong>{{ __('messages.unit_price') }}:</strong> <span x-text="currentReview?.order?.total_amount ?? 0"></span> ر.س</p>
                    </div>
                </div>
            </template>
        </div>

        {{-- Buttons or Closed Message --}}
        <div class="mt-4 flex gap-4 justify-end">
            <template x-if="currentReview?.status !== 'rejected'">
                <div class="flex gap-4">
                    {{-- Trigger button --}}
                    <button @click="showTakeActionModal = true"
                            class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                        {{ __('messages.take_action') }}
                    </button>

                    {{-- Actions Modal --}}
                    <div x-show="showTakeActionModal" x-cloak
                         @click.outside="showTakeActionModal = false"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md md:max-w-lg relative overflow-y-auto max-h-[90vh]">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-2xl font-bold text-center w-full">{{ __('messages.supplier_action') }}</h2>
                                <button @click="showTakeActionModal = false"
                                        class="text-gray-500 hover:text-gray-800 text-xl font-bold">×</button>
                            </div>
                            <form @submit.prevent="submitActionForm" class="space-y-6">
                                <div>
                                    <label class="block font-bold mb-2">{{ __('messages.action') }}</label>
                                    <div class="border rounded-xl overflow-hidden">
                                        <label class="flex items-center justify-between px-4 py-2 cursor-pointer hover:bg-gray-100">
                                            <span>{{ __('messages.send_warning') }}</span>
                                            <input type="radio" name="action" value="approved" x-model="form.action">
                                        </label>
                                        <label class="flex items-center justify-between px-4 py-2 cursor-pointer border-t hover:bg-gray-100">
                                            <span>{{ __('messages.suspend_account') }}</span>
                                            <input type="radio" name="action" value="pending" x-model="form.action">
                                        </label>
                                        <label class="flex items-center justify-between px-4 py-2 cursor-pointer border-t hover:bg-gray-100">
                                            <span>{{ __('messages.no_action') }}</span>
                                            <input type="radio" name="action" value="rejected" x-model="form.action">
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label class="block font-bold mb-2">{{ __('messages.notes') }}</label>
                                    <textarea x-model="form.notes" placeholder="{{ __('messages.enter_notes') }}"
                                              class="w-full border border-gray-300 rounded-xl p-3 resize-none"></textarea>
                                </div>
                                <div x-show="message" class="mt-2 text-sm text-green-600" x-text="message"></div>
                                <button type="submit"
                                        class="w-full bg-[#185D31] hover:bg-green-800 text-white font-semibold py-3 rounded-xl transition-all duration-200 shadow-md">
                                    {{ __('messages.submit') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <button @click="closeComplaint()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300">
                        {{ __('messages.close_complaint') }}
                    </button>
                </div>
            </template>

            <template x-if="currentReview?.status === 'rejected'">
                <div class="w-full text-center bg-gray-100 text-gray-700 py-3 rounded-lg">
                    {{ __('messages.complaint_closed') }}
                </div>
            </template>
        </div>
    </div>
</div>

    </div>
</div>

@endsection