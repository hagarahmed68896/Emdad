<div class="w-full overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
    <tr>
        <th scope="col"
            class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
            <input type="checkbox" x-model="selectAll" @click="toggleSelectAll"
                class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
        </th>
        <th scope="col"
            class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
            {{ __('messages.image') }}
        </th>
        <th scope="col"
            class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
            {{ __('messages.product_name') }}
        </th>
        <th scope="col"
            class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
            {{ __('messages.supplier_name') }}
        </th>
        <th scope="col"
            class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
            {{ __('messages.category') }}
        </th>
        <th scope="col"
            class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
            {{ __('messages.description') }}
        </th>
        <th scope="col"
            class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
            {{ __('messages.price') }}
        </th>
        <th scope="col"
            class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
            {{ __('messages.orders_count') }}
        </th>
        <th scope="col"
            class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
            {{ __('messages.rating') }}
        </th>
        <th scope="col"
            class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
            {{ __('messages.status') }}
        </th>
        <th scope="col"
            class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
            {{ __('messages.date') }}
        </th>
    </tr>
</thead>

    <tbody>
        @forelse($products as $product)
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                {{-- ✅ Checkbox --}}
                <td class="px-4 py-3 text-center">
                    <input type="checkbox" :value="{{ $product->id }}" x-model="selectedProducts"
                        class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                </td>

                {{-- ✅ الصورة --}}
                <td class="px-4 py-3 text-center">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                            class="w-12 h-12 object-cover rounded-md mx-auto">
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>

                {{-- ✅ اسم المنتج --}}
            <td class="px-4 py-3 text-right text-gray-800">
    {{ app()->getLocale() === 'ar' ? $product->name : $product->name_en }}
</td>


                {{-- ✅ اسم المورد --}}
                <td class="px-4 py-3 text-right text-gray-800">
                    {{ $product->supplier->company_name ?? '—' }}
                </td>

                {{-- ✅ الفئة --}}
                <td class="px-4 py-3 text-right text-gray-800">
                    {{ $product->subCategory->category->name ?? '—' }}
                </td>

                {{-- ✅ الوصف --}}
                <td class="px-4 py-3 text-right text-gray-600 max-w-xs truncate">
                    {{ Str::limit($product->description, 50) ?? '—' }}
                </td>

                {{-- ✅ السعر --}}
                <td class="px-4 py-3 text-right text-gray-800">
{{ number_format($product->price, 2) }} {{ __('messages.currency') }}
                </td>

                {{-- ✅ عدد الطلبات --}}
                <td class="px-4 py-3 text-right text-gray-800">
                    {{ $product->orders_count ?? 0 }}
                </td>

                {{-- ✅ التقييم --}}
                <td class="px-4 py-3 text-right text-yellow-500">
                    @for ($i = 0; $i < 5; $i++)
                        @if ($i < round($product->rating ?? 0))
                            <i class="fas fa-star"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                </td>

                {{-- ✅ الحالة --}}
       <td class="px-4 py-3 text-right">
    @if ($product->product_status)
        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-xl">
            {{ __('messages.available') }}
        </span>
    @else
        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-xl">
            {{ __('messages.unavailable') }}
        </span>
    @endif
</td>


                {{-- ✅ التاريخ --}}
                <td class="px-4 py-3 text-right text-gray-500">
                    {{ $product->created_at ? $product->created_at->translatedFormat('Y-m-d') : '—' }}
                </td>
            </tr>
        @empty
            <tr>
               <td colspan="11" class="px-4 py-6 text-center text-gray-500">
                {{ __('messages.no_products') }}
            </td>

            </tr>
        @endforelse
    </tbody>

</table>
</div>
{{-- ✅ Pagination --}}
<div class="mt-4">
    {{ $products->links() }}
</div>
