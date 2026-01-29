@extends('admin.layouts.admin.admin')

@section('title', 'Kelola Peserta')
@section('page-title', 'Kelola Peserta')
@section('page-subtitle', 'Lihat dan kelola semua peserta')

@push('head')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header dengan filter -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Peserta</h2>
            <p class="text-gray-600">Kelola semua peserta dari semua event</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
            <!-- Search -->
            <form method="GET" action="{{ route('admin.peserta.index') }}" class="relative">
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari peserta..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </form>
            
            <!-- Scan Barcode Button -->
            <button id="scanBarcodeBtn" 
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center justify-center">
                <i class="fas fa-barcode mr-2"></i> Scan Barcode
            </button>
            
            <!-- Filter Button -->
            <button id="toggleFilterBtn" 
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center justify-center">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
        </div>
    </div>

    <!-- Filter Panel -->
    <div id="filterPanel" class="bg-white rounded-xl shadow-lg p-6 hidden">
        <form method="GET" action="{{ route('admin.peserta.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Sudah Bayar</option>
                    <option value="verified" {{ request('payment_status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                </select>
            </div>
            
            <!-- Event Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event</label>
                <select name="event_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Event</option>
                    @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                        {{ $event->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dari</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sampai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Action Buttons -->
            <div class="md:col-span-2 lg:col-span-4 flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.peserta.index') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Reset
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Peserta</p>
                    <p class="text-2xl md:text-3xl font-bold">{{ $totalParticipants }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Menunggu Bayar</p>
                    <p class="text-2xl md:text-3xl font-bold">{{ $pendingCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div>
                    <p class="text-blue-100 text-sm font-medium">Sudah Bayar</p>
                    <p class="text-2xl md:text-3xl font-bold">{{ $paidCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-green-100 text-sm font-medium">Terverifikasi</p>
                    <p class="text-2xl md:text-3xl font-bold">{{ $verifiedCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Participants -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="participantsTableBody">
                    @forelse($participants as $participant)
                    <tr class="hover:bg-gray-50 transition-colors duration-150" data-participant-id="{{ $participant->id }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3 
                                    @if($participant->payment_status == 'verified') bg-gradient-to-r from-green-100 to-green-200
                                    @elseif($participant->payment_status == 'paid') bg-gradient-to-r from-blue-100 to-blue-200
                                    @else bg-gradient-to-r from-yellow-100 to-yellow-200 @endif">
                                    <i class="fas fa-user 
                                        @if($participant->payment_status == 'verified') text-green-600
                                        @elseif($participant->payment_status == 'paid') text-blue-600
                                        @else text-yellow-600 @endif">
                                    </i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $participant->full_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $participant->email }}</p>
                                    <p class="text-xs text-gray-400">{{ $participant->transaction_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">
                                {{ $participant->event->name ?? 'Event tidak ditemukan' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                @if($participant->event)
                                    {{ $participant->event->date->format('d M Y') }}
                                @endif
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $participant->payment_method }}</p>
                            @if($participant->event && $participant->event->price > 0)
                            <p class="text-sm text-blue-600 font-medium">
                                Rp {{ number_format($participant->event->price, 0, ',', '.') }}
                            </p>
                            @endif
                        </td>
                        <td class="px-6 py-4 status-cell">
                            @if($participant->payment_status == 'verified')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-green-100 to-green-200 text-green-800">
                                    <i class="fas fa-check-circle mr-1.5"></i>
                                    Terverifikasi
                                </span>
                            @elseif($participant->payment_status == 'paid')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800">
                                    <i class="fas fa-money-bill-wave mr-1.5"></i>
                                    Sudah Bayar
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800">
                                    <i class="fas fa-clock mr-1.5"></i>
                                    Menunggu
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 action-buttons">
                            <div class="flex space-x-2">
                                <button data-id="{{ $participant->id }}" class="view-detail-btn px-3 py-1.5 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:from-gray-200 hover:to-gray-300 transition-all duration-200 shadow-sm hover:shadow text-sm">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </button>
                                <button data-id="{{ $participant->id }}" class="verify-btn px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow text-sm">
                                    <i class="fas fa-edit mr-1"></i> Verifikasi
                                </button>
                                <button data-id="{{ $participant->id }}" class="delete-btn px-3 py-1.5 bg-gradient-to-r from-red-100 to-red-200 text-red-700 rounded-lg hover:from-red-200 hover:to-red-300 transition-all duration-200 shadow-sm hover:shadow text-sm">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-users-slash text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada peserta</h3>
                            <p class="text-gray-500">Tidak ada peserta yang ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($participants->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $participants->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Participant Detail Modal -->
<div id="participantModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Detail Peserta</h3>
                <button id="closeParticipantModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="participantDetailContent">
                <!-- Content akan diisi oleh JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div id="verificationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Verifikasi Pembayaran</h3>
                <button id="closeVerificationModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="verificationForm">
                @csrf
                <input type="hidden" id="verificationParticipantId" name="participant_id">
                
                <div class="mb-6">
                    <div id="participantInfo" class="bg-gray-50 p-4 rounded-lg mb-4">
                        <!-- Info peserta akan diisi -->
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Status Pembayaran:</label>
                        <select id="paymentStatus" name="payment_status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pending">Menunggu Pembayaran</option>
                            <option value="paid">Sudah Bayar</option>
                            <option value="verified">Terverifikasi</option>
                        </select>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 mb-2">Catatan:</label>
                        <textarea id="verificationNotes" name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tambahkan catatan jika perlu..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <button type="button" id="cancelVerification" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 font-semibold">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus Peserta</h3>
                <p class="text-gray-600">Apakah Anda yakin ingin menghapus peserta ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <form id="deleteForm">
                @csrf
                @method('DELETE')
                <input type="hidden" id="deleteParticipantId" name="participant_id">
                <div class="flex justify-end space-x-4">
                    <button type="button" id="cancelDelete" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 font-semibold">
                        <i class="fas fa-trash mr-2"></i> Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Barcode Scanner Modal -->
<div id="barcodeScannerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Scan Barcode Peserta</h3>
                <button id="closeBarcodeScanner" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="mb-6">
                <p class="text-gray-600 mb-4">Arahkan kamera ke barcode QR Code peserta atau masukkan kode transaksi secara manual:</p>
                
                <!-- Manual input -->
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Masukkan kode transaksi:</label>
                    <div class="flex gap-2">
                        <input type="text" id="manualBarcodeInput" 
                               placeholder="Masukkan kode transaksi"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               autocomplete="off">
                        <button id="searchBarcodeBtn" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
                
                <!-- QR Code Reader -->
                <div class="mb-6 hidden" id="qrReaderContainer">
                    <div id="qr-reader" style="width: 100%"></div>
                </div>
                
                <!-- Toggle QR Reader -->
                <div class="mb-6">
                    <button id="toggleQRReader" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 w-full">
                        <i class="fas fa-camera mr-2"></i> Aktifkan QR Code Scanner
                    </button>
                </div>
                
                <!-- Scan results -->
                <div id="scanResult" class="hidden">
                    <div id="scanResultContent" class="bg-gray-50 p-4 rounded-lg">
                        <!-- Scan result will be displayed here -->
                    </div>
                </div>
                
                <!-- Loading indicator -->
                <div id="scanLoading" class="hidden text-center py-4">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="text-gray-600 mt-2">Memproses scan...</p>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button id="closeScannerBtn" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div id="emailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Kirim Email ke Peserta</h3>
                <button id="closeEmailModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="emailForm">
                @csrf
                <input type="hidden" id="emailParticipantId" name="participant_id">
                
                <div class="mb-6">
                    <div id="emailParticipantInfo" class="bg-gray-50 p-4 rounded-lg mb-4">
                        <!-- Info peserta akan diisi -->
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Jenis Email:</label>
                        <select id="emailType" name="email_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="confirmation">Konfirmasi Pendaftaran</option>
                            <option value="reminder">Pengingat Event</option>
                            <option value="information">Informasi Penting</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Subjek Email:</label>
                        <input type="text" id="emailSubject" name="subject" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="Masukkan subjek email" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Pesan:</label>
                        <textarea id="emailMessage" name="message" rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                  placeholder="Tulis pesan yang ingin disampaikan..." required></textarea>
                    </div>
                    
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" id="sendQrcode" name="send_qrcode" value="1" checked class="mr-2">
                            <span class="text-gray-700">Sertakan QR Code</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1">QR Code akan berisi kode transaksi unik peserta</p>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 mb-2">Catatan Internal:</label>
                        <textarea id="emailNotes" name="notes" rows="2" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                  placeholder="Catatan untuk internal admin..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <button type="button" id="cancelEmail" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 font-semibold">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .animate-pulse {
        animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }
    
    #qr-reader {
        width: 100%;
    }
    
    #qr-reader__dashboard_section {
        padding: 10px;
    }
    
    #qr-reader__camera_selection {
        width: 100%;
        margin-bottom: 10px;
    }
    
    .image-preview {
        max-height: 400px;
        object-fit: contain;
        border-radius: 8px;
        border: 2px dashed #ddd;
    }
    
    .proof-image-container {
        position: relative;
        background: #f8f9fa;
        border-radius: 10px;
        overflow: hidden;
        padding: 15px;
    }
    
    .proof-image-loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }
    
    .proof-image-error {
        background: #fff5f5;
        border: 1px solid #fed7d7;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // CSRF Token untuk AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Element references
    const participantModal = document.getElementById('participantModal');
    const verificationModal = document.getElementById('verificationModal');
    const deleteModal = document.getElementById('deleteModal');
    const barcodeScannerModal = document.getElementById('barcodeScannerModal');
    const emailModal = document.getElementById('emailModal');
    
    // QR Code Scanner Variables
    let html5QrcodeScanner = null;
    let isScannerActive = false;
    
    // ==================== FUNGSI CLOSE MODAL ====================
    function closeParticipantModal() {
        if (participantModal) {
            participantModal.classList.add('hidden');
            participantModal.classList.remove('flex');
        }
    }
    
    function closeVerificationModal() {
        if (verificationModal) {
            verificationModal.classList.add('hidden');
            verificationModal.classList.remove('flex');
        }
    }
    
    function closeDeleteModal() {
        if (deleteModal) {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        }
    }
    
    function closeBarcodeScanner() {
        if (barcodeScannerModal) {
            barcodeScannerModal.classList.add('hidden');
            barcodeScannerModal.classList.remove('flex');
            stopQRCodeScanner();
        }
        const scanResult = document.getElementById('scanResult');
        const scanLoading = document.getElementById('scanLoading');
        const manualBarcodeInput = document.getElementById('manualBarcodeInput');
        const qrReaderContainer = document.getElementById('qrReaderContainer');
        const toggleQRReaderBtn = document.getElementById('toggleQRReader');
        
        if (scanResult) scanResult.classList.add('hidden');
        if (scanLoading) scanLoading.classList.add('hidden');
        if (manualBarcodeInput) manualBarcodeInput.value = '';
        if (qrReaderContainer) qrReaderContainer.classList.add('hidden');
        if (toggleQRReaderBtn) toggleQRReaderBtn.innerHTML = '<i class="fas fa-camera mr-2"></i> Aktifkan QR Code Scanner';
        
        isScannerActive = false;
    }
    
    function closeEmailModal() {
        if (emailModal) {
            emailModal.classList.add('hidden');
            emailModal.classList.remove('flex');
        }
    }
    
    // ==================== FUNGSI BUKA MODAL ====================
    function openDeleteModal(participantId) {
        document.getElementById('deleteParticipantId').value = participantId;
        deleteModal.classList.remove('hidden');
        deleteModal.classList.add('flex');
    }
    
    function openBarcodeScanner() {
        barcodeScannerModal.classList.remove('hidden');
        barcodeScannerModal.classList.add('flex');
        const manualBarcodeInput = document.getElementById('manualBarcodeInput');
        if (manualBarcodeInput) manualBarcodeInput.focus();
    }
    
    async function openEmailModal(participantId) {
        await loadParticipantForEmail(participantId);
    }
    
    async function openVerificationModal(participantId) {
        try {
            showLoading('Memuat data peserta...');
            
            const response = await fetch(`/admin/peserta/${participantId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Gagal memuat data peserta');
            }
            
            const participant = data.participant;
            
            // Isi informasi peserta
            document.getElementById('participantInfo').innerHTML = `
                <h4 class="font-semibold mb-2">Detail Peserta:</h4>
                <p class="text-sm"><strong>Nama:</strong> ${participant.full_name || '-'}</p>
                <p class="text-sm"><strong>Email:</strong> ${participant.email || '-'}</p>
                <p class="text-sm"><strong>Event:</strong> ${participant.event?.name || 'Event tidak ditemukan'}</p>
                <p class="text-sm"><strong>Kode Transaksi:</strong> <code>${participant.transaction_code || '-'}</code></p>
                ${participant.payment_proof_url ? `
                <p class="text-sm">
                    <strong>Bukti Bayar:</strong> 
                    <a href="${participant.payment_proof_url}" target="_blank" class="text-blue-600 underline ml-2">
                        <i class="fas fa-external-link-alt mr-1"></i>Lihat Bukti
                    </a>
                </p>` : ''}
            `;
            
            // Set form data
            document.getElementById('verificationParticipantId').value = participantId;
            document.getElementById('paymentStatus').value = participant.payment_status || 'pending';
            document.getElementById('verificationNotes').value = participant.notes || '';
            
            hideLoading();
            verificationModal.classList.remove('hidden');
            verificationModal.classList.add('flex');
            
        } catch (error) {
            hideLoading();
            console.error('Error opening verification modal:', error);
            showNotification('Gagal membuka modal verifikasi: ' + error.message, 'error');
        }
    }
    
    // ==================== FUNGSI UTAMA ====================
    
    // Fungsi untuk menampilkan detail peserta dengan bukti transfer
    async function viewParticipantDetail(participantId) {
        try {
            showLoading('Memuat detail peserta...');
            
            const response = await fetch(`/admin/peserta/${participantId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Gagal memuat data peserta');
            }
            
            const participant = data.participant;
            
            // Format bukti pembayaran dengan path yang benar
            let proofHtml = '';
            if (participant.payment_proof_url) {
                // Mendapatkan nama file dari path
                const fileName = participant.payment_proof.split('/').pop() || 'bukti-transfer.jpg';
                
                proofHtml = `
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-700 mb-3">Bukti Pembayaran</h4>
                        <div class="proof-image-container">
                            <div id="proofImageLoading" class="proof-image-loading">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                                <p class="ml-3 text-gray-600">Memuat gambar...</p>
                            </div>
                            
                            <div id="proofImageError" class="proof-image-error hidden">
                                <i class="fas fa-exclamation-triangle text-2xl text-red-500 mb-3"></i>
                                <p class="text-gray-700 mb-2">Gagal memuat bukti pembayaran</p>
                                <button onclick="retryLoadProofImage('${participant.payment_proof_url}', ${participant.id})" 
                                        class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                                    <i class="fas fa-redo mr-2"></i>Coba Lagi
                                </button>
                            </div>
                            
                            <img id="proofImage-${participant.id}" 
                                 src="${participant.payment_proof_url}" 
                                 alt="Bukti Pembayaran ${participant.full_name}" 
                                 class="w-full image-preview hidden"
                                 onload="document.getElementById('proofImageLoading').style.display='none'; this.classList.remove('hidden');"
                                 onerror="handleProofImageError(this, '${participant.payment_proof_url}', ${participant.id})">
                            
                            <div class="mt-4 flex flex-wrap gap-3 justify-center">
                                <button onclick="openProofImage('${participant.payment_proof_url}')" 
                                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700">
                                    <i class="fas fa-expand mr-2"></i> Buka Fullscreen
                                </button>
                                <button onclick="downloadProofImage('${participant.payment_proof_url}', 'bukti-transfer-${participant.transaction_code}')" 
                                        class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700">
                                    <i class="fas fa-download mr-2"></i> Download
                                </button>
                            </div>
                            
                            <div class="mt-4 text-sm text-gray-500 text-center">
                                <p><strong>File:</strong> ${fileName}</p>
                                <p><strong>Path:</strong> <code class="text-xs">${participant.payment_proof}</code></p>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                proofHtml = `
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-700 mb-3">Bukti Pembayaran</h4>
                        <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200 text-center">
                            <i class="fas fa-receipt text-3xl text-yellow-500 mb-3"></i>
                            <p class="text-yellow-700">Bukti pembayaran belum diunggah</p>
                            <p class="text-sm text-yellow-600 mt-1">Peserta belum mengirim bukti pembayaran</p>
                        </div>
                    </div>
                `;
            }
            
            // Format data untuk ditampilkan
            const html = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Info -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3">Informasi Pribadi</h4>
                        <div class="space-y-3">
                            <div class="flex">
                                <span class="w-32 text-gray-600">Nama Lengkap:</span>
                                <span class="font-medium">${participant.full_name || '-'}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-gray-600">Email:</span>
                                <span>${participant.email || '-'}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-gray-600">Telepon:</span>
                                <span>${participant.phone || '-'}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-gray-600">Jenis Kelamin:</span>
                                <span>${participant.gender || '-'}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-gray-600">NIK:</span>
                                <span>${participant.nik || '-'}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-gray-600">Alamat:</span>
                                <span class="text-sm">${participant.address || '-'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Registration Info -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-3">Informasi Pendaftaran</h4>
                        <div class="space-y-3">
                            <div class="flex">
                                <span class="w-32 text-gray-600">Event:</span>
                                <span class="font-medium">${participant.event?.name || 'Event tidak ditemukan'}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-gray-600">Kode Transaksi:</span>
                                <span><code class="bg-gray-100 px-2 py-1 rounded">${participant.transaction_code || '-'}</code></span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-gray-600">Metode Pembayaran:</span>
                                <span>${participant.payment_method || '-'}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-gray-600">Status:</span>
                                <span class="font-semibold participant-status ${getStatusColor(participant.payment_status)}">
                                    ${participant.payment_status_text || 'Menunggu Pembayaran'}
                                </span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-gray-600">Tanggal Daftar:</span>
                                <span>${participant.created_at ? new Date(participant.created_at).toLocaleString('id-ID') : '-'}</span>
                            </div>
                            ${participant.email_notification_sent ? `
                            <div class="flex">
                                <span class="w-32 text-gray-600">Email Terakhir:</span>
                                <span class="text-sm">${participant.email_sent_at ? new Date(participant.email_sent_at).toLocaleString('id-ID') : '-'}</span>
                            </div>
                            ` : ''}
                            ${participant.notes ? `
                            <div class="flex">
                                <span class="w-32 text-gray-600">Catatan:</span>
                                <span class="text-sm">${participant.notes}</span>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
                
                <!-- QR Code Display -->
                <div class="mt-6">
                    <h4 class="font-semibold text-gray-700 mb-3">QR Code Check-in</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div id="qrcode-container" class="flex justify-center mb-4"></div>
                        <p class="text-center text-sm text-gray-600">Kode Transaksi: <code>${participant.transaction_code}</code></p>
                        <div class="text-center mt-4">
                            <button onclick="downloadQRCode('${participant.transaction_code}', '${participant.full_name}')" 
                                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 text-sm">
                                <i class="fas fa-download mr-2"></i> Download QR Code
                            </button>
                        </div>
                    </div>
                </div>
                
                ${proofHtml}
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex flex-wrap gap-3">
                        <button onclick="openEmailModal('${participant.id}')" 
                                class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700">
                            <i class="fas fa-envelope mr-2"></i> Kirim Email
                        </button>
                        <button onclick="testBrevoEmail('${participant.id}')" 
                                class="px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700">
                            <i class="fas fa-bolt mr-2"></i> Test Brevo
                        </button>
                        <button onclick="openVerificationModal('${participant.id}')" 
                                class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700">
                            <i class="fas fa-edit mr-2"></i> Ubah Status
                        </button>
                        <button onclick="openDeleteModal('${participant.id}')" 
                                class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700">
                            <i class="fas fa-trash mr-2"></i> Hapus Peserta
                        </button>
                    </div>
                </div>
            `;
            
            document.getElementById('participantDetailContent').innerHTML = html;
            hideLoading();
            participantModal.classList.remove('hidden');
            participantModal.classList.add('flex');
            
            // Generate QR Code setelah konten dimuat
            setTimeout(() => {
                generateQRCode(participant.transaction_code, 'qrcode-container');
            }, 100);
            
        } catch (error) {
            hideLoading();
            console.error('Error loading participant detail:', error);
            showNotification('Gagal memuat detail peserta: ' + error.message, 'error');
        }
    }
    
    // Fungsi untuk load participant untuk email
    async function loadParticipantForEmail(participantId) {
        try {
            showLoading('Memuat data peserta...');
            
            const response = await fetch(`/admin/peserta/${participantId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Gagal memuat data peserta');
            }
            
            const participant = data.participant;
            
            // Isi informasi peserta
            document.getElementById('emailParticipantInfo').innerHTML = `
                <h4 class="font-semibold mb-2">Penerima Email:</h4>
                <p class="text-sm"><strong>Nama:</strong> ${participant.full_name || '-'}</p>
                <p class="text-sm"><strong>Email:</strong> ${participant.email || '-'}</p>
                <p class="text-sm"><strong>Event:</strong> ${participant.event?.name || 'Tidak tersedia'}</p>
                <p class="text-sm"><strong>Kode Transaksi:</strong> <code>${participant.transaction_code || '-'}</code></p>
                ${participant.email_notification_sent ? `<p class="text-sm text-green-600"><i class="fas fa-check-circle mr-1"></i>Email sudah pernah dikirim pada ${participant.email_sent_at ? new Date(participant.email_sent_at).toLocaleString('id-ID') : '-'}</p>` : ''}
            `;
            
            // Set form data
            document.getElementById('emailParticipantId').value = participantId;
            
            // Set default subject berdasarkan jenis email
            const emailType = document.getElementById('emailType');
            const emailSubject = document.getElementById('emailSubject');
            const emailMessage = document.getElementById('emailMessage');
            
            const eventName = participant.event?.name || 'Event';
            const defaultSubjects = {
                'confirmation': `Konfirmasi Pendaftaran - ${eventName}`,
                'reminder': `Pengingat Event - ${eventName}`,
                'information': `Informasi Penting - ${eventName}`,
                'custom': `Pesan dari Panitia - ${eventName}`
            };
            
            emailType.addEventListener('change', function() {
                emailSubject.value = defaultSubjects[this.value] || '';
                
                // Update message based on type
                const defaultMessages = {
                    'confirmation': `Halo ${participant.full_name},\n\nTerima kasih telah mendaftar pada event ${eventName}. Berikut adalah detail pendaftaran Anda:\n\n- Kode Transaksi: ${participant.transaction_code}\n- Nama: ${participant.full_name}\n- Email: ${participant.email}\n\nSimpan email ini sebagai bukti pendaftaran Anda.`,
                    'reminder': `Halo ${participant.full_name},\n\nIni adalah pengingat untuk event ${eventName} yang akan datang. Pastikan Anda datang tepat waktu.\n\nKode transaksi Anda: ${participant.transaction_code}`,
                    'information': `Halo ${participant.full_name},\n\nBerikut adalah informasi penting terkait event ${eventName}.\n\nKode transaksi Anda: ${participant.transaction_code}`,
                    'custom': `Halo ${participant.full_name},\n\nBerikut adalah pesan dari panitia event ${eventName}.\n\nKode transaksi Anda: ${participant.transaction_code}`
                };
                
                emailMessage.value = defaultMessages[this.value] || '';
            });
            
            // Set initial values
            emailSubject.value = defaultSubjects[emailType.value] || '';
            
            const defaultMessages = {
                'confirmation': `Halo ${participant.full_name},\n\nTerima kasih telah mendaftar pada event ${eventName}. Berikut adalah detail pendaftaran Anda:\n\n- Kode Transaksi: ${participant.transaction_code}\n- Nama: ${participant.full_name}\n- Email: ${participant.email}\n\nSimpan email ini sebagai bukti pendaftaran Anda.`,
                'reminder': `Halo ${participant.full_name},\n\nIni adalah pengingat untuk event ${eventName} yang akan datang. Pastikan Anda datang tepat waktu.\n\nKode transaksi Anda: ${participant.transaction_code}`,
                'information': `Halo ${participant.full_name},\n\nBerikut adalah informasi penting terkait event ${eventName}.\n\nKode transaksi Anda: ${participant.transaction_code}`,
                'custom': `Halo ${participant.full_name},\n\nBerikut adalah pesan dari panitia event ${eventName}.\n\nKode transaksi Anda: ${participant.transaction_code}`
            };
            
            emailMessage.value = defaultMessages[emailType.value] || '';
            
            hideLoading();
            emailModal.classList.remove('hidden');
            emailModal.classList.add('flex');
            
        } catch (error) {
            hideLoading();
            console.error('Error opening email modal:', error);
            showNotification('Gagal membuka modal email: ' + error.message, 'error');
        }
    }
    
    // ==================== FUNGSI VERIFIKASI - FIXED ====================
    
    // Fungsi untuk submit verifikasi
    async function submitVerification(e) {
        e.preventDefault();
        
        const participantId = document.getElementById('verificationParticipantId').value;
        const formData = new FormData(document.getElementById('verificationForm'));
        
        // Tampilkan loading
        showLoading('Menyimpan perubahan...');
        
        try {
            // Gunakan route khusus untuk update status dengan POST
            const response = await fetch(`/admin/peserta/${participantId}/update-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Gagal menyimpan perubahan');
            }
            
            hideLoading();
            showNotification(data.message || 'Status berhasil diperbarui', 'success');
            
            // Update row in table - FIX: check if data exists
            if (data.payment_status) {
                updateParticipantRow(participantId, data.payment_status);
            }
            
            // Update participant modal if open
            if (!participantModal.classList.contains('hidden')) {
                const statusElement = document.querySelector('#participantDetailContent .participant-status');
                if (statusElement && data.payment_status_text) {
                    statusElement.textContent = data.payment_status_text;
                    statusElement.className = `font-semibold participant-status ${getStatusColor(data.payment_status)}`;
                }
            }
            
            // Update scan result modal if open
            const scanResult = document.getElementById('scanResult');
            if (scanResult && !scanResult.classList.contains('hidden')) {
                // Refresh scan result
                const manualBarcodeInput = document.getElementById('manualBarcodeInput');
                if (manualBarcodeInput && manualBarcodeInput.value.trim()) {
                    searchParticipantByTransactionCode(manualBarcodeInput.value.trim());
                }
            }
            
            closeVerificationModal();
            
        } catch (error) {
            hideLoading();
            console.error('Error saving verification:', error);
            showNotification('Gagal menyimpan perubahan: ' + error.message, 'error');
        }
    }
    
    // Fungsi untuk update row di tabel - FIXED VERSION
    function updateParticipantRow(participantId, paymentStatus) {
        // Find the row
        const row = document.querySelector(`tr[data-participant-id="${participantId}"]`);
        if (!row) return;
        
        // Update status cell berdasarkan payment_status (not text)
        const statusCell = row.querySelector('.status-cell');
        if (statusCell) {
            let badgeHtml = '';
            
            if (paymentStatus === 'verified') {
                badgeHtml = `
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-green-100 to-green-200 text-green-800">
                        <i class="fas fa-check-circle mr-1.5"></i>
                        Terverifikasi
                    </span>
                `;
            } else if (paymentStatus === 'paid') {
                badgeHtml = `
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800">
                        <i class="fas fa-money-bill-wave mr-1.5"></i>
                        Sudah Bayar
                    </span>
                `;
            } else {
                badgeHtml = `
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800">
                        <i class="fas fa-clock mr-1.5"></i>
                        Menunggu
                    </span>
                `;
            }
            
            statusCell.innerHTML = badgeHtml;
        }
    }
    
    // Fungsi untuk submit delete
    async function submitDelete(e) {
        e.preventDefault();
        
        const participantId = document.getElementById('deleteParticipantId').value;
        const formData = new FormData(document.getElementById('deleteForm'));
        
        showLoading('Menghapus peserta...');
        
        try {
            const response = await fetch(`/admin/peserta/${participantId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Gagal menghapus peserta');
            }
            
            hideLoading();
            showNotification(data.message || 'Peserta berhasil dihapus', 'success');
            
            // Remove row from table
            const row = document.querySelector(`tr[data-participant-id="${participantId}"]`);
            if (row) {
                row.remove();
            }
            
            closeDeleteModal();
            
        } catch (error) {
            hideLoading();
            console.error('Error deleting participant:', error);
            showNotification('Gagal menghapus peserta: ' + error.message, 'error');
            
            closeDeleteModal();
        }
    }
    
    // Fungsi untuk submit email
    async function submitEmail(e) {
        e.preventDefault();
        
        const participantId = document.getElementById('emailParticipantId').value;
        const formData = new FormData(document.getElementById('emailForm'));
        
        showLoading('Mengirim email...');
        
        try {
            // Gunakan route yang benar untuk send-email
            const response = await fetch(`/admin/peserta/${participantId}/send-email`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Gagal mengirim email');
            }
            
            hideLoading();
            showNotification(data.message || 'Email berhasil dikirim', 'success');
            
            closeEmailModal();
            
        } catch (error) {
            hideLoading();
            console.error('Error sending email:', error);
            showNotification('Gagal mengirim email: ' + error.message, 'error');
        }
    }
    
    // ==================== FUNGSI BARCODE SCANNER - FIXED ====================
    
    // Fungsi untuk toggle QR reader
    document.getElementById('toggleQRReader')?.addEventListener('click', function() {
        const qrReaderContainer = document.getElementById('qrReaderContainer');
        if (qrReaderContainer.classList.contains('hidden')) {
            startQRCodeScanner();
            qrReaderContainer.classList.remove('hidden');
            this.innerHTML = '<i class="fas fa-stop mr-2"></i> Matikan QR Code Scanner';
            isScannerActive = true;
        } else {
            stopQRCodeScanner();
            qrReaderContainer.classList.add('hidden');
            this.innerHTML = '<i class="fas fa-camera mr-2"></i> Aktifkan QR Code Scanner';
            isScannerActive = false;
        }
    });
    
    // Fungsi untuk memulai QR code scanner
    function startQRCodeScanner() {
        if (!html5QrcodeScanner && typeof Html5QrcodeScanner !== 'undefined') {
            html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader",
                { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                false
            );
        }
        
        if (html5QrcodeScanner) {
            html5QrcodeScanner.render(
                function(decodedText) {
                    // Ketika QR code terdeteksi
                    handleBarcodeScan(decodedText);
                },
                function(error) {
                    // console.log(`QR Code scan error: ${error}`);
                }
            );
        }
    }
    
    // Fungsi untuk menghentikan QR code scanner
    function stopQRCodeScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
    }
    
    // Fungsi untuk menangani scan barcode
    function handleBarcodeScan(transactionCode) {
        if (!transactionCode || transactionCode.trim() === '') return;
        
        document.getElementById('manualBarcodeInput').value = transactionCode;
        searchParticipantByTransactionCode(transactionCode);
    }
    
    // Fungsi untuk mencari peserta berdasarkan kode transaksi - FIXED
    async function searchParticipantByTransactionCode(transactionCode) {
        if (!transactionCode || transactionCode.trim() === '') {
            showNotification('Masukkan kode transaksi', 'warning');
            return;
        }
        
        showScanLoading();
        
        try {
            // Gunakan route yang benar dari Laravel
            const response = await fetch(`/admin/peserta/search-by-code?transaction_code=${encodeURIComponent(transactionCode)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            hideScanLoading();
            
            if (data.success) {
                displayScanResult(data.participant);
            } else {
                showNotification(data.message || 'Peserta tidak ditemukan', 'error');
                document.getElementById('scanResult').classList.add('hidden');
            }
            
        } catch (error) {
            hideScanLoading();
            console.error('Error searching participant:', error);
            showNotification('Terjadi kesalahan saat mencari peserta: ' + error.message, 'error');
        }
    }
    
    // Fungsi untuk menampilkan hasil scan
    function displayScanResult(participant) {
        const scanResult = document.getElementById('scanResult');
        const scanResultContent = document.getElementById('scanResultContent');
        
        const statusColor = participant.payment_status === 'verified' ? 'text-green-600' : 
                           participant.payment_status === 'paid' ? 'text-blue-600' : 'text-yellow-600';
        const statusBgColor = participant.payment_status === 'verified' ? 'bg-green-100' : 
                             participant.payment_status === 'paid' ? 'bg-blue-100' : 'bg-yellow-100';
        
        // Link bukti transfer jika ada
        let proofLink = '';
        if (participant.payment_proof_url) {
            proofLink = `
                <div class="mt-2">
                    <a href="${participant.payment_proof_url}" target="_blank" 
                       class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 text-sm">
                        <i class="fas fa-receipt mr-2"></i> Lihat Bukti Transfer
                    </a>
                </div>
            `;
        }
        
        scanResultContent.innerHTML = `
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-lg text-green-600">
                        <i class="fas fa-check-circle mr-2"></i> Peserta Ditemukan
                    </h4>
                    <span class="px-3 py-1 rounded-full text-sm font-medium ${statusColor} bg-opacity-20 ${statusBgColor}">
                        ${participant.payment_status_text}
                    </span>
                </div>
                
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg mr-3">
                            ${participant.full_name.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">${participant.full_name}</p>
                            <p class="text-sm text-gray-600">${participant.email}</p>
                            <p class="text-xs text-gray-500">${participant.phone || '-'}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                        <div>
                            <p class="text-gray-500">Event</p>
                            <p class="font-medium">${participant.event?.name || 'Tidak tersedia'}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Kode Transaksi</p>
                            <p class="font-medium font-mono text-blue-600">${participant.transaction_code}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Metode Bayar</p>
                            <p class="font-medium">${participant.payment_method || '-'}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tanggal Daftar</p>
                            <p class="font-medium">${new Date(participant.created_at).toLocaleDateString('id-ID')}</p>
                        </div>
                    </div>
                    
                    ${proofLink}
                </div>
                
                
                <div class="text-center pt-3 border-t border-gray-100">
                    <button onclick="viewParticipantDetail(${participant.id})" 
                            class="text-sm text-blue-600 hover:text-blue-800">
                        <i class="fas fa-external-link-alt mr-1"></i> Buka Halaman Detail Lengkap
                    </button>
                </div>
            </div>
        `;
        
        scanResult.classList.remove('hidden');
    }
    
    // Helper functions untuk scan loading
    function showScanLoading() {
        document.getElementById('scanLoading').classList.remove('hidden');
        document.getElementById('scanResult').classList.add('hidden');
    }
    
    function hideScanLoading() {
        document.getElementById('scanLoading').classList.add('hidden');
    }
    
    // ==================== FUNGSI BUKTI TRANSFER ====================
    
    // Fungsi untuk menangani error gambar bukti transfer
    function handleProofImageError(imgElement, proofUrl, participantId) {
        const loadingElement = document.getElementById('proofImageLoading');
        const errorElement = document.getElementById('proofImageError');
        
        if (loadingElement) loadingElement.style.display = 'none';
        if (errorElement) errorElement.classList.remove('hidden');
        imgElement.style.display = 'none';
        
        console.error('Gagal memuat gambar bukti transfer:', proofUrl);
    }
    
    // Fungsi untuk mencoba ulang memuat gambar
    function retryLoadProofImage(proofUrl, participantId) {
        const imgElement = document.getElementById(`proofImage-${participantId}`);
        const loadingElement = document.getElementById('proofImageLoading');
        const errorElement = document.getElementById('proofImageError');
        
        if (errorElement) errorElement.classList.add('hidden');
        if (loadingElement) loadingElement.style.display = 'flex';
        
        // Tambahkan timestamp untuk menghindari cache
        const newProofUrl = proofUrl + (proofUrl.includes('?') ? '&' : '?') + 't=' + Date.now();
        imgElement.src = newProofUrl;
        imgElement.classList.add('hidden');
    }
    
    // Fungsi untuk membuka gambar fullscreen
    function openProofImage(imageUrl) {
        const modalHtml = `
            <div id="fullscreenModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4">
                <div class="relative max-w-4xl w-full max-h-[90vh]">
                    <button onclick="closeFullscreenModal()" 
                            class="absolute top-4 right-4 z-10 bg-red-500 text-white p-2 rounded-full hover:bg-red-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <div class="bg-black p-4 rounded-lg overflow-auto max-h-[85vh]">
                        <img src="${imageUrl}" 
                             alt="Bukti Transfer Fullscreen" 
                             class="w-full h-auto object-contain">
                    </div>
                    <div class="flex justify-center mt-4 space-x-4">
                        <button onclick="downloadProofImage('${imageUrl}', 'bukti-transfer-fullscreen')" 
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            <i class="fas fa-download mr-2"></i> Download
                        </button>
                        <button onclick="closeFullscreenModal()" 
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            <i class="fas fa-times mr-2"></i> Tutup
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        const modal = document.createElement('div');
        modal.innerHTML = modalHtml;
        document.body.appendChild(modal);
    }
    
    function closeFullscreenModal() {
        const modal = document.getElementById('fullscreenModal');
        if (modal) {
            modal.remove();
        }
    }
    
    // Fungsi untuk download gambar
    function downloadProofImage(imageUrl, fileName) {
        const link = document.createElement('a');
        link.href = imageUrl;
        link.download = fileName || 'bukti-transfer.jpg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showNotification('Download bukti transfer berhasil', 'success');
    }
    
    // ==================== FUNGSI TEST BREVO ====================
    
    // Fungsi untuk test email Brevo
    async function testBrevoEmail(participantId) {
        if (!confirm('Kirim email testing via Brevo ke peserta ini?\n\nIni akan mengirim email test untuk memverifikasi koneksi Brevo.')) {
            return;
        }
        
        showLoading('Mengirim email test via Brevo...');
        
        try {
            const response = await fetch(`/admin/peserta/${participantId}/test-brevo`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Gagal mengirim test email via Brevo');
            }
            
            hideLoading();
            showNotification(data.message, 'success');
            
        } catch (error) {
            hideLoading();
            console.error('Error sending Brevo test email:', error);
            showNotification('Gagal mengirim test email via Brevo: ' + error.message, 'error');
        }
    }
    
    // ==================== HELPER FUNCTIONS ====================
    
    function getStatusColor(status) {
        switch(status) {
            case 'verified': return 'text-green-600';
            case 'paid': return 'text-blue-600';
            default: return 'text-yellow-600';
        }
    }
    
    function generateQRCode(text, containerId) {
        const container = document.getElementById(containerId);
        if (!container || !text) return;
        
        container.innerHTML = '';
        try {
            // Pastikan QRCode library sudah dimuat
            if (typeof QRCode !== 'undefined') {
                new QRCode(container, {
                    text: text,
                    width: 200,
                    height: 200,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            } else {
                console.error('QRCode library not loaded');
                // Fallback: tampilkan text jika QRCode tidak tersedia
                container.innerHTML = `<div class="text-center">
                    <div class="text-gray-500 mb-2">QR Code tidak bisa ditampilkan</div>
                    <code class="bg-gray-100 p-2 rounded">${text}</code>
                </div>`;
            }
        } catch (error) {
            console.error('Error generating QR Code:', error);
            container.innerHTML = `<div class="text-center text-red-500">
                <i class="fas fa-exclamation-triangle"></i> Gagal generate QR Code
            </div>`;
        }
    }
    
    function downloadQRCode(text, filename) {
        try {
            // Create canvas untuk download
            const canvas = document.createElement('canvas');
            canvas.width = 400;
            canvas.height = 400;
            const ctx = canvas.getContext('2d');
            
            // Draw background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Simple QR Code simulation
            ctx.fillStyle = '#000000';
            ctx.font = 'bold 40px monospace';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('QR CODE', canvas.width/2, canvas.height/2 - 30);
            
            ctx.font = '20px monospace';
            ctx.fillText(text, canvas.width/2, canvas.height/2 + 30);
            
            // Download
            const link = document.createElement('a');
            link.download = `qrcode-${filename || 'participant'}.png`;
            link.href = canvas.toDataURL('image/png');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showNotification('QR Code berhasil diunduh', 'success');
        } catch (error) {
            console.error('Error downloading QR Code:', error);
            showNotification('Gagal mengunduh QR Code', 'error');
        }
    }
    
    function showLoading(message) {
        let loadingEl = document.getElementById('loadingOverlay');
        if (!loadingEl) {
            loadingEl = document.createElement('div');
            loadingEl.id = 'loadingOverlay';
            loadingEl.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            loadingEl.innerHTML = `
                <div class="bg-white p-6 rounded-xl shadow-lg flex flex-col items-center">
                    <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-4"></div>
                    <p class="text-gray-700">${message}</p>
                </div>
            `;
            document.body.appendChild(loadingEl);
        }
    }
    
    function hideLoading() {
        const loadingEl = document.getElementById('loadingOverlay');
        if (loadingEl) {
            document.body.removeChild(loadingEl);
        }
    }
    
    function showNotification(message, type) {
        // Remove existing notifications
        document.querySelectorAll('.notification').forEach(el => el.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} mr-3"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // ==================== EVENT LISTENERS ====================
    
    // Event listeners untuk tombol aksi di tabel
    document.querySelectorAll('.view-detail-btn').forEach(button => {
        button.addEventListener('click', function() {
            const participantId = this.getAttribute('data-id');
            viewParticipantDetail(participantId);
        });
    });
    
    document.querySelectorAll('.verify-btn').forEach(button => {
        button.addEventListener('click', function() {
            const participantId = this.getAttribute('data-id');
            openVerificationModal(participantId);
        });
    });
    
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const participantId = this.getAttribute('data-id');
            openDeleteModal(participantId);
        });
    });
    
    // Event listeners untuk tombol close di modal
    document.getElementById('closeParticipantModal')?.addEventListener('click', closeParticipantModal);
    document.getElementById('closeVerificationModal')?.addEventListener('click', closeVerificationModal);
    document.getElementById('cancelVerification')?.addEventListener('click', closeVerificationModal);
    document.getElementById('cancelDelete')?.addEventListener('click', closeDeleteModal);
    document.getElementById('closeBarcodeScanner')?.addEventListener('click', closeBarcodeScanner);
    document.getElementById('closeScannerBtn')?.addEventListener('click', closeBarcodeScanner);
    document.getElementById('closeEmailModal')?.addEventListener('click', closeEmailModal);
    document.getElementById('cancelEmail')?.addEventListener('click', closeEmailModal);
    
    // Event listener untuk tombol filter
    document.getElementById('toggleFilterBtn')?.addEventListener('click', function() {
        const filterPanel = document.getElementById('filterPanel');
        filterPanel.classList.toggle('hidden');
    });
    
    // Event listener untuk tombol scan barcode
    document.getElementById('scanBarcodeBtn')?.addEventListener('click', openBarcodeScanner);
    
    // Event listener untuk tombol cari barcode
    document.getElementById('searchBarcodeBtn')?.addEventListener('click', function() {
        const transactionCode = document.getElementById('manualBarcodeInput').value.trim();
        searchParticipantByTransactionCode(transactionCode);
    });
    
    // Event listener untuk enter pada input barcode
    document.getElementById('manualBarcodeInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('searchBarcodeBtn').click();
        }
    });
    
    // Event listeners untuk form submit
    document.getElementById('verificationForm')?.addEventListener('submit', submitVerification);
    document.getElementById('deleteForm')?.addEventListener('submit', submitDelete);
    document.getElementById('emailForm')?.addEventListener('submit', submitEmail);
    
    // Event listener untuk klik di luar modal
    document.addEventListener('click', function(event) {
        if (event.target === participantModal) {
            closeParticipantModal();
        }
        if (event.target === verificationModal) {
            closeVerificationModal();
        }
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
        if (event.target === barcodeScannerModal) {
            closeBarcodeScanner();
        }
        if (event.target === emailModal) {
            closeEmailModal();
        }
    });
    
    // Event listener untuk escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeParticipantModal();
            closeVerificationModal();
            closeDeleteModal();
            closeBarcodeScanner();
            closeEmailModal();
        }
    });
    
    // Ekspor fungsi ke window object
    window.closeParticipantModal = closeParticipantModal;
    window.closeVerificationModal = closeVerificationModal;
    window.closeDeleteModal = closeDeleteModal;
    window.closeBarcodeScanner = closeBarcodeScanner;
    window.closeEmailModal = closeEmailModal;
    window.openBarcodeScanner = openBarcodeScanner;
    window.openDeleteModal = openDeleteModal;
    window.openEmailModal = openEmailModal;
    window.openVerificationModal = openVerificationModal;
    window.viewParticipantDetail = viewParticipantDetail;
    window.downloadQRCode = downloadQRCode;
    window.testBrevoEmail = testBrevoEmail;
    window.openProofImage = openProofImage;
    window.downloadProofImage = downloadProofImage;
    window.retryLoadProofImage = retryLoadProofImage;
    window.handleProofImageError = handleProofImageError;
    window.closeFullscreenModal = closeFullscreenModal;
});
</script>
@endpush