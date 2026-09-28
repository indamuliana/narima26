<?php

namespace App\Services;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\RiwayatStatusSpmb;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use InvalidArgumentException;

class WithdrawalService
{
    public function __construct(
        protected SpmbStatusService $statusService
    ) {}

    /**
     * Mark a Calon Siswa as Mengundurkan Diri.
     * Only Admin and Kepala Sekolah are authorized.
     *
     * @throws AuthorizationException|InvalidArgumentException
     */
    public function withdraw(
        CalonSiswa $calonSiswa,
        string $alasan,
        ?string $catatan = null,
        ?User $by = null
    ): RiwayatStatusSpmb {
        $actor = $by ?? auth()->user();

        // Enforce authorization rule (Section 5.3: Admin or Kepala Sekolah only)
        if (! $actor || (! $actor->isAdmin() && ! $actor->isKepalaSekolah())) {
            throw new AuthorizationException(
                'Hanya Admin atau Kepala Sekolah yang memiliki wewenang untuk menetapkan status Mengundurkan Diri.'
            );
        }

        $currentStatus = is_string($calonSiswa->status_spmb)
            ? SpmbStatus::from($calonSiswa->status_spmb)
            : $calonSiswa->status_spmb;

        if ($currentStatus === SpmbStatus::MENGUNDURKAN_DIRI) {
            throw new InvalidArgumentException('Calon siswa sudah berstatus Mengundurkan Diri.');
        }

        $history = $this->statusService->changeStatus(
            calonSiswa: $calonSiswa,
            targetStatus: SpmbStatus::MENGUNDURKAN_DIRI,
            alasan: $alasan,
            catatan: $catatan,
            changedBy: $actor
        );

        if (function_exists('activity')) {
            activity('withdrawal')
                ->performedOn($calonSiswa)
                ->causedBy($actor)
                ->withProperties([
                    'status_sebelumnya' => $currentStatus->value,
                    'alasan' => $alasan,
                    'catatan' => $catatan,
                ])
                ->log("Calon siswa {$calonSiswa->nama_lengkap} ({$calonSiswa->nomor_pendaftaran}) ditandai Mengundurkan Diri oleh {$actor->name}. Alasan: {$alasan}");
        }

        return $history;
    }

    /**
     * Restore Calon Siswa from Mengundurkan Diri back to previous active status.
     * Only Admin and Kepala Sekolah are authorized.
     */
    public function restore(
        CalonSiswa $calonSiswa,
        string $alasanRestorasi,
        ?User $by = null
    ): RiwayatStatusSpmb {
        $actor = $by ?? auth()->user();

        if (! $actor || (! $actor->isAdmin() && ! $actor->isKepalaSekolah())) {
            throw new AuthorizationException(
                'Hanya Admin atau Kepala Sekolah yang memiliki wewenang untuk memulihkan status Mengundurkan Diri.'
            );
        }

        $currentStatus = is_string($calonSiswa->status_spmb)
            ? SpmbStatus::from($calonSiswa->status_spmb)
            : $calonSiswa->status_spmb;

        if ($currentStatus !== SpmbStatus::MENGUNDURKAN_DIRI) {
            throw new InvalidArgumentException('Calon siswa tidak sedang dalam status Mengundurkan Diri.');
        }

        // Find previous status from history
        $lastHistory = RiwayatStatusSpmb::where('calon_siswa_id', $calonSiswa->id)
            ->where('status_baru', SpmbStatus::MENGUNDURKAN_DIRI->value)
            ->latest('changed_at')
            ->first();

        $targetStatusString = $lastHistory?->status_sebelumnya ?? SpmbStatus::REGISTRASI->value;
        $targetStatus = SpmbStatus::from($targetStatusString);

        $history = $this->statusService->changeStatus(
            calonSiswa: $calonSiswa,
            targetStatus: $targetStatus,
            alasan: $alasanRestorasi,
            catatan: "Pemulihan status dari Mengundurkan Diri kembali ke {$targetStatus->value}",
            changedBy: $actor,
            force: true // Force transition back to previous active state
        );

        if (function_exists('activity')) {
            activity('withdrawal')
                ->performedOn($calonSiswa)
                ->causedBy($actor)
                ->withProperties([
                    'status_dipulihkan' => $targetStatus->value,
                    'alasan' => $alasanRestorasi,
                ])
                ->log("Status Mengundurkan Diri untuk {$calonSiswa->nama_lengkap} dibatalkan/dipulihkan kembali ke {$targetStatus->value} oleh {$actor->name}.");
        }

        return $history;
    }
}
