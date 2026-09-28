\# Implementation Plan — Nampi

\## Sistem Penerimaan Murid Baru (SPMB) SMK Wikrama 1 Garut



> Dokumen ini menjadi acuan utama implementasi aplikasi \*\*Nampi\*\* menggunakan Laravel dan MySQL pada lingkungan \*\*XAMPP v3.3.0\*\*.

>

> \*\*Prinsip utama:\*\* bangun schema dan state workflow terlebih dahulu, lalu modul secara bertahap. Jangan mengubah struktur bisnis yang telah ditetapkan tanpa memperbarui dokumen ini dan seluruh komponen yang terdampak.



\---



\# 0. Tujuan dan Ruang Lingkup



Aplikasi Nampi digunakan untuk mengelola seluruh proses SPMB dari registrasi calon siswa sampai proses daftar ulang dan perubahan status calon siswa menjadi resmi terdaftar atau mengundurkan diri.



Ruang lingkup utama:



1\. Landing page dan informasi SPMB.

2\. Registrasi calon siswa.

3\. Pembuatan akun calon siswa.

4\. Pembayaran seleksi.

5\. Verifikasi pembayaran oleh Bendahara.

6\. Pengisian data calon siswa.

7\. Pengisian data orang tua.

8\. Pengisian data akademik dan prestasi.

9\. Pengisian ukuran seragam.

10\. Pernyataan/kesepahaman dan cetak PDF.

11\. Wawancara calon siswa dan orang tua.

12\. Keputusan diterima/ditolak.

13\. Generate tagihan daftar ulang.

14\. Pembayaran dan verifikasi daftar ulang.

15\. Monitoring dan dashboard Admin serta Kepala Sekolah.

16\. Pencatatan Audit Trail.

17\. Export data dan PDF.

18\. Pengelolaan status \*\*Mengundurkan Diri\*\*.



\---



\# 1. Ketentuan Teknologi



\## 1.1 Environment



\- OS development: Windows.

\- Web server lokal: XAMPP v3.3.0.

\- Database: MySQL bawaan XAMPP.

\- PHP: gunakan versi PHP yang kompatibel dengan versi Laravel yang dipilih.

\- Composer: versi aktif dan kompatibel dengan PHP.

\- Browser modern: Chrome/Edge/Firefox.

\- Git: digunakan untuk version control.



\## 1.2 Framework dan package



\- Laravel 12.

\- Blade.

\- Tailwind CSS.

\- Vite.

\- MySQL.

\- `spatie/laravel-activitylog` untuk Audit Trail.

\- Library PDF Laravel yang kompatibel dengan PHP dan Laravel yang digunakan.

\- Gunakan package authorization tambahan hanya bila memang diperlukan; utamakan Laravel Policy/Gate untuk kebutuhan awal.



\## 1.3 Pemeriksaan environment sebelum coding



Jalankan:



```bash

php -v

composer -V

mysql --version

node -v

npm -v

```



Pastikan Laravel, PHP, Composer, Node.js, dan package yang dipilih saling kompatibel.



\---



\# 2. Identitas dan Branding Aplikasi



\## Nama aplikasi



\*\*Nampi\*\*



\## Konteks



\*\*SPMB SMK Wikrama 1 Garut\*\*



\## Color Palette



```text

Orange  : #FF9D50

Cream   : #FFF9D8

Cyan    : #1DCED8

Green   : #55E07E

```



\## Kebutuhan UI



\- Responsive desktop, tablet, dan mobile.

\- Layout publik terpisah dari layout authenticated.

\- Sidebar/dashboard berdasarkan role.

\- Gunakan komponen UI konsisten untuk:

&#x20; - card

&#x20; - button

&#x20; - badge status

&#x20; - table

&#x20; - modal

&#x20; - alert

&#x20; - form

&#x20; - upload file

&#x20; - timeline status

\- Sediakan floating WhatsApp Helpdesk:



```text

https://wa.me/6281323314430

```



\---



\# 3. Role dan Hak Akses



\## 3.1 Role



Aplikasi memiliki 5 role utama:



```text

Admin

Bendahara

Pewawancara

Kepala Sekolah

Calon Siswa

```



\## 3.2 Ringkasan hak akses



\### Admin



\- Kelola seluruh data administratif.

\- Kelola master data.

\- Kelola gelombang.

\- Kelola program dan jurusan.

\- Melihat dan mengedit data calon siswa sesuai kewenangan.

\- Melihat semua transaksi.

\- Mengelola status administrasi.

\- Menetapkan keputusan kelulusan sesuai hak akses.

\- Menetapkan status \*\*Mengundurkan Diri\*\*.

\- Melihat Audit Trail.

\- Export data.



\### Bendahara



\- Melihat data yang diperlukan untuk verifikasi pembayaran.

\- Verifikasi pembayaran seleksi.

\- Verifikasi pembayaran daftar ulang.

\- Mengubah/mengkoreksi nominal transaksi sesuai aturan.

\- Mengelola tagihan.

\- Memberikan catatan verifikasi.

\- Melihat laporan keuangan.

\- Tidak boleh mengubah hasil wawancara atau keputusan kelulusan.



\### Pewawancara



\- Melihat data calon siswa yang diperlukan untuk wawancara.

\- Mengisi wawancara siswa.

\- Mengisi wawancara orang tua.

\- Mengisi indikator/rubrik wawancara.

\- Menyimpan catatan wawancara.

\- Menandai wawancara selesai.

\- Tidak boleh mengubah transaksi keuangan atau keputusan kelulusan.



\### Kepala Sekolah



\- Dashboard manajerial.

\- Melihat seluruh statistik SPMB.

\- Melihat data calon siswa.

\- Melihat status pembayaran.

\- Melihat hasil wawancara.

\- Melihat keputusan.

\- Menetapkan status \*\*Mengundurkan Diri\*\*.

\- Export laporan.

\- Tidak mengubah histori transaksi keuangan.

\- Tidak melakukan edit teknis master kecuali izin khusus diberikan.



\### Calon Siswa



\- Login menggunakan akun hasil registrasi.

