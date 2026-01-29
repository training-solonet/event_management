<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject ?? 'Konfirmasi Pendaftaran' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px dashed #667eea;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        table td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .highlight {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }
        @media only screen and (max-width: 600px) {
            .container {
                border-radius: 0;
            }
            .content {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $participant->event->name ?? 'Event' }}</h1>
            <p>Konfirmasi Pendaftaran Peserta</p>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $participant->full_name }}</strong>,</p>
            
            <p>{{ nl2br(e($customMessage)) }}</p>
            
            <div class="info-card">
                <h3 style="margin-top: 0; color: #667eea;">Informasi Pendaftaran</h3>
                <table>
                    <tr>
                        <td>Kode Transaksi:</td>
                        <td><strong>{{ $participant->transaction_code }}</strong></td>
                    </tr>
                    <tr>
                        <td>Nama Lengkap:</td>
                        <td>{{ $participant->full_name }}</td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td>{{ $participant->email }}</td>
                    </tr>
                    <tr>
                        <td>No. Telepon:</td>
                        <td>{{ $participant->phone }}</td>
                    </tr>
                    <tr>
                        <td>Event:</td>
                        <td>{{ $participant->event->name ?? 'Tidak tersedia' }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Event:</td>
                        <td>{{ $participant->event->date ? $participant->event->date->format('d F Y') : 'Tidak tersedia' }}</td>
                    </tr>
                    <tr>
                        <td>Status Pembayaran:</td>
                        <td>
                            @if($participant->payment_status == 'verified')
                                <span style="color: green; font-weight: bold;">✓ Terverifikasi</span>
                            @elseif($participant->payment_status == 'paid')
                                <span style="color: blue; font-weight: bold;">✓ Sudah Bayar</span>
                            @else
                                <span style="color: orange; font-weight: bold;">⏳ Menunggu Pembayaran</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            
            @if($sendQrcode)
            <div class="qr-section">
                <h3 style="color: #667eea;">QR Code Check-in</h3>
                <p>Gunakan QR Code berikut untuk check-in di lokasi event:</p>
                <div style="margin: 20px 0;">
                    <!-- QR Code akan digenerate di email client -->
                    <div style="background: white; padding: 20px; display: inline-block; border: 2px solid #ddd; border-radius: 5px;">
                        <div style="font-family: monospace; font-size: 24px; font-weight: bold; color: #333;">
                            {{ substr($participant->transaction_code, 0, 4) }}<br>
                            {{ substr($participant->transaction_code, 4, 4) }}<br>
                            {{ substr($participant->transaction_code, 8, 4) }}
                        </div>
                    </div>
                </div>
                <p><strong>Kode:</strong> {{ $participant->transaction_code }}</p>
                <p style="font-size: 12px; color: #666;">* Tunjukkan kode ini saat check-in di lokasi event</p>
            </div>
            @endif
            
            <div class="highlight">
                <p><strong>📌 Catatan Penting:</strong></p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Simpan email ini sebagai bukti pendaftaran</li>
                    <li>Tunjukkan QR Code/kode transaksi saat check-in</li>
                    <li>Datang 30 menit sebelum acara dimulai</li>
                    <li>Bawa identitas diri (KTP/SIM/Kartu Pelajar)</li>
                </ul>
            </div>
            
            <p style="margin-top: 30px;">
                Salam hormat,<br>
                <strong>Panitia {{ $participant->event->name ?? 'Event' }}</strong>
            </p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
            <p>Jika Anda memiliki pertanyaan, hubungi panitia melalui:</p>
            <p>📧 Email: panitia@example.com | 📞 Telp: (021) 1234-5678</p>
            <p>&copy; {{ date('Y') }} {{ $participant->event->organizer ?? 'Panitia Event' }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>