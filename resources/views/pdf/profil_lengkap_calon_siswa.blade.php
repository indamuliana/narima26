@extends('pdf.layout', ['title' => 'Profil Lengkap Calon Siswa - ' . $calonSiswa->nomor_pendaftaran])

@section('content')
<style>
    .page-break {
        page-break-after: always;
    }
    .section-title {
        font-size: 10pt;
        font-weight: bold;
        text-transform: uppercase;
        background-color: #0f172a;
        color: #ffffff;
        padding: 4px 8px;
        margin-top: 14px;
        margin-bottom: 6px;
        letter-spacing: 0.5px;
        border-radius: 2px;
    }
    .sub-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }
    .sub-table td {
        padding: 3px 5px;
        vertical-align: top;
        font-size: 8.5pt;
    }
    .sub-table td.lbl {
        width: 25%;
        color: #475569;
        font-weight: 600;
    }
    .sub-table td.colon {
        width: 2%;
        text-align: center;
    }
    .sub-table td.val {
        width: 73%;
        color: #0f172a;
    }
    .grid-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 4px;
        margin-bottom: 8px;
    }
    .grid-table th, .grid-table td {
        border: 1px solid #cbd5e1;
        padding: 4px 6px;
        font-size: 8pt;
    }
    .grid-table th {
        background-color: #f1f5f9;
        color: #0f172a;
        font-weight: bold;
        text-align: left;
    }
    .grid-table td.center, .grid-table th.center {
        text-align: center;
    }
    .grid-table td.right, .grid-table th.right {
        text-align: right;
    }
    .badge-status {
        display: inline-block;
        padding: 2px 6px;
        font-size: 7.5pt;
        font-weight: bold;
        border-radius: 3px;
        text-transform: uppercase;
    }
    .badge-verified {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }
    .badge-pending {
        background-color: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-danger {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }
    .badge-info {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #7dd3fc;
    }
    .notes-box {
        background-color: #f8fafc;
        border: 1px dashed #cbd5e1;
        padding: 5px 8px;
        font-size: 8pt;
        color: #334155;
        border-radius: 3px;
        margin-top: 3px;
    }
    .box-avoid {
        page-break-inside: avoid;
    }
</style>

<div class="doc-title" style="font-size: 13pt; margin-top: 4px; margin-bottom: 2px;">
    LEMBAR DATA INDUK & PROFIL LENGKAP CALON SISWA
</div>
<div class="doc-number" style="margin-bottom: 10px; font-size: 9pt;">
    SPMB TAHUN AJARAN 2027/2028 &bull; NOMOR REGISTRASI: <strong>{{ $calonSiswa->nomor_pendaftaran }}</strong>
</div>

<!-- 1. DATA REGISTRASI & PILIHAN PROGRAM -->
<div class="box-avoid">
    <div class="section-title">1. Identitas Registrasi & Pilihan Program</div>
    <table class="sub-table">
        <tr>
            <td class="lbl">Nomor Pendaftaran</td>
            <td class="colon">:</td>
            <td class="val"><strong>{{ $calonSiswa->nomor_pendaftaran }}</strong></td>
            <td class="lbl">Status SPMB</td>
            <td class="colon">:</td>
            <td class="val">
                @php
                    $statusStr = is_string($calonSiswa->status_spmb) ? $calonSiswa->status_spmb : ($calonSiswa->status_spmb?->value ?? '-');
                @endphp
                <span class="badge-status {{ in_array($statusStr, ['DITERIMA', 'RESMI_TERDAFTAR', 'DAFTAR_ULANG_DIVERIFIKASI']) ? 'badge-verified' : (in_array($statusStr, ['DITOLAK', 'MENGUNDURKAN_DIRI']) ? 'badge-danger' : 'badge-pending') }}">
                    {{ str_replace('_', ' ', $statusStr) }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="lbl">NISN Siswa</td>
            <td class="colon">:</td>
            <td class="val">{{ $calonSiswa->nisn }}</td>
            <td class="lbl">Tanggal Pendaftaran</td>
            <td class="colon">:</td>
            <td class="val">{{ $calonSiswa->created_at ? $calonSiswa->created_at->translatedFormat('d F Y H:i') : '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Kompetensi Keahlian</td>
            <td class="colon">:</td>
            <td class="val"><strong>{{ $calonSiswa->jurusan?->nama ?? $calonSiswa->jurusan?->nama_jurusan ?? '-' }} ({{ $calonSiswa->jurusan?->kode ?? '-' }})</strong></td>
            <td class="lbl">Program Pendidikan</td>
            <td class="colon">:</td>
            <td class="val"><strong>{{ $calonSiswa->program?->nama ?? $calonSiswa->program?->nama_program ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="lbl">Gelombang Pendaftaran</td>
            <td class="colon">:</td>
            <td class="val">{{ $calonSiswa->gelombang?->nama ?? $calonSiswa->gelombang?->nama_gelombang ?? '-' }}</td>
            <td class="lbl">Status Data Siswa</td>
            <td class="colon">:</td>
            <td class="val"><span class="badge-status badge-info">{{ $calonSiswa->status_data ?? 'LENGKAP' }}</span></td>
        </tr>
        @if ($calonSiswa->referensi_jenis)
        <tr>
            <td class="lbl">Referensi / Promotor</td>
            <td class="colon">:</td>
            <td class="val" colspan="4">
                {{ str_replace('_', ' ', $calonSiswa->referensi_jenis) }} &mdash; <strong>{{ $calonSiswa->referensi_nama }}</strong>
                @if($calonSiswa->referensi_rayon) (Rayon: {{ $calonSiswa->referensi_rayon }}) @endif
                @if($calonSiswa->referensi_nomor_seleksi) (No: {{ $calonSiswa->referensi_nomor_seleksi }}) @endif
            </td>
        </tr>
        @endif
    </table>
</div>

<!-- 2. BIODATA PRIBADI -->
<div class="box-avoid">
    <div class="section-title">2. Biodata Pribadi Calon Siswa</div>
    <table class="sub-table">
        <tr>
            <td class="lbl">Nama Lengkap</td>
            <td class="colon">:</td>
            <td class="val"><strong>{{ strtoupper($calonSiswa->nama_lengkap) }}</strong> @if($calonSiswa->nama_panggilan) (Panggilan: {{ $calonSiswa->nama_panggilan }}) @endif</td>
            <td class="lbl">Jenis Kelamin</td>
            <td class="colon">:</td>
            <td class="val">{{ $calonSiswa->jenis_kelamin === 'L' || $calonSiswa->jenis_kelamin === 'Laki-laki' ? 'Laki-laki (L)' : 'Perempuan (P)' }}</td>
        </tr>
        <tr>
            <td class="lbl">Tempat, Tanggal Lahir</td>
            <td class="colon">:</td>
            <td class="val">{{ $calonSiswa->tempat_lahir ?? '-' }}, {{ $calonSiswa->tanggal_lahir ? $calonSiswa->tanggal_lahir->translatedFormat('d F Y') : '-' }}</td>
            <td class="lbl">Agama</td>
            <td class="colon">:</td>
            <td class="val">{{ $calonSiswa->agama ?? 'Islam' }}</td>
        </tr>
        <tr>
            <td class="lbl">Nomor Induk Kependudukan (NIK)</td>
            <td class="colon">:</td>
            <td class="val"><code>{{ $calonSiswa->nik ?? '-' }}</code></td>
            <td class="lbl">Nomor Kartu Keluarga (KK)</td>
            <td class="colon">:</td>
            <td class="val"><code>{{ $calonSiswa->no_kk ?? '-' }}</code></td>
        </tr>
        <tr>
            <td class="lbl">No. WhatsApp / HP Siswa</td>
            <td class="colon">:</td>
            <td class="val">{{ $calonSiswa->no_hp_siswa ?? '-' }}</td>
            <td class="lbl">Email Akun</td>
            <td class="colon">:</td>
            <td class="val">{{ $calonSiswa->email ?? $calonSiswa->user?->email ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat Domisili</td>
            <td class="colon">:</td>
            <td class="val" colspan="4">
                {{ $calonSiswa->alamat_lengkap ?? '-' }}
                @if($calonSiswa->rt || $calonSiswa->rw) (RT {{ $calonSiswa->rt ?? '0' }} / RW {{ $calonSiswa->rw ?? '0' }}) @endif
                @if($calonSiswa->desa), Desa/Kel. {{ $calonSiswa->desa->nama }} @endif
                @if($calonSiswa->kecamatan), Kec. {{ $calonSiswa->kecamatan->nama }} @endif
                @if($calonSiswa->kabupaten), Kab/Kota {{ $calonSiswa->kabupaten->nama }} @endif
                @if($calonSiswa->provinsi), Prov. {{ $calonSiswa->provinsi->nama }} @endif
                @if($calonSiswa->kode_pos) &bull; Kode Pos: {{ $calonSiswa->kode_pos }} @endif
            </td>
        </tr>
        <tr>
            <td class="lbl">Sekolah Asal (SMP/MTs)</td>
            <td class="colon">:</td>
            <td class="val" colspan="4">
                <strong>{{ $calonSiswa->sekolahAsal?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }}</strong>
                @if($calonSiswa->sekolahAsal?->npsn) (NPSN: {{ $calonSiswa->sekolahAsal->npsn }}) @endif
                @if($calonSiswa->sekolahAsal?->kabupaten) &bull; {{ $calonSiswa->sekolahAsal->kabupaten }} @endif
            </td>
        </tr>
    </table>
</div>

<!-- 3. DATA ORANG TUA / WALI -->
<div class="box-avoid">
    <div class="section-title">3. Data Orang Tua & Wali Murid</div>
    @php $ortu = $calonSiswa->dataOrangtua; @endphp
    @if ($ortu)
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 25%;">Keterangan Data</th>
                <th style="width: 37%;">Data Ayah Kandung</th>
                <th style="width: 38%;">Data Ibu Kandung</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Nama Lengkap</strong></td>
                <td>{{ $ortu->nama_ayah ?? '-' }} ({{ $ortu->status_ayah ?? 'Hidup' }})</td>
                <td>{{ $ortu->nama_ibu ?? '-' }} ({{ $ortu->status_ibu ?? 'Hidup' }})</td>
            </tr>
            <tr>
                <td><strong>NIK & Tahun Lahir</strong></td>
                <td>NIK: {{ $ortu->nik_ayah ?? '-' }} &bull; Thn: {{ $ortu->tahun_lahir_ayah ?? '-' }}</td>
                <td>NIK: {{ $ortu->nik_ibu ?? '-' }} &bull; Thn: {{ $ortu->tahun_lahir_ibu ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Pendidikan Terakhir</strong></td>
                <td>{{ $ortu->pendidikan_ayah ?? '-' }}</td>
                <td>{{ $ortu->pendidikan_ibu ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Pekerjaan</strong></td>
                <td>{{ $ortu->pekerjaanAyah?->nama ?? $ortu->pekerjaan_ayah ?? '-' }}</td>
                <td>{{ $ortu->pekerjaanIbu?->nama ?? $ortu->pekerjaan_ibu ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Penghasilan / Bulan</strong></td>
                <td>{{ $ortu->penghasilan_ayah ?? '-' }}</td>
                <td>{{ $ortu->penghasilan_ibu ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Nomor Telepon / HP</strong></td>
                <td>{{ $ortu->no_hp_ayah ?? '-' }}</td>
                <td>{{ $ortu->no_hp_ibu ?? '-' }}</td>
            </tr>
        </tbody>
    </table>
    @if(!empty($ortu->nama_wali))
    <div class="notes-box">
        <strong>Data Wali Murid:</strong> {{ $ortu->nama_wali }} (Hubungan: {{ $ortu->hubungan_wali ?? '-' }}) &bull; Pekerjaan: {{ $ortu->pekerjaanWali?->nama ?? $ortu->pekerjaan_wali ?? '-' }} &bull; Penghasilan: {{ $ortu->penghasilan_wali ?? '-' }} &bull; HP: {{ $ortu->no_hp_wali ?? '-' }}
    </div>
    @endif
    @else
        <p style="font-size: 8.5pt; font-style: italic; color: #64748b; margin: 4px 0;">Data orang tua belum dilengkapi di sistem.</p>
    @endif
</div>

<!-- 4. NILAI RAPOR SISWA -->
<div class="box-avoid">
    <div class="section-title">4. Nilai Rapor SMP/MTs (Semester 1 s.d. 5)</div>
    @php $rapor = $calonSiswa->nilaiRapor; @endphp
    @if ($rapor)
    @php
        $matrix = $rapor->matrix;
        $mapelLabels = [
            'ind' => 'Bahasa Indonesia',
            'eng' => 'Bahasa Inggris',
            'mtk' => 'Matematika',
            'pai' => 'Pendidikan Agama Islam',
        ];
    @endphp
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="center">No</th>
                <th style="width: 35%;">Mata Pelajaran Pokok</th>
                <th style="width: 10%;" class="center">Sem 1</th>
                <th style="width: 10%;" class="center">Sem 2</th>
                <th style="width: 10%;" class="center">Sem 3</th>
                <th style="width: 10%;" class="center">Sem 4</th>
                <th style="width: 10%;" class="center">Sem 5</th>
                <th style="width: 10%;" class="center">Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $i = 1; 
                $grandTotal = 0; 
                $grandCount = 0;
                $semTotals = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                $semCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
            @endphp
            @foreach($mapelLabels as $code => $label)
                @php
                    $rowSum = 0;
                    $rowCount = 0;
                    for ($s = 1; $s <= 5; $s++) {
                        $val = (float) ($matrix[$code][$s] ?? 0);
                        if ($val > 0) {
                            $rowSum += $val;
                            $rowCount++;
                            $semTotals[$s] += $val;
                            $semCounts[$s]++;
                        }
                    }
                    $rowAvg = $rowCount > 0 ? $rowSum / $rowCount : 0;
                    $grandTotal += $rowSum;
                    $grandCount += $rowCount;
                @endphp
                <tr>
                    <td class="center">{{ $i++ }}</td>
                    <td><strong>{{ $label }}</strong></td>
                    @for($s = 1; $s <= 5; $s++)
                        <td class="center">{{ number_format((float)($matrix[$code][$s] ?? 0), 1) }}</td>
                    @endfor
                    <td class="center" style="font-weight: bold; background-color: #f8fafc;">{{ number_format($rowAvg, 2) }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="2" class="right">Rata-rata Per Semester:</td>
                @for($s = 1; $s <= 5; $s++)
                    @php $sAvg = $semCounts[$s] > 0 ? $semTotals[$s] / $semCounts[$s] : 0; @endphp
                    <td class="center">{{ number_format($sAvg, 2) }}</td>
                @endfor
                <td class="center" style="color: #0369a1; font-size: 8.5pt;">
                    {{ number_format($grandCount > 0 ? $grandTotal / $grandCount : 0, 2) }}
                </td>
            </tr>
        </tbody>
    </table>
    @else
        <p style="font-size: 8.5pt; font-style: italic; color: #64748b; margin: 4px 0;">Nilai rapor semester 1 s.d. 5 belum diinput.</p>
    @endif
</div>

<!-- 5. UKURAN SERAGAM -->
<div class="box-avoid">
    <div class="section-title">5. Data Ukuran Seragam & Atribut Sekolah</div>
    @if ($calonSiswa->ukuranSeragam->isNotEmpty())
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="center">No</th>
                <th style="width: 45%;">Jenis Seragam / Perlengkapan</th>
                <th style="width: 20%;" class="center">Ukuran Terpilih</th>
                <th style="width: 15%;" class="center">Jumlah</th>
                <th style="width: 20%;" class="center">Status Pengadaan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($calonSiswa->ukuranSeragam as $idx => $srg)
            <tr>
                <td class="center">{{ $idx + 1 }}</td>
                <td>{{ $srg->seragam?->nama ?? $srg->seragam?->nama_seragam ?? 'Seragam Sekolah' }}</td>
                <td class="center"><strong>{{ $srg->ukuran ?? '-' }}</strong></td>
                <td class="center">{{ $srg->jumlah ?? 1 }} stel</td>
                <td class="center">{{ $srg->status_label ?? ($srg->beli_di_sekolah ? 'Pesan Sekarang' : 'Pesan Nanti') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p style="font-size: 8.5pt; font-style: italic; color: #64748b; margin: 4px 0;">Belum ada pilihan data ukuran seragam yang tercatat.</p>
    @endif
</div>

<!-- 6. KESEPAHAMAN / PAKTA INTEGRITAS (EULA) -->
<div class="box-avoid">
    <div class="section-title">6. Kesepahaman & Pakta Integritas (EULA)</div>
    @php $eula = $calonSiswa->kesepahaman->first(); @endphp
    @if ($eula)
    <table class="sub-table">
        <tr>
            <td class="lbl">Status Persetujuan</td>
            <td class="colon">:</td>
            <td class="val">
                <span class="badge-status {{ $eula->setuju ? 'badge-verified' : 'badge-danger' }}">
                    {{ $eula->setuju ? 'DISETUJUI SECARA ELEKTRONIK' : 'BELUM DISETUJUI' }}
                </span>
            </td>
            <td class="lbl">Waktu Persetujuan</td>
            <td class="colon">:</td>
            <td class="val">{{ $eula->agreed_at ? $eula->agreed_at->translatedFormat('d F Y H:i:s') : '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Versi Pakta / EULA</td>
            <td class="colon">:</td>
            <td class="val">{{ $eula->versi_dokumen ?? 'v1.0' }}</td>
            <td class="lbl">Alamat IP / Perangkat</td>
            <td class="colon">:</td>
            <td class="val">{{ $eula->ip_address ?? '127.0.0.1' }}</td>
        </tr>
        <tr>
            <td class="lbl">Pernyataan Kesepahaman</td>
            <td class="colon">:</td>
            <td class="val" colspan="4">
                <em>{{ $eula->isi_dokumen_atau_referensi_dokumen ?? 'Calon siswa dan orang tua/wali telah menyepakati seluruh ketentuan tata tertib, hak & kewajiban pembiayaan, serta integritas akademik SMK Wikrama 1 Garut.' }}</em>
            </td>
        </tr>
    </table>
    @else
        <p style="font-size: 8.5pt; font-style: italic; color: #64748b; margin: 4px 0;">Belum ada rekaman persetujuan kesepahaman pakta integritas.</p>
    @endif
</div>

<!-- 7. VERIFIKASI DOKUMEN PERSYARATAN -->
<div class="box-avoid">
    <div class="section-title">7. Status Verifikasi Berkas & Dokumen Pendaftaran</div>
    @php $doc = $calonSiswa->dokumenPendaftaran; @endphp
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="center">No</th>
                <th style="width: 45%;">Nama Dokumen Persyaratan</th>
                <th style="width: 25%;" class="center">Status Berkas</th>
                <th style="width: 25%;" class="center">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center">1</td>
                <td>Kartu Keluarga (KK)</td>
                <td class="center">
                    <span class="badge-status {{ $doc?->kk_path ? 'badge-verified' : 'badge-pending' }}">
                        {{ $doc?->kk_path ? 'BERKAS TERSEDIA' : 'BELUM DIUNGGAH' }}
                    </span>
                </td>
                <td class="center">{{ $doc?->kk_path ? 'Tervalidasi' : '-' }}</td>
            </tr>
            <tr>
                <td class="center">2</td>
                <td>Akta Kelahiran</td>
                <td class="center">
                    <span class="badge-status {{ $doc?->akta_path ? 'badge-verified' : 'badge-pending' }}">
                        {{ $doc?->akta_path ? 'BERKAS TERSEDIA' : 'BELUM DIUNGGAH' }}
                    </span>
                </td>
                <td class="center">{{ $doc?->akta_path ? 'Tervalidasi' : '-' }}</td>
            </tr>
            <tr>
                <td class="center">3</td>
                <td>Ijazah / Surat Keterangan Lulus (SKL) SMP</td>
                <td class="center">
                    <span class="badge-status {{ $doc?->ijazah_skl_path ? 'badge-verified' : 'badge-pending' }}">
                        {{ $doc?->ijazah_skl_path ? 'BERKAS TERSEDIA' : 'BELUM DIUNGGAH' }}
                    </span>
                </td>
                <td class="center">{{ $doc?->ijazah_skl_path ? 'Tervalidasi' : '-' }}</td>
            </tr>
            <tr>
                <td class="center">4</td>
                <td>Pas Foto Calon Siswa (Terbaru)</td>
                <td class="center">
                    <span class="badge-status {{ $doc?->pas_foto_path ? 'badge-verified' : 'badge-pending' }}">
                        {{ $doc?->pas_foto_path ? 'BERKAS TERSEDIA' : 'BELUM DIUNGGAH' }}
                    </span>
                </td>
                <td class="center">{{ $doc?->pas_foto_path ? 'Tervalidasi' : '-' }}</td>
            </tr>
            <tr>
                <td class="center">5</td>
                <td>Dokumen Pendukung / Prestasi / KIP (Opsional)</td>
                <td class="center">
                    <span class="badge-status {{ $doc?->dokumen_pendukung_path ? 'badge-verified' : 'badge-info' }}">
                        {{ $doc?->dokumen_pendukung_path ? 'BERKAS TERSEDIA' : 'TIDAK ADA' }}
                    </span>
                </td>
                <td class="center">{{ $doc?->dokumen_pendukung_path ? 'Dilampirkan' : '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- 8. PEMBAYARAN BIAYA SELEKSI -->
<div class="box-avoid">
    <div class="section-title">8. Data Pembayaran Biaya Seleksi Pendaftaran</div>
    @php $bayarSeleksi = $calonSiswa->pembayaranSeleksi; @endphp
    <table class="sub-table">
        <tr>
            <td class="lbl">Status Pembayaran</td>
            <td class="colon">:</td>
            <td class="val">
                <span class="badge-status {{ $bayarSeleksi?->status === 'DIVERIFIKASI' ? 'badge-verified' : ($bayarSeleksi?->status === 'DITOLAK' ? 'badge-danger' : 'badge-pending') }}">
                    {{ $bayarSeleksi?->status ?? 'BELUM BAYAR' }}
                </span>
            </td>
            <td class="lbl">Nominal Kewajiban / Dibayar</td>
            <td class="colon">:</td>
            <td class="val">
                Rp {{ number_format($bayarSeleksi?->nominal_tagihan ?? 200000, 0, ',', '.') }} / 
                <strong>Rp {{ number_format($bayarSeleksi?->nominal_dibayar ?? 0, 0, ',', '.') }}</strong>
            </td>
        </tr>
        <tr>
            <td class="lbl">Tanggal & Metode Bayar</td>
            <td class="colon">:</td>
            <td class="val">
                {{ $bayarSeleksi?->tanggal_bayar ? $bayarSeleksi->tanggal_bayar->translatedFormat('d F Y') : '-' }} &bull; 
                {{ strtoupper(str_replace('_', ' ', $bayarSeleksi?->metode_bayar ?? 'Transfer Bank')) }}
            </td>
            <td class="lbl">Bank & Nama Pengirim</td>
            <td class="colon">:</td>
            <td class="val">{{ $bayarSeleksi?->bank_pengirim ?? '-' }} a.n. {{ $bayarSeleksi?->nama_pengirim ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Verifikator & Waktu</td>
            <td class="colon">:</td>
            <td class="val">
                {{ $bayarSeleksi?->verifiedBy?->name ?? 'Bendahara Sekolah' }} &bull; 
                {{ $bayarSeleksi?->verified_at ? $bayarSeleksi->verified_at->translatedFormat('d F Y H:i') : '-' }}
            </td>
            <td class="lbl">Nomor Referensi Transaksi</td>
            <td class="colon">:</td>
            <td class="val"><code>{{ $bayarSeleksi?->nomor_referensi ?? '-' }}</code></td>
        </tr>
    </table>
</div>

<!-- 9. HASIL EVALUASI WAWANCARA -->
<div class="box-avoid">
    <div class="section-title">9. Hasil Evaluasi Wawancara (Siswa & Orang Tua)</div>
    @php
        $wSiswa = $calonSiswa->wawancaraSiswa;
        $wOrtu  = $calonSiswa->wawancaraOrangTua;
    @endphp

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 5px;">
                <div style="border: 1px solid #cbd5e1; padding: 6px 8px; border-radius: 4px; background-color: #fafafa;">
                    <div style="font-weight: bold; border-bottom: 1px solid #cbd5e1; padding-bottom: 3px; margin-bottom: 4px; font-size: 8.5pt;">
                        A. Wawancara Calon Siswa
                        <span class="badge-status {{ $wSiswa?->status === 'SELESAI' ? 'badge-verified' : 'badge-pending' }}" style="float: right;">
                            {{ $wSiswa?->status ?? 'BELUM' }}
                        </span>
                    </div>
                    @if ($wSiswa)
                    <table style="width: 100%; font-size: 7.5pt;">
                        <tr><td style="width: 40%; color: #475569;">Pewawancara</td><td>: {{ $wSiswa->pewawancara?->name ?? $wSiswa->nama_petugas }}</td></tr>
                        <tr><td style="color: #475569;">Tgl Wawancara</td><td>: {{ $wSiswa->tanggal_wawancara ? $wSiswa->tanggal_wawancara->translatedFormat('d M Y') : '-' }}</td></tr>
                        <tr><td style="color: #475569;">Rekomendasi</td><td>: 
                            <strong style="color: {{ $wSiswa->rekomendasi === 'TERIMA' ? '#15803d' : ($wSiswa->rekomendasi === 'PERTIMBANGKAN' ? '#b45309' : '#b91c1c') }}">
                                {{ $wSiswa->rekomendasi ?? '-' }}
                            </strong>
                        </td></tr>
                        <tr><td style="color: #475569;">Baca / Hafal Al-Qur'an</td><td>: {{ $wSiswa->baca_quran ?? '-' }} @if($wSiswa->hafalan_quran) ({{ $wSiswa->hafalan_quran }}) @endif</td></tr>
                        <tr><td style="color: #475569;">Kedisiplinan & Fisik</td><td>: Rambut: {{ $wSiswa->kerapihan_rambut ?? '-' }} | Seragam: {{ $wSiswa->kerapihan_seragam ?? '-' }}</td></tr>
                        <tr><td style="color: #475569;">Kondisi Kesehatan</td><td>: {{ $wSiswa->kondisi_kesehatan ?? '-' }} @if($wSiswa->status_penglihatan_lainnya) ({{ $wSiswa->status_penglihatan_lainnya }}) @endif</td></tr>
                        <tr><td style="color: #475569;">Catatan Pewawancara</td><td>: {{ $wSiswa->catatan_pewawancara ?: '-' }}</td></tr>
                    </table>
                    @else
                        <p style="font-size: 8pt; font-style: italic; color: #94a3b8;">Belum ada rekaman wawancara siswa.</p>
                    @endif
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 5px;">
                <div style="border: 1px solid #cbd5e1; padding: 6px 8px; border-radius: 4px; background-color: #fafafa;">
                    <div style="font-weight: bold; border-bottom: 1px solid #cbd5e1; padding-bottom: 3px; margin-bottom: 4px; font-size: 8.5pt;">
                        B. Wawancara Orang Tua / Wali
                        <span class="badge-status {{ $wOrtu?->status === 'SELESAI' ? 'badge-verified' : 'badge-pending' }}" style="float: right;">
                            {{ $wOrtu?->status ?? 'BELUM' }}
                        </span>
                    </div>
                    @if ($wOrtu)
                    <table style="width: 100%; font-size: 7.5pt;">
                        <tr><td style="width: 40%; color: #475569;">Pewawancara</td><td>: {{ $wOrtu->pewawancara?->name ?? $wOrtu->nama_petugas }}</td></tr>
                        <tr><td style="color: #475569;">Tgl Wawancara</td><td>: {{ $wOrtu->tanggal_wawancara ? $wOrtu->tanggal_wawancara->translatedFormat('d M Y') : '-' }}</td></tr>
                        <tr><td style="color: #475569;">Narasumber Hadir</td><td>: {{ $wOrtu->nama_diwawancarai }} ({{ $wOrtu->hubungan_dengan_siswa }})</td></tr>
                        <tr><td style="color: #475569;">Penanggung Jwb Belajar</td><td>: {{ $wOrtu->penanggung_jawab_belajar ?? '-' }}</td></tr>
                        <tr><td style="color: #475569;">Info Wikrama dari</td><td>: {{ $wOrtu->info_wikrama_dari ?? '-' }}</td></tr>
                        <tr><td style="color: #475569;">Perhatian Khusus Ortu</td><td>: {{ $wOrtu->hal_perhatian_ortu ?: '-' }}</td></tr>
                        <tr><td style="color: #475569;">Kesan Pewawancara</td><td>: {{ $wOrtu->kesan_pewawancara ?: '-' }}</td></tr>
                    </table>
                    @else
                        <p style="font-size: 8pt; font-style: italic; color: #94a3b8;">Belum ada rekaman wawancara orang tua.</p>
                    @endif
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- 10. KEUANGAN & DAFTAR ULANG -->
<div class="box-avoid">
    <div class="section-title">10. Data Keuangan & Daftar Ulang Siswa</div>
    @php
        $tagihanDU = $calonSiswa->tagihan->firstWhere('jenis_tagihan', 'DAFTAR_ULANG');
        $tagihanSRG = $calonSiswa->tagihan->firstWhere('jenis_tagihan', 'SERAGAM');
    @endphp

    @if ($tagihanDU || $tagihanSRG)
        <!-- Kondisi: Tagihan SUDAH Diterbitkan -->
        <table class="grid-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="center">No</th>
                    <th style="width: 25%;">No. Tagihan</th>
                    <th style="width: 25%;">Jenis Pembiayaan</th>
                    <th style="width: 15%;" class="right">Total Bruto</th>
                    <th style="width: 15%;" class="right">Diskon</th>
                    <th style="width: 15%;" class="right">Total Netto</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; $totalNettoAll = 0; @endphp
                @if ($tagihanDU)
                    @php $totalNettoAll += $tagihanDU->total_netto; @endphp
                    <tr>
                        <td class="center">{{ $no++ }}</td>
                        <td><code>{{ $tagihanDU->nomor_tagihan }}</code></td>
                        <td>Biaya Pendidikan (DSP, SPP & Asrama)</td>
                        <td class="right">Rp {{ number_format($tagihanDU->total_bruto, 0, ',', '.') }}</td>
                        <td class="right" style="color: #b91c1c;">- Rp {{ number_format($tagihanDU->total_diskon, 0, ',', '.') }}</td>
                        <td class="right" style="font-weight: bold; color: #0369a1;">Rp {{ number_format($tagihanDU->total_netto, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if ($tagihanSRG)
                    @php $totalNettoAll += $tagihanSRG->total_netto; @endphp
                    <tr>
                        <td class="center">{{ $no++ }}</td>
                        <td><code>{{ $tagihanSRG->nomor_tagihan }}</code></td>
                        <td>Paket Seragam & Atribut Sekolah</td>
                        <td class="right">Rp {{ number_format($tagihanSRG->total_bruto, 0, ',', '.') }}</td>
                        <td class="right" style="color: #b91c1c;">- Rp {{ number_format($tagihanSRG->total_diskon, 0, ',', '.') }}</td>
                        <td class="right" style="font-weight: bold; color: #0369a1;">Rp {{ number_format($tagihanSRG->total_netto, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @php
                    $totalBayarSemua = (float) $calonSiswa->pembayaranDaftarUlang->where('status', 'DIVERIFIKASI')->sum('nominal_dibayar');
                    $sisaSemua = max(0, $totalNettoAll - $totalBayarSemua);
                @endphp
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="5" class="right">TOTAL KEWAJIBAN BIAYA NETTO:</td>
                    <td class="right" style="color: #0369a1;">Rp {{ number_format($totalNettoAll, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="right" style="color: #15803d; font-weight: bold;">TOTAL TELAH DIBAYAR (DIVERIFIKASI):</td>
                    <td class="right" style="color: #15803d; font-weight: bold;">Rp {{ number_format($totalBayarSemua, 0, ',', '.') }}</td>
                </tr>
                <tr style="background-color: #fffbeb; font-weight: bold;">
                    <td colspan="5" class="right" style="color: #b45309;">SISA PIUTANG KEWAJIBAN:</td>
                    <td class="right" style="color: #b45309; font-size: 8.5pt;">Rp {{ number_format($sisaSemua, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <!-- Kondisi: Tagihan BELUM Diterbitkan (Estimasi Biaya Baku) -->
        <div style="background-color: #fefce8; border: 1px solid #fef08a; padding: 5px 8px; border-radius: 4px; margin-bottom: 6px; font-size: 8pt; color: #854d0e;">
            <strong>Keterangan:</strong> Invoice resmi daftar ulang belum diterbitkan oleh Bendahara. Tabel di bawah merupakan estimasi biaya baku sesuai kebijakan Master Biaya Gelombang & Program siswa.
        </div>
        <table class="grid-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="center">No</th>
                    <th style="width: 20%;">Kode Biaya</th>
                    <th style="width: 45%;">Komponen Estimasi Pembiayaan</th>
                    <th style="width: 15%;" class="center">Kategori</th>
                    <th style="width: 15%;" class="right">Nominal Baku</th>
                </tr>
            </thead>
            <tbody>
                @php $estimasiTotal = 0; $no = 1; @endphp
                @foreach($estimasiBiayaDaftarUlang as $item)
                    @php $estimasiTotal += $item->nominal; @endphp
                    <tr>
                        <td class="center">{{ $no++ }}</td>
                        <td><code>{{ $item->kode_biaya }}</code></td>
                        <td>{{ $item->nama_biaya }}</td>
                        <td class="center">{{ strtoupper($item->kategori) }}</td>
                        <td class="right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                @if(isset($estimasiBiayaSeragam) && $estimasiBiayaSeragam->isNotEmpty())
                    @php $totSeragam = $estimasiBiayaSeragam->sum('nominal'); $estimasiTotal += $totSeragam; @endphp
                    <tr>
                        <td class="center">{{ $no++ }}</td>
                        <td><code>SRG-PAKET</code></td>
                        <td>Estimasi Paket Seragam Wajib ({{ $estimasiBiayaSeragam->count() }} Komponen)</td>
                        <td class="center">SERAGAM</td>
                        <td class="right">Rp {{ number_format($totSeragam, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="4" class="right">ESTIMASI TOTAL BIAYA BAKU DAFTAR ULANG:</td>
                    <td class="right" style="color: #0369a1; font-size: 8.5pt;">Rp {{ number_format($estimasiTotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif
</div>

<!-- 11. LEMBAR PENGESAHAN -->
<div class="box-avoid" style="margin-top: 15px;">
    <table style="width: 100%; text-align: center; font-size: 8.5pt;">
        <tr>
            <td style="width: 33%;">
                Mengetahui / Menyetujui,<br>
                <strong>Orang Tua / Wali Siswa</strong>
                <div style="height: 50px;"></div>
                ( .................................................... )
            </td>
            <td style="width: 33%;">
                Diverifikasi Oleh,<br>
                <strong>Panitia SPMB / Petugas</strong>
                <div style="height: 50px;"></div>
                ( {{ auth()->user()?->name ?? 'Panitia SPMB' }} )
            </td>
            <td style="width: 34%;">
                Garut, {{ now()->translatedFormat('d F Y') }}<br>
                <strong>Kepala SMK Wikrama 1 Garut</strong>
                <div style="height: 50px;"></div>
                <strong>( Kepala Sekolah )</strong>
            </td>
        </tr>
    </table>
</div>

@endsection