\- Melihat status proses SPMB miliknya.

\- Upload bukti pembayaran.

\- Melengkapi biodata.

\- Melengkapi data orang tua.

\- Melengkapi data akademik/prestasi.

\- Mengisi ukuran seragam.

\- Menyetujui kesepahaman.

\- Melihat tagihan yang menjadi kewajibannya.

\- Upload bukti pembayaran daftar ulang.

\- Mencetak dokumen yang diizinkan.



\---



\# 4. Aturan Akun Calon Siswa



> \*\*Jangan mengubah desain akun calon siswa yang telah ditetapkan.\*\*



Saat registrasi berhasil:



1\. Sistem membuat Nomor Pendaftaran otomatis.

2\. Sistem membuat akun calon siswa.

3\. Username menggunakan \*\*NISN\*\*.

4\. Password awal menggunakan \*\*Nomor HP\*\*.

5\. Informasi akun ditampilkan setelah registrasi berhasil.

6\. Informasi akun dapat diunduh dalam bentuk PDF.

7\. Sistem mengarahkan calon siswa ke halaman login.



Catatan implementasi keamanan:



\- Password tetap disimpan menggunakan hashing Laravel, bukan plaintext.

\- Nilai password tidak boleh ditampilkan di halaman lain setelah proses registrasi selesai.



\---



\# 5. State/Status Workflow SPMB



Gunakan status terkontrol di database, bukan hanya kondisi tombol pada UI.



\## 5.1 Status utama



```text

REGISTRASI

MENUNGGU\_PEMBAYARAN\_SELEKSI

PEMBAYARAN\_SELEKSI\_DIVERIFIKASI

MELENGKAPI\_DATA

DATA\_LENGKAP

MENUNGGU\_WAWANCARA

SUDAH\_DIWAWANCARA

MENUNGGU\_KEPUTUSAN

DITERIMA

DITOLAK

MENUNGGU\_DAFTAR\_ULANG

DAFTAR\_ULANG\_DIVERIFIKASI

RESMI\_TERDAFTAR

MENGUNDURKAN\_DIRI

```



\## 5.2 Alur normal



```text

REGISTRASI

&#x20;   ↓

MENUNGGU\_PEMBAYARAN\_SELEKSI

&#x20;   ↓

PEMBAYARAN\_SELEKSI\_DIVERIFIKASI

&#x20;   ↓

MELENGKAPI\_DATA

&#x20;   ↓

DATA\_LENGKAP

&#x20;   ↓

MENUNGGU\_WAWANCARA

&#x20;   ↓

SUDAH\_DIWAWANCARA

&#x20;   ↓

MENUNGGU\_KEPUTUSAN

&#x20;   ↓

DITERIMA / DITOLAK

&#x20;   ↓

\[JIKA DITERIMA]

MENUNGGU\_DAFTAR\_ULANG

&#x20;   ↓

DAFTAR\_ULANG\_DIVERIFIKASI

&#x20;   ↓

RESMI\_TERDAFTAR

```



\## 5.3 Status Mengundurkan Diri



Status `MENGUNDURKAN\_DIRI` dapat ditetapkan oleh:



\- Admin.

\- Kepala Sekolah.



Saat status ditetapkan wajib menyimpan:



```text

status\_sebelumnya

status\_baru

alasan\_pengunduran\_diri

catatan

changed\_by

changed\_at

```



Aturan:



\- Tidak boleh menghapus data calon siswa.

\- Data pembayaran tetap tersimpan.

\- Data wawancara tetap tersimpan.

\- Audit Trail wajib tercatat.

\- UI harus menampilkan status dan alasan secara jelas.

\- Perubahan kembali dari `MENGUNDURKAN\_DIRI` ke status aktif harus dibatasi dan dicatat secara khusus.



\---



\# 6. Struktur Database



\## 6.1 Tabel User



\### `users`



Field utama:



```text

id

name

email

password

role

is\_active

email\_verified\_at

remember\_token

created\_at

updated\_at

```



Gunakan unique index pada `email`.



\---



\# 7. Master Data



Gunakan prefix `master\_` untuk seluruh master.



\## 7.1 `master\_program`



Contoh data dummy:



```text

1 | Reguler

2 | Unggulan

```



Field:



```text

id

kode

nama

keterangan

aktif

created\_at

updated\_at

deleted\_at

```



\## 7.2 `master\_jurusan`



Data awal:



```text

TJKT

PPLG

PEMASARAN

PERHOTELAN

```



Field:



```text

id

kode

nama

keterangan

aktif

created\_at

updated\_at

deleted\_at

```



\## 7.3 `master\_gelombang`



Contoh data dummy:



```text

Gelombang 1

Gelombang 2

Gelombang 3

```



Field:



```text

id

kode

nama

periode\_mulai

periode\_selesai

aktif

created\_at

updated\_at

deleted\_at

```



\## 7.4 `master\_pekerjaan`



Contoh data dummy:



```text

PNS

TNI/Polri

Karyawan Swasta

Wiraswasta

Petani

Buruh

Guru

Pedagang

Tidak Bekerja

Lainnya

```



\## 7.5 `master\_seragam`



Field:



```text

id

kode

nama\_jenis

ukuran

keterangan

aktif

created\_at

updated\_at

deleted\_at

```



\## 7.6 `master\_sekolah\_asal`



Field:



```text

id

npsn

nama\_sekolah

alamat

kecamatan

kabupaten\_kota

aktif

created\_at

updated\_at

deleted\_at

```



Form registrasi harus menyediakan pilihan sekolah dari master serta opsi:



```text

Lainnya

```



Jika memilih `Lainnya`, tampilkan input nama sekolah asal manual.



\## 7.7 `master\_biaya`



Master biaya menjadi sumber pembentukan tagihan.



Field:



```text

id

kode\_biaya

nama\_biaya

kategori

program\_id

gelombang\_id

nominal

tipe\_nominal

wajib

aktif

keterangan

created\_at

updated\_at

deleted\_at

```



Contoh dummy biaya:



