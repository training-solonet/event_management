<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::latest()->get();
        $activeCount = PaymentMethod::where('is_active', true)->count();
        $bankEwalletCount = PaymentMethod::whereIn('type', ['bank', 'ewallet'])->count();
        
        return view('admin.pages.pembayaran.index', compact('paymentMethods', 'activeCount', 'bankEwalletCount'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,ewallet,cash,other',
            'account_number' => 'required_if:type,bank,ewallet|nullable|string|max:100',
            'account_name' => 'required_if:type,bank,ewallet|nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $validated['is_active'] = $request->boolean('is_active');

        PaymentMethod::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran berhasil ditambahkan'
        ]);
    }

    public function show(PaymentMethod $payment)
    {
        return response()->json([
            'success' => true,
            'payment' => $payment
        ]);
    }

    public function update(Request $request, PaymentMethod $payment)
    {
        // Cek jika ini request untuk toggle status
        if ($request->has('toggle_status') && $request->toggle_status == true) {
            // Hanya update status tanpa validasi field lainnya
            $payment->update([
                'is_active' => !$payment->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah',
                'is_active' => $payment->is_active
            ]);
        }

        // Jika bukan toggle, lakukan validasi untuk update biasa
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,ewallet,cash,other',
            'account_number' => 'required_if:type,bank,ewallet|nullable|string|max:100',
            'account_name' => 'required_if:type,bank,ewallet|nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $validated['is_active'] = $request->boolean('is_active');

        if (!in_array($validated['type'], ['bank', 'ewallet'])) {
            $validated['account_number'] = null;
            $validated['account_name'] = null;
        }

        $payment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran berhasil diperbarui'
        ]);
    }

    public function destroy(PaymentMethod $payment)
    {
        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran berhasil dihapus'
        ]);
    }
}