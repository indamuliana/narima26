<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBiodataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('calon_siswa');
    }

    public function rules(): array
    {
        $isLuarNegeri = $this->boolean('is_luar_negeri');

        $rules = [
            'nama_lengkap' => ['sometimes', 'required', 'string', 'max:150'],
            'nama_panggilan' => ['nullable', 'string', 'max:50'],
            'jenis_kelamin' => ['sometimes', 'required', 'in:L,P'],
            'nik' => ['required', 'string', 'digits:16'],
            'no_kk' => ['required', 'string', 'digits:16'],
            'agama' => ['required', 'string', 'max:30'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat_lengkap' => ['required', 'string', 'max:500'],
            'kode_pos' => ['nullable', 'string', 'max:20'],
            'no_hp_siswa' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'is_luar_negeri' => ['nullable', 'boolean'],
            'anak_ke' => ['nullable', 'integer', 'min:1', 'max:9'],
            'jumlah_saudara' => ['nullable', 'integer', 'min:1', 'max:9'],
            'tahun_lulus' => ['nullable', 'string', 'digits:4'],
        ];

        if ($isLuarNegeri) {
            $rules['negara'] = ['required', 'string', 'max:100'];
            $rules['provinsi_luar_negeri'] = ['required', 'string', 'max:100'];
            $rules['kabupaten_luar_negeri'] = ['required', 'string', 'max:100'];
            $rules['kecamatan_luar_negeri'] = ['nullable', 'string', 'max:100'];
            $rules['desa_luar_negeri'] = ['nullable', 'string', 'max:100'];
            $rules['rt'] = ['nullable', 'string', 'max:10'];
            $rules['rw'] = ['nullable', 'string', 'max:10'];
            $rules['provinsi_nama'] = ['nullable', 'string', 'max:100'];
            $rules['kabupaten_nama'] = ['nullable', 'string', 'max:100'];
            $rules['kecamatan_nama'] = ['nullable', 'string', 'max:100'];
            $rules['desa_nama'] = ['nullable', 'string', 'max:100'];
            $rules['provinsi_id'] = ['nullable'];
            $rules['kabupaten_id'] = ['nullable'];
            $rules['kecamatan_id'] = ['nullable'];
            $rules['desa_id'] = ['nullable'];
        } else {
            $rules['negara'] = ['nullable', 'string', 'max:100'];
            $rules['provinsi_luar_negeri'] = ['nullable', 'string', 'max:100'];
            $rules['kabupaten_luar_negeri'] = ['nullable', 'string', 'max:100'];
            $rules['kecamatan_luar_negeri'] = ['nullable', 'string', 'max:100'];
            $rules['desa_luar_negeri'] = ['nullable', 'string', 'max:100'];
            $rules['rt'] = ['required', 'string', 'max:10'];
            $rules['rw'] = ['required', 'string', 'max:10'];
            $rules['provinsi_nama'] = ['required_without:provinsi_id', 'nullable', 'string', 'max:100'];
            $rules['kabupaten_nama'] = ['required_without:kabupaten_id', 'nullable', 'string', 'max:100'];
            $rules['kecamatan_nama'] = ['required_without:kecamatan_id', 'nullable', 'string', 'max:100'];
            $rules['desa_nama'] = ['required_without:desa_id', 'nullable', 'string', 'max:100'];
            $rules['provinsi_id'] = ['nullable'];
            $rules['kabupaten_id'] = ['nullable'];
            $rules['kecamatan_id'] = ['nullable'];
            $rules['desa_id'] = ['nullable'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nik.digits' => 'NIK harus berjumlah 16 digit.',
            'no_kk.digits' => 'Nomor Kartu Keluarga (KK) harus berjumlah 16 digit.',
            'provinsi_nama.required' => 'Provinsi wajib diisi.',
            'provinsi_nama.required_without' => 'Provinsi wajib diisi.',
            'kabupaten_nama.required' => 'Kabupaten / Kota wajib diisi.',
            'kabupaten_nama.required_without' => 'Kabupaten / Kota wajib diisi.',
            'kecamatan_nama.required' => 'Kecamatan wajib diisi.',
            'kecamatan_nama.required_without' => 'Kecamatan wajib diisi.',
            'desa_nama.required' => 'Desa / Kelurahan wajib diisi.',
            'desa_nama.required_without' => 'Desa / Kelurahan wajib diisi.',
            'provinsi_id.required' => 'Provinsi wajib diisi.',
            'kabupaten_id.required' => 'Kabupaten/Kota wajib diisi.',
            'kecamatan_id.required' => 'Kecamatan wajib diisi.',
            'desa_id.required' => 'Desa/Kelurahan wajib diisi.',
            'rt.required' => 'RT wajib diisi.',
            'rw.required' => 'RW wajib diisi.',
            'negara.required' => 'Nama Negara tempat tinggal wajib diisi untuk domisili luar negeri.',
            'provinsi_luar_negeri.required' => 'Nama Provinsi / Wilayah Bagian wajib diisi untuk domisili luar negeri.',
            'kabupaten_luar_negeri.required' => 'Nama Kota / Kabupaten wajib diisi untuk domisili luar negeri.',
        ];
    }
}
