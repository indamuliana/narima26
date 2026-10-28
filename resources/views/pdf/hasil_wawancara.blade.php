@extends('pdf.layout', ['title' => 'Hasil Wawancara - ' . $calonSiswa->nomor_pendaftaran . ' - ' . $calonSiswa->nama_lengkap])

@section('content')
    <style>
        .section-header {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 8px;
            margin-top: 10px;
            margin-bottom: 6px;
            border-radius: 3px;
        }
        .sub-header {
            background-color: #f1f5f9;
            color: #1e293b;
            font-size: 8.5pt;
            font-weight: bold;
            padding: 3px 6px;
            margin-top: 6px;
            margin-bottom: 4px;
            border-left: 3px solid #0284c7;
        }
        .w-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .w-table td {
            padding: 3px 5px;
            vertical-align: top;
            font-size: 8pt;
            border-bottom: 1px dotted #e2e8f0;
        }
        .w-table td.lbl {
            width: 32%;
            color: #475569;
            font-weight: 600;
        }
        .w-table td.col {
            width: 2%;
            text-align: center;
            color: #94a3b8;
        }
        .w-table td.val {
            width: 66%;
            color: #0f172a;
        }
        .box-note {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 5px 8px;
            font-size: 8pt;
            margin-top: 4px;
            margin-bottom: 6px;
        }
        .tag-rekom {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8.5pt;
        }
        .tag-terima { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .tag-timbang { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .tag-tolak { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .page-break {
            page-break-after: always;
        }
    </style>

    <div class="doc-title" style="margin-top: 0; font-size: 13pt;">LEMBAR HASIL EVALUASI WAWANCARA SELEKSI</div>
    <div class="doc-number" style="margin-bottom: 8px;">
        Nomor Pendaftaran: <strong>{{ $calonSiswa->nomor_pendaftaran }}</strong> &bull; NISN: <strong>{{ $calonSiswa->nisn }}</strong>
    </div>

    <!-- REKAPITULASI IDENTITAS CALON SISWA -->
    <table class="info-table" style="margin-bottom: 8px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 6px; background-color: #fafafa;">
        <tr>
            <td class="label" style="width: 20%; font-size: 8.5pt; padding: 2px 4px;">Nama Lengkap</td>
            <td class="colon" style="width: 2%; font-size: 8.5pt; padding: 2px 2px;">:</td>
            <td class="value" style="width: 40%; font-size: 8.5pt; padding: 2px 4px;"><strong>{{ strtoupper($calonSiswa->nama_lengkap) }}</strong> ({{ $calonSiswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }})</td>
            <td class="label" style="width: 16%; font-size: 8.5pt; padding: 2px 4px;">Program</td>
            <td class="colon" style="width: 2%; font-size: 8.5pt; padding: 2px 2px;">:</td>
            <td class="value" style="width: 20%; font-size: 8.5pt; padding: 2px 4px;">{{ $calonSiswa->program?->nama ?? $calonSiswa->program?->nama_program ?? 'Reguler' }}</td>
        </tr>
        <tr>
            <td class="label" style="font-size: 8.5pt; padding: 2px 4px;">Asal Sekolah</td>
            <td class="colon" style="font-size: 8.5pt; padding: 2px 2px;">:</td>
            <td class="value" style="font-size: 8.5pt; padding: 2px 4px;">{{ $calonSiswa->sekolah_asal_text }}</td>
            <td class="label" style="font-size: 8.5pt; padding: 2px 4px;">Jurusan Pil. 1</td>
            <td class="colon" style="font-size: 8.5pt; padding: 2px 2px;">:</td>
            <td class="value" style="font-size: 8.5pt; padding: 2px 4px;"><strong>{{ $calonSiswa->jurusan?->nama_jurusan ?? ($calonSiswa->jurusan?->nama ?? '-') }}</strong></td>
        </tr>
        <tr>
            <td class="label" style="font-size: 8.5pt; padding: 2px 4px;">Gelombang</td>
            <td class="colon" style="font-size: 8.5pt; padding: 2px 2px;">:</td>
            <td class="value" style="font-size: 8.5pt; padding: 2px 4px;">{{ $calonSiswa->gelombang?->nama ?? 'Gelombang 1' }}</td>
            <td class="label" style="font-size: 8.5pt; padding: 2px 4px;">Jurusan Pil. 2</td>
            <td class="colon" style="font-size: 8.5pt; padding: 2px 2px;">:</td>
            <td class="value" style="font-size: 8.5pt; padding: 2px 4px;">{{ $calonSiswa->jurusan2?->nama_jurusan ?? ($calonSiswa->jurusan2?->nama ?? '-') }}</td>
        </tr>
    </table>

    <!-- BAGIAN I: WAWANCARA SISWA -->
    <div class="section-header">
        I. Hasil Wawancara Calon Siswa
        <span style="float: right; font-weight: normal; font-size: 8pt;">
            Status: <strong>{{ $wawancaraSiswa?->status ?? 'BELUM' }}</strong> &bull;
            Pewawancara: {{ $wawancaraSiswa?->pewawancara?->name ?? ($wawancaraSiswa?->nama_petugas ?? '-') }} &bull;
            Tgl: {{ $wawancaraSiswa?->tanggal_wawancara ? $wawancaraSiswa->tanggal_wawancara->translatedFormat('d M Y') : '-' }}
        </span>
    </div>

    @if ($wawancaraSiswa)
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 5px;">
                    <div class="sub-header">A. Motivasi, Minat & Cita-Cita</div>
                    <table class="w-table">
                        <tr><td class="lbl">Masuk Wikrama Karena</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->alasan_masuk_wikrama ?: '-' }}</td></tr>
                        <tr><td class="lbl">Alasan Pilih Program</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->alasan_pilih_program ?: '-' }}</td></tr>
                        <tr><td class="lbl">Alasan Pilih Jurusan</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->alasan_pilih_jurusan ?: '-' }}</td></tr>
                        <tr><td class="lbl">Cita-Cita Siswa</td><td class="col">:</td><td class="val"><strong>{{ $wawancaraSiswa->cita_cita ?: '-' }}</strong></td></tr>
                        <tr><td class="lbl">Aktivitas Rutin Luar Sekolah</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->aktivitas_rutin ?: '-' }}</td></tr>
                    </table>

                    <div class="sub-header">B. Keagamaan & Pembiasaan</div>
                    <table class="w-table">
                        <tr><td class="lbl">Kemampuan Baca Al-Qur'an</td><td class="col">:</td><td class="val"><strong>{{ $wawancaraSiswa->baca_quran ?: '-' }}</strong></td></tr>
                        <tr><td class="lbl">Jumlah Hafalan Qur'an</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->hafalan_quran ?: '-' }}</td></tr>
                        <tr><td class="lbl">Tinggal Bersama</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->tinggal_bersama ?: '-' }}</td></tr>
                        <tr><td class="lbl">Penanggung Jawab Belajar</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->penanggung_jawab_belajar ?: '-' }}</td></tr>
                        <tr><td class="lbl">Informasi Wikrama dari</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->info_wikrama_dari ?: '-' }}</td></tr>
                        <tr><td class="lbl">Kesan Terhadap Siswa Aktif</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->kesan_siswa_aktif ?: '-' }}</td></tr>
                    </table>
                </td>

                <td style="width: 50%; vertical-align: top; padding-left: 5px;">
                    <div class="sub-header">C. Kesehatan & Kebiasaan</div>
                    <table class="w-table">
                        <tr><td class="lbl">Status Merokok / Vape</td><td class="col">:</td><td class="val"><strong>{{ $wawancaraSiswa->merokok ?: '-' }}</strong></td></tr>
                        <tr><td class="lbl">Kondisi Kesehatan Saat Ini</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->kondisi_kesehatan ?: '-' }}</td></tr>
                        <tr><td class="lbl">Penyakit Menahun</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->penyakit_menahun ?: '-' }}</td></tr>
                        <tr><td class="lbl">Disabilitas Fisik</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->disabilitas ?: 'Tidak Ada' }}</td></tr>
                        <tr><td class="lbl">Riwayat Alergi</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->alergi ?: '-' }}</td></tr>
                    </table>

                    <div class="sub-header">D. Observasi Fisik & Kedisiplinan</div>
                    <table class="w-table">
                        <tr><td class="lbl">Kerapihan Rambut</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->kerapihan_rambut }}</td></tr>
                        <tr><td class="lbl">Kerapihan Pakaian / Seragam</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->kerapihan_seragam }}</td></tr>
                        <tr><td class="lbl">Pemeriksaan Pendengaran</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->status_pendengaran }}</td></tr>
                        <tr><td class="lbl">Kondisi Penglihatan</td><td class="col">:</td><td class="val">
                            {{ $wawancaraSiswa->status_penglihatan }}
                            @if($wawancaraSiswa->mata_kiri || $wawancaraSiswa->mata_kanan)
                                (Kiri: {{ $wawancaraSiswa->mata_kiri ?? '0' }}, Kanan: {{ $wawancaraSiswa->mata_kanan ?? '0' }})
                            @endif
                            @if($wawancaraSiswa->status_penglihatan_lainnya)
                                &bull; {{ $wawancaraSiswa->status_penglihatan_lainnya }}
                            @endif
                        </td></tr>
                    </table>

                    <div class="sub-header">E. Evaluasi & Rekomendasi Pewawancara</div>
                    <table class="w-table">
                        <tr><td class="lbl">Rekomendasi Akhir</td><td class="col">:</td><td class="val">
                            @if($wawancaraSiswa->rekomendasi === 'TERIMA')
                                <span class="tag-rekom tag-terima">DIREKOMENDASIKAN DITERIMA</span>
                            @elseif($wawancaraSiswa->rekomendasi === 'PERTIMBANGKAN')
                                <span class="tag-rekom tag-timbang">DIPERTIMBANGKAN</span>
                            @else
                                <span class="tag-rekom tag-tolak">TIDAK DIREKOMENDASIKAN</span>
                            @endif
                        </td></tr>
                        <tr><td class="lbl">Perhatian Khusus</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->hal_perhatian_khusus ?: '-' }}</td></tr>
                        <tr><td class="lbl">Kesan Pewawancara</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->kesan_pewawancara ?: '-' }}</td></tr>
                        <tr><td class="lbl">Catatan Penting</td><td class="col">:</td><td class="val">{{ $wawancaraSiswa->catatan_pewawancara ?: '-' }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    @else
        <div class="box-note" style="color: #64748b; font-style: italic;">Data instrumen wawancara siswa belum diinput oleh petugas pewawancara.</div>
    @endif

    <!-- BAGIAN II: WAWANCARA ORANG TUA / WALI -->
    <div class="section-header" style="margin-top: 8px;">
        II. Hasil Wawancara Orang Tua / Wali
        <span style="float: right; font-weight: normal; font-size: 8pt;">
            Status: <strong>{{ $wawancaraOrangTua?->status ?? 'BELUM' }}</strong> &bull;
            Pewawancara: {{ $wawancaraOrangTua?->pewawancara?->name ?? ($wawancaraOrangTua?->nama_petugas ?? '-') }} &bull;
            Tgl: {{ $wawancaraOrangTua?->tanggal_wawancara ? $wawancaraOrangTua->tanggal_wawancara->translatedFormat('d M Y') : '-' }}
        </span>
    </div>

    @if ($wawancaraOrangTua)
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 5px;">
                    <div class="sub-header">A. Identitas Narasumber & Lingkungan</div>
                    <table class="w-table">
                        <tr><td class="lbl">Narasumber Hadir</td><td class="col">:</td><td class="val"><strong>{{ $wawancaraOrangTua->nama_diwawancarai }}</strong> ({{ $wawancaraOrangTua->hubungan_dengan_siswa }})</td></tr>
                        <tr><td class="lbl">Selama Sekolah Tinggal Bersama</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->tinggal_bersama ?: '-' }}</td></tr>
                        <tr><td class="lbl">Jarak Rumah ke Sekolah</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->jarak_rumah ?: '-' }}</td></tr>
                        <tr><td class="lbl">Transportasi ke Sekolah</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->transportasi ?: '-' }}</td></tr>
                        <tr><td class="lbl">Penanggung Jawab Belajar</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->penanggung_jawab_belajar ?: '-' }}</td></tr>
                        <tr><td class="lbl">Informasi Wikrama dari</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->info_wikrama_dari ?: '-' }}</td></tr>
                    </table>

                    <div class="sub-header">B. Kebiasaan & Karakter Anak</div>
                    <table class="w-table">
                        <tr><td class="lbl">Merapihkan Tempat Tidur</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->kebiasaan_tempat_tidur ?: '-' }}</td></tr>
                        <tr><td class="lbl">Hobi Siswa</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->hobi ?: '-' }}</td></tr>
                        <tr><td class="lbl">Apakah Pernah Merokok</td><td class="col">:</td><td class="val"><strong>{{ $wawancaraOrangTua->merokok ?: '-' }}</strong></td></tr>
                        <tr><td class="lbl">Penyakit yang Diderita</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->penyakit_diderita ?: '-' }}</td></tr>
                        <tr><td class="lbl">Alergi Tertentu</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->alergi ?: '-' }}</td></tr>
                    </table>
                </td>

                <td style="width: 50%; vertical-align: top; padding-left: 5px;">
                    <div class="sub-header">C. Keagamaan & Dukungan Pilihan</div>
                    <table class="w-table">
                        <tr><td class="lbl">Lancar Baca Al-Qur'an</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->baca_quran ?: '-' }}</td></tr>
                        <tr><td class="lbl">Hafalan Qur'an Dimiliki</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->hafalan_quran ?: '-' }}</td></tr>
                        <tr><td class="lbl">Minat Program</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->minat_program ?: '-' }}</td></tr>
                        <tr><td class="lbl">Minat Jurusan</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->minat_jurusan ?: '-' }}</td></tr>
                        <tr><td class="lbl">Alasan Masuk Wikrama</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->alasan_masuk_wikrama ?: '-' }}</td></tr>
                        <tr><td class="lbl">Alasan Pilih Program/Jurusan</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->alasan_pilih_program_jurusan ?: '-' }}</td></tr>
                    </table>

                    <div class="sub-header">D. Komitmen Pembiayaan & Catatan Pewawancara</div>
                    <table class="w-table">
                        <tr><td class="lbl">Kesanggupan Infaq Bulanan</td><td class="col">:</td><td class="val">
                            <strong style="color: #047857; font-size: 8.5pt;">
                                {{ $wawancaraOrangTua->infaq_rutin_bulanan !== null ? 'Rp ' . number_format($wawancaraOrangTua->infaq_rutin_bulanan, 0, ',', '.') . ' / bulan' : '-' }}
                            </strong>
                        </td></tr>
                        <tr><td class="lbl">Perhatian Khusus dari Ortu</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->hal_perhatian_ortu ?: '-' }}</td></tr>
                        <tr><td class="lbl">Kesan Pewawancara</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->kesan_pewawancara ?: '-' }}</td></tr>
                        <tr><td class="lbl">Catatan Tambahan Pewawancara</td><td class="col">:</td><td class="val">{{ $wawancaraOrangTua->catatan_tambahan ?: '-' }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    @else
        <div class="box-note" style="color: #64748b; font-style: italic;">Data instrumen wawancara orang tua belum diinput oleh petugas pewawancara.</div>
    @endif

    <!-- LEMBAR PENGESAHAN & TANDA TANGAN -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 14px; page-break-inside: avoid;">
        <tr>
            <td colspan="3" style="text-align: right; font-size: 8pt; color: #64748b; padding-bottom: 4px;">
                Garut, {{ now()->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td style="width: 33%; text-align: center; vertical-align: top; font-size: 8pt;">
                <p style="margin: 0; color: #475569;">Pewawancara Siswa,</p>
                <div style="height: 48px;"></div>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                    {{ $wawancaraSiswa?->pewawancara?->name ?? ($wawancaraSiswa?->nama_petugas ?? 'Petugas Pewawancara Siswa') }}
                </p>
                <p style="margin: 0; font-size: 7.5pt; color: #64748b;">Tim Wawancara SPMB</p>
            </td>
            <td style="width: 33%; text-align: center; vertical-align: top; font-size: 8pt;">
                <p style="margin: 0; color: #475569;">Pewawancara Orang Tua,</p>
                <div style="height: 48px;"></div>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                    {{ $wawancaraOrangTua?->pewawancara?->name ?? ($wawancaraOrangTua?->nama_petugas ?? 'Petugas Pewawancara Orang Tua') }}
                </p>
                <p style="margin: 0; font-size: 7.5pt; color: #64748b;">Tim Wawancara SPMB</p>
            </td>
            <td style="width: 34%; text-align: center; vertical-align: top; font-size: 8pt;">
                <p style="margin: 0; color: #475569;">Mengetahui,<br>Kepala SMK Wikrama 1 Garut</p>
                <div style="height: 38px;"></div>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">Kunedi, S.Si., Gr.</p>
                <p style="margin: 0; font-size: 7.5pt; color: #64748b;">NIP. -</p>
            </td>
        </tr>
    </table>
@endsection
