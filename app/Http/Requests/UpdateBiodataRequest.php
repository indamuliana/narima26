<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBiodataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('calon_siswa');
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'digits:16'],
            'no_kk' => ['required', 'string', 'digits:16'],
            'agama' => ['required', 'string', 'max:30'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat_lengkap' => ['required', 'string', 'max:500'],
            'rt' => ['required', 'string', 'max:10'],
            'rw' => ['required', 'string', 'max:10'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'provinsi_id' => ['required', 'exists:master_provinsi,id'],
            'kabupaten_id' => ['required', 'exists:master_kabupaten,id'],
            'kecamatan_id' => ['required', 'exists:master_kecamatan,id'],
            'desa_id' => ['required', 'exists:master_desa,id'],
            'no_hp_siswa' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.digits' => 'NIK harus berjumlah 16 digit.',
            'no_kk.digits' => 'Nomor Kartu Keluarga (KK) harus berjumlah 16 digit.',
            'provinsi_id.required' => 'Provinsi wajib dipilih.',
            'kabupaten_id.required' => 'Kabupaten/Kota wajib dipilih.',
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'desa_id.required' => 'Desa/Kelurahan wajib dipilih.',
        ];
    }
}
