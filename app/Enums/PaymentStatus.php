<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case DIVERIFIKASI = 'DIVERIFIKASI';
    case DITOLAK = 'DITOLAK';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::DIVERIFIKASI => 'Terverifikasi',
            self::DITOLAK => 'Ditolak',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::PENDING => 'orange',
            self::DIVERIFIKASI => 'green',
            self::DITOLAK => 'red',
        };
    }
}
