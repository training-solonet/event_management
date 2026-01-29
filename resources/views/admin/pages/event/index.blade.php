@extends('admin.layouts.admin.admin')

@section('title', 'Kelola Event')
@section('page-title', 'Kelola Event')
@section('page-subtitle', 'Tambah, edit, dan monitor event')

@section('content')
<div class="space-y-6">
    <!-- Header dengan tombol tambah -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Event</h2>
            <p class="text-gray-600">Kelola semua event dan lihat statistik pendaftaran</p>
        </div>
        <button onclick="openCreateModal()" 
           class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow hover:shadow-md flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Buat Event Baru
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Event</p>
                    <p class="text-2xl md:text-3xl font-bold">{{ $events->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Peserta</p>
                    <p class="text-2xl md:text-3xl font-bold">{{ $totalParticipants }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-purple-100 text-sm font-medium">Event Aktif</p>
                    <p class="text-2xl md:text-3xl font-bold">{{ $activeEventsCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Events -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($events as $event)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3 
                                    @if($event->is_active) bg-gradient-to-r from-green-100 to-green-200
                                    @else bg-gradient-to-r from-gray-100 to-gray-200 @endif">
                                    <i class="fas fa-calendar 
                                        @if($event->is_active) text-green-600
                                        @else text-gray-400 @endif">
                                    </i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $event->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $event->event_code }}</p>
                                    @if($event->price > 0)
                                    <p class="text-xs text-blue-600 font-medium">
                                        Rp {{ number_format($event->price, 0, ',', '.') }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="far fa-calendar-alt text-gray-400 mr-2"></i>
                                <span class="text-sm">{{ $event->date->format('d M Y') }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $event->location }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-center">
                                <p class="text-xl font-bold text-gray-800">{{ $event->participants_count }}</p>
                                <div class="flex items-center justify-center mt-1">
                                    @if($event->available_slots)
                                    <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                        @php
                                            $percentage = $event->available_slots > 0 
                                                ? min(100, ($event->participants_count / $event->available_slots) * 100) 
                                                : 0;
                                        @endphp
                                        <div class="h-2 rounded-full 
                                            @if($percentage >= 100) bg-red-500
                                            @elseif($percentage >= 70) bg-yellow-500
                                            @else bg-gradient-to-r from-green-500 to-green-600 @endif" 
                                             style="width: {{ $percentage }}%">
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        {{ $event->available_slots ? $event->participants_count . '/' . $event->available_slots : '∞' }}
                                    </span>
                                    @else
                                    <span class="text-xs text-gray-500">Unlimited</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($event->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-green-100 to-green-200 text-green-800">
                                    <i class="fas fa-check-circle mr-1.5"></i>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800">
                                    <i class="fas fa-pause-circle mr-1.5"></i>
                                    Nonaktif
                                </span>
                            @endif
                            @if($event->available_slots && $event->participants_count >= $event->available_slots)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-red-100 to-red-200 text-red-800 mt-1">
                                    <i class="fas fa-user-slash mr-1.5"></i>
                                    Penuh
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <button onclick="showEventDetail({{ $event->id }})" 
                                        class="px-3 py-1.5 bg-gradient-to-r from-indigo-100 to-indigo-200 text-indigo-700 rounded-lg hover:from-indigo-200 hover:to-indigo-300 transition-all duration-200 shadow-sm hover:shadow text-sm">
                                    <i class="fas fa-info-circle mr-1"></i> Detail
                                </button>
                                <button onclick="viewEventParticipants({{ $event->id }})" 
                                        class="px-3 py-1.5 bg-gradient-to-r from-blue-100 to-blue-200 text-blue-700 rounded-lg hover:from-blue-200 hover:to-blue-300 transition-all duration-200 shadow-sm hover:shadow text-sm">
                                    <i class="fas fa-users mr-1"></i> Peserta
                                </button>
                                <button onclick="openEditModal({{ $event->id }})" 
                                   class="px-3 py-1.5 bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-700 rounded-lg hover:from-yellow-200 hover:to-yellow-300 transition-all duration-200 shadow-sm hover:shadow text-sm">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>
                                <button onclick="deleteEvent({{ $event->id }})" 
                                        class="px-3 py-1.5 bg-gradient-to-r from-red-100 to-red-200 text-red-700 rounded-lg hover:from-red-200 hover:to-red-300 transition-all duration-200 shadow-sm hover:shadow text-sm">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada event</h3>
                            <p class="text-gray-500">Mulai dengan membuat event pertama Anda.</p>
                            <button onclick="openCreateModal()" class="inline-block mt-4 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                                <i class="fas fa-plus mr-2"></i> Buat Event Pertama
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($events->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $events->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Create/Edit Event -->
<div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800" id="modalTitle">Tambah Event Baru</h3>
                <button onclick="closeEventModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="eventForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="event_id" id="eventId">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 mb-2">Nama Event *</label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Nama event">
                        <div class="text-xs text-red-500 mt-1 error-message" id="error-name"></div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Tanggal Event *</label>
                        <input type="date" name="date" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="text-xs text-red-500 mt-1 error-message" id="error-date"></div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Harga Tiket (Rp) *</label>
                        <input type="number" name="price" required min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="0">
                        <div class="text-xs text-red-500 mt-1 error-message" id="error-price"></div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Lokasi *</label>
                        <input type="text" name="location" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Lokasi event">
                        <div class="text-xs text-red-500 mt-1 error-message" id="error-location"></div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Tipe Event *</label>
                        <select name="type" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Tipe</option>
                            <option value="seminar">Seminar</option>
                            <option value="workshop">Workshop</option>
                            <option value="konferensi">Konferensi</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        <div class="text-xs text-red-500 mt-1 error-message" id="error-type"></div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Kuota Tersedia</label>
                        <input type="number" name="available_slots" min="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Kosongkan untuk unlimited">
                        <div class="text-xs text-red-500 mt-1 error-message" id="error-available_slots"></div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="description" rows="3"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Deskripsi event"></textarea>
                        <div class="text-xs text-red-500 mt-1 error-message" id="error-description"></div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" class="mr-2 rounded">
                            <span class="text-gray-700">Aktifkan event</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1">Event tidak aktif tidak akan ditampilkan ke publik</p>
                        <div class="text-xs text-red-500 mt-1 error-message" id="error-is_active"></div>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end space-x-4">
                    <button type="button" onclick="closeEventModal()" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 font-semibold">
                        <i class="fas fa-save mr-2"></i> Simpan Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Event -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800" id="detailModalTitle">Detail Event</h3>
                <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div id="detailContent">
                <!-- Content akan diisi oleh JavaScript -->
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex justify-end">
                    <button onclick="closeDetailModal()" 
                            class="px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 font-semibold">
                        <i class="fas fa-times mr-2"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Peserta Event -->
<div id="participantsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800" id="modalEventTitle"></h3>
                    <p class="text-gray-600 text-sm">Daftar peserta yang mendaftar</p>
                </div>
                <button onclick="closeParticipantsModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="participantsListContent">
                <!-- Content akan diisi oleh JavaScript -->
            </div>
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
                <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus Event</h3>
                <p class="text-gray-600">Apakah Anda yakin ingin menghapus event ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeDeleteModal()" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 font-semibold">
                        <i class="fas fa-trash mr-2"></i> Hapus Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Open create modal
    function openCreateModal() {
        clearFormErrors();
        document.getElementById('modalTitle').textContent = 'Tambah Event Baru';
        document.getElementById('eventForm').action = '{{ route("admin.event.store") }}';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('eventId').value = '';
        
        // Reset form dan set default values
        document.getElementById('eventForm').reset();
        document.querySelector('input[name="is_active"]').checked = true;
        document.querySelector('input[name="price"]').value = '0';
        
        document.getElementById('eventModal').classList.remove('hidden');
        document.getElementById('eventModal').classList.add('flex');
    }

    // Open edit modal
    async function openEditModal(eventId) {
        try {
            showLoading('Memuat data event...');
            
            const response = await fetch(`/admin/event/${eventId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                hideLoading();
                
                if (data.success && data.event) {
                    const event = data.event;
                    clearFormErrors();
                    
                    document.getElementById('modalTitle').textContent = 'Edit Event';
                    document.getElementById('eventForm').action = `/admin/event/${eventId}`;
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('eventId').value = event.id;
                    
                    // Fill form dengan data yang aman
                    document.querySelector('input[name="name"]').value = event.name || '';
                    
                    // Tangani date dengan aman - PERBAIKAN PENTING
                    if (event.date) {
                        // Jika date adalah objek string dengan format Y-m-d, langsung gunakan
                        let dateValue = event.date;
                        // Jika date mengandung waktu, ambil hanya bagian tanggalnya
                        if (event.date.includes(' ')) {
                            dateValue = event.date.split(' ')[0];
                        } else if (event.date.includes('T')) {
                            dateValue = event.date.split('T')[0];
                        }
                        document.querySelector('input[name="date"]').value = dateValue;
                    }
                    
                    document.querySelector('input[name="price"]').value = event.price || 0;
                    document.querySelector('input[name="location"]').value = event.location || '';
                    document.querySelector('select[name="type"]').value = event.type || '';
                    document.querySelector('input[name="available_slots"]').value = event.available_slots || '';
                    document.querySelector('textarea[name="description"]').value = event.description || '';
                    document.querySelector('input[name="is_active"]').checked = event.is_active == 1;
                    
                    document.getElementById('eventModal').classList.remove('hidden');
                    document.getElementById('eventModal').classList.add('flex');
                } else {
                    showNotification(data.message || 'Gagal memuat data event', 'error');
                }
            } else {
                hideLoading();
                if (response.status === 500) {
                    showNotification('Server error. Coba lagi nanti.', 'error');
                } else {
                    const errorData = await response.json().catch(() => ({}));
                    showNotification(errorData.message || 'Gagal memuat data event', 'error');
                }
            }
        } catch (error) {
            hideLoading();
            console.error('Error loading event:', error);
            showNotification('Gagal memuat data event. Periksa koneksi internet.', 'error');
        }
    }

    // Show event detail - DIPERBAIKI
    async function showEventDetail(eventId) {
        try {
            showLoading('Memuat detail event...');
            
            const response = await fetch(`/admin/event/${eventId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                hideLoading();
                
                if (data.success && data.event) {
                    const event = data.event;
                    
                    // Format date untuk display
                    const eventDate = new Date(event.date);
                    const formattedDate = eventDate.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    
                    // Format price
                    const formattedPrice = event.price == 0 ? 'Gratis' : 'Rp ' + parseInt(event.price).toLocaleString('id-ID');
                    
                    // Status badge
                    const statusBadge = event.is_active ? 
                        '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1.5"></i>Aktif</span>' :
                        '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"><i class="fas fa-pause-circle mr-1.5"></i>Nonaktif</span>';
                    
                    // Available slots info - FIX: Variabel dideklarasikan di scope yang benar
                    let slotsInfo = 'Unlimited';
                    let progressColor = 'bg-green-500'; // default value
                    let percentage = 0;
                    let quotaMessage = '';
                    
                    if (event.available_slots) {
                        const participantsCount = event.participants_count || 0;
                        slotsInfo = `${participantsCount} / ${event.available_slots}`;
                        
                        // Calculate percentage for progress bar
                        percentage = Math.min(100, (participantsCount / event.available_slots) * 100);
                        progressColor = percentage >= 100 ? 'bg-red-500' : 
                                      percentage >= 70 ? 'bg-yellow-500' : 'bg-green-500';
                        
                        quotaMessage = percentage >= 100 ? 'Kuota sudah penuh' : 
                                     percentage >= 70 ? 'Kuota hampir penuh' : 
                                     'Masih tersedia kuota';
                    }
                    
                    // Create detail HTML - FIX: Template string yang benar
                    const detailHtml = `
                        <div class="space-y-6">
                            <!-- Header -->
                            <div class="flex items-start space-x-4">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center bg-gradient-to-r from-blue-100 to-blue-200">
                                    <i class="fas fa-calendar text-2xl text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900">${event.name}</h4>
                                    <div class="flex items-center mt-1">
                                        <span class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">${event.event_code}</span>
                                        <div class="ml-3">
                                            ${statusBadge}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Stats Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-calendar-day text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Tanggal</p>
                                            <p class="font-semibold text-gray-900">${formattedDate}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-ticket-alt text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Harga Tiket</p>
                                            <p class="font-semibold text-gray-900">${formattedPrice}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-purple-50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-users text-purple-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Peserta</p>
                                            <p class="font-semibold text-gray-900">${event.participants_count || 0}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Details Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Basic Info -->
                                <div class="space-y-4">
                                    <h5 class="font-semibold text-gray-800 border-b pb-2">Informasi Dasar</h5>
                                    <div class="space-y-3">
                                        <div class="flex">
                                            <span class="w-32 text-gray-600">Lokasi:</span>
                                            <span class="font-medium">${event.location || '-'}</span>
                                        </div>
                                        <div class="flex">
                                            <span class="w-32 text-gray-600">Tipe Event:</span>
                                            <span class="font-medium capitalize">${event.type || '-'}</span>
                                        </div>
                                        <div class="flex">
                                            <span class="w-32 text-gray-600">Kuota:</span>
                                            <span class="font-medium">${event.available_slots ? slotsInfo : 'Unlimited'}</span>
                                        </div>
                                        <div class="flex">
                                            <span class="w-32 text-gray-600">Status:</span>
                                            <span class="font-medium">${event.is_active ? 'Aktif' : 'Nonaktif'}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Description -->
                                <div class="space-y-4">
                                    <h5 class="font-semibold text-gray-800 border-b pb-2">Deskripsi</h5>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-gray-700">${event.description || 'Tidak ada deskripsi'}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progress Bar untuk kuota -->
                            ${event.available_slots ? `
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Kuota Tersisa</span>
                                    <span class="font-medium">${slotsInfo}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="h-2.5 rounded-full ${progressColor}" style="width: ${percentage}%"></div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    ${quotaMessage}
                                </div>
                            </div>
                            ` : ''}
                            
                            <!-- Additional Info -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="font-semibold text-gray-800 mb-3">Informasi Tambahan</h5>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <p><i class="fas fa-hashtag mr-2 text-gray-400"></i> Kode Event: <span class="font-mono">${event.event_code}</span></p>
                                    <p><i class="fas fa-calendar-plus mr-2 text-gray-400"></i> Dibuat: ${formatDateTime(event.created_at)}</p>
                                    ${event.updated_at && event.updated_at !== event.created_at ? 
                                      `<p><i class="fas fa-calendar-check mr-2 text-gray-400"></i> Diperbarui: ${formatDateTime(event.updated_at)}</p>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('detailModalTitle').textContent = `Detail: ${event.name}`;
                    document.getElementById('detailContent').innerHTML = detailHtml;
                    document.getElementById('detailModal').classList.remove('hidden');
                    document.getElementById('detailModal').classList.add('flex');
                } else {
                    showNotification(data.message || 'Gagal memuat detail event', 'error');
                }
            } else {
                hideLoading();
                showNotification('Gagal memuat detail event', 'error');
            }
        } catch (error) {
            hideLoading();
            console.error('Error loading event detail:', error);
            showNotification('Gagal memuat detail event', 'error');
        }
    }
async function viewEventParticipants(eventId) {
    try {
        showLoading('Memuat daftar peserta...');
        
        const response = await fetch(`/admin/event/${eventId}?participants=true`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            hideLoading();
            
            if (data.success) {
                document.getElementById('modalEventTitle').textContent = `Peserta Event: ${data.event.name} (${data.event.participants_count} peserta)`;
                
                let participantsHtml = '';
                if (data.participants && data.participants.length > 0) {
                    // Hitung statistik
                    const verifiedCount = data.participants.filter(p => p.payment_status === 'verified').length;
                    const paidCount = data.participants.filter(p => p.payment_status === 'paid').length;
                    const pendingCount = data.participants.filter(p => p.payment_status === 'pending').length;
                    
                    participantsHtml = `
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Terverifikasi</p>
                                        <p class="text-xl font-bold text-gray-900">${verifiedCount}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-money-bill-wave text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Sudah Bayar</p>
                                        <p class="text-xl font-bold text-gray-900">${paidCount}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-clock text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Menunggu</p>
                                        <p class="text-xl font-bold text-gray-900">${pendingCount}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email/Telepon</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>

                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    ${data.participants.map(participant => `
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 
                                                        ${participant.payment_status === 'verified' ? 'bg-green-100 text-green-600' :
                                                          participant.payment_status === 'paid' ? 'bg-blue-100 text-blue-600' :
                                                          'bg-yellow-100 text-yellow-600'}">
                                                        <i class="fas fa-user text-sm"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-gray-900">${participant.full_name || 'Tidak diketahui'}</p>
                                                        <p class="text-xs text-gray-500">${participant.transaction_code || 'Tidak ada kode'}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <p class="text-sm text-gray-900">${participant.email || 'Tidak ada email'}</p>
                                                <p class="text-xs text-gray-500">${participant.phone || 'Tidak ada telepon'}</p>
                                            </td>
                                            <td class="px-6 py-4">
                                                ${participant.payment_status === 'verified' ? 
                                                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Terverifikasi</span>' :
                                                  participant.payment_status === 'paid' ?
                                                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Sudah Bayar</span>' :
                                                    '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu</span>'}
                                            </td>
                                            <td class="px-6 py-4">
                                                <p class="text-sm">${formatDate(participant.created_at)}</p>
                                                <p class="text-xs text-gray-500">${formatTime(participant.created_at)}</p>
                                            </td>

                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-gray-600">Total: <span class="font-semibold">${data.participants.length}</span> peserta</p>
                                    <p class="text-xs text-gray-500">
                                        Terverifikasi: ${verifiedCount} | 
                                        Sudah Bayar: ${paidCount} | 
                                        Menunggu: ${pendingCount}
                                    </p>
                                </div>
                                <div class="flex space-x-2">

                                    <button onclick="closeParticipantsModal()" 
                                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    participantsHtml = `
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-users-slash text-gray-400 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Belum ada peserta</h4>
                            <p class="text-gray-500">Belum ada yang mendaftar di event ini.</p>
                            <div class="mt-4">
                                <button onclick="closeParticipantsModal()" 
                                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    `;
                }
                
                document.getElementById('participantsListContent').innerHTML = participantsHtml;
                document.getElementById('participantsModal').classList.remove('hidden');
                document.getElementById('participantsModal').classList.add('flex');
            } else {
                showNotification(data.message || 'Gagal memuat data peserta', 'error');
            }
        } else {
            hideLoading();
            showNotification('Gagal memuat data peserta', 'error');
        }
    } catch (error) {
        hideLoading();
        console.error('Error loading participants:', error);
        showNotification('Gagal memuat data peserta: ' + error.message, 'error');
    }
}

    // View participants for a specific event
    async function viewParticipantDetail(participantId) {
    try {
        showLoading('Memuat detail peserta...');
        
        // Gunakan endpoint yang sudah ada
        const response = await fetch(`/admin/peserta/${participantId}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            hideLoading();
            
            if (data.success && data.data) {
                const participant = data.data;
                
                // Tampilkan detail peserta dalam modal yang sama
                let html = `
                    <div class="mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-full ${participant.payment_status === 'verified' ? 'bg-green-100 text-green-600' :
                                  participant.payment_status === 'paid' ? 'bg-blue-100 text-blue-600' : 'bg-yellow-100 text-yellow-600'} 
                                  flex items-center justify-center">
                                <i class="fas fa-user text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">${participant.full_name || 'Tidak diketahui'}</h4>
                                <p class="text-sm text-gray-600">${participant.transaction_code || 'Tidak ada kode'}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Info -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-semibold text-gray-700 mb-3 border-b pb-2">Informasi Pribadi</h5>
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
                                    <span>${participant.address || '-'}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Registration Info -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-semibold text-gray-700 mb-3 border-b pb-2">Informasi Pendaftaran</h5>
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="w-32 text-gray-600">Event:</span>
                                    <span class="font-medium">${participant.event ? participant.event.name : 'Tidak ditemukan'}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-gray-600">Kode Transaksi:</span>
                                    <span><code class="bg-gray-200 px-2 py-1 rounded">${participant.transaction_code || '-'}</code></span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-gray-600">Metode Pembayaran:</span>
                                    <span>${participant.payment_method || '-'}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-gray-600">Status:</span>
                                    <span>${participant.payment_status_text || participant.payment_status || '-'}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-gray-600">Tanggal Daftar:</span>
                                    <span>${participant.created_at ? formatDateTime(participant.created_at) : '-'}</span>
                                </div>
                                ${participant.payment_proof ? `
                                <div class="flex">
                                    <span class="w-32 text-gray-600">Bukti Bayar:</span>
                                    <a href="${participant.payment_proof_url || '#'}" target="_blank" 
                                       class="text-blue-600 hover:text-blue-800 underline">
                                        Lihat Bukti
                                    </a>
                                </div>
                                ` : ''}
                                ${participant.notes ? `
                                <div class="flex">
                                    <span class="w-32 text-gray-600">Catatan:</span>
                                    <span>${participant.notes}</span>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                    
                    ${participant.payment_proof && participant.payment_proof_url ? `
                    <div class="mt-6">
                        <h5 class="font-semibold text-gray-700 mb-3 border-b pb-2">Bukti Transfer</h5>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <img src="${participant.payment_proof_url}" alt="Bukti Transfer" 
                                 class="max-w-full h-auto rounded-lg mx-auto" 
                                 onerror="this.style.display='none'; this.parentElement.innerHTML='<p class=\'text-gray-500 text-center\'>Gambar tidak dapat dimuat</p>'">
                        </div>
                    </div>
                    ` : ''}
                    
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex space-x-4">
                            <button onclick="openVerificationModal(${participant.id})" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-edit mr-2"></i> Ubah Status
                            </button>
                            <button onclick="viewEventParticipants(${participant.event_id})" 
                                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                            </button>
                        </div>
                    </div>
                `;
                
                document.getElementById('modalEventTitle').textContent = `Detail Peserta: ${participant.full_name || 'Tidak diketahui'}`;
                document.getElementById('participantsListContent').innerHTML = html;
            } else {
                showNotification(data.message || 'Gagal memuat detail peserta', 'error');
            }
        } else {
            hideLoading();
            showNotification('Gagal memuat detail peserta', 'error');
        }
    } catch (error) {
        hideLoading();
        console.error('Error loading participant detail:', error);
        showNotification('Gagal memuat detail peserta', 'error');
    }
}

// Fungsi export participants (placeholder)
function exportParticipants(eventId) {
    showNotification('Fitur export sedang dalam pengembangan', 'info');
}

    // Handle form submission - FIX untuk masalah 422
    document.getElementById('eventForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const action = form.getAttribute('action');
        const method = document.getElementById('formMethod').value;
        
        // Pastikan is_active ada nilainya jika checkbox tidak dicentang
        if (!formData.has('is_active')) {
            formData.append('is_active', '0');
        }
        
        // Untuk PUT method, tambahkan _method field
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }
        
        // Clear previous errors
        clearFormErrors();
        
        showLoading('Menyimpan data...');
        
        try {
            const response = await fetch(action, {
                method: 'POST', // Selalu POST karena kita menggunakan _method untuk spoofing
                headers: {
                    'X-CSRF-TOKEN': csrfToken, // Dari layout utama
                    'Accept': 'application/json'
                    // Jangan set Content-Type untuk FormData, biarkan browser mengatur
                },
                body: formData
            });
            
            const data = await response.json();
            hideLoading();
            
            if (data.success) {
                showNotification(data.message, 'success');
                closeEventModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                if (data.errors) {
                    // Tampilkan error validasi
                    for (const key in data.errors) {
                        const errorElement = document.getElementById(`error-${key}`);
                        if (errorElement) {
                            errorElement.textContent = data.errors[key][0];
                        }
                    }
                    showNotification('Mohon perbaiki kesalahan pada form', 'error');
                } else {
                    showNotification(data.message || 'Gagal menyimpan data', 'error');
                }
            }
        } catch (error) {
            hideLoading();
            console.error('Error saving event:', error);
            showNotification('Gagal menyimpan data', 'error');
        }
    });

    // Delete event
    function deleteEvent(eventId) {
        document.getElementById('deleteForm').action = `/admin/event/${eventId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    // Handle delete form submission
    document.getElementById('deleteForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const action = form.getAttribute('action');
        
        showLoading('Menghapus event...');
        
        try {
            const response = await fetch(action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            hideLoading();
            
            if (data.success) {
                showNotification(data.message, 'success');
                closeDeleteModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.message, 'error');
                closeDeleteModal();
            }
        } catch (error) {
            hideLoading();
            console.error('Error deleting event:', error);
            showNotification('Gagal menghapus event', 'error');
            closeDeleteModal();
        }
    });

    // Helper functions
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    }
    
    function formatTime(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    function formatDateTime(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Clear form errors
    function clearFormErrors() {
        document.querySelectorAll('.error-message').forEach(element => {
            element.textContent = '';
        });
    }

    // Modal control functions
    function closeEventModal() {
        document.getElementById('eventModal').classList.add('hidden');
        document.getElementById('eventModal').classList.remove('flex');
        clearFormErrors();
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.getElementById('detailModal').classList.remove('flex');
    }

    function closeParticipantsModal() {
        document.getElementById('participantsModal').classList.add('hidden');
        document.getElementById('participantsModal').classList.remove('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }

    // Notification functions (gunakan yang sudah ada di layout)
    function showNotification(message, type = 'success') {
        if (type === 'success') {
            document.getElementById('toastMessage').textContent = message;
            document.getElementById('successToast').classList.remove('translate-x-full');
            setTimeout(() => {
                document.getElementById('successToast').classList.add('translate-x-full');
            }, 3000);
        } else {
            document.getElementById('errorToastMessage').textContent = message;
            document.getElementById('errorToast').classList.remove('translate-x-full');
            setTimeout(() => {
                document.getElementById('errorToast').classList.add('translate-x-full');
            }, 3000);
        }
    }
    
    function showLoading(message) {
        document.getElementById('loadingText').textContent = message;
        document.getElementById('loadingOverlay').classList.remove('hidden');
        document.getElementById('loadingOverlay').classList.add('flex');
    }
    
    function hideLoading() {
        document.getElementById('loadingOverlay').classList.add('hidden');
        document.getElementById('loadingOverlay').classList.remove('flex');
    }

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeEventModal();
            closeDetailModal();
            closeParticipantsModal();
            closeDeleteModal();
        }
    });

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
        const eventModal = document.getElementById('eventModal');
        const detailModal = document.getElementById('detailModal');
        const participantsModal = document.getElementById('participantsModal');
        const deleteModal = document.getElementById('deleteModal');
        
        if (eventModal && !eventModal.classList.contains('hidden') && 
            event.target === eventModal) {
            closeEventModal();
        }
        
        if (detailModal && !detailModal.classList.contains('hidden') && 
            event.target === detailModal) {
            closeDetailModal();
        }
        
        if (participantsModal && !participantsModal.classList.contains('hidden') && 
            event.target === participantsModal) {
            closeParticipantsModal();
        }
        
        if (deleteModal && !deleteModal.classList.contains('hidden') && 
            event.target === deleteModal) {
            closeDeleteModal();
        }
    });
</script>
@endpush