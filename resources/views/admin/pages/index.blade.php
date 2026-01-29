@extends('admin.layouts.admin.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Ringkasan dan monitoring sistem')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6 transform transition-transform duration-200 hover:scale-105">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-blue-100 text-sm font-medium">Total Peserta</p>
                    <div class="flex items-end justify-between">
                        <p id="totalParticipants" class="text-2xl md:text-3xl font-bold">{{ $stats['total_participants'] }}</p>
                        <div class="text-right">
                            <div class="text-green-300 text-xs">
                                <i class="fas fa-chart-line mr-1"></i>
                                <span>{{ $stats['total_participants'] > 0 ? 'Aktif' : '0' }}</span>
                            </div>
                            <p class="text-blue-200 text-xs">Total peserta</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6 transform transition-transform duration-200 hover:scale-105">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-calendar-check text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-green-100 text-sm font-medium">Event Aktif</p>
                    <div class="flex items-end justify-between">
                        <p id="activeEvents" class="text-2xl md:text-3xl font-bold">{{ $stats['active_events'] }}</p>
                        <div class="text-right">
                            <div class="text-green-300 text-xs">
                                <i class="fas fa-check mr-1"></i>
                                <span>{{ $stats['active_events'] }} aktif</span>
                            </div>
                            <p class="text-green-200 text-xs">{{ $stats['total_events'] }} total</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl shadow-lg p-6 transform transition-transform duration-200 hover:scale-105">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-yellow-100 text-sm font-medium">Pending Payment</p>
                    <div class="flex items-end justify-between">
                        <p id="pendingPayments" class="text-2xl md:text-3xl font-bold">{{ $stats['pending_payments'] }}</p>
                        <div class="text-right">
                            <div class="text-yellow-300 text-xs">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <span id="pendingBadge">{{ $stats['pending_payments'] }} pending</span>
                            </div>
                            <p class="text-yellow-200 text-xs">Perlu verifikasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6 transform transition-transform duration-200 hover:scale-105">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-purple-100 text-sm font-medium">Verified</p>
                    <div class="flex items-end justify-between">
                        <p id="verifiedParticipants" class="text-2xl md:text-3xl font-bold">{{ $stats['verified_participants'] }}</p>
                        <div class="text-right">
                            <div class="text-purple-300 text-xs">
                                <i class="fas fa-percentage mr-1"></i>
                                <span>
                                    @php
                                        $percentage = $stats['total_participants'] > 0 
                                            ? round(($stats['verified_participants'] / $stats['total_participants']) * 100) 
                                            : 0;
                                    @endphp
                                    {{ $percentage }}%
                                </span>
                            </div>
                            <p class="text-purple-200 text-xs">Dari total</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Registrations -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Pendaftaran Terbaru</h2>
                <p class="text-gray-600 text-sm">10 pendaftaran terakhir yang perlu diverifikasi</p>
            </div>
            <button onclick="loadRecentRegistrations()" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow hover:shadow-md">
                <i class="fas fa-sync-alt mr-2"></i> Refresh
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="recentRegistrations" class="divide-y divide-gray-200">
                    @forelse($recentRegistrations as $participant)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
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
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">
                                {{ $participant->event->name ?? 'Event tidak ditemukan' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                @if($participant->event)
                                    Rp {{ number_format($participant->event->price, 0, ',', '.') }}
                                @endif
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="far fa-calendar-alt text-gray-400 mr-2"></i>
                                <span class="text-sm">{{ $participant->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $participant->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
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
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <button onclick="viewParticipantDetail({{ $participant->id }})" 
                                        class="px-3 py-1.5 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:from-gray-200 hover:to-gray-300 transition-all duration-200 shadow-sm hover:shadow text-sm">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </button>
                                @if($participant->payment_status == 'pending')
                                    <button onclick="openVerificationModal({{ $participant->id }})" 
                                            class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow text-sm">
                                        <i class="fas fa-check mr-1"></i> Verifikasi
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada pendaftaran</h3>
                            <p class="text-gray-500">Belum ada peserta yang mendaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Active Events -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Event Aktif</h2>
            </div>
            <div id="activeEventsList" class="p-6 space-y-4">
                @forelse($activeEvents as $event)
                <div class="flex items-center justify-between p-4 rounded-lg border hover:shadow-md transition-all duration-200 
                    @if($event->available_slots && $event->participants->count() >= $event->available_slots) 
                        border-red-200 bg-gradient-to-r from-red-50 to-white
                    @elseif($event->available_slots && $event->participants->count() >= ($event->available_slots * 0.7)) 
                        border-yellow-200 bg-gradient-to-r from-yellow-50 to-white
                    @else 
                        border-blue-200 bg-gradient-to-r from-blue-50 to-white 
                    @endif">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-800">{{ $event->name }}</h3>
                        <div class="flex flex-wrap items-center mt-2 text-sm text-gray-600 gap-3">
                            <span class="flex items-center">
                                <i class="far fa-calendar mr-2 text-blue-500"></i>
                                {{ $event->date->format('d M Y') }}
                            </span>
                            <span class="flex items-center">
                                <i class="fas fa-users mr-2 text-green-500"></i>
                                {{ $event->participants->count() }} Peserta
                            </span>
                            @if($event->price > 0)
                            <span class="flex items-center">
                                <i class="fas fa-money-bill-wave mr-2 text-purple-500"></i>
                                Rp {{ number_format($event->price, 0, ',', '.') }}
                            </span>
                            @endif
                        </div>
                        @if($event->available_slots)
                            @php
                                $percentage = $event->available_slots > 0 
                                    ? min(100, ($event->participants->count() / $event->available_slots) * 100) 
                                    : 0;
                            @endphp
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Progress Pendaftaran</span>
                                    <span>{{ round($percentage) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full 
                                        @if($event->participants->count() >= $event->available_slots) 
                                            bg-red-500
                                        @elseif($event->participants->count() >= ($event->available_slots * 0.7)) 
                                            bg-yellow-500
                                        @else 
                                            bg-gradient-to-r from-blue-500 to-blue-600 
                                        @endif" 
                                         style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('admin.event.edit', $event->id) }}" 
                       class="ml-4 px-4 py-2 bg-white text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 hover:shadow transition-all duration-200 text-sm font-medium">
                        Kelola
                    </a>
                </div>
                @empty
                <div class="text-center py-8">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="far fa-calendar-times text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-500">Tidak ada event aktif saat ini.</p>
                    <a href="{{ route("admin.event.index") }}" class="inline-block mt-4 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i> Buat Event Baru
                    </a>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Quick Actions (DIPERBAIKI) -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Aksi Cepat</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4">
                    <!-- Buat Event Baru -->
                    <a href="{{ route("admin.event.index") }}" 
                       class="flex items-center p-4 rounded-lg bg-gradient-to-r from-purple-50 to-white border border-purple-100 hover:from-purple-100 hover:to-white hover:shadow-md transition-all duration-200 group">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-plus-circle text-white text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-800">Buat Event Baru</p>
                            <p class="text-sm text-gray-600">Tambahkan event baru ke sistem</p>
                        </div>
                        <div class="ml-auto text-purple-400 group-hover:text-purple-600">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                    
                    <!-- Verifikasi Pembayaran -->
                    <a href="{{ route('admin.peserta.index') }}?status=pending" 
                       class="flex items-center p-4 rounded-lg bg-gradient-to-r from-yellow-50 to-white border border-yellow-100 hover:from-yellow-100 hover:to-white hover:shadow-md transition-all duration-200 group">
                        <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-800">Verifikasi Pembayaran</p>
                            <p class="text-sm text-gray-600">Verifikasi pembayaran yang pending</p>
                        </div>
                        <div class="ml-auto text-yellow-400 group-hover:text-yellow-600">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                    
                    <!-- Kelola Pembayaran -->
                    <a href="{{ route('admin.payment.index') }}" 
                       class="flex items-center p-4 rounded-lg bg-gradient-to-r from-indigo-50 to-white border border-indigo-100 hover:from-indigo-100 hover:to-white hover:shadow-md transition-all duration-200 group">
                        <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-credit-card text-white text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-800">Kelola Pembayaran</p>
                            <p class="text-sm text-gray-600">Metode pembayaran tersedia</p>
                        </div>
                        <div class="ml-auto text-indigo-400 group-hover:text-indigo-600">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                    
                    <!-- Download Laporan (SIMPLE VERSION) -->
                    <button onclick="exportReport()" 
                            class="flex items-center p-4 rounded-lg bg-gradient-to-r from-blue-50 to-white border border-blue-100 hover:from-blue-100 hover:to-white hover:shadow-md transition-all duration-200 group">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-download text-white text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-800">Download Laporan</p>
                            <p class="text-sm text-gray-600">Excel atau PDF report</p>
                        </div>
                        <div class="ml-auto text-blue-400 group-hover:text-blue-600">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Participant Detail Modal -->
<div id="participantModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Detail Peserta</h3>
                <button onclick="closeParticipantModal()" class="text-gray-500 hover:text-gray-700">
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
                <button onclick="closeVerificationModal()" class="text-gray-500 hover:text-gray-700">
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
                        <label class="block text-gray-700 mb-2">Status Verifikasi:</label>
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
                    <button type="button" onclick="closeVerificationModal()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 font-semibold">
                        <i class="fas fa-check-circle mr-2"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Simple Export Modal -->
<div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full">
        <div class="p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-file-export text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Export Data</h3>
            <p class="text-gray-600 mb-6">Pilih format export:</p>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <button onclick="exportToExcel()" 
                        class="px-4 py-3 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 text-green-700 rounded-lg hover:from-green-100 hover:to-green-200 transition-all duration-200">
                    <i class="fas fa-file-excel text-lg mb-1"></i><br>
                    <span class="text-sm font-medium">Excel</span>
                </button>
                <button onclick="exportToPDF()" 
                        class="px-4 py-3 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 text-red-700 rounded-lg hover:from-red-100 hover:to-red-200 transition-all duration-200">
                    <i class="fas fa-file-pdf text-lg mb-1"></i><br>
                    <span class="text-sm font-medium">PDF</span>
                </button>
            </div>
            <div class="text-xs text-gray-500 mb-4">
                <p>Data akan diexport berdasarkan data yang tampil di dashboard.</p>
            </div>
            <button onclick="closeExportModal()" 
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 w-full">
                Batal
            </button>
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
</style>
@endpush

@push('scripts')
<script>
    // Set CSRF token
    // const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Deklarasi variabel global
    let currentParticipantId = null;
    
    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', function() {
        // Form submission for verification
        const verificationForm = document.getElementById('verificationForm');
        if (verificationForm) {
            verificationForm.addEventListener('submit', handleVerificationSubmit);
        }
        
        // Animate stats cards on load
        setTimeout(() => {
            document.querySelectorAll('.transform.hover\\:scale-105').forEach(card => {
                card.classList.add('scale-100');
            });
        }, 100);
        
        // Start auto-refresh
        startAutoRefresh();
    });
    
    // Simple Export Functions
    function exportReport() {
        document.getElementById('exportModal').classList.remove('hidden');
        document.getElementById('exportModal').classList.add('flex');
    }
    
    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
        document.getElementById('exportModal').classList.remove('flex');
    }
    
    async function exportToExcel() {
        try {
            showLoading('Menyiapkan file Excel...');
            
            // Ambil data peserta dari dashboard
            const participants = @json($recentRegistrations);
            
            // Buat header CSV
            let csvContent = "No,Kode Transaksi,Nama Lengkap,Email,Telepon,Event,Status,Tanggal Daftar,Catatan\n";
            
            // Tambahkan data
            participants.forEach((participant, index) => {
                const row = [
                    index + 1,
                    `"${participant.transaction_code}"`,
                    `"${participant.full_name}"`,
                    `"${participant.email}"`,
                    `"${participant.phone || '-'}"`,
                    `"${participant.event ? participant.event.name : '-'}"`,
                    `"${getStatusText(participant.payment_status)}"`,
                    `"${new Date(participant.created_at).toLocaleDateString('id-ID')}"`,
                    `"${participant.notes || '-'}"`
                ];
                csvContent += row.join(',') + "\n";
            });
            
            // Buat blob dan download
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            
            link.setAttribute("href", url);
            link.setAttribute("download", `data-peserta-${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            hideLoading();
            closeExportModal();
            showToast('File Excel berhasil diunduh!', 'Berhasil!');
            
        } catch (error) {
            hideLoading();
            console.error('Error exporting to Excel:', error);
            showToast('Gagal mengexport data', 'Error!', 'error');
        }
    }
    
    async function exportToPDF() {
        try {
            showLoading('Menyiapkan file PDF...');
            
            // Ambil data peserta dari dashboard
            const participants = @json($recentRegistrations);
            
            // Buat konten HTML sederhana
            let htmlContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Data Peserta</title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; text-align: left; }
                        td { border: 1px solid #dee2e6; padding: 8px; }
                    </style>
                </head>
                <body>
                    <h1>Data Peserta</h1>
                    <p>Tanggal: ${new Date().toLocaleDateString('id-ID')}</p>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Event</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            participants.forEach((participant, index) => {
                htmlContent += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${participant.full_name}</td>
                        <td>${participant.email}</td>
                        <td>${participant.event ? participant.event.name : '-'}</td>
                        <td>${getStatusText(participant.payment_status)}</td>
                        <td>${new Date(participant.created_at).toLocaleDateString('id-ID')}</td>
                    </tr>
                `;
            });
            
            htmlContent += `
                        </tbody>
                    </table>
                </body>
                </html>
            `;
            
            // Buat blob dan download
            const blob = new Blob([htmlContent], { type: 'text/html;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            
            link.setAttribute("href", url);
            link.setAttribute("download", `data-peserta-${new Date().toISOString().split('T')[0]}.html`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            hideLoading();
            closeExportModal();
            showToast('File PDF berhasil diunduh!', 'Berhasil!');
            
        } catch (error) {
            hideLoading();
            console.error('Error exporting to PDF:', error);
            showToast('Gagal mengexport data', 'Error!', 'error');
        }
    }
    
    // View participant detail - DIPERBAIKI: menggunakan route baru
    async function viewParticipantDetail(participantId) {
        try {
            showLoading('Memuat detail peserta...');
            
            // Gunakan route baru yang sudah dibuat
            const response = await fetch(`{{ url('admin/participant') }}/${participantId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            hideLoading();
            
            if (data.success) {
                displayParticipantDetail(data.data);
            } else {
                showToast('Gagal memuat detail peserta', 'Error!', 'error');
            }
            
        } catch (error) {
            hideLoading();
            console.error('Error loading participant detail:', error);
            showToast('Gagal memuat detail peserta', 'Error!', 'error');
        }
    }
    
    // Show verification modal - DIPERBAIKI: menggunakan route baru
    async function showVerificationModal(participantId) {
        currentParticipantId = participantId;
        
        try {
            // Gunakan route baru yang sudah dibuat
            const response = await fetch(`{{ url('admin/participant') }}/${participantId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                const participant = data.data;
                
                let proofInfo = '';
                if (participant.payment_proof_url) {
                    proofInfo = `
                        <div class="mt-2">
                            <strong>Bukti Transfer:</strong><br>
                            <div class="mt-2">
                                <a href="${participant.payment_proof_url}" target="_blank" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100">
                                    <i class="fas fa-eye mr-2"></i> Lihat Bukti Transfer
                                </a>
                            </div>
                        </div>
                    `;
                }
                
                document.getElementById('participantInfo').innerHTML = `
                    <h4 class="font-semibold mb-2">Detail Peserta:</h4>
                    <p><strong>Nama:</strong> ${participant.full_name}</p>
                    <p><strong>Email:</strong> ${participant.email}</p>
                    <p><strong>Event:</strong> ${participant.event ? participant.event.name : 'Event tidak ditemukan'}</p>
                    <p><strong>Jumlah:</strong> ${participant.event ? 'Rp ' + participant.event.price.toLocaleString() : '-'}</p>
                    <p><strong>Kode Transaksi:</strong> <code>${participant.transaction_code}</code></p>
                    ${proofInfo}
                `;
                
                document.getElementById('verificationParticipantId').value = participantId;
                document.getElementById('paymentStatus').value = participant.payment_status;
                document.getElementById('verificationNotes').value = participant.notes || '';
                
                document.getElementById('verificationModal').classList.remove('hidden');
                document.getElementById('verificationModal').classList.add('flex');
            }
        } catch (error) {
            console.error('Error opening verification modal:', error);
            showToast('Gagal membuka modal verifikasi', 'error');
        }
    }
    
    // Handle verification form submission - DIPERBAIKI: menggunakan route yang sudah ada
    async function handleVerificationSubmit(event) {
        event.preventDefault();
        
        const participantId = document.getElementById('verificationParticipantId').value;
        const paymentStatus = document.getElementById('paymentStatus').value;
        const notes = document.getElementById('verificationNotes').value;
        
        try {
            showLoading('Memperbarui status...');
            
            // Gunakan route yang sudah ada: admin.peserta.update-status
            const response = await fetch(`{{ url('admin/peserta') }}/${participantId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    payment_status: paymentStatus,
                    notes: notes
                })
            });
            
            const data = await response.json();
            
            hideLoading();
            
            if (data.success) {
                showToast('Status pembayaran berhasil diperbarui!', 'Berhasil!');
                closeVerificationModal();
                
                // Refresh semua data setelah update
                updateDashboardStats();
                updateRecentRegistrations();
                
                // Jika sedang melihat detail peserta, refresh juga
                if (document.getElementById('participantModal').classList.contains('flex')) {
                    viewParticipantDetail(participantId);
                }
            } else {
                showToast(data.message || 'Gagal memperbarui status', 'error');
            }
        } catch (error) {
            hideLoading();
            console.error('Error updating verification:', error);
            showToast('Gagal memperbarui status', 'error');
        }
    }
    
    // Fungsi untuk menampilkan detail peserta
    function displayParticipantDetail(participant) {
        const statusClass = getStatusClass(participant.payment_status);
        const statusText = getStatusText(participant.payment_status);
        
        // Format tanggal
        const registrationDate = new Date(participant.created_at);
        const formattedDate = registrationDate.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        let proofHtml = '';
        if (participant.payment_proof_url) {
            proofHtml = `
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-700 mb-3">Bukti Transfer</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex flex-col items-center">
                            <div class="relative w-full max-w-md mb-3 bg-gray-100 rounded-lg flex items-center justify-center min-h-[200px]">
                                <div class="w-full text-center">
                                    <img src="${participant.payment_proof_url}" 
                                         alt="Bukti Transfer ${participant.full_name}"
                                         class="w-full h-auto rounded-lg object-contain max-h-96 mx-auto">
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-3 justify-center mt-3">
                                <button onclick="window.open('${participant.payment_proof_url}', '_blank')" 
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Buka di Tab Baru
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        document.getElementById('participantDetailContent').innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-3">Informasi Pribadi</h4>
                    <div class="space-y-2">
                        <p><strong class="text-gray-700">Nama Lengkap:</strong><br>${participant.full_name}</p>
                        <p><strong class="text-gray-700">Email:</strong><br>${participant.email}</p>
                        <p><strong class="text-gray-700">Telepon:</strong><br>${participant.phone || '-'}</p>
                        <p><strong class="text-gray-700">NIK:</strong><br>${participant.nik || '-'}</p>
                        <p><strong class="text-gray-700">Jenis Kelamin:</strong><br>${participant.gender || '-'}</p>
                        ${participant.address ? `<p><strong class="text-gray-700">Alamat:</strong><br>${participant.address}</p>` : ''}
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-3">Informasi Pendaftaran</h4>
                    <div class="space-y-2">
                        <p><strong class="text-gray-700">Event:</strong><br>${participant.event ? participant.event.name : 'Event tidak ditemukan'}</p>
                        <p><strong class="text-gray-700">Kode Transaksi:</strong><br><code class="bg-blue-50 px-2 py-1 rounded">${participant.transaction_code}</code></p>
                        <p><strong class="text-gray-700">Status Pembayaran:</strong><br>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusClass.badge} mt-1">
                                <i class="${getStatusIcon(participant.payment_status)} mr-1.5"></i>
                                ${statusText}
                            </span>
                        </p>
                        <p><strong class="text-gray-700">Tanggal Daftar:</strong><br>${formattedDate}</p>
                        ${participant.event && participant.event.price ? 
                            `<p><strong class="text-gray-700">Biaya Event:</strong><br>Rp ${participant.event.price.toLocaleString('id-ID')}</p>` 
                            : ''}
                        ${participant.notes ? `<p><strong class="text-gray-700">Catatan:</strong><br>${participant.notes}</p>` : ''}
                    </div>
                </div>
            </div>
            
            ${proofHtml}
            
            <div class="mt-6 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="text-sm text-gray-500">
                    <p>ID Peserta: <span class="font-mono">${participant.id}</span></p>
                    <p>Terakhir Diperbarui: ${new Date(participant.updated_at).toLocaleDateString('id-ID')}</p>
                </div>
                <div class="flex space-x-4">
                    <button onclick="openVerificationModal(${participant.id})" 
                            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow">
                        <i class="fas fa-edit mr-2"></i> Ubah Status
                    </button>
                </div>
            </div>
        `;
        
        document.getElementById('participantModal').classList.remove('hidden');
        document.getElementById('participantModal').classList.add('flex');
    }
    
    // Helper functions untuk status
    function getStatusClass(status) {
        switch(status) {
            case 'verified':
                return {
                    bg: 'bg-gradient-to-r from-green-100 to-green-200',
                    text: 'text-green-600',
                    badge: 'bg-gradient-to-r from-green-100 to-green-200 text-green-800'
                };
            case 'paid':
                return {
                    bg: 'bg-gradient-to-r from-blue-100 to-blue-200',
                    text: 'text-blue-600',
                    badge: 'bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800'
                };
            default:
                return {
                    bg: 'bg-gradient-to-r from-yellow-100 to-yellow-200',
                    text: 'text-yellow-600',
                    badge: 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800'
                };
        }
    }
    
    function getStatusText(status) {
        switch(status) {
            case 'verified': return 'Terverifikasi';
            case 'paid': return 'Sudah Bayar';
            default: return 'Menunggu';
        }
    }
    
    function getStatusIcon(status) {
        switch(status) {
            case 'verified': return 'fas fa-check-circle';
            case 'paid': return 'fas fa-money-bill-wave';
            default: return 'fas fa-clock';
        }
    }
    
    // Modal control functions
    function closeParticipantModal() {
        document.getElementById('participantModal').classList.add('hidden');
        document.getElementById('participantModal').classList.remove('flex');
    }
    
    function closeVerificationModal() {
        document.getElementById('verificationModal').classList.add('hidden');
        document.getElementById('verificationModal').classList.remove('flex');
        currentParticipantId = null;
    }
    
    // Utility functions
    function showLoading(message = 'Memuat...') {
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'globalLoading';
        loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        loadingDiv.innerHTML = `
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-700">${message}</p>
            </div>
        `;
        document.body.appendChild(loadingDiv);
    }
    
    function hideLoading() {
        const loadingDiv = document.getElementById('globalLoading');
        if (loadingDiv) {
            loadingDiv.remove();
        }
    }
    
    function showToast(message, title = 'Sukses', type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform transition-transform duration-300 translate-y-0 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} mr-3"></i>
                <div>
                    <p class="font-semibold">${title}</p>
                    <p>${message}</p>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('translate-y-full');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // Auto refresh untuk data realtime
    function startAutoRefresh() {
        // Refresh stats setiap 30 detik
        setInterval(updateDashboardStats, 30000);
        
        // Refresh recent registrations setiap 45 detik
        setInterval(updateRecentRegistrations, 45000);
    }
    
    // Update dashboard statistics
    async function updateDashboardStats() {
        try {
            const statElements = {
                'totalParticipants': document.getElementById('totalParticipants'),
                'activeEvents': document.getElementById('activeEvents'),
                'pendingPayments': document.getElementById('pendingPayments'),
                'verifiedParticipants': document.getElementById('verifiedParticipants')
            };
            
            // Tambahkan efek loading
            Object.values(statElements).forEach(el => {
                if (el) el.classList.add('animate-pulse');
            });
            
            // Kirim request untuk data terbaru
            const response = await fetch('{{ route("admin.index") }}?stats_only=true', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                
                if (data.success) {
                    // Update stats dengan animasi
                    const stats = data.stats;
                    animateCounter(statElements.totalParticipants, stats.total_participants);
                    animateCounter(statElements.activeEvents, stats.active_events);
                    animateCounter(statElements.pendingPayments, stats.pending_payments);
                    animateCounter(statElements.verifiedParticipants, stats.verified_participants);
                    
                    // Update pending badge
                    const pendingBadge = document.getElementById('pendingBadge');
                    if (pendingBadge) {
                        pendingBadge.textContent = `${stats.pending_payments} pending`;
                    }
                }
            }
        } catch (error) {
            console.error('Error updating stats:', error);
        } finally {
            // Hapus efek loading
            const statElements = {
                'totalParticipants': document.getElementById('totalParticipants'),
                'activeEvents': document.getElementById('activeEvents'),
                'pendingPayments': document.getElementById('pendingPayments'),
                'verifiedParticipants': document.getElementById('verifiedParticipants')
            };
            Object.values(statElements).forEach(el => {
                if (el) el.classList.remove('animate-pulse');
            });
        }
    }
    
    // Update recent registrations
    async function updateRecentRegistrations() {
        try {
            const response = await fetch('{{ route("admin.index") }}?registrations_only=true', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                
                if (data.success && data.registrations) {
                    const registrations = data.registrations;
                    const container = document.getElementById('recentRegistrations');
                    
                    if (registrations.length > 0) {
                        let html = '';
                        registrations.forEach(participant => {
                            const statusClass = getStatusClass(participant.payment_status);
                            const statusText = getStatusText(participant.payment_status);
                            const statusIcon = getStatusIcon(participant.payment_status);
                            
                            html += `
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3 ${statusClass.bg}">
                                                <i class="fas fa-user ${statusClass.text}"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">${participant.full_name}</p>
                                                <p class="text-sm text-gray-500">${participant.email}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-900">${participant.event ? participant.event.name : 'Event tidak ditemukan'}</p>
                                        <p class="text-sm text-gray-500">${participant.event ? 'Rp ' + participant.event.price.toLocaleString() : ''}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <i class="far fa-calendar-alt text-gray-400 mr-2"></i>
                                            <span class="text-sm">${new Date(participant.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            ${new Date(participant.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusClass.badge}">
                                            <i class="${statusIcon} mr-1.5"></i>
                                            ${statusText}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <button onclick="viewParticipantDetail(${participant.id})" 
                                                    class="px-3 py-1.5 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:from-gray-200 hover:to-gray-300 transition-all duration-200 shadow-sm hover:shadow text-sm">
                                                <i class="fas fa-eye mr-1"></i> Detail
                                            </button>
                                            ${participant.payment_status === 'pending' ? 
                                                `<button onclick="openVerificationModal(${participant.id})" 
                                                        class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow text-sm">
                                                    <i class="fas fa-check mr-1"></i> Verifikasi
                                                </button>` 
                                                : ''}
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        container.innerHTML = html;
                        showToast('Data pendaftaran diperbarui', 'Berhasil!');
                    } else {
                        container.innerHTML = `
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center">
                                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-users text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada pendaftaran</h3>
                                    <p class="text-gray-500">Belum ada peserta yang mendaftar.</p>
                                </td>
                            </tr>
                        `;
                    }
                }
            }
        } catch (error) {
            console.error('Error loading registrations:', error);
            showToast('Gagal memuat data pendaftaran', 'Error!', 'error');
        }
    }
    
    // Animated counter update
    function animateCounter(element, targetValue) {
        if (!element) return;
        
        const current = parseInt(element.textContent.replace(/,/g, '')) || 0;
        const target = parseInt(targetValue) || 0;
        
        if (current === target) return;
        
        const duration = 800;
        const steps = 60;
        const increment = (target - current) / steps;
        let currentStep = 0;
        
        const timer = setInterval(() => {
            currentStep++;
            const newVal = Math.round(current + (increment * currentStep));
            element.textContent = newVal.toLocaleString();
            
            if (currentStep >= steps) {
                element.textContent = target.toLocaleString();
                clearInterval(timer);
            }
        }, duration / steps);
    }
    
    // Fungsi yang dipanggil oleh tombol
    function loadRecentRegistrations() {
        updateRecentRegistrations();
    }
    
    function openVerificationModal(participantId) {
        showVerificationModal(participantId);
    }
    
    function createEvent() {
        window.location.href = '{{ route("admin.event.create") }}';
    }
    
    function viewPendingPayments() {
        window.location.href = '{{ route("admin.peserta.index") }}?status=pending';
    }
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeParticipantModal();
            closeVerificationModal();
            closeExportModal();
        }
    });
    
    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
        const modals = [
            'participantModal',
            'verificationModal',
            'exportModal'
        ];
        
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && !modal.classList.contains('hidden') && event.target === modal) {
                if (modalId === 'participantModal') closeParticipantModal();
                if (modalId === 'verificationModal') closeVerificationModal();
                if (modalId === 'exportModal') closeExportModal();
            }
        });
    });
</script>
@endpush