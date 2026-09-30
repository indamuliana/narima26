@extends('pdf.layout', ['title' => 'Kartu Pendaftaran - ' . $calonSiswa->nomor_pendaftaran])

@section('content')
    <div class="doc-title">KARTU TANDA PESERTA SPMB</div>
    <div class="doc-number">Nomor Pendaftaran: <strong>{{ $calonSiswa->nomor_pendaftaran }}</strong></div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
        <tr>
            <td style="width: 76%; vertical-align: top;">
                <table class="info-table" style="margin-bottom: 0;">
                    <tr>
                        <td class="label" style="padding: 2.5px 4px; font-size: 9pt;">NISN</td>
                        <td class="colon" style="padding: 2.5px 2px; font-size: 9pt;">:</td>
                        <td class="value" style="padding: 2.5px 4px; font-size: 9pt;"><strong>{{ $calonSiswa->nisn }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label" style="padding: 2.5px 4px; font-size: 9pt;">Nama Lengkap</td>
                        <td class="colon" style="padding: 2.5px 2px; font-size: 9pt;">:</td>
                        <td class="value" style="padding: 2.5px 4px; font-size: 9pt;"><strong>{{ strtoupper($calonSiswa->nama_lengkap) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label" style="padding: 2.5px 4px; font-size: 9pt;">Jenis Kelamin</td>
                        <td class="colon" style="padding: 2.5px 2px; font-size: 9pt;">:</td>
                        <td class="value" style="padding: 2.5px 4px; font-size: 9pt;">{{ $calonSiswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td class="label" style="padding: 2.5px 4px; font-size: 9pt;">Tempat, Tanggal Lahir</td>
                        <td class="colon" style="padding: 2.5px 2px; font-size: 9pt;">:</td>
                        <td class="value" style="padding: 2.5px 4px; font-size: 9pt;">{{ $calonSiswa->tempat_lahir }}, {{ $calonSiswa->tanggal_lahir?->translatedFormat('d F Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label" style="padding: 2.5px 4px; font-size: 9pt;">Asal Sekolah</td>
                        <td class="colon" style="padding: 2.5px 2px; font-size: 9pt;">:</td>
                        <td class="value" style="padding: 2.5px 4px; font-size: 9pt;">{{ $calonSiswa->sekolah_asal_text }}</td>
                    </tr>
                    <tr>
                        <td class="label" style="padding: 2.5px 4px; font-size: 9pt;">Program Pilihan</td>
                        <td class="colon" style="padding: 2.5px 2px; font-size: 9pt;">:</td>
                        <td class="value" style="padding: 2.5px 4px; font-size: 9pt;">{{ $calonSiswa->program?->nama ?? $calonSiswa->program?->nama_program ?? 'Reguler' }}</td>
                    </tr>
                    <tr>
                        <td class="label" style="padding: 2.5px 4px; font-size: 9pt;">Jurusan / Kompetensi</td>
                        <td class="colon" style="padding: 2.5px 2px; font-size: 9pt;">:</td>
                        <td class="value" style="padding: 2.5px 4px; font-size: 9pt;"><strong>{{ $calonSiswa->jurusan_pilihan_text }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label" style="padding: 2.5px 4px; font-size: 9pt;">Gelombang Pendaftaran</td>
                        <td class="colon" style="padding: 2.5px 2px; font-size: 9pt;">:</td>
                        <td class="value" style="padding: 2.5px 4px; font-size: 9pt;">{{ $calonSiswa->gelombang?->nama ?? $calonSiswa->gelombang?->nama_gelombang ?? 'Gelombang 1' }}</td>
                    </tr>
                    <tr>
                        <td class="label" style="padding: 2.5px 4px; font-size: 9pt;">Status Saat Ini</td>
                        <td class="colon" style="padding: 2.5px 2px; font-size: 9pt;">:</td>
                        <td class="value" style="padding: 2.5px 4px; font-size: 9pt;"><span class="badge badge-success">{{ $calonSiswa->status_spmb?->label() ?? 'TERDAFTAR' }}</span></td>
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
                    <img src="{{ $fotoBase64 }}" style="width: 95px; height: 125px; object-fit: cover; border: 1px solid #94a3b8; border-radius: 4px;" alt="Pas Foto">
                @else
                    <div style="width: 95px; height: 125px; border: 1px dashed #94a3b8; border-radius: 4px; display: inline-block; background-color: #f8fafc; text-align: center; padding-top: 45px; font-size: 8pt; color: #64748b;">
                        Pas Foto<br>3 x 4
                    </div>
                @endif
                <div style="font-family: monospace; font-size: 7.5pt; font-weight: bold; color: #334155; margin-top: 4px; letter-spacing: 0.5px;">
                    {{ $calonSiswa->nomor_pendaftaran }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Box Kredensial Akun Login Calon Siswa -->
    <div style="background-color: #f0fdf4; border: 1.5px solid #86efac; border-radius: 5px; padding: 7px 12px; margin-top: 6px; margin-bottom: 8px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 58%; vertical-align: top;">
                    <div style="font-size: 7.5pt; text-transform: uppercase; font-weight: bold; color: #166534; letter-spacing: 0.5px; margin-bottom: 3px;">
                        Kredensial Login Portal Calon Siswa
                    </div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 8.5pt;">
                        <tr>
                            <td style="width: 36%; color: #374151; font-weight: bold; padding: 1.5px 0;">URL Portal Login</td>
                            <td style="width: 4%; color: #374151;">:</td>
                            <td style="color: #1e40af; font-family: monospace; font-weight: bold;">{{ url('/login') }}</td>
                        </tr>
                        <tr>
                            <td style="color: #374151; font-weight: bold; padding: 1.5px 0;">Username (NISN)</td>
                            <td style="color: #374151;">:</td>
                            <td style="color: #0f172a; font-family: monospace; font-weight: bold; font-size: 9pt;">{{ $calonSiswa->user?->username ?? $calonSiswa->nisn }}</td>
                        </tr>
                        <tr>
                            <td style="color: #374151; font-weight: bold; padding: 1.5px 0;">Password Awal</td>
                            <td style="color: #374151;">:</td>
                            <td style="color: #15803d; font-family: monospace; font-weight: bold; font-size: 9pt;">{{ $calonSiswa->nomor_pendaftaran }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 42%; vertical-align: middle; border-left: 1px dashed #86efac; padding-left: 10px; font-size: 7.5pt; color: #166534; line-height: 1.3;">
                    <strong>PENTING:</strong><br>
                    Gunakan <strong>Username (NISN)</strong> dan <strong>Password Awal (Nomor Pendaftaran)</strong> di samping untuk masuk ke portal SPMB guna melengkapi berkas, melihat hasil seleksi, dan status pembayaran.
                </td>
            </tr>
        </table>
    </div>

    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 6px 10px; margin-top: 6px; font-size: 8pt;">
        <strong style="color: #0f172a; text-transform: uppercase;">Petunjuk Penting untuk Peserta:</strong>
        <ol style="margin: 3px 0 0 14px; padding: 0; line-height: 1.3;">
            <li>Kartu tanda peserta ini wajib dicetak dan dibawa saat pelaksanaan tes wawancara dan observasi.</li>
            <li>Peserta hadir mengenakan seragam rapi sekolah asal (SMP/MTs) lengkap dan bersepatu.</li>
            <li>Membawa berkas persyaratan fisik (FC Akta Kelahiran, FC Kartu Keluarga, dan Surat Keterangan Lulus/Raport).</li>
            <li>Hadir didampingi oleh orang tua / wali calon peserta didik.</li>
        </ol>
    </div>

    <div class="signature-box" style="margin-top: 10px;">
        <table>
            <tr>
                <td style="width: 50%;">
                    Calon Peserta Didik,
                    <div class="signature-space" style="height: 45px;"></div>
                    <strong>( {{ strtoupper($calonSiswa->nama_lengkap) }} )</strong>
                </td>
                <td style="width: 50%;">
                    Garut, {{ now()->translatedFormat('d F Y') }}<br>
                    Panitia Penerimaan Murid Baru,
                    <div class="signature-space" style="height: 45px;"></div>
                    <strong>( Panitia SPMB Wikrama )</strong>
                </td>
            </tr>
        </table>
    </div>
@endsection
