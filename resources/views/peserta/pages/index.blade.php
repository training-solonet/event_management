<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EventHub - Platform Pendaftaran Event</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- QR Code Generator -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    
    <!-- html2canvas -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .qr-code-placeholder {
            width: 160px;
            height: 160px;
            background: linear-gradient(45deg, #f3f4f6 25%, transparent 25%), 
                        linear-gradient(-45deg, #f3f4f6 25%, transparent 25%), 
                        linear-gradient(45deg, transparent 75%, #f3f4f6 75%), 
                        linear-gradient(-45deg, transparent 75%, #f3f4f6 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-code-img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        ::webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <i class="fas fa-calendar-alt text-blue-600 text-2xl mr-3"></i>
                    <span class="text-xl font-bold text-gray-800">EventHub</span>
                </div>
                
                <div class="flex items-center space-x-6">
                    <a href="#events" class="text-gray-600 hover:text-blue-600 transition font-medium">Events</a>
                    <button id="btnSearchModal" class="text-gray-600 hover:text-blue-600 transition font-medium">
                        <i class="fas fa-search mr-1"></i> Cek Pendaftaran
                    </button>
                    @if(session('admin'))
                        <a href="{{ route('admin.index') }}" class="text-gray-600 hover:text-blue-600 transition font-medium">
                            <i class="fas fa-user-shield mr-1"></i> Admin Panel
                        </a>
                    @else
                        {{-- <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 transition font-medium">
                            <i class="fas fa-user mr-1"></i> Login Admin
                        </a> --}}
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Temukan Event Menarik</h1>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Bergabunglah dengan berbagai event seru dan tingkatkan pengetahuan serta jaringan Anda</p>
        </div>
</section>

    <!-- Events Section -->
    <section id="events" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">Event Tersedia</h2>
            
            <!-- Search Event -->
            <div class="mb-8 max-w-2xl mx-auto">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="searchEventInput" 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="Cari event berdasarkan nama, lokasi, atau deskripsi...">
                    <button id="clearSearch" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 hidden">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            @if($events->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-gray-400 text-6xl mb-4"></i>
                    <p class="text-gray-600 text-xl">Tidak ada event yang tersedia saat ini</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="eventsContainer">
                    @foreach($events as $event)
                    <div class="event-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <div class="h-48 bg-gradient-to-r 
                            @if($event->type == 'online') from-blue-400 to-blue-600
                            @elseif($event->type == 'offline') from-purple-400 to-purple-600
                            @else from-green-400 to-green-600
                            @endif relative">
                            <div class="absolute top-4 left-4">
                                @if($event->type == 'online')
                                <span class="bg-white/90 backdrop-blur-sm text-blue-600 text-xs font-semibold px-3 py-1 rounded-full">Online</span>
                                @elseif($event->type == 'offline')
                                <span class="bg-white/90 backdrop-blur-sm text-purple-600 text-xs font-semibold px-3 py-1 rounded-full">Offline</span>
                                @else
                                <span class="bg-white/90 backdrop-blur-sm text-green-600 text-xs font-semibold px-3 py-1 rounded-full">Hybrid</span>
                                @endif
                            </div>
                            @if(!$event->canRegister())
                            <div class="absolute top-4 right-4">
                                <span class="bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">Penuh</span>
                            </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">{{ $event->name }}</h3>
                                    <p class="text-gray-600 mt-1"><i class="far fa-calendar-alt mr-2"></i>{{ \Carbon\Carbon::parse($event->date)->translatedFormat('d F Y') }}</p>
                                    <p class="text-gray-600"><i class="fas fa-map-marker-alt mr-2"></i>{{ $event->location }}</p>
                                </div>
                                @if($event->price == 0)
                                <span class="bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded-full">Free</span>
                                @else
                                <span class="bg-yellow-100 text-yellow-800 text-sm font-semibold px-3 py-1 rounded-full">Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <p class="text-gray-700 mb-6 line-clamp-2">{{ $event->description }}</p>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-sm text-gray-600">
                                    <i class="fas fa-users mr-1"></i> 
                                    {{ $event->registered_count }} terdaftar
                                    @if($event->available_slots !== null)
                                    / {{ $event->available_slots - $event->registered_count }} tersedia
                                    @endif
                                </span>
                            </div>
                            <button data-event-id="{{ $event->id }}" 
                                    class="btn-register w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold
                                    {{ !$event->canRegister() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ !$event->canRegister() ? 'disabled' : '' }}>
                                <i class="fas fa-user-plus mr-2"></i> 
                                {{ $event->canRegister() ? 'Daftar Sekarang' : 'Event Penuh' }}
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-calendar-alt text-blue-400 text-2xl mr-3"></i>
                        <span class="text-xl font-bold">EventHub</span>
                    </div>
                    <p class="text-gray-400 max-w-md">Platform manajemen event terpercaya yang menghubungkan penyelenggara dengan peserta sejak 2023.</p>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-gray-400 mb-2"><i class="fas fa-phone mr-2"></i> (021) 1234-5678</p>
                    <p class="text-gray-400 mb-2"><i class="fas fa-envelope mr-2"></i> info@eventhub.com</p>
                    <p class="text-gray-400">© 2023 EventHub. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Registration Modal -->
    <div id="registrationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Form Pendaftaran Event</h3>
                    <button id="btnCloseRegistrationModal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                
                <form id="registrationForm" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Data -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Data Pribadi</h4>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Nama Lengkap *</label>
                            <input type="text" name="full_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nama lengkap">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="contoh@email.com">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Nomor HP/WhatsApp *</label>
                            <input type="tel" name="phone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0812-3456-7890">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Jenis Kelamin *</label>
                            <select name="gender" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">NIK *</label>
                            <input type="text" name="nik" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="16 digit NIK">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 mb-2">Alamat Lengkap *</label>
                            <textarea name="address" required rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan alamat lengkap"></textarea>
                        </div>
                        
                        <!-- Event Selection -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Pilihan Event</h4>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 mb-2">Event yang Dipilih</label>
                            <input type="text" id="selected_event_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" readonly>
                            <input type="hidden" name="event_id" id="selected_event_id">
                        </div>
                        
                        <!-- Payment Method -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Metode Pembayaran</h4>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 mb-2">Pilih Bank/E-Wallet *</label>
                            <select id="paymentMethod" name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Metode Pembayaran --</option>
                            </select>
                        </div>
                        
                        <!-- Payment Details -->
                        <div id="paymentDetails" class="md:col-span-2 bg-blue-50 p-4 rounded-lg hidden">
                            <h5 class="font-semibold text-gray-800 mb-2">Instruksi Pembayaran:</h5>
                            <p class="text-gray-700 mb-2">Silakan transfer ke rekening berikut:</p>
                            <div class="bg-white p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Bank: <span class="font-semibold" id="bankName">-</span></p>
                                <p class="text-sm text-gray-600">Nomor Rekening: <span class="font-bold text-gray-800" id="accountNumber">-</span></p>
                                <p class="text-sm text-gray-600">Atas Nama: <span class="font-semibold" id="accountHolder">-</span></p>
                                <p class="text-sm text-gray-600 mt-2">Jumlah: <span class="font-bold text-red-600" id="paymentAmount">-</span></p>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">* Upload bukti pembayaran setelah transfer</p>
                        </div>
                        
                        <!-- Payment Proof Upload -->
                        <div id="paymentProofSection" class="md:col-span-2 mt-4 hidden">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Bukti Pembayaran</h4>
                            <div class="bg-yellow-50 p-4 rounded-lg mb-4">
                                <p class="text-sm text-yellow-700">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Upload bukti pembayaran untuk mempercepat proses verifikasi. Format: JPG, PNG, atau PDF (max: 2MB)
                                </p>
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2">Upload Bukti Pembayaran</label>
                                <input type="file" name="payment_proof" id="payment_proof" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       accept=".jpg,.jpeg,.png,.pdf">
                                <p class="text-sm text-gray-500 mt-1">File yang diterima: JPG, PNG, PDF (Maksimal 2MB)</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" id="btnCancelRegistration" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                            <i class="fas fa-paper-plane mr-2"></i> Daftar & Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Search Participant Modal -->
    <div id="searchModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Cek Status Pendaftaran</h3>
                    <button id="btnCloseSearchModal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Cari berdasarkan NIK, Email, atau Kode Transaksi</label>
                    <div class="flex">
                        <input type="text" id="searchInput" placeholder="Masukkan NIK, Email, atau Kode Transaksi" 
                               class="flex-grow px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button id="btnSearchParticipant" class="bg-blue-600 text-white px-6 py-3 rounded-r-lg hover:bg-blue-700 transition font-medium">
                            <i class="fas fa-search mr-2"></i> Cari
                        </button>
                    </div>
                </div>
                
                <!-- Search Results -->
                <div id="searchResults" class="hidden">
                    <div id="searchResultsContent">
                        <!-- Results will be displayed here -->
                    </div>
                    
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-700">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Jika data tidak ditemukan atau terdapat kesalahan, silakan hubungi admin di 
                            <a href="mailto:admin@eventhub.com" class="text-blue-600 hover:underline">admin@eventhub.com</a>
                        </p>
                    </div>
                </div>
                
                <!-- Empty State -->
                <div id="emptyState" class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-600">Masukkan NIK, Email, atau Kode Transaksi Anda untuk mengecek status pendaftaran</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal with WhatsApp Integration -->
    <div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Pendaftaran Berhasil!</h3>
                <p id="successMessage" class="text-gray-600 mb-4"></p>
                
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <p class="text-sm text-gray-600 mb-2">Kode Transaksi Anda:</p>
                    <p id="transactionCode" class="font-mono font-bold text-lg text-gray-800 mb-2"></p>
                    
                    <!-- WhatsApp Countdown -->
                    <div id="whatsappCountdown" class="mt-4 mb-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <i class="fab fa-whatsapp text-green-500 text-xl mb-2"></i>
                            <p class="text-sm text-blue-700">
                                Anda akan diarahkan ke WhatsApp dalam 
                                <span id="countdownTimer" class="font-bold">5</span> 
                                detik untuk konfirmasi...
                            </p>
                            <p class="text-xs text-blue-600 mt-2">
                                Jika tidak otomatis terhubung, klik tombol "Kirim ke WhatsApp" di bawah.
                            </p>
                        </div>
                    </div>
                    
                    <!-- QR Code Container -->
                    <div id="qrCodeContainer" class="mt-4 mb-4 hidden">
                        <p class="text-sm text-gray-600 mb-2">Scan QR Code untuk verifikasi:</p>
                        <div class="flex justify-center">
                            <div id="successQRCode" class="w-48 h-48 bg-white p-4 rounded-lg border border-gray-300 flex items-center justify-center">
                                <!-- QR Code will be generated here -->
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">* QR Code ini untuk keperluan check-in event</p>
                    </div>

                    <!-- Payment Pending Message -->
                    <div id="paymentPendingMessage" class="mt-4 mb-4 hidden">
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <i class="fas fa-clock text-yellow-500 text-xl mb-2"></i>
                            <p class="text-sm text-yellow-700">
                                QR Code akan tersedia setelah pembayaran diverifikasi oleh admin.
                                Status pembayaran Anda saat ini: <span class="font-semibold">Menunggu Verifikasi</span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Download Button -->
                    <div id="downloadButtonContainer" class="mt-4 hidden">
                        <button id="btnDownloadTicket" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            <i class="fas fa-download mr-2"></i> Download Tiket
                        </button>
                    </div>
                </div>
                
                <div class="text-left bg-yellow-50 p-4 rounded-lg mb-6">
                    <h4 class="font-semibold text-yellow-800 mb-2"><i class="fas fa-info-circle mr-2"></i>Instruksi Selanjutnya:</h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>1. Simpan kode transaksi ini</li>
                        <li>2. Bukti pembayaran akan diverifikasi oleh admin dalam 1x24 jam</li>
                        <li>3. Setelah verifikasi, QR Code akan tersedia untuk check-in</li>
                        <li>4. Anda akan menerima konfirmasi via WhatsApp setelah verifikasi</li>
                        <li>5. Gunakan fitur "Cari Peserta" untuk mengecek status kapan saja</li>
                    </ul>
                </div>
                
                <div class="flex flex-col space-y-3">
                    <button id="btnWhatsApp" class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                        <i class="fab fa-whatsapp mr-2"></i> Kirim ke WhatsApp
                    </button>
                    <button id="btnCloseSuccessModal" class="w-full px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden WhatsApp Data Container -->
    <div id="whatsappData" class="hidden"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded - WhatsApp Integration System');
        
        let currentEvent = null;
        let paymentMethods = [];
        let whatsappTimer = null;

        // Element references
        const btnSearchModal = document.getElementById('btnSearchModal');
        const btnCloseSearchModal = document.getElementById('btnCloseSearchModal');
        const searchModal = document.getElementById('searchModal');
        const btnSearchParticipant = document.getElementById('btnSearchParticipant');
        const searchInput = document.getElementById('searchInput');
        const emptyState = document.getElementById('emptyState');
        const searchResults = document.getElementById('searchResults');
        const searchResultsContent = document.getElementById('searchResultsContent');
        
        const registrationModal = document.getElementById('registrationModal');
        const btnCloseRegistrationModal = document.getElementById('btnCloseRegistrationModal');
        const btnCancelRegistration = document.getElementById('btnCancelRegistration');
        const registrationForm = document.getElementById('registrationForm');
        const paymentMethodSelect = document.getElementById('paymentMethod');
        const paymentDetails = document.getElementById('paymentDetails');
        const paymentProofSection = document.getElementById('paymentProofSection');
        const searchEventInput = document.getElementById('searchEventInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        
        const successModal = document.getElementById('successModal');
        const successMessage = document.getElementById('successMessage');
        const transactionCode = document.getElementById('transactionCode');
        const successQRCode = document.getElementById('successQRCode');
        const qrCodeContainer = document.getElementById('qrCodeContainer');
        const paymentPendingMessage = document.getElementById('paymentPendingMessage');
        const downloadButtonContainer = document.getElementById('downloadButtonContainer');
        const btnCloseSuccessModal = document.getElementById('btnCloseSuccessModal');
        const btnDownloadTicket = document.getElementById('btnDownloadTicket');
        const btnWhatsApp = document.getElementById('btnWhatsApp');
        const whatsappCountdown = document.getElementById('whatsappCountdown');
        const countdownTimer = document.getElementById('countdownTimer');
        
        const whatsappData = document.getElementById('whatsappData');

        // Event Listeners
        if (btnSearchModal) {
            btnSearchModal.addEventListener('click', openSearchModal);
        }
        
        if (btnCloseSearchModal) {
            btnCloseSearchModal.addEventListener('click', closeSearchModal);
        }
        
        if (btnSearchParticipant) {
            btnSearchParticipant.addEventListener('click', searchParticipant);
        }
        
        if (btnCloseRegistrationModal) {
            btnCloseRegistrationModal.addEventListener('click', closeRegistrationModal);
        }
        
        if (btnCancelRegistration) {
            btnCancelRegistration.addEventListener('click', closeRegistrationModal);
        }
        
        if (btnCloseSuccessModal) {
            btnCloseSuccessModal.addEventListener('click', closeSuccessModal);
        }
        
        if (btnDownloadTicket) {
            btnDownloadTicket.addEventListener('click', downloadTicket);
        }
        
        if (btnWhatsApp) {
            btnWhatsApp.addEventListener('click', function() {
                if (whatsappData.dataset.message) {
                    sendToWhatsApp(whatsappData.dataset.message);
                    clearInterval(whatsappTimer);
                    whatsappCountdown.classList.add('hidden');
                }
            });
        }
        
        // Registration button listeners
        document.querySelectorAll('.btn-register').forEach(button => {
            button.addEventListener('click', function() {
                const eventId = this.getAttribute('data-event-id');
                openRegistrationModal(eventId);
            });
        });
        
        // Payment method change listener
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', function(e) {
                updatePaymentDetails(e.target.value);
            });
        }
        
        // Form submission
        if (registrationForm) {
            registrationForm.addEventListener('submit', submitRegistration);
        }
        
        // Search event listener
        if (searchEventInput) {
            searchEventInput.addEventListener('input', filterEvents);
        }
        
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                if (searchEventInput) {
                    searchEventInput.value = '';
                    filterEvents();
                }
            });
        }
        
        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target === registrationModal) {
                closeRegistrationModal();
            }
            if (e.target === searchModal) {
                closeSearchModal();
            }
            if (e.target === successModal) {
                closeSuccessModal();
            }
        });

        // ========== FUNGSI UTAMA ==========
        
        // 1. Search Event Functionality
        function filterEvents() {
            const searchTerm = searchEventInput.value.toLowerCase().trim();
            const eventCards = document.querySelectorAll('.event-card');
            let hasVisibleEvents = false;
            
            if (clearSearchBtn) {
                if (searchTerm) {
                    clearSearchBtn.classList.remove('hidden');
                } else {
                    clearSearchBtn.classList.add('hidden');
                }
            }
            
            eventCards.forEach(card => {
                const eventName = card.querySelector('h3').textContent.toLowerCase();
                const eventLocation = card.querySelectorAll('p')[1].textContent.toLowerCase();
                const eventDescription = card.querySelector('.line-clamp-2').textContent.toLowerCase();
                const eventType = card.querySelector('span').textContent.toLowerCase();
                
                if (eventName.includes(searchTerm) || 
                    eventLocation.includes(searchTerm) || 
                    eventDescription.includes(searchTerm) ||
                    eventType.includes(searchTerm)) {
                    card.style.display = 'block';
                    hasVisibleEvents = true;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show message if no events found
            const noEventsMessage = document.getElementById('noEventsMessage');
            if (!hasVisibleEvents && searchTerm && eventCards.length > 0) {
                if (!noEventsMessage) {
                    const eventsContainer = document.getElementById('eventsContainer');
                    if (eventsContainer) {
                        const message = document.createElement('div');
                        message.id = 'noEventsMessage';
                        message.className = 'col-span-3 text-center py-12';
                        message.innerHTML = `
                            <i class="fas fa-search text-gray-400 text-5xl mb-4"></i>
                            <p class="text-gray-600 text-xl mb-2">Tidak ditemukan event yang sesuai</p>
                            <p class="text-gray-500">Coba dengan kata kunci lain</p>
                        `;
                        eventsContainer.appendChild(message);
                    }
                }
            } else if (noEventsMessage) {
                noEventsMessage.remove();
            }
        }

        // 2. Generate QR Code
        function generateQRCode(elementId, code) {
            if (!code || !elementId) return;
            
            console.log('Generating QR Code for:', code, 'in element:', elementId);
            
            const element = document.getElementById(elementId);
            if (!element) {
                console.error('Element not found:', elementId);
                return;
            }
            
            // Clear previous content
            element.innerHTML = '';
            
            // Show loading indicator
            element.innerHTML = '<div class="qr-code-placeholder"><div class="text-gray-400 text-sm">Membuat QR Code...</div></div>';
            
            try {
                // Gunakan qrcode-generator library
                const qr = qrcode(4, 'M');
                qr.addData(code);
                qr.make();
                
                setTimeout(() => {
                    try {
                        const qrImageUrl = qr.createDataURL(4, 0);
                        
                        const img = document.createElement('img');
                        img.src = qrImageUrl;
                        img.alt = `QR Code for ${code}`;
                        img.className = 'qr-code-img';
                        
                        element.innerHTML = '';
                        element.appendChild(img);
                        console.log('QR Code generated successfully');
                    } catch (innerError) {
                        console.error('Error creating QR Code image:', innerError);
                        showTextFallback(element, code);
                    }
                }, 100);
                
            } catch (error) {
                console.error('Error in QRCode generation:', error);
                showTextFallback(element, code);
            }
        }
        
        function showTextFallback(element, code) {
            element.innerHTML = `
                <div class="text-center p-4 bg-gray-100 rounded-lg">
                    <p class="font-mono text-sm break-all font-bold">${code}</p>
                    <p class="text-xs text-gray-500 mt-2">(Kode Transaksi)</p>
            </div>
            `;
        }

        // 3. WhatsApp Functions - FIXED ERROR: Handle null currentEvent
        function composeWhatsAppMessage(participantData, eventData, transactionCode) {
            // PERBAIKAN: Validasi eventData sebelum mengakses propertinya
            if (!eventData) {
                console.error('Event data is null or undefined in composeWhatsAppMessage');
                return `*KONFIRMASI PENDAFTARAN EVENT*
                
Kode Transaksi: *${transactionCode}*
Nama: ${participantData.full_name}
Email: ${participantData.email}
Nomor: ${participantData.phone}
Alamat: ${participantData.address}
Event: Event tidak tersedia
Tanggal Event: Tanggal tidak tersedia
Lokasi: Lokasi tidak tersedia
Status: Menunggu Verifikasi Pembayaran

Selamat kamu telah berhasil mendaftar selanjutnya tunggu konfirmasi pembayaran dan lakukan pengecekan berkala di situs web dengan menggunakan kode transaksi yang sudah di dapatkan.

*PENTING:* Jangan lupa untuk melakukan pengecekan berkala di situs web menggunakan kode transaksi yang sudah didapatkan.

Terima kasih telah mendaftar!`;
            }
            
            // Format tanggal dengan error handling
            let eventDateStr = 'Tanggal tidak tersedia';
            try {
                if (eventData.date) {
                    const eventDate = new Date(eventData.date);
                    if (!isNaN(eventDate.getTime())) {
                        eventDateStr = eventDate.toLocaleDateString('id-ID', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        });
                    }
                }
            } catch (error) {
                console.error('Error formatting date:', error);
            }
            
            return `*KONFIRMASI PENDAFTARAN EVENT*
            
Kode Transaksi: *${transactionCode}*
Nama: ${participantData.full_name}
Email: ${participantData.email}
Nomor: ${participantData.phone}
Alamat: ${participantData.address}
Event: ${eventData.name || 'Event tidak tersedia'}
Tanggal Event: ${eventDateStr}
Lokasi: ${eventData.location || 'Lokasi tidak tersedia'}
Status: Menunggu Verifikasi Pembayaran

Selamat kamu telah berhasil mendaftar selanjutnya tunggu konfirmasi pembayaran dan lakukan pengecekan berkala di situs web dengan menggunakan kode transaksi yang sudah di dapatkan.

*PENTING:* Jangan lupa untuk melakukan pengecekan berkala di situs web menggunakan kode transaksi yang sudah didapatkan.

Terima kasih telah mendaftar!`;
        }

        function sendToWhatsApp(message) {
            const phoneNumber = '6285934554395';
            const encodedMessage = encodeURIComponent(message);
            const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
            
            // Buka WhatsApp di tab baru
            window.open(whatsappUrl, '_blank');
            
            // Juga buka di jendela yang sama untuk mobile
            setTimeout(() => {
                window.location.href = whatsappUrl;
            }, 500);
        }

        function startWhatsAppCountdown(message) {
            if (!whatsappCountdown || !countdownTimer) return;
            
            whatsappCountdown.classList.remove('hidden');
            let seconds = 5;
            
            // Clear existing timer
            if (whatsappTimer) {
                clearInterval(whatsappTimer);
            }
            
            whatsappTimer = setInterval(() => {
                countdownTimer.textContent = seconds;
                seconds--;
                
                if (seconds < 0) {
                    clearInterval(whatsappTimer);
                    if (message) {
                        sendToWhatsApp(message);
                    }
                    whatsappCountdown.classList.add('hidden');
                }
            }, 1000);
        }

        // 4. Search Modal Functions - DIPERBAIKI: Menambahkan fungsi untuk mendapatkan pesan berdasarkan status
        function getStatusMessage(paymentStatus, notes) {
            // Default message dari controller
            const defaultNote = 'Pendaftaran berhasil. Silakan tunggu verifikasi pembayaran.';
            
            // Jika notes adalah default dan payment status berubah, tampilkan pesan yang sesuai
            if (notes === defaultNote) {
                switch(paymentStatus) {
                    case 'pending':
                        return 'Menunggu pembayaran. Silakan lakukan pembayaran dan upload bukti pembayaran untuk mempercepat proses verifikasi.';
                    case 'paid':
                        return 'Pembayaran sudah diterima. Sedang dalam proses verifikasi oleh admin. Status akan berubah menjadi "Terverifikasi" setelah proses selesai.';
                    case 'verified':
                        return 'Pembayaran sudah diverifikasi. Anda telah terdaftar resmi sebagai peserta event. QR Code sudah dapat digunakan untuk check-in.';
                    default:
                        return notes;
                }
            }
            
            // Jika admin sudah mengubah notes, tampilkan notes yang ada
            return notes;
        }

        function openSearchModal() {
            console.log('Opening search modal');
            if (searchModal) {
                searchModal.classList.remove('hidden');
                searchModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }
        
        function closeSearchModal() {
            if (searchModal) {
                searchModal.classList.add('hidden');
                searchModal.classList.remove('flex');
                document.body.style.overflow = 'auto';
                resetSearch();
            }
        }
        
        function resetSearch() {
            if (searchInput) searchInput.value = '';
            if (emptyState) emptyState.classList.remove('hidden');
            if (searchResults) searchResults.classList.add('hidden');
        }
        
        async function searchParticipant() {
            const searchValue = searchInput ? searchInput.value.trim() : '';
            if (searchValue === '') {
                alert('Silakan masukkan NIK, Email, atau Kode Transaksi terlebih dahulu');
                return;
            }
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    alert('Token CSRF tidak ditemukan');
                    return;
                }
                
                const response = await fetch('/search-participant', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ search: searchValue })
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan saat mencari data');
                }
                
                displaySearchResults(data.participants);
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mencari data: ' + error.message);
            }
        }
        
        function displaySearchResults(participants) {
            if (!searchResultsContent) return;
            
            if (!participants || participants.length === 0) {
                searchResultsContent.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-search text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-600">Data tidak ditemukan</p>
                        <p class="text-sm text-gray-500 mt-2">Pastikan NIK, Email, atau Kode Transaksi yang Anda masukkan benar</p>
                    </div>
                `;
            } else {
                let html = '';
                participants.forEach(participant => {
                    let statusColor = '';
                    let statusText = '';
                    let badgeColor = '';
                    
                    switch(participant.payment_status) {
                        case 'verified':
                            statusColor = 'text-green-600';
                            statusText = 'Terverifikasi';
                            badgeColor = 'bg-green-100 text-green-800';
                            break;
                        case 'paid':
                            statusColor = 'text-blue-600';
                            statusText = 'Sudah Bayar';
                            badgeColor = 'bg-blue-100 text-blue-800';
                            break;
                        case 'pending':
                        default:
                            statusColor = 'text-yellow-600';
                            statusText = 'Menunggu Pembayaran';
                            badgeColor = 'bg-yellow-100 text-yellow-800';
                            break;
                    }
                    
                    // Check if payment proof exists
                    const hasProof = participant.payment_proof ? 
                        `<a href="/storage/payment_proofs/${participant.payment_proof}" target="_blank" class="text-blue-600 hover:underline text-sm">
                            <i class="fas fa-file-invoice mr-1"></i> Lihat Bukti
                        </a>` : 
                        '<span class="text-gray-500 text-sm"><i class="fas fa-times-circle mr-1"></i> Belum upload</span>';
                    
                    // Create unique ID for QR code
                    const qrCodeId = `qrcode-${participant.id}`;
                    
                    // Determine if QR code should be shown (only for verified/paid status)
                    const showQRCode = participant.payment_status === 'verified' || participant.payment_status === 'paid';
                    
                    // DAPATKAN PESAN BERDASARKAN STATUS
                    const statusMessage = getStatusMessage(participant.payment_status, participant.notes || '');
                    
                    html += `
                        <div class="border-l-4 ${participant.payment_status === 'verified' ? 'border-green-500' : participant.payment_status === 'paid' ? 'border-blue-500' : 'border-yellow-500'} bg-gray-50 p-4 rounded-r-lg mb-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">${participant.full_name}</h4>
                                    <p class="text-sm text-gray-600 mt-1">${participant.event.name}</p>
                                    <p class="text-sm text-gray-600">Email: ${participant.email}</p>
                                    <p class="text-sm text-gray-600">No. HP: ${participant.phone}</p>
                                    <p class="text-sm text-gray-600">Kode Transaksi: <span class="font-mono font-bold">${participant.transaction_code}</span></p>
                                    <p class="text-sm text-gray-600 mt-2">Status: <span class="font-semibold ${statusColor}">${statusText}</span></p>
                                    <p class="text-sm text-gray-600 mt-1">Bukti Bayar: ${hasProof}</p>
                                </div>
                                <span class="${badgeColor} px-3 py-1 rounded-full text-sm font-semibold ml-2">
                                    ${participant.payment_status === 'verified' ? 'Lunas' : participant.payment_status === 'paid' ? 'Sudah Bayar' : 'Pending'}
                                </span>
                            </div>
                            
                            <!-- DIPERBAIKI: Menampilkan pesan status yang sesuai -->
                            <div class="mt-4 p-3 bg-white rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-600"><strong>Status Pendaftaran:</strong> ${statusMessage}</p>
                            </div>
                            
                            ${showQRCode ? `
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm font-semibold text-gray-700 mb-2">QR Code Tiket:</p>
                                <div class="bg-white p-4 rounded-lg border border-gray-300">
                                    <div id="${qrCodeId}" class="w-40 h-40 mx-auto"></div>
                                    <p class="text-xs text-center text-gray-500 mt-2">${participant.transaction_code}</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 text-center">* Gunakan QR Code ini untuk check-in event</p>
                            </div>
                            ` : `
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="bg-yellow-50 p-3 rounded-lg">
                                    <p class="text-sm text-yellow-700">
                                        <i class="fas fa-clock mr-2"></i>
                                        QR Code akan tersedia setelah pembayaran diverifikasi oleh admin.
                                    </p>
                                </div>
                            </div>
                            `}
                            
                            <!-- DIPERBAIKI: Hanya tampilkan notes jika berbeda dari default -->
                            ${(participant.notes && participant.notes !== 'Pendaftaran berhasil. Silakan tunggu verifikasi pembayaran.') ? `
                            <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <p class="text-sm text-blue-700"><strong>Catatan Admin:</strong> ${participant.notes}</p>
                            </div>
                            ` : ''}
                        </div>
                    `;
                });
                
                searchResultsContent.innerHTML = html;
                
                // Generate QR codes for each participant with verified/paid status
                participants.forEach(participant => {
                    if (participant.payment_status === 'verified' || participant.payment_status === 'paid') {
                        const qrCodeId = `qrcode-${participant.id}`;
                        setTimeout(() => {
                            generateQRCode(qrCodeId, participant.transaction_code);
                        }, 300);
                    }
                });
            }
            
            if (emptyState) emptyState.classList.add('hidden');
            if (searchResults) searchResults.classList.remove('hidden');
        }
        
        // 5. Registration Modal Functions - FIXED: Store event data in form
        async function openRegistrationModal(eventId) {
            try {
                console.log('Fetching event details for ID:', eventId);
                const response = await fetch(`/event/${eventId}/details`);
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal memuat data event');
                }
                
                currentEvent = data.event;
                paymentMethods = data.payment_methods;
                
                console.log('Current event data:', currentEvent);
                
                // Populate form
                const selectedEventId = document.getElementById('selected_event_id');
                const selectedEventName = document.getElementById('selected_event_name');
                
                if (selectedEventId) selectedEventId.value = currentEvent.id;
                if (selectedEventName) selectedEventName.value = currentEvent.name;
                
                // Store event data in form data attributes for later use
                if (registrationForm) {
                    registrationForm.dataset.eventName = currentEvent.name;
                    registrationForm.dataset.eventDate = currentEvent.date;
                    registrationForm.dataset.eventLocation = currentEvent.location;
                    console.log('Event data stored in form:', {
                        name: currentEvent.name,
                        date: currentEvent.date,
                        location: currentEvent.location
                    });
                }
                
                // Populate payment methods dropdown
                if (paymentMethodSelect) {
                    paymentMethodSelect.innerHTML = '<option value="">-- Pilih Metode Pembayaran --</option>';
                    
                    paymentMethods.forEach(method => {
                        const option = document.createElement('option');
                        option.value = method.name;
                        option.textContent = method.name;
                        option.setAttribute('data-account-number', method.account_number);
                        option.setAttribute('data-account-name', method.account_name);
                        paymentMethodSelect.appendChild(option);
                    });
                }
                
                // Reset payment proof section
                if (paymentProofSection) {
                    paymentProofSection.classList.add('hidden');
                }
                
                // Show modal
                if (registrationModal) {
                    registrationModal.classList.remove('hidden');
                    registrationModal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                }
                
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal memuat data event. Silakan coba lagi. Error: ' + error.message);
            }
        }
        
        function closeRegistrationModal() {
            if (registrationModal) {
                registrationModal.classList.add('hidden');
                registrationModal.classList.remove('flex');
                document.body.style.overflow = 'auto';
            }
            if (registrationForm) {
                registrationForm.reset();
                // Clear stored event data
                delete registrationForm.dataset.eventName;
                delete registrationForm.dataset.eventDate;
                delete registrationForm.dataset.eventLocation;
            }
            if (paymentDetails) paymentDetails.classList.add('hidden');
            if (paymentProofSection) paymentProofSection.classList.add('hidden');
            currentEvent = null;
        }
        
        function updatePaymentDetails(selectedMethod) {
            if (!selectedMethod) {
                if (paymentDetails) paymentDetails.classList.add('hidden');
                if (paymentProofSection) paymentProofSection.classList.add('hidden');
                return;
            }
            
            if (paymentDetails) paymentDetails.classList.remove('hidden');
            
            const selectedOption = paymentMethodSelect.querySelector(`option[value="${selectedMethod}"]`);
            if (!selectedOption) return;
            
            const bankName = document.getElementById('bankName');
            const accountNumber = document.getElementById('accountNumber');
            const accountHolder = document.getElementById('accountHolder');
            const paymentAmount = document.getElementById('paymentAmount');
            
            if (bankName) bankName.textContent = selectedMethod;
            if (accountNumber) accountNumber.textContent = selectedOption.getAttribute('data-account-number') || '-';
            if (accountHolder) accountHolder.textContent = selectedOption.getAttribute('data-account-name') || '-';
            
            if (currentEvent) {
                if (currentEvent.price > 0) {
                    if (paymentAmount) paymentAmount.textContent = 'Rp ' + parseFloat(currentEvent.price).toLocaleString('id-ID');
                    if (paymentProofSection) paymentProofSection.classList.remove('hidden');
                } else {
                    if (paymentAmount) paymentAmount.textContent = 'Gratis';
                    if (paymentProofSection) paymentProofSection.classList.add('hidden');
                }
            }
        }
        
        // 6. Success Modal Functions
        function showSuccessModal(message, code, paymentStatus = 'pending', whatsappMessage = '') {
            if (successMessage) successMessage.textContent = message;
            if (transactionCode) transactionCode.textContent = code;
            
            // Store WhatsApp message in hidden container
            if (whatsappMessage && whatsappData) {
                whatsappData.dataset.message = whatsappMessage;
            }
            
            // Determine if QR code should be shown based on payment status
            const showQRCode = paymentStatus === 'verified' || paymentStatus === 'paid';
            
            if (showQRCode) {
                if (qrCodeContainer) {
                    qrCodeContainer.classList.remove('hidden');
                    setTimeout(() => {
                        if (successQRCode && code) {
                            generateQRCode('successQRCode', code);
                        }
                    }, 500);
                }
                if (paymentPendingMessage) {
                    paymentPendingMessage.classList.add('hidden');
                }
                if (downloadButtonContainer) {
                    downloadButtonContainer.classList.remove('hidden');
                }
                if (whatsappCountdown) {
                    whatsappCountdown.classList.add('hidden');
                }
            } else {
                if (qrCodeContainer) {
                    qrCodeContainer.classList.add('hidden');
                }
                if (paymentPendingMessage) {
                    paymentPendingMessage.classList.remove('hidden');
                }
                if (downloadButtonContainer) {
                    downloadButtonContainer.classList.add('hidden');
                }
                if (whatsappCountdown) {
                    whatsappCountdown.classList.remove('hidden');
                    // Start WhatsApp countdown
                    startWhatsAppCountdown(whatsappMessage);
                }
            }
            
            if (successModal) {
                successModal.classList.remove('hidden');
                successModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }
        
        function closeSuccessModal() {
            // Clear WhatsApp timer
            if (whatsappTimer) {
                clearInterval(whatsappTimer);
                whatsappTimer = null;
            }
            
            if (successModal) {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
                document.body.style.overflow = 'auto';
            }
        }
        
        // 7. Download Ticket Function
        async function downloadTicket() {
            try {
                const ticketContent = document.querySelector('#successModal .bg-gray-50');
                if (!ticketContent) return;
                
                // Use html2canvas to capture the ticket
                const canvas = await html2canvas(ticketContent, {
                    scale: 2,
                    backgroundColor: '#f9fafb',
                    useCORS: true,
                    logging: false
                });
                
                // Convert canvas to data URL
                const dataURL = canvas.toDataURL('image/png');
                
                // Create download link
                const link = document.createElement('a');
                link.href = dataURL;
                link.download = `tiket-${transactionCode.textContent}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Show success message
                alert('Tiket berhasil didownload!');
            } catch (error) {
                console.error('Error downloading ticket:', error);
                alert('Gagal mendownload tiket. Silakan coba lagi.');
            }
        }
        
        // 8. Submit Registration Function - FIXED ERROR: Use stored event data
        async function submitRegistration(e) {
            e.preventDefault();
            
            // Get event data from form data attributes (safer than relying on currentEvent variable)
            const eventData = {
                name: registrationForm.dataset.eventName,
                date: registrationForm.dataset.eventDate,
                location: registrationForm.dataset.eventLocation
            };
            
            console.log('Form event data:', eventData);
            
            if (!eventData.name || !eventData.date || !eventData.location) {
                alert('Data event tidak tersedia. Silakan tutup form dan coba lagi.');
                return;
            }
            
            // Validate required fields
            const requiredFields = ['full_name', 'email', 'phone', 'gender', 'nik', 'address', 'payment_method'];
            for (const field of requiredFields) {
                const input = registrationForm.querySelector(`[name="${field}"]`);
                if (input && !input.value.trim()) {
                    alert(`Field ${field.replace('_', ' ')} wajib diisi`);
                    input.focus();
                    return;
                }
            }
            
            // Get event price from currentEvent or use default
            const eventPrice = currentEvent ? currentEvent.price : 0;
            
            // Validate file if event is paid
            const paymentProofInput = document.getElementById('payment_proof');
            if (eventPrice > 0 && paymentProofInput && !paymentProofInput.files[0]) {
                if (!confirm('Anda belum mengupload bukti pembayaran. Lanjutkan pendaftaran tanpa bukti?')) {
                    return;
                }
            }
            
            // Validate file size and type
            if (paymentProofInput && paymentProofInput.files[0]) {
                const file = paymentProofInput.files[0];
                const validTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!validTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan JPG, PNG, atau PDF.');
                    return;
                }
                
                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    return;
                }
            }
            
            // Tampilkan loading state
            const submitButton = registrationForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            submitButton.disabled = true;
            
            const formData = new FormData(registrationForm);
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    alert('Token CSRF tidak ditemukan');
                    submitButton.innerHTML = originalButtonText;
                    submitButton.disabled = false;
                    return;
                }
                
                const response = await fetch('/daftar', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (!response.ok) {
                    throw new Error(result.message || 'Terjadi kesalahan saat mendaftar');
                }
                
                if (result.success) {
                    closeRegistrationModal();
                    
                    // Prepare WhatsApp message
                    const participantData = {
                        full_name: formData.get('full_name'),
                        email: formData.get('email'),
                        phone: formData.get('phone'),
                        address: formData.get('address')
                    };
                    
                    console.log('Creating WhatsApp message with event data:', eventData);
                    
                    const whatsappMessage = composeWhatsAppMessage(
                        participantData,
                        eventData,
                        result.transaction_code
                    );
                    
                    // Show success modal with WhatsApp integration
                    showSuccessModal(
                        result.message, 
                        result.transaction_code, 
                        'pending', 
                        whatsappMessage
                    );
                    
                } else {
                    alert('Terjadi kesalahan: ' + result.message);
                    submitButton.innerHTML = originalButtonText;
                    submitButton.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mendaftar: ' + error.message);
                submitButton.innerHTML = originalButtonText;
                submitButton.disabled = false;
            }
        }
        
        // Test library availability
        console.log('qrcode library available:', typeof qrcode !== 'undefined' ? 'Yes' : 'No');
        console.log('html2canvas library available:', typeof html2canvas !== 'undefined' ? 'Yes' : 'No');
    });
    </script>
</body>
</html>