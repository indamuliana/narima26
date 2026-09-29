<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKesehatanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tinggi_badan' => ['nullable', 'integer', 'min:50', 'max:250'],
            'berat_badan' => ['nullable', 'integer', 'min:10', 'max:200'],
            'golongan_darah' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-,Tidak Tahu'],
            'buta_warna' => ['nullable', 'string', 'in:Tidak buta warna,Buta warna parsial,Buta warna'],
            'penyakit_pernah_diderita' => ['nullable', 'string', 'in:Asma,Hepatitis,Tipes,TBC,Penyakit jantung,Usus buntu,Diabetes,Lupus,Patah tulang,Tidak ada,Lainnya'],
            'penyakit_pernah_diderita_lainnya' => ['nullable', 'string', 'max:150', \Illuminate\Validation\Rule::requiredIf(fn () => $this->input('penyakit_pernah_diderita') === 'Lainnya')],
            'penyakit_sedang_diderita' => ['nullable', 'string', 'in:Asma,Hepatitis,Tipes,TBC,Penyakit jantung,Usus buntu,Diabetes,Lupus,Patah tulang,Tidak ada,Lainnya'],
            'penyakit_sedang_diderita_lainnya' => ['nullable', 'string', 'max:150', \Illuminate\Validation\Rule::requiredIf(fn () => $this->input('penyakit_sedang_diderita') === 'Lainnya')],
            'kesehatan_mata' => ['nullable', 'string', 'in:Normal,Minus,Plus,Silinder'],
            'jenis_alergi' => ['nullable', 'string', 'max:255'],
        ];
    }
}
