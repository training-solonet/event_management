<!-- Sidebar -->
<div class="h-full flex flex-col">
    <!-- Logo & Brand -->
    <div class="p-6 border-b border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">EventHub</h1>
                    <p class="text-xs text-gray-400">Admin Panel</p>
                </div>
            </div>
            <!-- Close button for mobile -->
            <button class="md:hidden text-gray-400 hover:text-white" onclick="toggleMobileMenu()">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    </div>
    
    <!-- User Profile -->
    <div class="p-4 border-b border-gray-700">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-user text-white"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate">Administrator</p>
                <p class="text-xs text-gray-400 truncate">Admin</p>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <a href="{{ url('/admin') }}" 
           class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-all duration-200 {{ request()->is('admin') ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg' : 'hover:bg-gray-700 hover:text-white hover:shadow-md' }}">
            <i class="fas fa-tachometer-alt mr-3 text-lg w-6 text-center"></i>
            <span class="font-medium">Dashboard</span>
        </a>
        
        <a href="{{ route('admin.event.index') }}" 
           class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-all duration-200 {{ request()->is('admin/event*') ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg' : 'hover:bg-gray-700 hover:text-white hover:shadow-md' }}">
            <i class="fas fa-calendar mr-3 text-lg w-6 text-center"></i>
            <span class="font-medium">Kelola Event</span>
        </a>
        
        <a href="{{ route('admin.peserta.index') }}" 
           class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-all duration-200 {{ request()->is('admin/peserta*') ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg' : 'hover:bg-gray-700 hover:text-white hover:shadow-md' }}">
            <i class="fas fa-users mr-3 text-lg w-6 text-center"></i>
            <span class="font-medium">Peserta</span>
        </a>

        <a href="{{ route('admin.payment.index') }}" 
           class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.payment.*') ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg' : 'hover:bg-gray-700 hover:text-white hover:shadow-md' }}">
            <i class="fas fa-money-check-alt mr-3 text-lg w-6 text-center"></i>
            <span class="font-medium">Metode</span>
        </a>
        
        <!-- Divider -->
        <div class="pt-4">
            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lainnya</p>
        </div>
        
        <a href="{{ url('/') }}" target="_blank" 
           class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-all duration-200 hover:bg-gray-700 hover:text-white hover:shadow-md">
            <i class="fas fa-external-link-alt mr-3 text-lg w-6 text-center"></i>
            <span class="font-medium">Lihat Website</span>
        </a>
        
        <!-- LOGOUT -->
        <a href="#" 
           onclick="event.preventDefault(); showConfirmLogout();" 
           class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-all duration-200 hover:bg-red-600 hover:text-white hover:shadow-md">
            <i class="fas fa-sign-out-alt mr-3 text-lg w-6 text-center"></i>
            <span class="font-medium">Logout</span>
        </a>
    </nav>
    
    <!-- Footer -->
    <div class="p-4 border-t border-gray-700">
        <div class="text-center">
            <p class="text-xs text-gray-400">
                <i class="fas fa-shield-alt mr-1"></i>
                v1.0.0
            </p>
            <p class="text-xs text-gray-500 mt-1">© {{ date('Y') }} EventHub</p>
        </div>
    </div>
</div>

<!-- Logout Script -->
<script>
    function showConfirmLogout() {
        if (!confirm('Apakah Anda yakin ingin keluar dari sistem?')) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('logout') }}";

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;

        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
</script>
