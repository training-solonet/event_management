<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = Participant::with('event')->latest();
        
        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }
        
        // Filter by event
        if ($request->has('event_id') && $request->event_id != '') {
            $query->where('event_id', $request->event_id);
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('transaction_code', 'like', "%$search%");
            });
        }
        
        $participants = $query->paginate(20);
        $events = Event::all();
        
        // Statistics
        $totalParticipants = Participant::count();
        $pendingCount = Participant::where('payment_status', 'pending')->count();
        $paidCount = Participant::where('payment_status', 'paid')->count();
        $verifiedCount = Participant::where('payment_status', 'verified')->count();
        
        return view('admin.pages.peserta.index', compact(
            'participants', 
            'events',
            'totalParticipants',
            'pendingCount',
            'paidCount',
            'verifiedCount'
        ));
    }

    public function show($id)
    {
        try {
            $participant = Participant::with('event')->findOrFail($id);
            
            $participant->payment_status_text = $participant->getStatusTextAttribute();
            
            // Add payment proof URL if exists
            if ($participant->payment_proof) {
                $participant->payment_proof_url = Storage::url($participant->payment_proof);
            }
            
            return response()->json([
                'success' => true,
                'participant' => $participant
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data peserta tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    public function edit(Participant $participant)
    {
        return view('admin.pages.peserta.edit', compact('participant'));
    }

    public function sendEmail(Request $request, $id)
    {
        try {
            Log::info('=== MULAI PROSES KIRIM EMAIL DENGAN BREVO API ===');
            Log::info('Participant ID: ' . $id);
            Log::info('Request Data: ', $request->all());
            
            $participant = Participant::with('event')->findOrFail($id);
            
            Log::info('Participant ditemukan: ' . $participant->email);
            
            // Validasi request
            $validated = $request->validate([
                'email_type' => 'required|in:reminder,confirmation,information,custom',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
                'send_qrcode' => 'boolean',
                'notes' => 'nullable|string'
            ]);
            
            // Validasi email peserta
            if (empty($participant->email) || !filter_var($participant->email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Email peserta tidak valid: ' . $participant->email);
            }
            
            // Tambahkan default message jika kosong
            if (empty(trim($validated['message']))) {
                $validated['message'] = $this->getDefaultMessage($validated['email_type'], $participant);
            }
            
            // Kirim email menggunakan Brevo API
            $result = $this->sendViaBrevoAPI($participant, $validated);
            
            if (!$result['success']) {
                throw new \Exception($result['message']);
            }
            
            // Update status email
            $participant->update([
                'email_notification_sent' => true,
                'email_sent_at' => now(),
                'email_notes' => $request->notes
            ]);
            
            Log::info('✅ EMAIL BERHASIL DIKIRIM VIA BREVO API');
            Log::info('Email dikirim ke: ' . $participant->email);
            Log::info('Subject: ' . $validated['subject']);
            
            return response()->json([
                'success' => true,
                'message' => '✅ Email berhasil dikirim ke ' . $participant->email
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ GAGAL MENGIRIM EMAIL');
            Log::error('Error: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => '❌ Gagal mengirim email: ' . $e->getMessage()
            ], 500);
        }
    }

    private function sendViaBrevoAPI($participant, $data)
    {
        try {
            $brevoApiKey = env('BREVO_API_KEY');
            if (empty($brevoApiKey)) {
                throw new \Exception('Brevo API key tidak ditemukan di .env');
            }
            
            // Generate HTML content untuk email
            $htmlContent = $this->generateEmailHTML($participant, $data);
            
            // Prepare payload untuk Brevo API
            $payload = [
                'sender' => [
                    'name' => env('BREVO_SENDER_NAME', 'Event Monitoring System'),
                    'email' => env('BREVO_SENDER_EMAIL', 'muabadak@gmail.com')
                ],
                'to' => [
                    [
                        'email' => $participant->email,
                        'name' => $participant->full_name
                    ]
                ],
                'subject' => $data['subject'],
                'htmlContent' => $htmlContent,
                'tags' => ['event-registration', $participant->event->name ?? 'Event']
            ];
            
            Log::info('Mengirim request ke Brevo API:', [
                'to' => $participant->email,
                'subject' => $data['subject']
            ]);
            
            // Kirim request ke Brevo API
            $response = Http::withHeaders([
                'api-key' => $brevoApiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post('https://api.brevo.com/v3/smtp/email', $payload);
            
            $responseData = $response->json();
            
            if ($response->successful()) {
                Log::info('Brevo API Response Success:', $responseData);
                return [
                    'success' => true,
                    'message' => 'Email berhasil dikirim via Brevo API',
                    'message_id' => $responseData['messageId'] ?? null
                ];
            } else {
                Log::error('Brevo API Response Error:', [
                    'status' => $response->status(),
                    'body' => $responseData
                ]);
                return [
                    'success' => false,
                    'message' => 'Brevo API Error: ' . ($responseData['message'] ?? 'Unknown error')
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Exception in sendViaBrevoAPI:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    private function generateEmailHTML($participant, $data)
    {
        $eventName = $participant->event->name ?? 'Event';
        $dateFormatted = $participant->event->date 
            ? $participant->event->date->format('d F Y') 
            : 'Akan diumumkan kemudian';
        
        // Format tanggal saat ini dengan benar
        $currentDate = now()->format('d F Y H:i');
        $currentYear = date('Y');
        
        $statusText = $participant->getStatusTextAttribute();
        $statusColor = $participant->payment_status == 'verified' ? '#10B981' : 
                      ($participant->payment_status == 'paid' ? '#3B82F6' : '#F59E0B');
        
        // Generate QR Code lokal menggunakan library PHP
        $qrCodeSection = '';
        if (isset($data['send_qrcode']) && $data['send_qrcode']) {
            $qrCodeHTML = $this->generateQRCodeHTML($participant->transaction_code);
            
            $qrCodeSection = <<<HTML
            <!-- QR Code Section -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0;">
                <tr>
                    <td align="center">
                        <table width="90%" cellpadding="0" cellspacing="0" border="0" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 10px; border: 2px dashed #667eea; padding: 20px;">
                            <tr>
                                <td align="center" style="padding-bottom: 15px;">
                                    <h3 style="color: #667eea; margin: 0; font-size: 18px; font-weight: bold;">🔳 QR Code Check-in</h3>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="padding-bottom: 15px;">
                                    <p style="color: #555; margin: 0; font-size: 14px; line-height: 1.5;">Scan QR Code berikut untuk check-in di lokasi event:</p>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="padding-bottom: 20px;">
                                    <table cellpadding="0" cellspacing="0" border="0" style="background: white; border-radius: 8px; padding: 15px; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <tr>
                                            <td align="center">
                                                {$qrCodeHTML}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="padding-bottom: 15px;">
                                    <table cellpadding="0" cellspacing="0" border="0" style="background: white; border-radius: 6px; padding: 15px; display: inline-block;">
                                        <tr>
                                            <td align="center">
                                                <p style="margin: 0; font-size: 14px; color: #666; font-weight: bold;">Kode Transaksi:</p>
                                                <p style="margin: 5px 0 0 0; font-size: 18px; font-weight: bold; color: #667eea; font-family: 'Courier New', monospace; letter-spacing: 1px;">
                                                    {$participant->transaction_code}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            HTML;
        }
        
        $html = <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>{$data['subject']}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333333;
                    margin: 0;
                    padding: 20px;
                    background-color: #f5f5f5;
                }
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                }
                .email-header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 40px 30px;
                    text-align: center;
                }
                .email-content {
                    padding: 40px 30px;
                }
                .info-section {
                    background: white;
                    border: 1px solid #e9ecef;
                    border-radius: 8px;
                    padding: 25px;
                    margin: 30px 0;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                }
                .transaction-code {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 6px;
                    font-family: 'Courier New', monospace;
                    font-size: 20px;
                    font-weight: bold;
                    text-align: center;
                    letter-spacing: 2px;
                    color: #667eea;
                    border: 2px dashed #667eea;
                    margin: 20px 0;
                }
                .important-notes {
                    background: #fff9e6;
                    border-left: 4px solid #ffc107;
                    padding: 20px;
                    border-radius: 0 8px 8px 0;
                    margin: 30px 0;
                }
                .email-footer {
                    background: #f8f9fa;
                    padding: 30px;
                    text-align: center;
                    border-top: 1px solid #e9ecef;
                    color: #666;
                    font-size: 14px;
                }
                @media only screen and (max-width: 600px) {
                    .email-content {
                        padding: 20px 15px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <!-- Header -->
                <div class="email-header">
                    <h1 style="margin: 0; font-size: 28px; font-weight: bold;">{$eventName}</h1>
                    <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">{$data['subject']}</p>
                </div>

                <!-- Content -->
                <div class="email-content">
                    <!-- Greeting -->
                    <div style="font-size: 18px; margin-bottom: 25px;">
                        Halo <strong>{$participant->full_name}</strong>,
                    </div>

                    <!-- Custom Message -->
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 25px 0; border-left: 4px solid #667eea; white-space: pre-line; line-height: 1.8;">
                        {$data['message']}
                    </div>

                    <!-- Information Section -->
                    <div class="info-section">
                        <h3 style="color: #667eea; margin: 0 0 20px 0; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0;">📋 Detail Pendaftaran Anda</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px;">
                            <div>
                                <div style="font-weight: 600; color: #555; margin-bottom: 5px; font-size: 14px;">Kode Transaksi</div>
                                <div class="transaction-code">{$participant->transaction_code}</div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #555; margin-bottom: 5px; font-size: 14px;">Nama Lengkap</div>
                                <div style="color: #333; font-size: 16px; font-weight: 500;">{$participant->full_name}</div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #555; margin-bottom: 5px; font-size: 14px;">Email</div>
                                <div style="color: #333; font-size: 16px; font-weight: 500;">{$participant->email}</div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #555; margin-bottom: 5px; font-size: 14px;">No. Telepon</div>
                                <div style="color: #333; font-size: 16px; font-weight: 500;">{$participant->phone}</div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #555; margin-bottom: 5px; font-size: 14px;">Event</div>
                                <div style="color: #333; font-size: 16px; font-weight: 500;">{$eventName}</div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #555; margin-bottom: 5px; font-size: 14px;">Tanggal Event</div>
                                <div style="color: #333; font-size: 16px; font-weight: 500;">{$dateFormatted}</div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #555; margin-bottom: 5px; font-size: 14px;">Status Pembayaran</div>
                                <div style="color: {$statusColor}; font-weight: bold;">
                                    {$statusText}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Section -->
                    {$qrCodeSection}

                    <!-- Important Notes -->
                    <div class="important-notes">
                        <h4 style="color: #d4a017; margin-top: 0;">📌 Catatan Penting</h4>
                        <ul style="margin: 15px 0; padding-left: 20px;">
                            <li>Simpan email ini sebagai bukti pendaftaran</li>
                            <li>Tunjukkan QR Code atau kode transaksi saat check-in di lokasi event</li>
                            <li>Datang 30 menit sebelum acara dimulai</li>
                            <li>Bawa identitas diri (KTP/SIM/Kartu Pelajar)</li>
                            <li>Email ini dikirim secara otomatis, mohon tidak dibalas</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div style="text-align: center; margin: 30px 0;">
                        <p style="color: #666; margin-bottom: 15px;">Butuh bantuan? Hubungi kami:</p>
                        <a href="mailto:muabadak@gmail.com" style="display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: 600; margin: 10px 5px;">📧 Email Panitia</a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="email-footer">
                    <div style="background: white; padding: 15px; border-radius: 6px; margin: 20px 0; display: inline-block;">
                        <p style="margin: 0; font-weight: bold; color: #333;">Event Monitoring System</p>
                        <p style="margin: 5px 0; color: #666;">Sistem Monitoring Event Terpadu</p>
                        <p style="margin: 5px 0;">📍 Email dikirim dari: muabadak@gmail.com</p>
                        <p style="margin: 5px 0;">📅 Tanggal: {$currentDate}</p>
                    </div>
                    <p style="margin-top: 20px;">
                        &copy; {$currentYear} Event Monitoring System. All rights reserved.<br>
                        This email was sent automatically. Please do not reply to this email.
                    </p>
                </div>
            </div>
        </body>
        </html>
        HTML;
        
        return $html;
    }

    private function generateQRCodeHTML($transactionCode)
    {
        try {
            // Method 1: Generate QR Code menggunakan Simple QrCode
            if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                // Generate QR Code sebagai SVG string (lebih kecil dan kompatibel dengan email)
                $qrCodeSVG = QrCode::size(150)
                    ->margin(2)
                    ->color(40, 40, 40)
                    ->backgroundColor(255, 255, 255)
                    ->generate($transactionCode);
                
                // Konversi SVG ke base64
                $base64SVG = base64_encode($qrCodeSVG);
                
                return <<<HTML
                <div style="text-align: center;">
                    <img src="data:image/svg+xml;base64,{$base64SVG}" 
                         alt="QR Code {$transactionCode}" 
                         style="width: 150px; height: 150px; border: 1px solid #e0e0e0; border-radius: 5px;">
                    <p style="margin-top: 10px; font-size: 12px; color: #666;">
                        <strong>Kode:</strong> {$transactionCode}
                    </p>
                </div>
                HTML;
            }
            
            // Method 2: Jika library tidak tersedia, gunakan API dengan HTTPS dan user-agent
            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . 
                         urlencode($transactionCode) . 
                         "&format=png&margin=2&color=282828&bgcolor=FFFFFF";
            
            return <<<HTML
            <div style="text-align: center;">
                <img src="{$qrCodeUrl}" 
                     alt="QR Code {$transactionCode}" 
                     style="width: 150px; height: 150px; border: 1px solid #e0e0e0; border-radius: 5px;">
                <p style="margin-top: 10px; font-size: 12px; color: #666;">
                    <strong>Kode:</strong> {$transactionCode}
                </p>
            </div>
            HTML;
            
        } catch (\Exception $e) {
            Log::error('Error generating QR Code: ' . $e->getMessage());
            
            // Fallback: Tampilkan kode transaksi dalam format khusus
            return <<<HTML
            <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px; border: 2px dashed #667eea;">
                <div style="font-size: 24px; font-weight: bold; color: #667eea; margin-bottom: 10px;">
                    QR CODE
                </div>
                <div style="font-family: 'Courier New', monospace; font-size: 16px; font-weight: bold; letter-spacing: 2px; color: #333; margin: 10px 0;">
                    {$transactionCode}
                </div>
                <div style="font-size: 12px; color: #666; margin-top: 10px;">
                    Tunjukkan kode ini saat check-in
                </div>
            </div>
            HTML;
        }
    }

    private function getDefaultMessage($emailType, $participant)
    {
        $eventName = $participant->event->name ?? 'Event';
        $dateFormatted = $participant->event->date 
            ? $participant->event->date->format('d F Y') 
            : 'Akan diumumkan kemudian';
        
        $messages = [
            'confirmation' => "Terima kasih telah mendaftar pada event **{$eventName}**.\n\n" .
                "📋 **Detail Pendaftaran Anda:**\n" .
                "• Kode Transaksi: **{$participant->transaction_code}**\n" .
                "• Nama: {$participant->full_name}\n" .
                "• Email: {$participant->email}\n" .
                "• Status: {$participant->payment_status_text}\n" .
                "• Tanggal Event: {$dateFormatted}\n\n" .
                "QR Code check-in telah disertakan dalam email ini.\n" .
                "Gunakan QR Code atau kode transaksi untuk check-in di lokasi event.\n\n" .
                "Simpan email ini sebagai bukti pendaftaran Anda.\n\n" .
                "Terima kasih,\n" .
                "Panitia {$eventName}",

            'reminder' => "Ini adalah pengingat untuk event **{$eventName}**.\n\n" .
                "⏰ **Event akan dilaksanakan pada:**\n" .
                "Tanggal: {$dateFormatted}\n\n" .
                "🔑 **Kode Transaksi Anda:**\n" .
                "**{$participant->transaction_code}**\n\n" .
                "QR Code check-in telah disertakan dalam email ini.\n\n" .
                "📌 **Persiapan yang perlu dibawa:**\n" .
                "1. Email ini (digital atau print)\n" .
                "2. QR Code atau kode transaksi\n" .
                "3. Identitas diri (KTP/SIM/Kartu Pelajar)\n\n" .
                "Datang 30 menit sebelum acara dimulai.\n\n" .
                "Panitia {$eventName}",

            'information' => "Berikut informasi penting terkait event **{$eventName}**.\n\n" .
                "📝 **Detail Event:**\n" .
                "• Kode Transaksi: **{$participant->transaction_code}**\n" .
                "• Tanggal: {$dateFormatted}\n\n" .
                "QR Code check-in telah disertakan dalam email ini.\n\n" .
                "Mohon perhatikan informasi yang disampaikan.\n\n" .
                "Panitia {$eventName}",

            'custom' => "Berikut adalah informasi dari panitia event **{$eventName}**.\n\n" .
                "Kode Transaksi Anda: **{$participant->transaction_code}**\n\n" .
                "QR Code check-in telah disertakan dalam email ini.\n\n" .
                "Panitia {$eventName}"
        ];
        
        return $messages[$emailType] ?? $messages['confirmation'];
    }

    public function searchByTransactionCode(Request $request)
    {
        try {
            $request->validate([
                'transaction_code' => 'required|string'
            ]);
            
            Log::info('Searching participant by transaction code: ' . $request->transaction_code);
            
            $participant = Participant::with('event')
                ->where('transaction_code', $request->transaction_code)
                ->first();
            
            if (!$participant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peserta tidak ditemukan dengan kode transaksi: ' . $request->transaction_code
                ], 404);
            }
            
            $participant->payment_status_text = $participant->getStatusTextAttribute();
            
            // PERBAIKAN: Gunakan path yang benar untuk payment proof
            if ($participant->payment_proof) {
                // Cek apakah sudah ada path 'payment_proofs/'
                if (strpos($participant->payment_proof, 'payment_proofs/') === false) {
                    $participant->payment_proof_url = Storage::url('payment_proofs/' . $participant->payment_proof);
                } else {
                    $participant->payment_proof_url = Storage::url($participant->payment_proof);
                }
            }
            
            Log::info('Participant found: ' . $participant->full_name);
            
            return response()->json([
                'success' => true,
                'participant' => $participant
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error searching participant by transaction code: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update participant status - FIXED VERSION
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('=== UPDATE PARTICIPANT STATUS ===');
            Log::info('Participant ID: ' . $id);
            Log::info('Request Data: ', $request->all());
            
            $participant = Participant::findOrFail($id);
            
            $validated = $request->validate([
                'payment_status' => 'required|in:pending,paid,verified',
                'notes' => 'nullable|string|max:500'
            ]);

            $oldStatus = $participant->payment_status;
            $participant->update($validated);
            
            Log::info('Status updated from ' . $oldStatus . ' to ' . $participant->payment_status);

            // Return JSON for AJAX requests
            return response()->json([
                'success' => true,
                'message' => 'Status peserta berhasil diperbarui',
                'payment_status' => $participant->payment_status,
                'payment_status_text' => $participant->getStatusTextAttribute(),
                'status_badge' => $participant->getStatusBadgeAttribute()
            ]);
                
        } catch (\Exception $e) {
            Log::error('Error updating participant: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $participant = Participant::findOrFail($id);
            
            // Delete payment proof if exists
            if ($participant->payment_proof && Storage::exists($participant->payment_proof)) {
                Storage::delete($participant->payment_proof);
            }

            $participant->delete();

            // Return JSON for AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Peserta berhasil dihapus'
                ]);
            }

            return redirect()->route('admin.peserta.index')
                ->with('success', 'Peserta berhasil dihapus');
                
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.peserta.index')
                ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    // Test Email Function untuk testing Brevo API
    public function testBrevoEmail($id)
    {
        try {
            Log::info('=== TEST BREVO API EMAIL ===');
            
            $participant = Participant::with('event')->findOrFail($id);
            
            Log::info('Mengirim test email ke: ' . $participant->email);
            
            $testData = [
                'email_type' => 'confirmation',
                'subject' => '🎉 Test Email dari Event Monitoring System',
                'message' => 'Ini adalah email testing dari sistem Event Monitoring. Jika Anda menerima email ini, berarti koneksi ke Brevo API berfungsi dengan baik! ✅',
                'send_qrcode' => true,
                'notes' => 'Email testing dari admin untuk verifikasi Brevo API'
            ];
            
            $result = $this->sendViaBrevoAPI($participant, $testData);
            
            if (!$result['success']) {
                throw new \Exception($result['message']);
            }
            
            Log::info('✅ TEST EMAIL BERHASIL DIKIRIM VIA BREVO API');
            
            return response()->json([
                'success' => true,
                'message' => '✅ Test email berhasil dikirim ke ' . $participant->email . ' via Brevo API'
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ TEST BREVO API FAILED');
            Log::error('Error: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => '❌ Gagal mengirim test email via Brevo API: ' . $e->getMessage()
            ], 500);
        }
    }

    // Test API Connection
    public function testBrevoConnection()
    {
        try {
            $brevoApiKey = env('BREVO_API_KEY');
            if (empty($brevoApiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brevo API key tidak ditemukan di .env'
                ]);
            }
            
            // Test connection dengan mengambil account info
            $response = Http::withHeaders([
                'api-key' => $brevoApiKey,
                'Accept' => 'application/json'
            ])->get('https://api.brevo.com/v3/account');
            
            if ($response->successful()) {
                $accountInfo = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => '✅ Koneksi Brevo API berhasil',
                    'data' => [
                        'email' => $accountInfo['email'] ?? 'N/A',
                        'plan' => $accountInfo['plan'][0]['type'] ?? 'N/A',
                        'credits' => $accountInfo['plan'][0]['credits'] ?? 'N/A'
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Gagal koneksi ke Brevo API: ' . $response->body()
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Exception: ' . $e->getMessage()
            ]);
        }
    }

    
}