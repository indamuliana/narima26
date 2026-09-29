<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAkademikRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('calon_siswa');
    }

    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Calculate averages from matrix if matrix values exist
        $mtkValues = array_values(array_filter([
            $this->mtk_sem1, $this->mtk_sem2, $this->mtk_sem3, $this->mtk_sem4, $this->mtk_sem5
        ], fn($v) => is_numeric($v) && $v !== ''));

        $indValues = array_values(array_filter([
            $this->ind_sem1, $this->ind_sem2, $this->ind_sem3, $this->ind_sem4, $this->ind_sem5
        ], fn($v) => is_numeric($v) && $v !== ''));

        $engValues = array_values(array_filter([
            $this->eng_sem1, $this->eng_sem2, $this->eng_sem3, $this->eng_sem4, $this->eng_sem5
        ], fn($v) => is_numeric($v) && $v !== ''));

        $paiValues = array_values(array_filter([
            $this->pai_sem1, $this->pai_sem2, $this->pai_sem3, $this->pai_sem4, $this->pai_sem5
        ], fn($v) => is_numeric($v) && $v !== ''));

        $merge = [];
        if ((!isset($input['nilai_matematika']) || $input['nilai_matematika'] === '') && count($mtkValues) > 0) {
            $merge['nilai_matematika'] = round(array_sum($mtkValues) / count($mtkValues), 2);
        }
        if ((!isset($input['nilai_bahasa_indonesia']) || $input['nilai_bahasa_indonesia'] === '') && count($indValues) > 0) {
            $merge['nilai_bahasa_indonesia'] = round(array_sum($indValues) / count($indValues), 2);
        }
        if ((!isset($input['nilai_bahasa_inggris']) || $input['nilai_bahasa_inggris'] === '') && count($engValues) > 0) {
            $merge['nilai_bahasa_inggris'] = round(array_sum($engValues) / count($engValues), 2);
        }

        $allSubjectValues = array_merge($mtkValues, $indValues, $engValues, $paiValues);
        if ((!isset($input['nilai_rata_rata']) || $input['nilai_rata_rata'] === '') && count($allSubjectValues) > 0) {
            $merge['nilai_rata_rata'] = round(array_sum($allSubjectValues) / count($allSubjectValues), 2);
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
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
            'nilai_ipa' => ['nullable', 'numeric', 'between:0,100'],
            'nilai_lainnya' => ['nullable', 'numeric', 'between:0,100'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'prestasi' => ['nullable', 'array'],
            'prestasi.*.jenis_prestasi' => ['nullable', 'string', 'max:50'],
            'prestasi.*.tingkat' => ['nullable', 'string', 'max:50'],
            'prestasi.*.nama_prestasi' => ['nullable', 'string', 'max:150'],
            'prestasi.*.tahun' => ['nullable', 'string', 'max:10'],
            'prestasi.*.peringkat' => ['nullable', 'string', 'max:50'],
            'prestasi.*.keterangan' => ['nullable', 'string', 'max:255'],

            // Nilai Rapor Matrix (4 mapel x 5 semester)
            'mtk_sem1' => ['nullable', 'numeric', 'between:0,100'],
            'mtk_sem2' => ['nullable', 'numeric', 'between:0,100'],
            'mtk_sem3' => ['nullable', 'numeric', 'between:0,100'],
            'mtk_sem4' => ['nullable', 'numeric', 'between:0,100'],
            'mtk_sem5' => ['nullable', 'numeric', 'between:0,100'],
            'ind_sem1' => ['nullable', 'numeric', 'between:0,100'],
            'ind_sem2' => ['nullable', 'numeric', 'between:0,100'],
            'ind_sem3' => ['nullable', 'numeric', 'between:0,100'],
            'ind_sem4' => ['nullable', 'numeric', 'between:0,100'],
            'ind_sem5' => ['nullable', 'numeric', 'between:0,100'],
            'eng_sem1' => ['nullable', 'numeric', 'between:0,100'],
            'eng_sem2' => ['nullable', 'numeric', 'between:0,100'],
            'eng_sem3' => ['nullable', 'numeric', 'between:0,100'],
            'eng_sem4' => ['nullable', 'numeric', 'between:0,100'],
            'eng_sem5' => ['nullable', 'numeric', 'between:0,100'],
            'pai_sem1' => ['nullable', 'numeric', 'between:0,100'],
            'pai_sem2' => ['nullable', 'numeric', 'between:0,100'],
            'pai_sem3' => ['nullable', 'numeric', 'between:0,100'],
            'pai_sem4' => ['nullable', 'numeric', 'between:0,100'],
            'pai_sem5' => ['nullable', 'numeric', 'between:0,100'],
        ];
    }

    public function messages(): array
    {
        return [
            'nilai_rata_rata.required' => 'Nilai rata-rata rapor wajib diisi.',
            'nilai_bahasa_indonesia.required' => 'Nilai Bahasa Indonesia wajib diisi.',
            'nilai_matematika.required' => 'Nilai Matematika wajib diisi.',
            'nilai_bahasa_inggris.required' => 'Nilai Bahasa Inggris wajib diisi.',
            'nilai_rata_rata.between' => 'Nilai harus berkisar antara 0 sampai 100.',
        ];
    }
}