```text

Seleksi                  Rp 250.000

DSP                      Rp 3.000.000

SPP Bulanan              Rp 450.000

Asrama                   Rp 2.000.000

Seragam                  Rp 1.000.000

Kegiatan/Komponen Lain   Rp 500.000

```



> Semua angka di atas adalah \*\*dummy\*\* dan harus dapat diedit Admin sebelum digunakan secara nyata.



\## 7.8 `master\_kriteria\_wawancara`



Field:



```text

id

kode

nama\_kriteria

jenis\_penilaian

urutan

aktif

keterangan

created\_at

updated\_at

```



Contoh:



```text

Kerapihan

Sikap

Komunikasi

Kedisiplinan

Motivasi

Kesiapan mengikuti program

Pemahaman pilihan program

```



\---



\# 8. Data Calon Siswa



\## 8.1 `calon\_siswa`



Field yang disarankan:



```text

id

nomor\_pendaftaran

user\_id

nisn

jenis\_kelamin

nama\_lengkap

nama\_panggilan

tempat\_lahir

tanggal\_lahir

nik

no\_kk

agama

alamat\_lengkap

rt

rw

kode\_pos

provinsi\_id

kabupaten\_id

kecamatan\_id

desa\_id

no\_hp\_siswa

no\_hp\_ayah

no\_hp\_ibu

email

asal\_sekolah\_id

asal\_sekolah\_lainnya

program\_id

jurusan\_id

gelombang\_id

status\_spmb

status\_data

catatan\_admin

created\_at

updated\_at

deleted\_at

```



Perbaiki nama field typo pada implementasi menjadi:



```text

gelombang\_id

```



Unique index:



```text

nomor\_pendaftaran

nisn

```



\## 8.2 `riwayat\_status\_spmb`



Gunakan tabel khusus untuk menyimpan seluruh perubahan status SPMB. Jangan hanya mengandalkan nilai `status\_spmb` terakhir pada `calon\_siswa`.



Field:



```text

id

calon\_siswa\_id

status\_sebelumnya

status\_baru

alasan

catatan

changed\_by

changed\_at

created\_at

```



Aturan:



\- Setiap perubahan status menghasilkan satu record.

\- `status\_spmb` pada `calon\_siswa` menyimpan status terkini.

\- `riwayat\_status\_spmb` menyimpan histori perubahan.

\- Perubahan status melalui service transaction agar status terkini dan histori selalu konsisten.



Relasi:



```text

calon\_siswa → user

calon\_siswa → master\_program

calon\_siswa → master\_jurusan

calon\_siswa → master\_gelombang

calon\_siswa → master\_sekolah\_asal

```



\---



\# 9. Data Orang Tua



\## `data\_orangtua`



Satu calon siswa memiliki satu record orang tua/wali.



Field:



```text

id

calon\_siswa\_id

nama\_ayah

nik\_ayah

tahun\_lahir\_ayah

pekerjaan\_ayah\_id

penghasilan\_ayah

pendidikan\_ayah

no\_hp\_ayah

alamat\_ayah

nama\_ibu

nik\_ibu

tahun\_lahir\_ibu

pekerjaan\_ibu\_id

penghasilan\_ibu

pendidikan\_ibu

no\_hp\_ibu

alamat\_ibu

nama\_wali

hubungan\_wali

pekerjaan\_wali\_id

penghasilan\_wali

no\_hp\_wali

alamat\_wali

created\_at

updated\_at

```



\---



\# 10. Data Akademik dan Prestasi



\## `data\_akademik`



Field:



```text

id

calon\_siswa\_id

nama\_sekolah

npsn

nisn

nilai\_rata\_rata

nilai\_bahasa\_indonesia

nilai\_matematika

nilai\_bahasa\_inggris

nilai\_ipa

nilai\_lainnya

catatan

created\_at

updated\_at

```



\## `prestasi`



Field:



```text

id

calon\_siswa\_id

jenis\_prestasi

tingkat

nama\_prestasi

tahun

peringkat

keterangan

created\_at

updated\_at

```



\---



\# 11. Ukuran Seragam



\## `ukuran\_seragam`



Field:



```text

id

calon\_siswa\_id

jenis\_seragam\_id

ukuran

jumlah

keterangan

created\_at

updated\_at

```



Belum menimbulkan tagihan pada tahap ini kecuali sudah diatur oleh modul biaya daftar ulang.



\---



\# 12. Dokumen Persyaratan — TIDAK GENERIK



> Struktur dokumen \*\*tetap spesifik\*\*, bukan tabel jenis dokumen dinamis.



Gunakan field khusus pada data pendaftaran untuk dokumen yang memang ditetapkan sekolah.



Contoh field:



```text

file\_kartu\_keluarga

file\_akta\_kelahiran

file\_ijazah\_atau\_skl

file\_pas\_foto

file\_dokumen\_pendukung

```



Tambahkan field hanya untuk dokumen yang benar-benar diwajibkan oleh SPMB.



Setiap file wajib mempunyai:



```text

path

original\_name

mime\_type

size

uploaded\_at

```



Jika implementasi menggunakan tabel terpisah untuk menjaga kerapihan database, tabel tetap boleh dibuat khusus untuk dokumen yang fixed, misalnya:



```text

dokumen\_pendaftaran

```



dengan kolom fixed:



```text

calon\_siswa\_id

kk\_path

akta\_path

ijazah\_skl\_path

pas\_foto\_path

dokumen\_pendukung\_path

```



Jangan membuat `master\_jenis\_dokumen` atau mekanisme document type dinamis pada versi awal aplikasi.



\---



\# 13. Generate Nomor Pendaftaran



Jangan menggunakan Mutator.



Gunakan:



```text

RegistrationNumberService

```



Format:



```text

26AAYXXXX

```



Contoh dummy:



```text

26AAY0001

26AAY0002

26AAY0003

```



Aturan:



1\. Nomor dibuat saat registrasi berhasil.

2\. Nomor harus unik.

3\. Generate dilakukan melalui service.

4\. Gunakan database transaction.

5\. Gunakan unique index.

6\. Hindari duplikasi ketika dua proses registrasi berjalan bersamaan.



