<?php

namespace App\Http\Requests;

use App\Services\PhoneNumberService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterCalonSiswaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $phoneService = app(PhoneNumberService::class);

        return [
            'nisn' => [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('calon_siswa', 'nisn'),
                Rule::unique('users', 'username'),
            ],
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'nama_panggilan' => ['nullable', 'string', 'max:50'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'no_hp_siswa' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($phoneService) {
                    if (!$phoneService->isValid($value)) {
                        $fail('Nomor HP siswa harus berupa nomor seluler Indonesia yang valid (contoh: 081234567890).');
                    }
                },
            ],
            'no_hp_ayah' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($phoneService) {
                    if (!empty($value) && !$phoneService->isValid($value)) {
                        $fail('Nomor HP ayah harus berupa nomor seluler Indonesia yang valid.');
                    }
                },
            ],
            'no_hp_ibu' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($phoneService) {
                    if (!empty($value) && !$phoneService->isValid($value)) {
                        $fail('Nomor HP ibu harus berupa nomor seluler Indonesia yang valid.');
                    }
                },
            ],
            'email' => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('users', 'email'),
                Rule::unique('calon_siswa', 'email'),
            ],
            'program_id' => ['required', 'exists:master_program,id'],
            'jurusan_id' => ['required', 'exists:master_jurusan,id'],
            'gelombang_id' => ['nullable', 'exists:master_gelombang,id'],
            'asal_sekolah_id' => ['nullable', 'exists:master_sekolah_asal,id'],
            'asal_sekolah_lainnya' => ['nullable', 'string', 'max:150'],
        ];
    }

    /**
     * Pesan validasi kustom dalam bahasa Indonesia ramah pengguna.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.numeric' => 'NISN harus berupa angka.',
            'nisn.digits' => 'NISN harus tepat 10 digit.',
            'nisn.unique' => 'NISN ini sudah terdaftar di sistem SPMB.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'no_hp_siswa.required' => 'Nomor HP siswa wajib diisi untuk koordinasi dan verifikasi WhatsApp.',
            'program_id.required' => 'Program pendidikan (Reguler/Unggulan) wajib dipilih.',
            'jurusan_id.required' => 'Kompetensi keahlian / jurusan pilihan wajib dipilih.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini telah terdaftar di sistem.',
        ];
    }
}
