<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case BENDAHARA = 'bendahara';
    case PEWAWANCARA = 'pewawancara';
    case KEPALA_SEKOLAH = 'kepala_sekolah';
    case CALON_SISWA = 'calon_siswa';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::BENDAHARA => 'Bendahara',
            self::PEWAWANCARA => 'Pewawancara',
            self::KEPALA_SEKOLAH => 'Kepala Sekolah',
            self::CALON_SISWA => 'Calon Siswa',
        };
    }
}