\---



\# 14. Normalisasi Nomor Telepon



Gunakan service/helper, bukan Mutator sebagai mekanisme generate nomor pendaftaran.



Contoh normalisasi:



```text

081233445566

→ 6281233445566

```



```text

+6281233445566

→ 6281233445566

```



Validasi panjang dan karakter harus dilakukan sebelum data disimpan.



\---



\# 15. Cascading Wilayah



Gunakan data wilayah lokal.



Struktur:



```text

Provinsi

&#x20; ↓

Kabupaten/Kota

&#x20; ↓

Kecamatan

&#x20; ↓

Desa/Kelurahan

```



Tabel:



```text

master\_provinsi

master\_kabupaten

master\_kecamatan

master\_desa

```



Relasi:



```text

master\_kabupaten.provinsi\_id

master\_kecamatan.kabupaten\_id

master\_desa.kecamatan\_id

```



Frontend mengambil data melalui endpoint internal Laravel.



Jangan bergantung pada API eksternal ketika calon siswa sedang mengisi formulir.



\---



\# 16. Modul Registrasi



\## Form registrasi awal



Field:



```text

NISN

Jenis Kelamin

Nama Lengkap sesuai KK

Asal Sekolah

Email Aktif

HP Calon Siswa

HP Ayah

HP Ibu

Program Pendaftaran

```



Alur:



```text

Isi Form

&#x20;  ↓

Validasi

&#x20;  ↓

Validasi NISN unik

&#x20;  ↓

Generate Nomor Pendaftaran

&#x20;  ↓

Generate User

&#x20;  ↓

Commit Transaction

&#x20;  ↓

Halaman Sukses

```



Halaman sukses menampilkan:



```text

Nomor Pendaftaran

Username

Password awal

```



Sediakan tombol:



```text

Download Informasi Akun PDF

Lanjut ke Login

```



\---



\# 17. Modul Pembayaran Seleksi



Nominal awal:



```text

Rp 250.000

```



Nominal dapat dikendalikan Admin melalui konfigurasi/master biaya.



\## 17.1 Tabel `pembayaran\_seleksi`



Field:



```text

id

calon\_siswa\_id

nominal\_tagihan

nominal\_dibayar

tanggal\_bayar

metode\_bayar

bank\_pengirim

nama\_pengirim

nomor\_referensi

bukti\_transfer\_path

status

catatan\_bendahara

verified\_by

verified\_at

created\_at

updated\_at

```



Status:



```text

PENDING

DIVERIFIKASI

DITOLAK

```



\## 17.2 Aturan



\- Calon siswa hanya dapat mengunggah bukti untuk dirinya sendiri.

\- File wajib divalidasi.

\- Bendahara memeriksa nominal, bukti, tanggal, dan data pendukung.

\- Jika valid → `DIVERIFIKASI`.

\- Jika tidak valid → `DITOLAK` dan wajib memberi alasan.

\- Verifikasi mengubah state SPMB menjadi `PEMBAYARAN\_SELEKSI\_DIVERIFIKASI`.

\- Seluruh aktivitas tercatat di Audit Trail.



\---



\# 18. Modul Lengkapi Data



Hanya dapat diakses setelah pembayaran seleksi terverifikasi.



Tahapan form:



```text

Biodata

&#x20;  ↓

Data Orang Tua

&#x20;  ↓

Akademik

&#x20;  ↓

Prestasi

&#x20;  ↓

Dokumen

&#x20;  ↓

Kesepahaman

&#x20;  ↓

Ukuran Seragam

```



Tampilkan progress completion, misalnya:



```text

Biodata       ✓

Orang Tua     ✓

Akademik      ✓

Prestasi      ✓

Dokumen       80%

Kesepahaman   -

Seragam       -

```



Status berubah menjadi `DATA\_LENGKAP` hanya jika seluruh komponen wajib selesai.



\---



\# 19. Kesepahaman / EULA



Simpan:



```text

calon\_siswa\_id

versi\_dokumen

isi\_dokumen\_atau\_referensi\_dokumen

setuju

agreed\_at

agreed\_by

ip\_address

user\_agent

```



Aturan:



\- Checkbox tidak boleh default aktif.

\- Tombol lanjut aktif hanya setelah persetujuan.

\- Setelah disetujui, generate PDF kesepahaman.

\- Simpan versi dokumen yang disetujui.



\---



\# 20. PDF Service



PDF harus dirancang sejak awal dan terpusat.



Buat:



```text

PdfService

```



Template PDF minimum:



1\. Informasi Akun Calon Siswa.

2\. Kartu Pendaftaran.

3\. Kesepahaman/EULA.

4\. Tagihan Daftar Ulang.

5\. Bukti/rekap pembayaran.

6\. Keputusan Kelulusan.



Semua PDF harus mempunyai:



\- Identitas SMK Wikrama 1 Garut.

\- Nama dokumen.

\- Nomor dokumen/nomor pendaftaran bila relevan.

\- Tanggal.

\- Data calon siswa.

\- Footer.



Jangan membuat kode generator PDF yang sama berulang-ulang di banyak controller.



\---



\# 21. Modul Wawancara



\## 21.1 Tabel `wawancara`



Field:



```text

id

calon\_siswa\_id

pewawancara\_id

tanggal\_wawancara

status

catatan\_umum

catatan\_orang\_tua

created\_at

updated\_at

```



\## 21.2 Tabel `wawancara\_detail`



Field:



```text

id

wawancara\_id

kriteria\_id

indikator

nilai

warna

catatan

created\_at

updated\_at

```



Indikator warna:



```text

HIJAU

ORANYE

MERAH

```



Warna hanya representasi UI; data sebenarnya tetap disimpan secara eksplisit.



\## 21.3 Panel wawancara



Sisi kiri:



\- Foto jika tersedia.

\- Nomor pendaftaran.

\- Nama siswa.

\- Program.

\- Jurusan.

\- Asal sekolah.

\- Status data.



Sisi kanan:



\- Form indikator.

\- Catatan.

\- Hasil wawancara orang tua.

\- Tombol simpan draft.

