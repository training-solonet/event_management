<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount(['participants as participants_count'])->latest()->paginate(10);
        $totalParticipants = Participant::count();
        $activeEventsCount = Event::where('is_active', true)->count();
        
        return view('admin.pages.event.index', compact('events', 'totalParticipants', 'activeEventsCount'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'available_slots' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $validated['event_code'] = 'EV-' . strtoupper(Str::random(8));
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        Event::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil dibuat'
        ]);
    }

    public function show(Request $request, Event $event)
    {
        try {
            if ($request->has('participants')) {
                // Load participants with their event relation
                $participants = $event->participants()
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                return response()->json([
                    'success' => true,
                    'event' => [
                        'id' => $event->id,
                        'name' => $event->name,
                        'event_code' => $event->event_code,
                        'participants_count' => $event->participants_count
                    ],
                    'participants' => $participants->map(function($participant) {
                        return [
                            'id' => $participant->id,
                            'full_name' => $participant->full_name,
                            'email' => $participant->email,
                            'phone' => $participant->phone,
                            'gender' => $participant->gender,
                            'nik' => $participant->nik,
                            'address' => $participant->address,
                            'transaction_code' => $participant->transaction_code,
                            'payment_method' => $participant->payment_method,
                            'payment_status' => $participant->payment_status,
                            'payment_status_text' => $participant->status_text,
                            'payment_proof' => $participant->payment_proof,
                            'payment_proof_url' => $participant->payment_proof_url,
                            'notes' => $participant->notes,
                            'created_at' => $participant->created_at ? $participant->created_at->format('Y-m-d H:i:s') : null,
                            'updated_at' => $participant->updated_at ? $participant->updated_at->format('Y-m-d H:i:s') : null
                        ];
                    })
                ]);
            }
            
            return response()->json([
                'success' => true,
                'event' => [
                    'id' => $event->id,
                    'event_code' => $event->event_code,
                    'name' => $event->name,
                    'date' => $event->date ? $event->date->format('Y-m-d') : null,
                    'price' => $event->price,
                    'location' => $event->location,
                    'type' => $event->type,
                    'description' => $event->description,
                    'is_active' => (bool) $event->is_active,
                    'available_slots' => $event->available_slots,
                    'participants_count' => $event->participants_count,
                    'created_at' => $event->created_at ? $event->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $event->updated_at ? $event->updated_at->format('Y-m-d H:i:s') : null
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading event data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data event: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'available_slots' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        $event->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil diperbarui'
        ]);
    }

    public function destroy(Event $event)
    {
        if ($event->participants()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus event yang sudah memiliki peserta'
            ], 400);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil dihapus'
        ]);
    }
}