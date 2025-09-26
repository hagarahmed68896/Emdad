<div class="overflow-x-auto rounded-xl border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col"
                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                        class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                    #
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                    {{ __('messages.customer_name') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                    {{ __('messages.product_name') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                    {{ __('messages.comment') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                    {{ __('messages.rating') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                    {{ __('messages.status') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">
                    {{ __('messages.date') }}
                </th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($reviews as $review)
                <tr>
                    <td class="px-4 py-4 text-center">
                        <input type="checkbox" :value="{{ $review->id }}" x-model="selectedReviews"
                            class="ml-1 h-4 w-4 text-[#185D31] accent-[#185D31] border-[#185D31] rounded">
                    </td>

                    <td class="px-6 py-4 text-right">
                        {{ $loop->iteration + $reviews->firstItem() - 1 }}
                    </td>

                    <td class="px-6 py-4 text-right">
                        {{ $review->user->full_name ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-right">
                        {{ $review->product->name ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-right">
                        {{ Str::limit($review->comment, 50) ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-right text-yellow-500">
                        @for ($i = 0; $i < 5; $i++)
                            @if ($i < round($review->rating ?? 0))
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </td>

                    <td class="px-6 py-4 text-right">
                        @php
                            $statusClass = '';
                            $statusText = '';
                            if($review->is_complaint){
                                $statusClass = 'bg-red-100 text-red-800';
                                $statusText = __('messages.complaint');
                            }
                            elseif ($review->rating >= 4) {
                                $statusClass = 'bg-green-100 text-green-800';
                                $statusText = __('messages.positive');
                            } elseif ($review->rating >= 2) {
                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                $statusText = __('messages.negative');
                            }
                        @endphp

                        <span
                            class="px-2 py-1 inline-flex w-[100px] text-center items-center justify-center text-[14px] leading-5 rounded-full {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-right">
                        {{ $review->created_at->format('Y-m-d H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                        {{ __('messages.no_reviews') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
