<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            {{-- Master Checkbox TH --}}
            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                <input type="checkbox" @click="toggleSelectAll" :checked="selectAll"
                    class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">
            </th>

            {{-- Other Headers --}}
            <th scope="col" class="px-6 py-3 rtl:text-right text-[18px] font-bold text-[#212121] uppercase">#</th>
            <th scope="col" class="px-6 py-3 rtl:text-right text-[18px] font-bold text-[#212121] uppercase">{{ __('messages.image') }}</th>
            <th scope="col" class="px-6 py-3 rtl:text-right text-[18px] font-bold text-[#212121] uppercase">{{ __('messages.category_name') }}</th>
            <th scope="col" class="px-6 py-3 rtl:text-right text-[18px] font-bold text-[#212121] uppercase">{{ __('messages.description') }}</th>
            <th scope="col" class="px-6 py-3 rtl:text-right text-[18px] font-bold text-[#212121] uppercase">{{ __('messages.category_type') }}</th>
            <th scope="col" class="px-6 py-3 rtl:text-right text-[18px] font-bold text-[#212121] uppercase">{{ __('messages.parent') }}</th>
            <th scope="col" class="px-6 py-3 rtl:text-right text-[18px] font-bold text-[#212121] uppercase">{{ __('messages.products_count') }}</th>
            <th scope="col" class="px-6 py-3 rtl:text-right text-[18px] font-bold text-[#212121] uppercase">{{ __('messages.actions') }}</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($items as $item)
            <tr>
                {{-- Checkbox --}}
                <td class="px-4 py-4 whitespace-nowrap text-center">
                    <input type="checkbox"
                        class="h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded"
                        :value="JSON.stringify({ id: {{ $item->id }}, type: '{{ $item->type }}' })"
                        @change="
                            const val = JSON.parse($event.target.value);
                            if ($event.target.checked) {
                                selectedCategories.push(val);
                            } else {
                                selectedCategories = selectedCategories.filter(
                                    cat => !(cat.id === val.id && cat.type === val.type)
                                );
                            }
                        ">
                </td>

                {{-- # --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">
                    {{ $loop->iteration + $items->firstItem() - 1 }}
                </td>

                {{-- الصورة --}}
                <td class="px-6 py-4">
                    @if ($item->iconUrl)
                        <img src="{{ asset('storage/' . $item->iconUrl) }}" alt="{{ __('messages.image') }}"
                            class="w-12 h-12 rounded-md object-cover">
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>

                {{-- اسم الفئة --}}
                <td class="px-6 py-4">{{ $item->name }}</td>

                {{-- الوصف --}}
                <td class="px-6 py-4">{{ $item->description }}</td>

                {{-- نوع الفئة --}}
                <td class="px-6 py-4">
                    {{ $item->type === 'category' ? __('messages.general') : __('messages.sub') }}
                </td>

                {{-- تابعة لـ --}}
                <td class="px-6 py-4">{{ $item->parent }}</td>

                {{-- عدد المنتجات --}}
                <td class="px-6 py-4">{{ $item->products_count }}</td>

                {{-- الإجراءات --}}
                <td class="px-6 py-4">
                    {{-- زر التعديل --}}
                    <a class="text-[#185D31] mx-4"
                       href="{{ $item->type === 'category'
                           ? route('admin.categories.edit', $item->id)
                           : route('admin.sub-categories.edit', $item->id)
                       }}">
                       <i class="fas fa-edit"></i>
                    </a>

                    {{-- زر الحذف --}}
                    <div x-data="{ open: false }" class="inline-block">
                        <button type="button" @click="open = true" class="text-[#185D31]">
                            <i class="fas fa-trash"></i>
                        </button>

                        <div x-show="open" x-cloak x-transition.opacity
                            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                            <div @click.away="open = false"
                                class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center">

                                <h2 class="text-lg font-bold mb-4">{{ __('messages.delete_confirm_title') }}</h2>
                                <p class="text-gray-600 mb-6">{{ __('messages.delete_confirm_message') }}</p>

                                <form action="{{ route('admin.categories.destroy', $item->id) }}" method="POST"
                                    class="flex justify-center gap-4">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">
                                        {{ __('messages.confirm_delete') }}
                                    </button>
                                    <button type="button" @click="open = false"
                                        class="px-4 py-2 rounded-xl bg-gray-300 text-gray-800 hover:bg-gray-400">
                                        {{ __('messages.cancel') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center text-gray-500 py-4">{{ __('messages.no_data') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>
