<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAkademikRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('calon_siswa');
    }

    public function rules(): array
    {
        return [
            'nama_sekolah' => ['nullable', 'string', 'max:150'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'nilai_rata_rata' => ['required', 'numeric', 'between:0,100'],
            'nilai_bahasa_indonesia' => ['required', 'numeric', 'between:0,100'],
            'nilai_matematika' => ['required', 'numeric', 'between:0,100'],
            'nilai_bahasa_inggris' => ['required', 'numeric', 'between:0,100'],
            'nilai_ipa' => ['required', 'numeric', 'between:0,100'],
            'nilai_lainnya' => ['nullable', 'numeric', 'between:0,100'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'prestasi' => ['nullable', 'array'],
            'prestasi.*.jenis_prestasi' => ['nullable', 'string', 'max:50'],
            'prestasi.*.tingkat' => ['nullable', 'string', 'max:50'],
            'prestasi.*.nama_prestasi' => ['nullable', 'string', 'max:150'],
            'prestasi.*.tahun' => ['nullable', 'string', 'max:10'],
            'prestasi.*.peringkat' => ['nullable', 'string', 'max:50'],
            'prestasi.*.keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nilai_rata_rata.required' => 'Nilai rata-rata rapor wajib diisi.',
            'nilai_bahasa_indonesia.required' => 'Nilai Bahasa Indonesia wajib diisi.',
            'nilai_matematika.required' => 'Nilai Matematika wajib diisi.',
            'nilai_bahasa_inggris.required' => 'Nilai Bahasa Inggris wajib diisi.',
            'nilai_ipa.required' => 'Nilai IPA wajib diisi.',
            'nilai_rata_rata.between' => 'Nilai harus berkisar antara 0 sampai 100.',
        ];
    }
}