\- Tombol selesai wawancara.



\---



\# 22. Modul Tagihan Daftar Ulang



Gunakan \*\*snapshot tagihan\*\*.



Jangan mengandalkan nilai master biaya secara langsung setelah tagihan dibuat.



\## 22.1 Tabel `tagihan`



Field:



```text

id

calon\_siswa\_id

nomor\_tagihan

golongan/program pada saat tagihan dibuat

gelombang pada saat tagihan dibuat

total\_bruto

total\_diskon

total\_netto

status

created\_at

updated\_at

```



Gunakan nama field database yang konsisten, misalnya:



```text

program\_snapshot

&#x20;gelombang\_snapshot

```



\## 22.2 Tabel `tagihan\_detail`



Field:



```text

id

tagihan\_id

kode\_biaya\_snapshot

nama\_biaya\_snapshot

kategori\_snapshot

nominal\_snapshot

jumlah

subtotal

created\_at

updated\_at

```



\## 22.3 Dummy tagihan



Contoh dummy:



```text

DSP                 Rp 3.000.000

SPP                 Rp   450.000

Asrama              Rp 2.000.000

Seragam             Rp 1.000.000

Komponen lain       Rp   500.000

\--------------------------------

Subtotal            Rp 6.950.000

Diskon              Rp   500.000

\--------------------------------

Total               Rp 6.450.000

```



> Seluruh nominal adalah \*\*DUMMY\*\* dan harus mudah diubah melalui master biaya.



\## 22.4 Aturan snapshot



Setelah tagihan dibuat:



\- Perubahan master biaya tidak boleh mengubah tagihan lama.

\- Nama biaya yang tercantum pada tagihan harus tetap sama dengan saat tagihan dibuat.

\- Nominal snapshot harus tetap tersimpan.

\- Perubahan tagihan manual oleh Bendahara harus tercatat di Audit Trail.



\---



\# 23. Modul Pembayaran Daftar Ulang



\## `pembayaran\_daftar\_ulang`



Field:



```text

id

calon\_siswa\_id

tagihan\_id

nominal\_tagihan

nominal\_dibayar

tanggal\_bayar

metode\_bayar

bank\_pengirim

nama\_pengirim

nomor\_referensi

bukti\_transfer\_path

status

catatan\_bendahara

verified\_by

verified\_at

created\_at

updated\_at

```



Status:



```text

PENDING

DIVERIFIKASI

DITOLAK

```



\## Aturan



\- Pembayaran harus terkait ke tagihan.

\- Sistem menghitung kekurangan pembayaran.

\- Bendahara dapat mengoreksi nominal sesuai kewenangan.

\- Jika pembayaran telah memenuhi tagihan → dapat diverifikasi.

\- Setelah verifikasi berhasil:



```text

MENUNGGU\_DAFTAR\_ULANG

&#x20;       ↓

DAFTAR\_ULANG\_DIVERIFIKASI

&#x20;       ↓

RESMI\_TERDAFTAR

```



\---



\# 24. Aturan Diskon



Diskon bukan sekadar satu kolom angka.



Gunakan struktur yang dapat ditelusuri.



Field minimal:



```text

jenis\_diskon

metode\_diskon

nilai\_diskon

alasan

keterangan

diberikan\_oleh

disetujui\_oleh

diberikan\_at

```



Contoh:



```text

Diskon Prestasi

Persentase

20%

```



atau:



```text

Diskon Saudara Kandung

Nominal

Rp 500.000

```



Aturan:



\- Diskon tidak boleh membuat total netto negatif.

\- Perubahan diskon wajib masuk Audit Trail.

\- Hanya role yang berwenang yang dapat menetapkan diskon.

\- Semua perubahan menghasilkan histori.



\---



\# 25. Modul Keputusan Kelulusan



\## `keputusan\_kelulusan`



Field:



```text

id

calon\_siswa\_id

keputusan

alasan/catatan

diskon\_id jika digunakan

ditetapkan\_oleh

ditetapkan\_at

versi\_keputusan

created\_at

updated\_at

```



Keputusan:



```text

DITERIMA

DITOLAK

```



Jika keputusan `DITERIMA`:



```text

DITERIMA

&#x20;  ↓

MENUNGGU\_DAFTAR\_ULANG

```



Jika `DITOLAK`:



```text

DITOLAK

```



\---



\# 26. Modul Mengundurkan Diri



\## Akses



Hanya:



```text

Admin

Kepala Sekolah

```



\## Tombol



Pada detail calon siswa:



```text

\[ Tandai Mengundurkan Diri ]

```



Gunakan modal konfirmasi berisi:



```text

Status saat ini

Alasan pengunduran diri

Catatan

Konfirmasi

```



Sistem menyimpan:



```text

status\_sebelumnya

status\_baru = MENGUNDURKAN\_DIRI

alasan

catatan

user\_id

waktu

```



Audit Trail wajib.



\---



\# 27. Dashboard Bendahara



Widget minimum:



\- Total tagihan.

\- Total nominal tagihan.

\- Total pembayaran masuk.

\- Pembayaran pending.

\- Pembayaran diverifikasi.

\- Pembayaran ditolak.

\- Total kekurangan pembayaran.

\- Rekap berdasarkan gelombang.

\- Rekap berdasarkan program.



Tabel utama:



```text

Nomor Pendaftaran

Nama

Program

Gelombang

Nomor Tagihan

Total Tagihan

Total Bayar

Kekurangan

Status

Aksi

```



\---



\# 28. Dashboard Admin



Statistic Cards:



\- Total pendaftar.

\- Menunggu pembayaran.

\- Pembayaran terverifikasi.

\- Data lengkap.

\- Menunggu wawancara.

\- Sudah diwawancara.

\- Diterima.

\- Ditolak.

\- Menunggu daftar ulang.

\- Resmi terdaftar.

\- Mengundurkan diri.



DataTables harus menyediakan:



\- Search.

\- Filter status.

\- Filter program.

\- Filter jurusan.

\- Filter gelombang.

\- Filter asal sekolah.

\- Sort.

\- Pagination.

