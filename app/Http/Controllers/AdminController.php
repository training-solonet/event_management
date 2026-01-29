<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        try {
            // Stats data
            $stats = [
                'total_participants' => Participant::count(),
                'active_events' => Event::where('is_active', true)->count(),
                'pending_payments' => Participant::where('payment_status', 'pending')->count(),
                'verified_participants' => Participant::where('payment_status', 'verified')->count(),
                'total_events' => Event::count()
            ];
            
            // Recent registrations (10 terbaru)
            $recentRegistrations = Participant::with('event')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
                
            // Active events
            $activeEvents = Event::where('is_active', true)
                ->orderBy('date', 'asc')
                ->limit(5)
                ->get();
                
            // Handle AJAX requests
            if (request()->ajax()) {
                if (request()->has('stats_only')) {
                    return response()->json([
                        'success' => true,
                        'stats' => $stats
                    ]);
                }
                
                if (request()->has('registrations_only')) {
                    $registrations = Participant::with('event')
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                    
                    return response()->json([
                        'success' => true,
                        'registrations' => $registrations->map(function($participant) {
                            // PERBAIKAN: Gunakan path yang benar untuk payment proof
                            $participant->payment_proof_url = $participant->payment_proof ? 
                                Storage::url('payment_proofs/' . $participant->payment_proof) : 
                                Storage::url($participant->payment_proof);
                            return $participant;
                        })
                    ]);
                }
            }
                
            return view("admin.pages.index", compact('stats', 'recentRegistrations', 'activeEvents'));
            
        } catch (\Exception $e) {
            // Fallback jika ada error
            return view("admin.pages.index", [
                'stats' => [
                    'total_participants' => 0,
                    'active_events' => 0,
                    'pending_payments' => 0,
                    'verified_participants' => 0,
                    'total_events' => 0
                ],
                'recentRegistrations' => collect(),
                'activeEvents' => collect()
            ]);
        }
    }
    
    // Handle participant status update
    public function update(Request $request, $id)
    {
        try {
            // Find the participant
            $participant = Participant::findOrFail($id);
            
            // Validate the request
            $validated = $request->validate([
                'payment_status' => 'required|in:pending,paid,verified',
                'notes' => 'nullable|string'
            ]);
            
            // Update participant
            $participant->update($validated);
            
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Status peserta berhasil diperbarui',
                'participant' => $participant->fresh()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Method untuk mengambil data peserta
    public function getParticipant($id)
    {
        try {
            $participant = Participant::with('event')->findOrFail($id);
            
            // PERBAIKAN: Tambahkan URL untuk bukti pembayaran dengan path yang benar
            $participant->payment_proof_url = $participant->payment_proof ? 
                Storage::url('payment_proofs/' . $participant->payment_proof) : 
                null;
            
            return response()->json([
                'success' => true,
                'data' => $participant
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data peserta tidak ditemukan'
            ], 404);
        }
    }
    
    // Method edit untuk modal verifikasi
    public function edit($id)
    {
        try {
            $participant = Participant::with('event')->findOrFail($id);
            
            // PERBAIKAN: Tambahkan URL untuk bukti pembayaran dengan path yang benar
            $participant->payment_proof_url = $participant->payment_proof ? 
                Storage::url('payment_proofs/' . $participant->payment_proof) : 
                null;
            
            return response()->json([
                'success' => true,
                'data' => $participant
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }
    
    // Method lainnya
    public function create() { /* ... */ }
    public function store(Request $request) { /* ... */ }
    public function show($id) { 
        try {
            $participant = Participant::with('event')->findOrFail($id);
            
            // PERBAIKAN: Tambahkan URL untuk bukti pembayaran dengan path yang benar
            $participant->payment_proof_url = $participant->payment_proof ? 
                Storage::url('payment_proofs/' . $participant->payment_proof) : 
                null;
            
            return response()->json([
                'success' => true,
                'data' => $participant
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }
    public function destroy($id) { /* ... */ }
}