@extends('admin.layouts.admin.admin')

@section('title', 'Metode Pembayaran')
@section('page-title', 'Metode Pembayaran')
@section('page-subtitle', 'Kelola metode pembayaran yang tersedia')

@section('content')
<div class="space-y-6">
    <!-- Header dengan tombol tambah -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Metode Pembayaran</h2>
            <p class="text-gray-600">Kelola semua metode pembayaran yang tersedia</p>
        </div>
        <button onclick="openCreateModal()" 
                class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow hover:shadow-md flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Metode
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-credit-card text-xl"></i>
                </div>
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Metode</p>
                    <p class="text-2xl md:text-3xl font-bold">{{ $paymentMethods->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-toggle-on text-xl"></i>
                </div>
                <div>
                    <p class="text-green-100 text-sm font-medium">Aktif</p>
                    <p class="text-2xl md:text-3xl font-bold">{{ $activeCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-bank text-xl"></i>
                </div>
                <div>
                    <p class="text-purple-100 text-sm font-medium">Bank & E-Wallet</p>
                    <p class="text-2xl md:text-3xl font-bold">{{ $bankEwalletCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($paymentMethods as $method)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow duration-200">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-3 
                            @if($method->is_active) bg-gradient-to-r from-green-100 to-green-200
                            @else bg-gradient-to-r from-gray-100 to-gray-200 @endif">
                            @if($method->type == 'bank')
                                <i class="fas fa-university 
                                    @if($method->is_active) text-green-600
                                    @else text-gray-400 @endif text-xl">
                                </i>
                            @elseif($method->type == 'ewallet')
                                <i class="fas fa-wallet 
                                    @if($method->is_active) text-green-600
                                    @else text-gray-400 @endif text-xl">
                                </i>
                            @elseif($method->type == 'cash')
                                <i class="fas fa-money-bill-wave 
                                    @if($method->is_active) text-green-600
                                    @else text-gray-400 @endif text-xl">
                                </i>
                            @else
                                <i class="fas fa-credit-card 
                                    @if($method->is_active) text-green-600
                                    @else text-gray-400 @endif text-xl">
                                </i>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ $method->name }}</h3>
                            <div class="flex items-center mt-1">
                                @if($method->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-circle text-xs mr-1"></i> Aktif
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-circle text-xs mr-1"></i> Nonaktif
                                </span>
                                @endif
                                <span class="ml-2 text-xs text-gray-500 capitalize">{{ $method->type }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <button onclick="toggleOptions({{ $method->id }})" 
                                class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <div id="options-{{ $method->id }}" class="hidden absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg z-10 border border-gray-200">
                            <button onclick="openEditModal({{ $method->id }})" 
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </button>
                            <button onclick="toggleStatus({{ $method->id }})" 
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                @if($method->is_active)
                                <i class="fas fa-toggle-off mr-2"></i> Nonaktifkan
                                @else
                                <i class="fas fa-toggle-on mr-2"></i> Aktifkan
                                @endif
                            </button>
                            <button onclick="deleteMethod({{ $method->id }})" 
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <i class="fas fa-trash mr-2"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Account Details -->
                @if(in_array($method->type, ['bank', 'ewallet']))
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <h4 class="font-medium text-gray-700 mb-2">Informasi Akun</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Nomor Rekening/Akun:</span>
                            <span class="font-medium">{{ $method->account_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 text-sm">Atas Nama:</span>
                            <span class="font-medium">{{ $method->account_name }}</span>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Footer Stats -->
                <div class="flex items-center justify-between text-sm text-gray-500 pt-4 border-t border-gray-200">
                    <span>
                        <i class="far fa-calendar mr-1"></i>
                        {{ $method->created_at->format('d M Y') }}
                    </span>
                    @if(in_array($method->type, ['bank', 'ewallet']))
                    <span>
                        <i class="fas fa-info-circle mr-1"></i>
                        Transfer {{ $method->type == 'bank' ? 'Bank' : 'E-Wallet' }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="md:col-span-2 lg:col-span-3">
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-credit-card text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada metode pembayaran</h3>
                <p class="text-gray-500 mb-6">Tambahkan metode pembayaran untuk memulai</p>
                <button onclick="openCreateModal()" 
                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700">
                    <i class="fas fa-plus mr-2"></i> Tambah Metode Pertama
                </button>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800" id="modalTitle">Tambah Metode Pembayaran</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="paymentForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="payment_id" id="paymentId">
                
                <div class="space-y-4">
                    <!-- Nama Metode -->
                    <div>
                        <label class="block text-gray-700 mb-2">Nama Metode Pembayaran *</label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: Bank BCA, OVO, Dana">
                    </div>
                    
                    <!-- Tipe Metode -->
                    <div>
                        <label class="block text-gray-700 mb-2">Tipe Pembayaran *</label>
                        <select name="type" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onchange="toggleAccountFields(this.value)">
                            <option value="">Pilih Tipe</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="ewallet">E-Wallet</option>
                            <option value="cash">Tunai</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    
                    <!-- Account Fields (for bank and ewallet) -->
                    <div id="accountFields" class="hidden space-y-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Nomor Rekening/Akun *</label>
                            <input type="text" name="account_number"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Contoh: 1234567890">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Atas Nama *</label>
                            <input type="text" name="account_name"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Contoh: John Doe">
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" checked class="mr-2">
                            <span class="text-gray-700">Aktifkan metode ini</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1">Metode tidak aktif tidak akan ditampilkan ke peserta</p>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end space-x-4">
                    <button type="button" onclick="closeModal()" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 font-semibold">
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
                <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus Metode</h3>
                <p class="text-gray-600">Apakah Anda yakin ingin menghapus metode pembayaran ini?</p>
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
                        <i class="fas fa-trash mr-2"></i> Hapus
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
</style>
@endpush

@push('scripts')
<script>
    // // Set CSRF token - PASTIKAN INI ADA
    // const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Toggle options dropdown
    function toggleOptions(methodId) {
        const dropdown = document.getElementById(`options-${methodId}`);
        dropdown.classList.toggle('hidden');
        
        // Close other dropdowns
        document.querySelectorAll('[id^="options-"]').forEach(el => {
            if (el.id !== `options-${methodId}`) {
                el.classList.add('hidden');
            }
        });
    }
    
    // Toggle status - FIXED: Menggunakan route update dengan parameter khusus
    async function toggleStatus(methodId) {
        try {
            showLoading('Mengubah status...');
            
            // Kirim request PUT ke route update dengan data khusus untuk toggle
            const response = await fetch(`{{ url('admin/payment') }}/${methodId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    toggle_status: true // Parameter khusus untuk toggle
                })
            });
            
            const data = await response.json();
            hideLoading();
            
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                // Tampilkan error validasi jika ada
                if (data.errors) {
                    let errorMessages = [];
                    for (const [field, errors] of Object.entries(data.errors)) {
                        errorMessages.push(...errors);
                    }
                    showNotification('Validasi gagal: ' + errorMessages.join(', '), 'error');
                } else {
                    showNotification(data.message || 'Gagal mengubah status', 'error');
                }
            }
        } catch (error) {
            hideLoading();
            console.error('Error toggling status:', error);
            showNotification('Gagal mengubah status', 'error');
        }
    }
    
    // Open create modal
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Metode Pembayaran';
        document.getElementById('paymentForm').action = '{{ route("admin.payment.store") }}';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('paymentId').value = '';
        document.getElementById('paymentForm').reset();
        document.getElementById('accountFields').classList.add('hidden');
        document.querySelector('input[name="is_active"]').checked = true;
        
        document.getElementById('paymentModal').classList.remove('hidden');
        document.getElementById('paymentModal').classList.add('flex');
    }
    
    // Open edit modal
    async function openEditModal(methodId) {
        try {
            showLoading('Memuat data...');
            
            const response = await fetch(`{{ url('admin/payment') }}/${methodId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            hideLoading();
            
            if (!data.success || !data.payment) {
                throw new Error('Data metode pembayaran tidak ditemukan');
            }
            
            const method = data.payment;
            
            document.getElementById('modalTitle').textContent = 'Edit Metode Pembayaran';
            document.getElementById('paymentForm').action = `{{ url('admin/payment') }}/${methodId}`;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('paymentId').value = method.id;
            
            // Fill form
            document.querySelector('input[name="name"]').value = method.name;
            document.querySelector('select[name="type"]').value = method.type;
            document.querySelector('input[name="is_active"]').checked = method.is_active;
            
            // Toggle account fields
            toggleAccountFields(method.type);
            
            if (method.type === 'bank' || method.type === 'ewallet') {
                document.querySelector('input[name="account_number"]').value = method.account_number || '';
                document.querySelector('input[name="account_name"]').value = method.account_name || '';
            }
            
            document.getElementById('paymentModal').classList.remove('hidden');
            document.getElementById('paymentModal').classList.add('flex');
        } catch (error) {
            hideLoading();
            console.error('Error opening edit modal:', error);
            showNotification('Gagal memuat data', 'error');
        }
    }
    
    // Toggle account fields based on payment type
    function toggleAccountFields(type) {
        const accountFields = document.getElementById('accountFields');
        const accountNumberInput = document.querySelector('input[name="account_number"]');
        const accountNameInput = document.querySelector('input[name="account_name"]');
        
        if (type === 'bank' || type === 'ewallet') {
            accountFields.classList.remove('hidden');
            accountNumberInput.required = true;
            accountNameInput.required = true;
        } else {
            accountFields.classList.add('hidden');
            accountNumberInput.required = false;
            accountNameInput.required = false;
        }
    }
    
    // Handle form submission untuk create/edit
    document.getElementById('paymentForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const action = form.getAttribute('action');
        const method = document.getElementById('formMethod').value;
        const paymentId = document.getElementById('paymentId').value;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Convert is_active to boolean
        data.is_active = form.querySelector('input[name="is_active"]').checked;
        
        // For bank/ewallet, ensure account_number and account_name are included
        if (data.type !== 'bank' && data.type !== 'ewallet') {
            data.account_number = null;
            data.account_name = null;
        }
        
        showLoading('Menyimpan data...');
        
        try {
            const response = await fetch(action, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const responseData = await response.json();
            hideLoading();
            
            if (responseData.success) {
                showNotification(responseData.message, 'success');
                closeModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                // Tampilkan error validasi
                if (responseData.errors) {
                    let errorMessages = [];
                    for (const [field, errors] of Object.entries(responseData.errors)) {
                        errorMessages.push(...errors);
                    }
                    showNotification('Validasi gagal: ' + errorMessages.join(', '), 'error');
                } else {
                    showNotification(responseData.message || 'Gagal menyimpan data', 'error');
                }
            }
        } catch (error) {
            hideLoading();
            console.error('Error saving payment method:', error);
            showNotification('Gagal menyimpan data', 'error');
        }
    });
    
    // Delete method
    function deleteMethod(methodId) {
        document.getElementById('deleteForm').action = `{{ url('admin/payment') }}/${methodId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }
    
    // Handle delete form submission
    document.getElementById('deleteForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const action = form.getAttribute('action');
        
        showLoading('Menghapus metode...');
        
        try {
            const response = await fetch(action, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
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
                showNotification(data.message || 'Gagal menghapus metode', 'error');
                closeDeleteModal();
            }
        } catch (error) {
            hideLoading();
            console.error('Error deleting method:', error);
            showNotification('Gagal menghapus metode', 'error');
            closeDeleteModal();
        }
    });
    
    // Close modals
    function closeModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        document.getElementById('paymentModal').classList.remove('flex');
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.matches('.relative *')) {
            document.querySelectorAll('[id^="options-"]').forEach(el => {
                el.classList.add('hidden');
            });
        }
    });
    
    // Helper functions
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
            loadingEl.remove();
        }
    }
    
    function showNotification(message, type) {
        // Hapus notifikasi sebelumnya
        document.querySelectorAll('.notification').forEach(el => el.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-3"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
            closeDeleteModal();
        }
    });
    
    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
        const paymentModal = document.getElementById('paymentModal');
        const deleteModal = document.getElementById('deleteModal');
        
        if (paymentModal && event.target === paymentModal) {
            closeModal();
        }
        
        if (deleteModal && event.target === deleteModal) {
            closeDeleteModal();
        }
    });
</script>
@endpush