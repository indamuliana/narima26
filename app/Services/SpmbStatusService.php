<?php

namespace App\Services;

use App\Enums\SpmbStatus;
use App\Models\CalonSiswa;
use App\Models\RiwayatStatusSpmb;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SpmbStatusService
{
    /**
     * Alias for changeStatus for transition workflow.
     */
    public function transition(
        CalonSiswa $calonSiswa,
        SpmbStatus|string|null $targetStatus = null,
        ?string $catatan = null,
        ?User $changedBy = null,
        ?string $alasan = null,
        bool $force = false,
        SpmbStatus|string|null $newStatus = null,
        ?User $actor = null
    ): RiwayatStatusSpmb {
        $status = $targetStatus ?? $newStatus;
        $user = $changedBy ?? $actor;

        return $this->changeStatus($calonSiswa, $status, $alasan, $catatan, $user, $force);
    }

    /**
     * Change SPMB status of Calon Siswa with transition validation and history logging.
     *
     * @throws InvalidArgumentException
     */
    public function changeStatus(
        CalonSiswa $calonSiswa,
        SpmbStatus|string $targetStatus,
        ?string $alasan = null,
        ?string $catatan = null,
        ?User $changedBy = null,
        bool $force = false
    ): RiwayatStatusSpmb {
        $targetEnum = is_string($targetStatus) ? SpmbStatus::from($targetStatus) : $targetStatus;
        $currentEnum = is_string($calonSiswa->status_spmb) ? SpmbStatus::from($calonSiswa->status_spmb) : $calonSiswa->status_spmb;

        // Check if status is unchanged
        if ($currentEnum === $targetEnum) {
            throw new InvalidArgumentException("Status calon siswa sudah berada pada {$targetEnum->value}.");
        }

        // Validate allowed state transition
        if (! $force && ! $currentEnum->canTransitionTo($targetEnum)) {
            throw new InvalidArgumentException(
                "Transisi status dari {$currentEnum->value} ke {$targetEnum->value} tidak diizinkan dalam alur kerja SPMB."
            );
        }

        return DB::transaction(function () use ($calonSiswa, $currentEnum, $targetEnum, $alasan, $catatan, $changedBy) {
            $user = $changedBy ?? auth()->user();

            // 1. Update status on CalonSiswa
            $calonSiswa->status_spmb = $targetEnum->value;
            $calonSiswa->save();

            // 2. Create history record in riwayat_status_spmb
            $history = RiwayatStatusSpmb::create([
                'calon_siswa_id' => $calonSiswa->id,
                'status_sebelumnya' => $currentEnum->value,
                'status_baru' => $targetEnum->value,
                'alasan' => $alasan,
                'catatan' => $catatan,
                'changed_by' => $user?->id,
                'changed_at' => now(),
            ]);

            // 3. Write to Audit Trail
            if (function_exists('activity')) {
                activity('spmb_status')
                    ->performedOn($calonSiswa)
                    ->causedBy($user)
                    ->withProperties([
                        'nomor_pendaftaran' => $calonSiswa->nomor_pendaftaran,
                        'status_sebelumnya' => $currentEnum->value,
                        'status_baru' => $targetEnum->value,
                        'alasan' => $alasan,
                        'catatan' => $catatan,
                    ])
                    ->log("Perubahan status SPMB {$calonSiswa->nama_lengkap} ({$calonSiswa->nomor_pendaftaran}) dari {$currentEnum->value} menjadi {$targetEnum->value}.");
            }

            return $history;
        });
    }

    /**
     * Get full status history for a Calon Siswa ordered by latest first.
     */
    public function getHistory(CalonSiswa $calonSiswa)
    {
        return $calonSiswa->riwayatStatus()
            ->with('changedBy')
            ->latest('changed_at')
            ->get();
    }
}
