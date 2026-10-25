<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Pengingat perpanjangan cloud server ARCH.
 *
 * UBAH DI SINI bila jadwal atau biaya berubah:
 *   DUE_MONTH / DUE_DAY  -> tanggal jatuh tempo tiap tahun
 *   AMOUNT               -> biaya per tahun (rupiah)
 */
class CloudReminder
{
    public const DUE_MONTH = 1;
    public const DUE_DAY = 23;
    public const AMOUNT = 1000000;

    /** Mulai tampil mencolok (kuning) bila sisa hari <= SOON, merah bila <= URGENT. */
    public const SOON = 30;
    public const URGENT = 7;

    public static function info(): array
    {
        $today = Carbon::now()->startOfDay();
        $due = Carbon::create($today->year, self::DUE_MONTH, self::DUE_DAY)->startOfDay();
        if ($due->lessThan($today)) {
            $due->addYear();
        }

        $days = (int) $today->diffInDays($due);
        $level = $days <= self::URGENT ? 'urgent' : ($days <= self::SOON ? 'soon' : 'calm');

        // Progres sejak jatuh tempo sebelumnya (0–100%).
        $prevDue = $due->copy()->subYear();
        $span = max(1, $prevDue->diffInDays($due));
        $progress = min(100, max(0, round($prevDue->diffInDays($today) / $span * 100)));

        return [
            'due' => $due,
            'days' => $days,
            'amount' => self::AMOUNT,
            'level' => $level,
            'progress' => $progress,
            'label' => $days === 0 ? 'Hari ini' : ($days === 1 ? 'Besok' : $days . ' hari'),
        ];
    }
}
