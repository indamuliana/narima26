<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanWawancaraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isPewawancara() || $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isDraft = $this->input('action') === 'draft';

        return [
            'action' => ['required', 'in:draft,selesai'],
            'tanggal_wawancara' => [$isDraft ? 'nullable' : 'required', 'date'],
            'catatan_umum' => ['nullable', 'string', 'max:3000'],
            'catatan_orang_tua' => ['nullable', 'string', 'max:3000'],
            'penilaian' => [$isDraft ? 'nullable' : 'required', 'array'],
            'penilaian.*.kriteria_id' => ['required_with:penilaian', 'integer', 'exists:master_kriteria_wawancara,id'],
            'penilaian.*.nilai' => ['required_with:penilaian', 'numeric', 'min:0', 'max:100'],
            'penilaian.*.warna' => ['required_with:penilaian', 'in:HIJAU,ORANYE,MERAH'],
            'penilaian.*.indikator' => ['nullable', 'string', 'max:150'],
            'penilaian.*.catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Aksi simpan tidak valid.',
            'tanggal_wawancara.required' => 'Tanggal pelaksanaan wawancara wajib diisi.',
            'penilaian.required' => 'Seluruh rubrik kriteria wawancara wajib diisi sebelum menyelesaikan wawancara.',
            'penilaian.*.nilai.required' => 'Nilai skor kriteria wajib diisi.',
            'penilaian.*.nilai.min' => 'Nilai skor minimal adalah 0.',
            'penilaian.*.nilai.max' => 'Nilai skor maksimal adalah 100.',
            'penilaian.*.warna.in' => 'Pilihan warna indikator harus salah satu dari: HIJAU, ORANYE, MERAH.',
        ];
    }
}
