<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EventHub Admin - @yield('title', 'Dashboard')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS for fixed sidebar -->
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Mobile menu animation */
        .sidebar-mobile {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .sidebar-mobile.open {
            transform: translateX(0);
        }
        
        /* Overlay for mobile */
        .sidebar-overlay {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }
        .sidebar-overlay.open {
            opacity: 1;
            visibility: visible;
        }
    </style>
    
    @stack('styles')
</head>
<body class="h-full bg-gray-100">
    <!-- Admin Layout -->
    <div class="h-full flex flex-col md:flex-row">
        <!-- Mobile Menu Button -->
        <div class="md:hidden fixed top-4 left-4 z-40">
            <button id="mobileMenuButton" class="p-2 bg-gray-800 text-white rounded-lg shadow-lg">
                <i class="fas fa-bars text-lg"></i>
            </button>
        </div>
        
        <!-- Mobile Overlay -->
        <div id="mobileOverlay" class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden"></div>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-mobile md:translate-x-0 fixed md:relative z-40 md:z-auto w-64 bg-gray-800 text-white flex-shrink-0 h-full overflow-y-auto">
            @include('admin.layouts.admin.sidebar')
        </aside>
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col md:ml-0 w-full">
            <!-- Header -->
            @include('admin.layouts.admin.header')
            
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Custom Alert Modal -->
    <div id="customAlert" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full transform transition-all duration-300 scale-95">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div id="alertIcon" class="w-10 h-10 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    <h3 id="alertTitle" class="text-xl font-bold text-gray-800"></h3>
                </div>
                <p id="alertMessage" class="text-gray-600 mb-6"></p>
                <div class="flex justify-end">
                    <button id="alertConfirm" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="successToast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-3 text-xl"></i>
            <div>
                <p class="font-semibold" id="toastTitle">Berhasil!</p>
                <p class="text-sm" id="toastMessage"></p>
            </div>
        </div>
    </div>

    <!-- Error Toast -->
    <div id="errorToast" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
            <div>
                <p class="font-semibold" id="errorToastTitle">Error!</p>
                <p class="text-sm" id="errorToastMessage"></p>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl p-8 max-w-sm w-full mx-4">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-4"></div>
                <p class="text-gray-700 font-semibold">Memproses...</p>
                <p class="text-gray-500 text-sm mt-2" id="loadingText">Harap tunggu sebentar</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // CSRF Token for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Format date
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }

        // Reusable function for AJAX requests
        async function ajaxRequest(url, method = 'GET', data = null) {
            showLoading();
            
            const config = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            };
            
            if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
                config.body = JSON.stringify(data);
            }
            
            try {
                const response = await fetch(url, config);
                hideLoading();
                return await response.json();
            } catch (error) {
                hideLoading();
                console.error('Error:', error);
                return { success: false, message: 'Terjadi kesalahan jaringan' };
            }
        }

        // Custom Alert System
        function showAlert(title, message, type = 'info', callback = null) {
            const alert = document.getElementById('customAlert');
            const alertIcon = document.getElementById('alertIcon');
            const alertTitle = document.getElementById('alertTitle');
            const alertMessage = document.getElementById('alertMessage');
            const alertConfirm = document.getElementById('alertConfirm');
            
            // Set type styling
            switch(type) {
                case 'success':
                    alertIcon.className = 'w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-3';
                    alertIcon.innerHTML = '<i class="fas fa-check-circle text-white"></i>';
                    break;
                case 'error':
                    alertIcon.className = 'w-10 h-10 bg-red-500 rounded-full flex items-center justify-center mr-3';
                    alertIcon.innerHTML = '<i class="fas fa-exclamation-circle text-white"></i>';
                    break;
                case 'warning':
                    alertIcon.className = 'w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center mr-3';
                    alertIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-white"></i>';
                    break;
                default:
                    alertIcon.className = 'w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3';
                    alertIcon.innerHTML = '<i class="fas fa-info-circle text-white"></i>';
            }
            
            alertTitle.textContent = title;
            alertMessage.textContent = message;
            
            // Show alert
            alert.classList.remove('hidden');
            alert.classList.add('flex');
            
            // Animate in
            setTimeout(() => {
                alert.querySelector('.transform').classList.remove('scale-95');
                alert.querySelector('.transform').classList.add('scale-100');
            }, 10);
            
            // Set up confirm button
            alertConfirm.onclick = function() {
                closeAlert();
                if (callback) callback();
            };
            
            // Close on overlay click
            alert.onclick = function(e) {
                if (e.target === alert) {
                    closeAlert();
                    if (callback) callback();
                }
            };
        }
        
        function closeAlert() {
            const alert = document.getElementById('customAlert');
            alert.querySelector('.transform').classList.remove('scale-100');
            alert.querySelector('.transform').classList.add('scale-95');
            
            setTimeout(() => {
                alert.classList.add('hidden');
                alert.classList.remove('flex');
            }, 200);
        }

        // Toast notification system
        function showToast(message, title = 'Berhasil!', type = 'success') {
            let toast;
            if (type === 'success') {
                toast = document.getElementById('successToast');
                document.getElementById('toastTitle').textContent = title;
                document.getElementById('toastMessage').textContent = message;
            } else {
                toast = document.getElementById('errorToast');
                document.getElementById('errorToastTitle').textContent = title;
                document.getElementById('errorToastMessage').textContent = message;
            }
            
            toast.classList.remove('translate-x-full');
            setTimeout(() => {
                toast.classList.add('translate-x-full');
            }, 3000);
        }

        // Loading overlay
        function showLoading(text = 'Memproses...') {
            const overlay = document.getElementById('loadingOverlay');
            document.getElementById('loadingText').textContent = text;
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
        }
        
        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }

        // Mobile sidebar control
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
            
            // Prevent body scroll when menu is open
            if (sidebar.classList.contains('open')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        }

        // Close mobile menu on resize
        function handleResize() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                document.body.style.overflow = 'auto';
            }
        }

        // Initialize when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu button
            document.getElementById('mobileMenuButton').addEventListener('click', toggleMobileMenu);
            
            // Mobile overlay click
            document.getElementById('mobileOverlay').addEventListener('click', toggleMobileMenu);
            
            // Close sidebar when clicking on a link (mobile)
            document.querySelectorAll('#sidebar a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        toggleMobileMenu();
                    }
                });
            });
            
            // Handle window resize
            window.addEventListener('resize', handleResize);
            
            // Auto-refresh dashboard every 30 seconds
            setInterval(() => {
                if (window.location.pathname === '/admin' && typeof updateDashboardStats === 'function') {
                    updateDashboardStats();
                }
            }, 30000);
            
            // Show current date in header
            updateCurrentDate();
            setInterval(updateCurrentDate, 60000);
        });

        function updateCurrentDate() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const dateElement = document.getElementById('currentDate');
            if (dateElement) {
                dateElement.textContent = now.toLocaleDateString('id-ID', options);
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>