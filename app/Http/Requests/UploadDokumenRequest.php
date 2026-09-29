<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDokumenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('calon_siswa');
    }

    public function rules(): array
    {
        return [
            'file_kartu_keluarga' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'file_akta_kelahiran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'file_ijazah_atau_skl' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'file_pas_foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'file_dokumen_pendukung' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'file_kartu_keluarga.mimes' => 'Kartu Keluarga harus bertipe PDF, JPG, JPEG, atau PNG.',
            'file_kartu_keluarga.max' => 'Ukuran berkas Kartu Keluarga maksimal 10 MB.',
            'file_akta_kelahiran.mimes' => 'Akta Kelahiran harus bertipe PDF, JPG, JPEG, atau PNG.',
            'file_akta_kelahiran.max' => 'Ukuran berkas Akta Kelahiran maksimal 10 MB.',
            'file_ijazah_atau_skl.mimes' => 'Ijazah/SKL harus bertipe PDF, JPG, JPEG, atau PNG.',
            'file_ijazah_atau_skl.max' => 'Ukuran berkas Ijazah/SKL maksimal 10 MB.',
            'file_pas_foto.mimes' => 'Pas Foto harus bertipe JPG, JPEG, atau PNG.',
            'file_pas_foto.max' => 'Ukuran pas foto maksimal 2 MB.',
        ];
    }
}
