<?php

namespace App\Exports;

use App\Enums\PaymentStatus;
use App\Models\Bill;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentBillsExport implements FromCollection, WithHeadings
{
    private Student $student;

    public function headings(): array
    {
        return ['Bulan', 'Tahun Ajaran', 'Nominal', 'Diskon', 'Total Dibayar', 'Sisa Tagihan'];
    }

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Bill::query()
            ->where('id_siswa', $this->student->id)
            ->select('bulan', 'tahun_ajaran', 'nominal', 'diskon')
            ->addSelect([
                'total_paid' => function ($query) {
                    $query->selectRaw('SUM(nominal) as total_paid')
                        ->from('pembayaran')
                        ->whereColumn('id_tagihan', 'tagihan.id')
                        ->where('status', PaymentStatus::VALIDATED->value);
                },
            ])
            ->latest('tagihan.created_at')

            ->get()
            ->each(function ($bill) {
                $bill->remaining_balance = $bill->nominal - $bill->total_paid - $bill->discount;
                $bill->total_paid = $bill->total_paid ?? 0;
            });
    }
}
