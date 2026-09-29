<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeragamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('calon_siswa');
    }

    public function rules(): array
    {
        return [
            'seragam' => ['required', 'array', 'min:1'],
            'seragam.*.jenis_seragam_id' => ['required', 'exists:master_seragam,id'],
            'seragam.*.ukuran' => ['required', 'string', 'max:10'],
            'seragam.*.status_pemesanan' => ['nullable', 'in:PESAN_SEKARANG,PESAN_NANTI'],
            'seragam.*.jumlah' => ['nullable', 'integer', 'min:1'],
            'seragam.*.beli_di_sekolah' => ['nullable'],
            'seragam.*.keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'seragam.required' => 'Pilihan ukuran seragam wajib ditentukan.',
            'seragam.*.ukuran.required' => 'Ukuran untuk setiap seragam wajib dipilih.',
        ];
    }
}
