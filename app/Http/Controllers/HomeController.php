<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Participant;
use App\Models\PaymentMethod;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $events = Event::where('is_active', true)
            ->orderBy('date', 'asc')
            ->get();
        
        return view('peserta.pages.index', compact('events'));
    }

    public function getEventDetails($id)
    {
        $event = Event::findOrFail($id);
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        
        return response()->json([
            'event' => $event,
            'payment_methods' => $paymentMethods
        ]);
    }

    public function searchParticipant(Request $request)
    {
        $request->validate([
            'search' => 'required|string'
        ]);
        
        $search = $request->search;
        
        $participants = Participant::with('event')
            ->where('nik', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('transaction_code', 'like', "%{$search}%")
            ->get();
        
        return response()->json([
            'success' => true,
            'participants' => $participants->map(function($participant) {
                return [
                    'id' => $participant->id,
                    'transaction_code' => $participant->transaction_code,
                    'full_name' => $participant->full_name,
                    'email' => $participant->email,
                    'phone' => $participant->phone,
                    'gender' => $participant->gender,
                    'nik' => $participant->nik,
                    'payment_method' => $participant->payment_method,
                    'payment_status' => $participant->payment_status,
                    'payment_proof' => $participant->payment_proof,
                    'notes' => $participant->notes,
                    'created_at' => $participant->created_at->format('d-m-Y H:i'),
                    'event' => [
                        'name' => $participant->event->name,
                        'date' => $participant->event->date->format('d-m-Y'),
                        'location' => $participant->event->location
                    ]
                ];
            })
        ]);
    }

    public function storeParticipant(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'nik' => 'required|string|max:20',
            'address' => 'required|string',
            'payment_method' => 'required|string',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);
        
        try {
            $event = Event::findOrFail($request->event_id);
            
            if (!$event->canRegister()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, event ini sudah penuh atau tidak aktif.'
                ], 400);
            }
            
            // Generate unique transaction code
            $transactionCode = $this->generateUniqueTransactionCode($event->event_code);
            
            $participantData = [
                'transaction_code' => $transactionCode,
                'event_id' => $request->event_id,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'nik' => $request->nik,
                'address' => $request->address,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'notes' => 'Pendaftaran berhasil. Silakan tunggu verifikasi pembayaran.'
            ];
            
            // Handle upload payment proof
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = time() . '_' . Str::slug($request->full_name) . '_' . $file->getClientOriginalName();
                $file->storeAs('payment_proofs', $filename, 'public');
                $participantData['payment_proof'] = $filename;
            }
            
            $participant = Participant::create($participantData);
            
            $event->increment('registered_count');
            
            // Return data for WhatsApp message
            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil! Anda akan diarahkan ke WhatsApp untuk konfirmasi.',
                'transaction_code' => $transactionCode,
                'participant' => [
                    'id' => $participant->id,
                    'full_name' => $participant->full_name,
                    'email' => $participant->email,
                    'phone' => $participant->phone,
                    'address' => $participant->address
                ],
                'event' => [
                    'name' => $event->name,
                    'date' => $event->date,
                    'location' => $event->location
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate unique transaction code
     */
    private function generateUniqueTransactionCode($eventCode)
    {
        do {
            // Format: EVENT-CODE-YYYYMMDD-RANDOM6
            $datePart = date('Ymd');
            $randomPart = strtoupper(Str::random(6));
            $transactionCode = $eventCode . '-' . $datePart . '-' . $randomPart;
        } while (Participant::where('transaction_code', $transactionCode)->exists());
        
        return $transactionCode;
    }
}