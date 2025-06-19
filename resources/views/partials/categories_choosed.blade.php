<section class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">اكتشف أبرز الفئات</h2>
        <a href="#" class="text-green-600 hover:text-green-700 font-semibold">عرض المزيد</a>
    </div>
    <p class="text-gray-600 mb-8">
        تصفح مجموعة متنوعة من المنتجات التي تناسب كل احتياجاتك
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($categories as $category)
            <div class="bg-white rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
                <a href="{{ route('categories.show', $category->slug) }}"> {{-- Assuming you have a route named 'categories.show' and a 'slug' field for your categories --}}
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-48 object-cover"> {{-- Assuming category images are stored in 'storage/app/public' and linked via 'storage:link' --}}
                    <div class="p-4 text-center">
                        <h3 class="text-xl font-semibold text-gray-700">{{ $category->name }}</h3>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- If you want to add pagination dots --}}
    <div class="flex justify-center mt-8">
        <span class="h-2 w-2 bg-gray-400 rounded-full mx-1"></span>
        <span class="h-2 w-2 bg-green-500 rounded-full mx-1"></span>
        <span class="h-2 w-2 bg-gray-400 rounded-full mx-1"></span>
    </div>
</section>