\- Export.



\---



\# 29. Dashboard Kepala Sekolah



Dashboard bersifat manajerial dan read-mostly.



Tampilkan:



```text

Total Pendaftar

Reguler

Unggulan

Per Jurusan

Per Gelombang

Pembayaran

Wawancara

Diterima

Ditolak

Menunggu Daftar Ulang

Resmi Terdaftar

Mengundurkan Diri

```



Tambahkan visualisasi:



\- Grafik pendaftar per gelombang.

\- Grafik pendaftar per jurusan.

\- Grafik pendaftar per program.

\- Grafik status SPMB.

\- Rekap pembayaran.



Jangan tampilkan data sensitif yang tidak diperlukan dalam dashboard ringkasan.



\---



\# 30. Audit Trail



Audit Trail dipasang sejak awal pengembangan, bukan pada fase terakhir saja.



Gunakan `spatie/laravel-activitylog`.



Aktivitas wajib dicatat:



```text

Login

Logout

Registrasi siswa

Perubahan biodata

Perubahan data orang tua

Perubahan data akademik

Upload bukti pembayaran

Verifikasi pembayaran

Penolakan pembayaran

Generate tagihan

Perubahan tagihan

Pemberian diskon

Perubahan diskon

Pengisian wawancara

Penyelesaian wawancara

Perubahan keputusan

Perubahan status SPMB

Menandai Mengundurkan Diri

Export data

Perubahan master

Soft delete master

Restore master

```



Audit minimal menyimpan:



```text

causer

subject

activity

old\_properties

new\_properties

ip\_address bila tersedia

user\_agent bila tersedia

created\_at

```



\---



\# 31. SoftDeletes



Jangan menerapkan `SoftDeletes` secara otomatis ke semua tabel.



\## Gunakan SoftDeletes pada master/non-transaksional



Contoh:



```text

master\_program

master\_jurusan

master\_gelombang

master\_pekerjaan

master\_seragam

master\_sekolah\_asal

master\_biaya

master\_kriteria\_wawancara

```



\## Jangan menyediakan delete permanen dari UI untuk transaksi



Contoh:



```text

pembayaran\_seleksi

pembayaran\_daftar\_ulang

tagihan

tagihan\_detail

keputusan\_kelulusan

wawancara

```



Jika terjadi pembatalan/revisi, gunakan status, histori, atau mekanisme void/revisi yang tercatat.



\---



\# 32. Validasi dan Keamanan



\## Validasi



Gunakan Laravel Form Request.



Validasi minimal:



\- NISN.

\- Email.

\- Nomor HP.

\- Tanggal lahir.

\- File upload.

\- Nominal pembayaran.

\- Status transisi.



\## Upload



Batasi:



\- MIME/type.

\- Extension.

\- Ukuran file.

\- Nama file aman.

\- Lokasi penyimpanan.



Jangan menggunakan nama file asli sebagai filename storage jika berpotensi menimbulkan konflik atau karakter berbahaya.



\## Authorization



Semua route sensitif wajib memakai middleware/policy.



Jangan mengandalkan:



```text

if tombol terlihat

```



Authorization harus dilakukan di server.



\---



\# 33. Service Layer



Jangan menaruh seluruh business logic di Controller.



Service minimum:



```text

RegistrationNumberService

RegistrationService

PhoneNumberService

PaymentVerificationService

InvoiceService

InvoiceSnapshotService

DiscountService

WawancaraService

DecisionService

WithdrawalService

PdfService

FileUploadService

SpmbStatusService

DashboardService

```



Controller bertugas menerima request, memanggil service, lalu mengembalikan response/view.



\---



\# 34. Database Transaction



Gunakan transaction pada proses kritis:



\### Registrasi



```text

validasi

→ create user

→ generate nomor pendaftaran

→ create calon siswa

→ set status

→ commit

```



\### Verifikasi pembayaran



```text

validasi

→ update payment

→ update status

→ write audit

→ commit

```



\### Generate tagihan



```text

ambil master biaya

→ buat tagihan

→ snapshot item

→ hitung diskon

→ hitung total

→ commit

```



\### Menandai Mengundurkan Diri



```text

validasi role

→ validasi status

→ simpan histori

→ update status

→ audit

→ commit

```



\---



\# 35. Routing



Pisahkan route berdasarkan area:



```text

/

/auth/\*

/calon-siswa/\*

/admin/\*

/bendahara/\*

/pewawancara/\*

/kepala-sekolah/\*

/api/internal/\*

```



Endpoint internal cascading wilayah:



```text

/api/internal/wilayah/provinsi

/api/internal/wilayah/kabupaten/{id}

/api/internal/wilayah/kecamatan/{id}

/api/internal/wilayah/desa/{id}

```



\---



\# 36. Struktur Menu



\## Publik



```text

Beranda

Tentang Sekolah

Program

Jurusan

Gelombang

Biaya

Persyaratan

Alur SPMB

FAQ

Kontak

Login

Registrasi

```



\## Calon Siswa



```text

Dashboard

Status SPMB

Profil

Orang Tua

Akademik

Prestasi

Dokumen

Kesepahaman

Seragam

Pembayaran

Tagihan Daftar Ulang

Dokumen/PDF

Akun

Logout

```



\## Admin



```text

Dashboard

Calon Siswa

Master Data

Gelombang

Program

Jurusan

Biaya

Sekolah Asal

Pekerjaan

Seragam

Kriteria Wawancara

Laporan

Audit Trail

Pengaturan

Logout

```



\## Bendahara



```text

Dashboard

Pembayaran Seleksi

Tagihan

Pembayaran Daftar Ulang

Diskon

Laporan Keuangan

Logout

```



\## Pewawancara



```text

Dashboard

Antrian Wawancara

Detail Calon Siswa

Instrumen Wawancara

Riwayat Wawancara

Logout

```



\## Kepala Sekolah



```text

Dashboard

Data Pendaftar

Monitoring Pembayaran

Monitoring Wawancara

Keputusan

Mengundurkan Diri

Laporan

Logout

```



\---



\# 37. Export dan Laporan



