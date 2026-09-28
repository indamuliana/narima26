<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrangTuaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('calon_siswa');
    }

    public function rules(): array
    {
        return [
            'nama_ayah' => ['required', 'string', 'max:150'],
            'nik_ayah' => ['nullable', 'string', 'digits:16'],
            'tahun_lahir_ayah' => ['nullable', 'string', 'max:10'],
            'pekerjaan_ayah_id' => ['required', 'exists:master_pekerjaan,id'],
            'penghasilan_ayah' => ['nullable', 'string', 'max:50'],
            'pendidikan_ayah' => ['nullable', 'string', 'max:50'],
            'no_hp_ayah' => ['required', 'string', 'max:20'],
            'alamat_ayah' => ['nullable', 'string', 'max:500'],

            'nama_ibu' => ['required', 'string', 'max:150'],
            'nik_ibu' => ['nullable', 'string', 'digits:16'],
            'tahun_lahir_ibu' => ['nullable', 'string', 'max:10'],
            'pekerjaan_ibu_id' => ['required', 'exists:master_pekerjaan,id'],
            'penghasilan_ibu' => ['nullable', 'string', 'max:50'],
            'pendidikan_ibu' => ['nullable', 'string', 'max:50'],
            'no_hp_ibu' => ['nullable', 'string', 'max:20'],
            'alamat_ibu' => ['nullable', 'string', 'max:500'],

            'nama_wali' => ['nullable', 'string', 'max:150'],
            'hubungan_wali' => ['nullable', 'string', 'max:50'],
            'pekerjaan_wali_id' => ['nullable', 'exists:master_pekerjaan,id'],
            'penghasilan_wali' => ['nullable', 'string', 'max:50'],
            'no_hp_wali' => ['nullable', 'string', 'max:20'],
            'alamat_wali' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_ayah.required' => 'Nama ayah kandung wajib diisi.',
            'pekerjaan_ayah_id.required' => 'Pekerjaan ayah wajib dipilih.',
            'no_hp_ayah.required' => 'Nomor HP/WhatsApp ayah wajib diisi.',
            'nama_ibu.required' => 'Nama ibu kandung wajib diisi.',
            'pekerjaan_ibu_id.required' => 'Pekerjaan ibu wajib dipilih.',
        ];
    }
}
