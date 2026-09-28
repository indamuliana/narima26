@extends('pdf.layout', ['title' => 'Surat Kesepahaman - ' . $calonSiswa->nomor_pendaftaran])

@section('content')
    <div class="doc-title">SURAT PERNYATAAN & KESEPAHAMAN SPMB</div>
    <div class="doc-number">Nomor Pendaftaran: <strong>{{ $calonSiswa->nomor_pendaftaran }}</strong></div>

    <p style="font-size: 10pt; line-height: 1.5; margin-bottom: 12px;">
        Yang bertanda tangan di bawah ini:
    </p>

    <table class="info-table">
        <tr>
            <td class="label">Nama Calon Siswa</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ strtoupper($calonSiswa->nama_lengkap) }}</strong></td>
        </tr>
        <tr>
            <td class="label">NISN / NIK</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->nisn }} / {{ $calonSiswa->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Asal Sekolah</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->sekolahAsal?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jurusan Pilihan</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $calonSiswa->jurusan?->nama_jurusan ?? '-' }} ({{ $calonSiswa->program?->nama_program ?? 'Reguler' }})</strong></td>
        </tr>
        <tr>
            <td class="label">Nama Orang Tua / Wali</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->orangTua?->nama_ayah ?? $calonSiswa->orangTua?->nama_ibu ?? $calonSiswa->orangTua?->nama_wali ?? 'Orang Tua Calon Siswa' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat Domisili</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->alamat_lengkap ?? '-' }}</td>
        </tr>
    </table>

    <p style="font-size: 10pt; font-weight: bold; margin-top: 10px; margin-bottom: 6px;">
        Menyatakan dengan sesungguhnya dan penuh kesadaran bahwa:
    </p>

    <ol style="font-size: 9.5pt; line-height: 1.6; margin: 0 0 16px 20px; padding: 0; text-align: justify;">
        <li>Seluruh data, dokumen, dan keterangan yang kami serahkan dalam rangka Penerimaan Murid Baru (SPMB) adalah <strong>BENAR dan SAH</strong>. Apabila di kemudian hari ditemukan pemalsuan data, kami bersedia menerima sanksi pembatalan kelulusan.</li>
        <li>Bersedia mematuhi dan menjunjung tinggi seluruh Tata Tertib Sekolah, Kode Etik Siswa, serta Pembiasaan Karakter yang berlaku di SMK Wikrama 1 Garut dengan penuh rasa tanggung jawab.</li>
        <li>Sanggup menyelesaikan kewajiban pembiayaan pendidikan dan daftar ulang sesuai jadwal serta ketentuan yang telah ditetapkan sekolah.</li>
        <li>Bersedia menjaga nama baik diri sendiri, keluarga, dan almamater SMK Wikrama 1 Garut baik di dalam maupun di luar lingkungan sekolah.</li>
        <li>Menyetujui penggunaan data pribadi untuk keperluan integrasi Data Pokok Pendidikan (Dapodik) Kementerian Pendidikan Dasar dan Menengah RI.</li>
    </ol>

    <p style="font-size: 9.5pt; margin-bottom: 12px;">
        Demikian surat pernyataan dan kesepahaman ini dibuat tanpa ada paksaan dari pihak manapun untuk dipergunakan sebagaimana mestinya.
    </p>

    @if(!empty($eula) && $eula->setuju)
        <div style="border: 1px solid #16a34a; background-color: #f0fdf4; border-radius: 4px; padding: 8px 12px; margin-bottom: 14px; font-size: 8pt; color: #166534; line-height: 1.4;">
            <strong style="text-transform: uppercase;">Persetujuan Digital Terverifikasi (EULA SPMB):</strong><br>
            Disetujui secara elektronik pada: <strong>{{ \Carbon\Carbon::parse($eula->agreed_at)->translatedFormat('d F Y H:i:s') }} WIB</strong><br>
            Versi Dokumen: <strong>{{ $eula->versi_dokumen }}</strong> &bull; IP Address: <strong>{{ $eula->ip_address ?? '-' }}</strong> &bull; Signature Token: <code>{{ strtoupper(substr(hash('sha256', $calonSiswa->nomor_pendaftaran . $eula->agreed_at), 0, 16)) }}</code>
        </div>
    @endif

    <div class="signature-box">
        <table>
            <tr>
                <td style="width: 50%;">
                    Mengetahui / Menyetujui,<br>
                    Orang Tua / Wali Calon Siswa,
                    <div class="signature-space"></div>
                    <strong>( {{ strtoupper($calonSiswa->orangTua?->nama_ayah ?? $calonSiswa->orangTua?->nama_ibu ?? '..................................') }} )</strong>
                </td>
                <td style="width: 50%;">
                    Garut, {{ now()->translatedFormat('d F Y') }}<br>
                    Calon Peserta Didik,
                    <div class="signature-space"></div>
                    <strong>( {{ strtoupper($calonSiswa->nama_lengkap) }} )</strong>
                </td>
            </tr>
        </table>
    </div>
@endsection
