@extends('layouts.admin')

@section('page_title', 'تعديل الفئة')

@section('content')
<div class="p-6 bg-white rounded-xl shadow overflow-y-auto"
     x-data="categoryForm()"
     x-init="init({{ $category->toJson() }})">

    <h1 class="text-2xl font-bold mb-6">تعديل الفئة</h1>

    <!-- رسائل الأخطاء -->
    <template x-if="errorMessages.length">
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            <ul class="list-disc list-inside">
                <template x-for="msg in errorMessages" :key="msg">
                    <li x-text="msg"></li>
                </template>
            </ul>
        </div>
    </template>

    <form @submit.prevent="submitForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- صورة الفئة -->
        <div class="mb-6">
            <label class="block mb-2 font-bold">صورة الفئة</label>
            <div @click="$refs.iconUrl.click()"
                 class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-xl cursor-pointer">
                <template x-if="!preview">
                    <img src="{{ $category->iconUrl ? asset('storage/'.$category->iconUrl) : asset('images/Frame 3508.svg') }}"
                         class="h-40 object-contain" />
                </template>
                <template x-if="preview">
                    <img :src="preview" class="h-40 object-contain" />
                </template>
                <input type="file" x-ref="iconUrl" class="hidden" @change="handleFile" accept="image/png,image/jpeg">
            </div>
            <template x-if="errors.iconUrl">
                <p class="text-red-600 text-sm mt-1" x-text="errors.iconUrl"></p>
            </template>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- اسم الفئة -->
            <div class="mb-4">
                <label class="block mb-1 font-bold">اسم الفئة</label>
                <input type="text" x-model="form.name" class="border w-full px-3 py-3 rounded-xl">
                <template x-if="errors.name">
                    <p class="text-red-600 text-sm" x-text="errors.name"></p>
                </template>
            </div>

            <!-- نوع الفئة (قابل للتجربة) -->
            <div class="mb-4">
                <label class="block mb-1 font-bold">نوع الفئة</label>
                <select x-model="form.type" class="border w-full px-3 py-3 rounded-xl bg-gray-100">
                    <option value="category">عامة</option>
                    <option value="sub_category">فرعية</option>
                </select>
            </div>
        </div>

        <!-- فئة الأم إذا كانت فرعية -->
        <div class="mb-4" x-show="form.type === 'sub_category'">
            <label class="block mb-1 font-bold">تابعة لـ</label>
            <select x-model="form.category_id" class="border w-full px-3 py-2 rounded-xl">
                <option value="">حدد الفئة الأم</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <template x-if="errors.category_id">
                <p class="text-red-600 text-sm" x-text="errors.category_id"></p>
            </template>
        </div>

        <!-- الوصف إذا كانت عامة -->
        <div class="mb-4" x-show="form.type === 'category'">
            <label class="block mb-1 font-bold">الوصف</label>
            <textarea x-model="form.description" class="border w-full px-3 py-2 rounded-xl"></textarea>
            <template x-if="errors.description">
                <p class="text-red-600 text-sm" x-text="errors.description"></p>
            </template>
        </div>

        <button type="submit" class="px-5 py-2 bg-[#185D31] text-white rounded-xl">تحديث الفئة</button>
    </form>
</div>

<script defer src="//unpkg.com/alpinejs"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('categoryForm', () => ({
        preview: null,
        form: {
            name: '',
            type: '',
            category_id: '',
            description: '',
        },
        errors: {},
        errorMessages: [],
        init(data) {
            this.form = {
                name: data.name,
                type: data.type,
                category_id: data.category_id ? data.category_id.toString() : '',
                description: data.description ?? '',
            };
            if (data.iconUrl) {
                this.preview = '{{ asset('storage') }}/' + data.iconUrl;
            }
        },
        handleFile(e) {
            this.errors.iconUrl = null;
            const file = e.target.files[0];
            if (file && ['image/jpeg', 'image/png'].includes(file.type)) {
                this.preview = URL.createObjectURL(file);
            } else {
                this.errors.iconUrl = 'امتداد الصورة غير مسموح.';
                e.target.value = '';
            }
        },
        async submitForm() {
            this.errors = {};
            this.errorMessages = [];

            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('name', this.form.name);
            formData.append('type', this.form.type);
            formData.append('category_id', this.form.category_id);
            formData.append('description', this.form.description);

            if (this.$refs.iconUrl.files.length) {
                formData.append('iconUrl', this.$refs.iconUrl.files[0]);
            }

            const res = await fetch('{{ $category->type === "category" ? route('admin.categories.update', $category->id) : route('admin.sub-categories.update', $category->id) }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();

            if (!res.ok) {
                if (data.errors) {
                    this.errors = data.errors;
                    this.errorMessages = Object.values(data.errors).flat();
                } else {
                    this.errorMessages = ['حدث خطأ غير معروف.'];
                }
            } else {
                window.location.href = '{{ route('admin.categories.index') }}';
            }
        }
    }));
});
</script>
@endsection
