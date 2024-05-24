<?php

namespace App\Http\Controllers;

use App\Enums\BillStatus;
use App\Enums\PaymentStatus;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return view('dashboard.pages.payments.index');
    }

    public function reject(Payment $payment)
    {
        $payment->update([
            'status' => PaymentStatus::UNVALIDATED->value,
        ]);

        toast('Berhasil menolak pembayaran', 'success');

        return redirect()->route('dashboard.payments.index');
    }

    public function accept(Payment $payment)
    {
        return view('dashboard.pages.payments.accept', [
            'payment' => $payment,
        ]);
    }

    public function acceptProcess(Request $request, Payment $payment)
    {
        $request->validate([
            'nominal' => 'required|numeric',
        ]);
        $payment->update([
            'status' => PaymentStatus::VALIDATED->value,
            'nominal' => $request->nominal,
        ]);

        $this->checkBillStatus($payment->bill);

        toast('Berhasil menyetujui pembayaran', 'success');

        return redirect()->route('dashboard.payments.index');
    }

    private function checkBillStatus(Bill $bill)
    {
        $totalPaid = Payment::query()
            ->where('bill_id', $bill->id)
            ->where('status', PaymentStatus::VALIDATED->value)
            ->sum('nominal');

        if ($bill->nominal - $totalPaid - $bill->discount <= 0) {
            $bill->update([
                'status' => BillStatus::PAID_OFF->value,
            ]);
        }
    }
}
