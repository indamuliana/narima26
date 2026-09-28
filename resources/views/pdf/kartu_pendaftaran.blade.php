@extends('pdf.layout', ['title' => 'Kartu Pendaftaran - ' . $calonSiswa->nomor_pendaftaran])

@section('content')
    <div class="doc-title">KARTU TANDA PESERTA SPMB</div>
    <div class="doc-number">Nomor Pendaftaran: <strong>{{ $calonSiswa->nomor_pendaftaran }}</strong></div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            <td style="width: 76%; vertical-align: top;">
                <table class="info-table" style="margin-bottom: 0;">
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
                        <td class="value">{{ $calonSiswa->program?->nama ?? $calonSiswa->program?->nama_program ?? 'Reguler' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jurusan / Kompetensi</td>
                        <td class="colon">:</td>
                        <td class="value"><strong>{{ $calonSiswa->jurusan?->nama ?? $calonSiswa->jurusan?->nama_jurusan ?? '-' }} ({{ $calonSiswa->jurusan?->kode ?? '-' }})</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Gelombang Pendaftaran</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $calonSiswa->gelombang?->nama ?? $calonSiswa->gelombang?->nama_gelombang ?? 'Gelombang 1' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status Saat Ini</td>
                        <td class="colon">:</td>
                        <td class="value"><span class="badge badge-success">{{ $calonSiswa->status_spmb?->label() ?? 'TERDAFTAR' }}</span></td>
                    </tr>
                </table>
            </td>
            <td style="width: 24%; text-align: center; vertical-align: top;">
                @php
                    $fotoPath = $calonSiswa->dokumenPendaftaran?->pas_foto_path ? public_path('storage/' . $calonSiswa->dokumenPendaftaran->pas_foto_path) : null;
                    $fotoBase64 = null;
                    if ($fotoPath && file_exists($fotoPath)) {
                        $fotoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($fotoPath));
                    }
                @endphp
                @if($fotoBase64)
                    <img src="{{ $fotoBase64 }}" style="width: 105px; height: 135px; object-fit: cover; border: 1px solid #94a3b8; border-radius: 4px;" alt="Pas Foto">
                @else
                    <div style="width: 105px; height: 135px; border: 1px dashed #94a3b8; border-radius: 4px; display: inline-block; background-color: #f8fafc; text-align: center; padding-top: 50px; font-size: 8pt; color: #64748b;">
                        Pas Foto<br>3 x 4
                    </div>
                @endif
                <div style="font-family: monospace; font-size: 8pt; font-weight: bold; color: #334155; margin-top: 6px; letter-spacing: 1px;">
                    {{ $calonSiswa->nomor_pendaftaran }}
                </div>
            </td>
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
