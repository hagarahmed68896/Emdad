<div class="bg-white rounded-xl shadow p-6 mt-6">
    <!-- شريط البحث -->
    <div class="flex justify-between mb-4">
        <form method="GET" action="{{ route('admin.users.show', $user->id) }}" class="relative w-full max-w-md">
            <input type="hidden" name="tab" value="reviews">

            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث باسم المنتج "
                class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500">

            <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 transform -translate-y-1/2" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z" />
            </svg>

            <button type="submit"
                class="absolute left-0 top-0 h-full px-4 text-white bg-[#185D31] hover:bg-green-800 rounded-l-xl">
                بحث
            </button>
        </form>
    </div>

    <!-- جدول التقييمات -->
    <div class="overflow-x-auto rounded-t-xl">
        <table class="min-w-full divide-y divide-gray-200 text-center">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-[18px] font-medium  uppercase tracking-wider font-bold">#</th>
                    <th class="px-6 py-3 text-[18px]  font-medium  uppercase tracking-wider font-bold">اسم المنتج</th>
                    <th class="px-6 py-3 text-[18px]  font-medium  uppercase tracking-wider font-bold">التعليق</th>
                    <th class="px-6 py-3 text-[18px]  font-medium  uppercase tracking-wider font-bold">التقييم</th>
                    <th class="px-6 py-3 text-[18px]  font-medium  uppercase tracking-wider font-bold">الحالة</th>
                    <th class="px-6 py-3 text-[18px]  font-medium  uppercase tracking-wider font-bold">التاريخ</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($reviews as $index => $review)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $review->product ? $review->product->name : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $review->comment ?? '-' }}
                        </td>
                        <td class="flex justify-center items-center text-center px-6 py-4 whitespace-nowrap text-sm">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M10 15l-5.878 3.09L5.64 12 1 7.91l6.06-.88L10 2l2.94 5.03L19 7.91 14.36 12l1.518 6.09z" />
                                </svg>
                            @endfor
                        </td>

                        <!-- الحالة حسب التقييم -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                if ($review->rating >= 4) {
                                    $color = 'bg-green-100 text-green-800';
                                    $label = ' تقييم ايجابي';
                                } elseif ($review->rating >= 3) {
                                    $color = 'bg-yellow-100 text-yellow-800';
                                    $label = 'تقييم سلبي';
                                } else {
                                    $color = 'bg-red-100 text-red-800';
                                    $label = 'شكوى';
                                }
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs {{ $color }}">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $review->created_at->format('Y-m-d') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            لا توجد تقييمات مطابقة.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
   <nav class="flex items-center rounded-b-xl justify-between px-4 py-2 bg-[#EDEDED]" aria-label="Pagination">
        <div class="flex-1 flex justify-between items-center">
            <span class="text-sm text-gray-700 ml-4">
                {{ $reviews->firstItem() }} - {{ $reviews->lastItem() }} من {{ $reviews->total() }}
            </span>
            <div class="flex">
                {{-- Pagination Links --}}
                <div class="flex">
                    {!! $reviews->appends(['tab' => 'reviews', 'search' => request('search')])->links('pagination::tailwind') !!}
                </div>
            </div>
        </div>
    </nav>
</div>
