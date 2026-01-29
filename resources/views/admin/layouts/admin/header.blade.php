<!-- Header -->
<header class="bg-white shadow-md">
    <div class="px-4 md:px-8 py-4">
        <div class="flex items-center justify-between">
            <!-- Page Title -->
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard Admin')</h1>
                <p class="text-gray-600 text-sm md:text-base">@yield('page-subtitle', 'Ringkasan dan statistik sistem')</p>
            </div>
            
            <!-- Right Section -->
            <div class="flex items-center space-x-4">
                <!-- Date & Time -->
                <div class="hidden md:block text-right">
                    <p class="font-semibold text-gray-800">Selamat Datang!</p>
                    <p class="text-sm text-gray-600">
                        <i class="far fa-calendar-alt mr-1"></i>
                        <span id="currentDate">{{ date('d F Y') }}</span>
                    </p>
                </div>
                
                <!-- Quick Stats (only on dashboard) -->
                @if(request()->is('admin'))
                <div class="hidden md:flex items-center space-x-2">
                    <div class="h-8 w-px bg-gray-300"></div>
                    <div class="text-sm text-gray-600">
                        {{-- <span id="pendingBadge" class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-medium">
                            {{ $stats['pending_payments'] ?? 0 }} pending
                        </span> --}}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</header>