Export minimum:



```text

Daftar seluruh calon siswa

Daftar pendaftar per gelombang

Daftar pendaftar per program

Daftar pendaftar per jurusan

Daftar pembayaran seleksi

Daftar tagihan daftar ulang

Daftar pembayaran daftar ulang

Daftar diterima

Daftar ditolak

Daftar mengundurkan diri

Daftar resmi terdaftar

```



Format:



```text

XLSX/CSV

PDF

```



Export harus menghormati hak akses role.



\---



\# 38. Testing



Testing harus dilakukan setiap selesai satu fase, bukan hanya di akhir proyek.



\## Unit Test



Uji:



\- RegistrationNumberService.

\- PhoneNumberService.

\- DiscountService.

\- InvoiceCalculation.

\- Snapshot tagihan.

\- Status transition.



\## Feature Test



Uji:



\- Registrasi.

\- Login calon siswa.

\- Upload pembayaran.

\- Verifikasi bendahara.

\- Lengkapi data.

\- Wawancara.

\- Keputusan.

\- Generate daftar ulang.

\- Verifikasi daftar ulang.

\- Status resmi terdaftar.

\- Status mengundurkan diri.



\## Authorization Test



Pastikan:



\- Calon siswa tidak dapat membuka URL admin.

\- Bendahara tidak dapat mengubah keputusan.

\- Pewawancara tidak dapat memverifikasi pembayaran.

\- Kepala Sekolah tidak dapat menghapus transaksi.

\- Hanya Admin/Kepala Sekolah yang dapat menetapkan Mengundurkan Diri.



\## Validation Test



Uji:



\- NISN duplikat.

\- Email duplikat.

\- Nomor HP invalid.

\- File terlalu besar.

\- File dengan MIME tidak diizinkan.

\- Nilai pembayaran invalid.

\- Diskon lebih besar dari tagihan.



\## Concurrency Test



Uji registrasi simultan untuk memastikan nomor pendaftaran tidak duplikat.



\---



\# 39. Backup dan Recovery



Siapkan:



\- Backup database.

\- Backup file upload.

\- Backup `.env` secara aman sesuai kebijakan server.

\- Prosedur restore database.

\- Prosedur restore file.

\- Uji restore secara berkala.



Jangan menyimpan backup produksi di lokasi yang sama tanpa perlindungan tambahan.



\---



\# 40. Deployment



Checklist production:



```text

APP\_ENV=production

APP\_DEBUG=false

APP\_URL=https://...

```



Pastikan:



\- HTTPS aktif.

\- Storage terkonfigurasi.

\- Permission folder benar.

\- Database production siap.

\- Migration production berhasil.

\- Cache route/config/view dijalankan setelah konfigurasi stabil.

\- Log diperiksa setelah deployment.

\- Backup sebelum migration besar.



\---



\# 41. Urutan Implementasi yang Wajib Diikuti



Jangan meminta AI membangun semua modul sekaligus.



Urutan:



```text

FASE 1  Setup Project

&#x20;  ↓

FASE 2  Authentication + Role

&#x20;  ↓

FASE 3  Migration + Master Data

&#x20;  ↓

FASE 4  Enum + State Workflow

&#x20;  ↓

FASE 5  Service + Helper

&#x20;  ↓

FASE 6  Registrasi + Akun

&#x20;  ↓

FASE 7  Pembayaran Seleksi

&#x20;  ↓

FASE 8  Lengkapi Data

&#x20;  ↓

FASE 9  Kesepahaman + PDF

&#x20;  ↓

FASE 10 Wawancara

&#x20;  ↓

FASE 11 Tagihan + Pembayaran Daftar Ulang

&#x20;  ↓

FASE 12 Keputusan + Mengundurkan Diri

&#x20;  ↓

FASE 13 Dashboard + Laporan

&#x20;  ↓

FASE 14 Audit + Testing + Backup + Deployment

```



\---



\# 42. Checklist Per Fase



\## FASE 1 — Setup



\- \[ ] Laravel terinstall.

\- \[ ] `.env` terkoneksi MySQL.

\- \[ ] Tailwind + Vite aktif.

\- \[ ] Layout dasar selesai.

\- \[ ] WhatsApp Helpdesk aktif.



\## FASE 2 — Authentication



\- \[ ] Login.

\- \[ ] Logout.

\- \[ ] Middleware role.

\- \[ ] Policy.

\- \[ ] Dashboard masing-masing role.



\## FASE 3 — Database



\- \[ ] Seluruh migration.

\- \[ ] Foreign key.

\- \[ ] Index.

\- \[ ] Seeder dummy.

\- \[ ] Factory dasar.



\## FASE 4 — Workflow



\- \[ ] Enum status.

\- \[ ] Transition rules.

\- \[ ] Status history.

\- \[ ] Audit status change.

\- \[ ] Withdrawal workflow.



\## FASE 5 — Service



\- \[ ] Registration Number Service.

\- \[ ] Phone Number Service.

\- \[ ] Invoice Service.

\- \[ ] Discount Service.

\- \[ ] PDF Service.

\- \[ ] Status Service.



\## FASE 6 — Registrasi



\- \[ ] Form registrasi.

\- \[ ] Validasi NISN.

\- \[ ] Generate nomor.

\- \[ ] Generate user.

\- \[ ] PDF akun.

\- \[ ] Login.



\## FASE 7 — Pembayaran Seleksi



\- \[ ] Upload bukti.

\- \[ ] Verifikasi.

\- \[ ] Tolak + catatan.

\- \[ ] Audit.

\- \[ ] Status transition.



\## FASE 8 — Lengkapi Data



\- \[ ] Biodata.

\- \[ ] Wilayah.

\- \[ ] Orang tua.

\- \[ ] Akademik.

\- \[ ] Prestasi.

\- \[ ] Dokumen spesifik.

\- \[ ] Seragam.



\## FASE 9 — Kesepahaman + PDF



\- \[ ] EULA.

\- \[ ] Versioning.

\- \[ ] Consent logging.

\- \[ ] PDF account.

\- \[ ] PDF kartu.

