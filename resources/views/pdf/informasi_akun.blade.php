@extends('pdf.layout', ['title' => 'Informasi Akun Pendaftaran - ' . $calonSiswa->nomor_pendaftaran])

@section('content')
    <div class="doc-title">BUKTI REGISTRASI & INFORMASI AKUN SPMB</div>
    <div class="doc-number">Nomor Registrasi: <strong>{{ $calonSiswa->nomor_pendaftaran }}</strong></div>

    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; margin-bottom: 16px;">
        <p style="font-size: 9.5pt; color: #334155; margin: 0; line-height: 1.5;">
            Simpan lembar dokumen ini sebagai bukti resmi pendaftaran dan informasi akses login ke portal SPMB SMK Wikrama 1 Garut. Jaga kerahasiaan kata sandi Anda.
        </p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nomor Pendaftaran</td>
            <td class="colon">:</td>
            <td class="value"><strong style="font-size: 11pt; color: #ea580c;">{{ $calonSiswa->nomor_pendaftaran }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ strtoupper($calonSiswa->nama_lengkap) }}</strong></td>
        </tr>
        <tr>
            <td class="label">Username Login (NISN)</td>
            <td class="colon">:</td>
            <td class="value"><strong style="font-family: monospace; font-size: 11pt;">{{ $calonSiswa->nisn }}</strong></td>
        </tr>
        <tr>
            <td class="label">Kata Sandi Default</td>
            <td class="colon">:</td>
            <td class="value"><span style="font-family: monospace; font-weight: bold; color: #ea580c;">{{ $calonSiswa->nomor_pendaftaran }}</span><br><small style="color: #64748b;">Gunakan Nomor Pendaftaran sebagai password login pertama kali</small></td>
        </tr>
        <tr>
            <td class="label">Alamat Portal Login</td>
            <td class="colon">:</td>
            <td class="value"><span style="color: #2563eb;">{{ config('app.url') }}/login</span></td>
        </tr>
        <tr>
            <td class="label">Pilihan Kompetensi Keahlian</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $calonSiswa->jurusan?->nama ?? $calonSiswa->jurusan?->nama_jurusan ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Program & Gelombang</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->program?->nama ?? 'Reguler' }} &bull; {{ $calonSiswa->gelombang?->nama ?? 'Gelombang 1' }}</td>
        </tr>
        <tr>
            <td class="label">Sekolah Asal</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->sekolahAsal?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Waktu Pendaftaran</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->created_at ? $calonSiswa->created_at->translatedFormat('d F Y H:i') . ' WIB' : now()->translatedFormat('d F Y H:i') . ' WIB' }}</td>
        </tr>
    </table>

    <div style="margin-top: 14px; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 12px 14px; font-size: 9pt; background-color: #fffbeb;">
        <strong style="color: #92400e; text-transform: uppercase;">Langkah Berikutnya:</strong>
        <ol style="margin: 6px 0 0 16px; padding: 0; line-height: 1.6; color: #78350f;">
            <li>Masuk ke portal login calon siswa menggunakan <strong>NISN</strong> dan password Anda.</li>
            <li>Selesaikan pembayaran biaya seleksi sebesar <strong>Rp 250.000</strong> ke rekening resmi Bank BJB <code>0123-4567-8900-1</code> a.n. <strong>SMK WIKRAMA 1 GARUT</strong>.</li>
            <li>Unggah bukti transfer dan tunggu verifikasi Bendahara sekolah.</li>
            <li>Lengkapi data diri, orang tua, nilai rapor, ukuran seragam, dan unggah berkas persyaratan.</li>
            <li>Setujui lembar kesepahaman dan cetak Kartu Tanda Peserta SPMB untuk pelaksanaan wawancara.</li>
        </ol>
    </div>

    <div class="signature-box" style="margin-top: 25px;">
        <table>
            <tr>
                <td style="width: 50%;">
                    Calon Peserta Didik,
                    <div class="signature-space"></div>
                    <strong>( {{ strtoupper($calonSiswa->nama_lengkap) }} )</strong>
                </td>
                <td style="width: 50%;">
                    Garut, {{ now()->translatedFormat('d F Y') }}<br>
                    Panitia SPMB SMK Wikrama 1 Garut,
                    <div class="signature-space"></div>
                    <strong>( Panitia Penerimaan Murid Baru )</strong>
                </td>
            </tr>
        </table>
    </div>
@endsection
