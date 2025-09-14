  <main class="flex-1 overflow-x-hidden overflow-y-auto p-2">
            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1: Total Users -->
                <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalUsers }}</p>
                        <p class="text-gray-500 text-sm">إجمالي المستخدمين</p>
                        <p class="text-[#185D31] text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
</svg>
@if( $totalUsers ==0)
                            0%
                            @else
                            100%
                            @endif
                        </p>
                    </div>
                    <i class="fas fa-users text-4xl text-[#185D31] opacity-20"></i>
                </div>
                <!-- Card 2: Total Customers -->
                <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalCustomers }}</p>

                        <p class="text-gray-500 text-sm">عدد العملاء</p>
                        <p class="text-[#185D31] text-sm flex items-center">
                           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
</svg>

                            {{ $customerPercent }}%
                        </p>
                    </div>
                    <i class="fas fa-user-friends text-4xl text-[#185D31] opacity-20"></i>
                </div>
                <!-- Card 3: Total Suppliers -->
                <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalSuppliers }}</p>

                        <p class="text-gray-500 text-sm">عدد الموردين</p>
                        <p class="text-[#185D31] text-sm flex items-center">
                           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
</svg>

                            {{ $supplierPercent }}%
                        </p>
                    </div>
                    <i class="fas fa-store text-4xl text-[#185D31] opacity-20"></i>
                </div>
                <!-- Card 4: Total Documents -->
                <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalDocuments }}</p>
                        <p class="text-gray-500 text-sm">عدد الوثائق</p>
                        <p class="text-[#185D31] text-sm flex items-center">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
</svg>
@if($totalDocuments)
                            100%
                            @else
                            0%
                            @endif
                        </p>
                    </div>
                    <i class="fas fa-file-alt text-4xl text-[#185D31] opacity-20"></i>
                </div>
            </div>

            <!-- Customer Data Table Section -->
            {{-- @include('admin.customer')
         --}}

        </main>