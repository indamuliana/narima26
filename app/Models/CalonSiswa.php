<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalonSiswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'calon_siswa';

    protected $fillable = [
        'nomor_pendaftaran',
        'user_id',
        'nisn',
        'jenis_kelamin',
        'nama_lengkap',
        'nama_panggilan',
        'tempat_lahir',
        'tanggal_lahir',
        'nik',
        'no_kk',
        'agama',
        'alamat_lengkap',
        'is_luar_negeri',
        'negara',
        'provinsi_nama',
        'kabupaten_nama',
        'kecamatan_nama',
        'desa_nama',
        'provinsi_luar_negeri',
        'kabupaten_luar_negeri',
        'kecamatan_luar_negeri',
        'desa_luar_negeri',
        'rt',
        'rw',
        'kode_pos',
        'provinsi_id',
        'kabupaten_id',
        'kecamatan_id',
        'desa_id',
        'anak_ke',
        'jumlah_saudara',
        'tahun_lulus',
        'no_hp_siswa',
        'no_hp_ayah',
        'no_hp_ibu',
        'email',
        'asal_sekolah_id',
        'asal_sekolah_lainnya',
        'referensi_jenis',
        'referensi_nama',
        'referensi_rayon',
        'referensi_nomor_seleksi',
        'program_id',
        'jurusan_id',
        'gelombang_id',
        'status_spmb',
        'status_data',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'status_spmb' => \App\Enums\SpmbStatus::class,
            'is_luar_negeri' => 'boolean',
        ];
    }

    public function getNamaProvinsiAttribute(): ?string
    {
        if ($this->is_luar_negeri) {
            return $this->provinsi_luar_negeri ?: $this->provinsi_nama;
        }
        return $this->provinsi_nama ?: $this->provinsi?->nama;
    }

    public function getNamaKabupatenAttribute(): ?string
    {
        if ($this->is_luar_negeri) {
            return $this->kabupaten_luar_negeri ?: $this->kabupaten_nama;
        }
        return $this->kabupaten_nama ?: $this->kabupaten?->nama;
    }

    public function getNamaKecamatanAttribute(): ?string
    {
        if ($this->is_luar_negeri) {
            return $this->kecamatan_luar_negeri ?: $this->kecamatan_nama;
        }
        return $this->kecamatan_nama ?: $this->kecamatan?->nama;
    }

    public function getNamaDesaAttribute(): ?string
    {
        if ($this->is_luar_negeri) {
            return $this->desa_luar_negeri ?: $this->desa_nama;
        }
        return $this->desa_nama ?: $this->desa?->nama;
    }

    public function getAlamatDomisiliLengkapAttribute(): string
    {
        if ($this->is_luar_negeri) {
            $parts = array_filter([
                $this->alamat_lengkap,
                $this->desa_luar_negeri ? "Desa/Kel: {$this->desa_luar_negeri}" : null,
                $this->kecamatan_luar_negeri ? "Kec/Distrik: {$this->kecamatan_luar_negeri}" : null,
                $this->kabupaten_luar_negeri ? "Kota: {$this->kabupaten_luar_negeri}" : null,
                $this->provinsi_luar_negeri ? "Prov/State: {$this->provinsi_luar_negeri}" : null,
                $this->negara ? "Negara: {$this->negara}" : null,
                $this->kode_pos ? "Kode Pos: {$this->kode_pos}" : null,
            ]);
            return implode(', ', $parts);
        }

        $rtRw = ($this->rt || $this->rw) ? "RT {$this->rt}/RW {$this->rw}" : null;
        $desa = $this->nama_desa ? "Desa/Kel. {$this->nama_desa}" : null;
        $kec = $this->nama_kecamatan ? "Kec. {$this->nama_kecamatan}" : null;
        $kab = $this->nama_kabupaten ? "Kab/Kota {$this->nama_kabupaten}" : null;
        $prov = $this->nama_provinsi ? "Prov. {$this->nama_provinsi}" : null;
        $kpos = $this->kode_pos ? "Kode Pos: {$this->kode_pos}" : null;

        $parts = array_filter([$this->alamat_lengkap, $rtRw, $desa, $kec, $kab, $prov, $kpos]);
        return implode(', ', $parts);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(MasterProgram::class, 'program_id');
    }

    public function programBelajar(): BelongsTo
    {
        return $this->belongsTo(MasterProgram::class, 'program_id');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(MasterJurusan::class, 'jurusan_id');
    }

    public function gelombang(): BelongsTo
    {
        return $this->belongsTo(MasterGelombang::class, 'gelombang_id');
    }

    public function asalSekolah(): BelongsTo
    {
        return $this->belongsTo(MasterSekolahAsal::class, 'asal_sekolah_id');
    }

    public function sekolahAsal(): BelongsTo
    {
        return $this->belongsTo(MasterSekolahAsal::class, 'asal_sekolah_id');
    }

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(MasterProvinsi::class, 'provinsi_id');
    }

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(MasterKabupaten::class, 'kabupaten_id');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(MasterKecamatan::class, 'kecamatan_id');
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(MasterDesa::class, 'desa_id');
    }

    public function dataKesehatan(): HasOne
    {
        return $this->hasOne(DataKesehatan::class);
    }

    public function dataOrangtua(): HasOne
    {
        return $this->hasOne(DataOrangtua::class, 'calon_siswa_id');
    }

    public function orangTua(): HasOne
    {
        return $this->hasOne(DataOrangtua::class, 'calon_siswa_id');
    }

    public function dataAkademik(): HasOne
    {
        return $this->hasOne(DataAkademik::class, 'calon_siswa_id');
    }

    public function nilaiRapor(): HasOne
    {
        return $this->hasOne(NilaiRapor::class, 'calon_siswa_id');
    }

    public function dokumenPendaftaran(): HasOne
    {
        return $this->hasOne(DokumenPendaftaran::class, 'calon_siswa_id');
    }

    public function pembayaranSeleksi(): HasOne
    {
        return $this->hasOne(PembayaranSeleksi::class, 'calon_siswa_id');
    }

    public function keputusanKelulusan(): HasOne
    {
        return $this->hasOne(KeputusanKelulusan::class, 'calon_siswa_id');
    }

    public function prestasi(): HasMany
    {
        return $this->hasMany(Prestasi::class, 'calon_siswa_id');
    }

    public function ukuranSeragam(): HasMany
    {
        return $this->hasMany(UkuranSeragam::class, 'calon_siswa_id');
    }

    public function kesepahaman(): HasMany
    {
        return $this->hasMany(KesepahamanEula::class, 'calon_siswa_id');
    }

    public function wawancaraSiswa(): HasOne
    {
        return $this->hasOne(WawancaraSiswa::class, 'calon_siswa_id');
    }

    public function wawancaraOrangTua(): HasOne
    {
        return $this->hasOne(WawancaraOrangTua::class, 'calon_siswa_id');
    }

    public function wawancara(): HasMany
    {
        return $this->hasMany(Wawancara::class, 'calon_siswa_id');
    }

    public function latestWawancara(): HasOne
    {
        return $this->hasOne(Wawancara::class, 'calon_siswa_id')->latestOfMany();
    }

    public function getWawancaraTerakhirAttribute(): ?Wawancara
    {
        if ($this->relationLoaded('latestWawancara')) {
            return $this->latestWawancara;
        }

        if ($this->relationLoaded('wawancara')) {
            return $this->wawancara->sortByDesc('id')->first();
        }

        return $this->wawancara()->latest('id')->first();
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'calon_siswa_id');
    }

    public function pembayaranDaftarUlang(): HasMany
    {
        return $this->hasMany(PembayaranDaftarUlang::class, 'calon_siswa_id');
    }

    public function diskon(): HasMany
    {
        return $this->hasMany(Diskon::class, 'calon_siswa_id');
    }

    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatusSpmb::class, 'calon_siswa_id');
    }
}
