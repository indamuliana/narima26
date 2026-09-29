<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrangTuaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('calon_siswa');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status_ayah' => $this->input('status_ayah', 'MASIH_HIDUP') ?? 'MASIH_HIDUP',
            'status_ibu' => $this->input('status_ibu', 'MASIH_HIDUP') ?? 'MASIH_HIDUP',
        ]);
    }

    public function rules(): array
    {
        return [
            'status_ayah' => ['required', 'in:MASIH_HIDUP,WAFAT'],
            'nama_ayah' => ['required', 'string', 'max:150'],
            'nik_ayah' => ['nullable', 'string', 'digits:16'],
            'tahun_lahir_ayah' => ['nullable', 'string', 'max:10'],
            'pekerjaan_ayah_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('status_ayah') === 'MASIH_HIDUP' && empty($value)) {
                        $fail('Pekerjaan ayah wajib dipilih.');
                    }
                },
                'nullable',
                'exists:master_pekerjaan,id',
            ],
            'penghasilan_ayah' => ['nullable', 'string', 'max:50'],
            'pendidikan_ayah' => ['nullable', 'string', 'max:50'],
            'no_hp_ayah' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('status_ayah') === 'MASIH_HIDUP' && empty($value)) {
                        $fail('Nomor HP/WhatsApp ayah wajib diisi.');
                    }
                },
                'nullable',
                'string',
                'max:20',
            ],
            'alamat_ayah' => ['nullable', 'string', 'max:500'],

            'status_ibu' => ['required', 'in:MASIH_HIDUP,WAFAT'],
            'nama_ibu' => ['required', 'string', 'max:150'],
            'nik_ibu' => ['nullable', 'string', 'digits:16'],
            'tahun_lahir_ibu' => ['nullable', 'string', 'max:10'],
            'pekerjaan_ibu_id' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('status_ibu') === 'MASIH_HIDUP' && empty($value)) {
                        $fail('Pekerjaan ibu wajib dipilih.');
                    }
                },
                'nullable',
                'exists:master_pekerjaan,id',
            ],
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
            'status_ayah.required' => 'Status ayah wajib dipilih.',
            'nama_ibu.required' => 'Nama ibu kandung wajib diisi.',
            'status_ibu.required' => 'Status ibu wajib dipilih.',
        ];
    }
}
