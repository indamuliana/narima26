@extends('pdf.layout', ['title' => 'Keputusan Kelulusan - ' . $calonSiswa->nomor_pendaftaran])

@section('content')
    <div class="doc-title">SURAT KEPUTUSAN HASIL SELEKSI SPMB</div>
    <div class="doc-number">Nomor: <strong>421.5/SPMB-{{ date('Y') }}/{{ $calonSiswa->nomor_pendaftaran }}</strong></div>

    <p style="font-size: 10pt; line-height: 1.5; margin-bottom: 12px; text-align: justify;">
        Berdasarkan hasil verifikasi berkas, tes wawancara, dan observasi kompetensi Calon Peserta Didik Baru SMK Wikrama 1 Garut Tahun Pelajaran {{ date('Y') }}/{{ date('Y') + 1 }}, Kepala Sekolah bersama Panitia SPMB menetapkan bahwa:
    </p>

    <table class="info-table">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ strtoupper($calonSiswa->nama_lengkap) }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nomor Pendaftaran</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $calonSiswa->nomor_pendaftaran }}</strong></td>
        </tr>
        <tr>
            <td class="label">NISN</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->nisn }}</td>
        </tr>
        <tr>
            <td class="label">Asal Sekolah</td>
            <td class="colon">:</td>
            <td class="value">{{ $calonSiswa->sekolahAsal?->nama_sekolah ?? $calonSiswa->asal_sekolah_lainnya ?? '-' }}</td>
        </tr>
    </table>

    <div style="text-align: center; margin: 18px 0; padding: 14px; background-color: {{ $keputusan === 'DITERIMA' ? '#f0fdf4' : '#fef2f2' }}; border: 2px solid {{ $keputusan === 'DITERIMA' ? '#22c55e' : '#ef4444' }}; border-radius: 6px;">
        <span style="font-size: 11pt; color: #475569; display: block; margin-bottom: 4px;">Dinyatakan:</span>
        <span style="font-size: 18pt; font-weight: bold; letter-spacing: 1px; color: {{ $keputusan === 'DITERIMA' ? '#15803d' : '#b91c1c' }};">
            {{ $keputusan === 'DITERIMA' ? 'DITERIMA' : 'TIDAK DITERIMA / DITOLAK' }}
        </span>
        @if($keputusan === 'DITERIMA')
            <p style="margin: 8px 0 0 0; font-size: 11pt; color: #166534;">
                Sebagai Calon Peserta Didik pada Kompetensi Keahlian:<br>
                <strong>{{ $calonSiswa->jurusan?->nama_jurusan ?? '-' }} ({{ $calonSiswa->program?->nama_program ?? 'Reguler' }})</strong>
            </p>
        @endif
    </div>

    @if($keputusan === 'DITERIMA')
        <div style="font-size: 9.5pt; line-height: 1.5; margin-bottom: 16px;">
            <strong>Ketentuan Daftar Ulang:</strong>
            <ol style="margin: 4px 0 0 16px; padding: 0;">
                <li>Calon siswa yang dinyatakan DITERIMA wajib melakukan proses <strong>Daftar Ulang</strong> melalui portal SPMB Nampi.</li>
                <li>Menyelesaikan pembayaran biaya daftar ulang atau cicilan tahap pertama sesuai rincian pada Surat Tagihan.</li>
                <li>Batas akhir proses daftar ulang adalah <strong>7 (tujuh) hari kalender</strong> sejak diterbitkannya surat keputusan ini.</li>
                <li>Apabila sampai batas waktu yang ditentukan tidak melakukan daftar ulang, maka calon peserta didik dianggap mengundurkan diri.</li>
            </ol>
        </div>
    @else
        <div style="font-size: 9.5pt; line-height: 1.5; margin-bottom: 16px;">
            <p>Terima kasih atas partisipasi dan minat Ananda dalam mengikuti rangkaian seleksi SPMB SMK Wikrama 1 Garut. Tetap semangat dalam menuntut ilmu di jenjang pendidikan selanjutnya.</p>
        </div>
    @endif

    <div class="signature-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    Garut, {{ \Carbon\Carbon::parse($tte['signedAt'] ?? now())->translatedFormat('d F Y') }}<br>
                    {{ $tte['penandatanganJabatan'] ?? 'Kepala SMK Wikrama 1 Garut' }},
                    <div style="margin: 4px 0; text-align: center;">
                        @if(!empty($tte['qrCodeBase64']))
                            <img src="{{ $tte['qrCodeBase64'] }}" alt="QR Code TTE" style="width: 68px; height: 68px; display: inline-block;">
                            <div style="font-size: 5pt; color: #475569; margin-top: 1px; line-height: 1.2;">
                                Ditandatangani secara elektronik<br>
                                <em>Scan QR untuk verifikasi keaslian</em>
                            </div>
                        @else
                            <div class="signature-space" style="height: 70px;"></div>
                        @endif
                    </div>
                    <strong style="text-decoration: underline;">( {{ $tte['penandatanganNama'] ?? 'Kunedi, S.Si., Gr.' }} )</strong><br>
                    <small>NIP / NUPTK. -</small>
                </td>
            </tr>
        </table>
    </div>
@endsection