\- \[ ] PDF kesepahaman.



\## FASE 10 — Wawancara



\- \[ ] Instrumen.

\- \[ ] Rubrik.

\- \[ ] Indikator warna.

\- \[ ] Catatan.

\- \[ ] Wawancara orang tua.

\- \[ ] Status selesai.



\## FASE 11 — Keuangan



\- \[ ] Master biaya.

\- \[ ] Snapshot tagihan.

\- \[ ] Tagihan daftar ulang.

\- \[ ] Diskon.

\- \[ ] Pembayaran.

\- \[ ] Verifikasi.



\## FASE 12 — Keputusan



\- \[ ] Diterima.

\- \[ ] Ditolak.

\- \[ ] Menunggu daftar ulang.

\- \[ ] Resmi terdaftar.

\- \[ ] Mengundurkan diri.

\- \[ ] Histori status.



\## FASE 13 — Dashboard



\- \[ ] Dashboard Admin.

\- \[ ] Dashboard Bendahara.

\- \[ ] Dashboard Pewawancara.

\- \[ ] Dashboard Kepala Sekolah.

\- \[ ] DataTables.

\- \[ ] Export.



\## FASE 14 — Finalisasi



\- \[ ] Audit Trail lengkap.

\- \[ ] Unit Test.

\- \[ ] Feature Test.

\- \[ ] Authorization Test.

\- \[ ] Security Test.

\- \[ ] Concurrency Test.

\- \[ ] Backup.

\- \[ ] Restore test.

\- \[ ] Deployment.



\---



\# 43. Aturan Khusus untuk AI Coding Agent / Antigravity



AI coding agent wajib mengikuti aturan berikut.



1\. Baca seluruh repository sebelum mengubah file.

2\. Jangan menghapus fitur yang sudah berjalan tanpa alasan dan tanpa menjaga kompatibilitas.

3\. Jangan membuat migration yang bertentangan dengan schema sebelumnya.

4\. Sebelum membuat Model, cek migration dan relasinya.

5\. Sebelum membuat Controller, cek Service, Request Validation, Policy dan route terkait.

6\. Jangan menaruh business logic besar di Blade.

7\. Jangan menaruh seluruh business logic di Controller.

8\. Gunakan database transaction pada proses kritis.

9\. Gunakan server-side authorization.

10\. Gunakan Form Request Validation.

11\. Jangan menggunakan Mutator untuk generate nomor pendaftaran.

12\. Gunakan `RegistrationNumberService` untuk nomor pendaftaran.

13\. Nomor pendaftaran wajib unique pada database.

14\. Tagihan daftar ulang wajib menggunakan snapshot.

15\. Jangan mengubah nominal tagihan lama hanya karena master biaya berubah.

16\. Jangan menghapus transaksi pembayaran secara permanen dari UI.

17\. Semua perubahan transaksi dan status penting harus masuk Audit Trail.

18\. Jangan mengubah desain akun calon siswa.

19\. Jangan membuat sistem dokumen generik/dinamis.

20\. Dokumen persyaratan harus menggunakan field/file yang spesifik sesuai requirement sekolah.

21\. Hanya Admin dan Kepala Sekolah yang dapat menetapkan `MENGUNDURKAN\_DIRI`.

22\. Perubahan status harus melalui state transition yang valid.

23\. Jangan mengandalkan visibilitas tombol sebagai authorization.

24\. Semua endpoint sensitif harus dilindungi middleware/policy.

25\. Setiap fase harus diuji sebelum pindah ke fase berikutnya.

26\. Jika menemukan konflik antara implementasi lama dan plan ini, pertahankan data existing dan laporkan konflik sebelum melakukan destructive change.

27\. Gunakan dummy data untuk biaya dan master awal agar Admin dapat mengeditnya.

28\. Jangan hard-code biaya final ke banyak tempat.

29\. Gunakan Service untuk perhitungan tagihan dan diskon.

30\. Pastikan semua fitur responsive.

31\. Pastikan UI menampilkan state SPMB secara jelas menggunakan badge/status indicator.

32\. Jangan menganggap status `DITERIMA` otomatis berarti `RESMI\_TERDAFTAR`; status resmi baru diberikan setelah daftar ulang diverifikasi.

33\. Status `MENGUNDURKAN\_DIRI` harus tetap menyimpan histori status sebelumnya.

34\. Jangan melakukan perubahan schema besar hanya untuk memperbaiki masalah UI sederhana.

35\. Setelah setiap migration/logic change, jalankan test yang terkait.



\---



\# 44. Definition of Done



Sebuah fase dinyatakan selesai hanya jika:



\- Kode berhasil dijalankan tanpa error utama.

\- Migration berhasil dijalankan.

\- Relasi model berjalan.

\- Validasi berjalan.

\- Authorization berjalan.

\- UI responsive.

\- Audit Trail tercatat untuk aksi yang diwajibkan.

\- Test terkait lulus.

\- Tidak merusak fase sebelumnya.



Aplikasi Nampi dinyatakan siap digunakan setelah seluruh alur berikut berhasil diuji:



```text

Registrasi

→ Akun

→ Pembayaran Seleksi

→ Verifikasi

→ Lengkapi Data

→ Kesepahaman

→ Wawancara

→ Keputusan

→ Generate Tagihan

→ Pembayaran Daftar Ulang

→ Verifikasi

→ Resmi Terdaftar

```



Dan alur alternatif:



```text

Calon Siswa

→ Status Aktif

→ Admin/Kepala Sekolah menetapkan Mengundurkan Diri

→ MENGUNDURKAN\_DIRI

→ Histori + Alasan + Audit Trail tersimpan

```



\---



\# 45. Catatan Implementasi



Dokumen ini adalah baseline. Perubahan requirement di kemudian hari harus dilakukan secara terkontrol:



```text

Requirement baru

→ cek dampak database

→ cek dampak workflow

→ cek dampak role

→ cek dampak transaksi

→ update implementation plan

→ migration bila diperlukan

→ implementasi

→ testing regresi

```



Jangan mengubah database production secara langsung tanpa migration dan backup.



