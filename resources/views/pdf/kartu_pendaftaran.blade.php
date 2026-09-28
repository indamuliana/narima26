@extends('pdf.layout', ['title' => 'Kartu Pendaftaran - ' . $calonSiswa->nomor_pendaftaran])

@section('content')
    <div class="doc-title">KARTU TANDA PESERTA SPMB</div>
    <div class="doc-number">Nomor Pendaftaran: <strong>{{ $calonSiswa->nomor_pendaftaran }}</strong></div>

    <table class="info-table">
        <tr>
            <td class="label">NISN</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $calonSiswa->nisn }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ strtoupper($calonSiswa->nama_lengkap) }}</strong></td>
        </tr>
        <tr>
            <td class="label">Jenis Kelamin</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td class="label">Tempat, Tanggal Lahir</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->tempat_lahir }}, {{ $calonSiswa->tanggal_lahir?->translatedFormat('d F Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Asal Sekolah</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->sekolahAsal?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Program Pilihan</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->program?->nama_program ?? 'Program Standar' }}</td>
        </tr>
        <tr>
            <td class="label">Jurusan / Kompetensi</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $calonSiswa->jurusan?->nama_jurusan ?? '-' }} ({{ $calonSiswa->jurusan?->kode_jurusan ?? '-' }})</strong></td>
        </tr>
        <tr>
            <td class="label">Gelombang Pendaftaran</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->gelombang?->nama_gelombang ?? 'Gelombang 1' }}</td>
        </tr>
        <tr>
            <td class="label">Status Saat Ini</td>
            <td class="colon">:</td>
            <td class="value"><span class="badge badge-success">{{ $calonSiswa->status_spmb?->label() ?? 'TERDAFTAR' }}</span></td>
        </tr>
    </table>

    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px 12px; margin-top: 14px; font-size: 9pt;">
        <strong style="color: #0f172a; text-transform: uppercase;">Petunjuk Penting untuk Peserta:</strong>
        <ol style="margin: 6px 0 0 16px; padding: 0;">
            <li>Kartu tanda peserta ini wajib dicetak dan dibawa saat pelaksanaan tes wawancara dan observasi.</li>
            <li>Peserta hadir mengenakan seragam rapi sekolah asal (SMP/MTs) lengkap dan bersepatu.</li>
            <li>Membawa berkas persyaratan fisik (FC Akta Kelahiran, FC Kartu Keluarga, dan Surat Keterangan Lulus/Raport).</li>
            <li>Hadir didampingi oleh orang tua / wali calon peserta didik.</li>
        </ol>
    </div>

    <div class="signature-box">
        <table>
            <tr>
                <td style="width: 50%;">
                    Calon Peserta Didik,
                    <div class="signature-space"></div>
                    <strong>( {{ strtoupper($calonSiswa->nama_lengkap) }} )</strong>
                </td>
                <td style="width: 50%;">
                    Garut, {{ now()->translatedFormat('d F Y') }}<br>
                    Panitia Penerimaan Murid Baru,
                    <div class="signature-space"></div>
                    <strong>( Panitia SPMB Wikrama )</strong>
                </td>
            </tr>
        </table>
    </div>
@endsection
