# Rencana Pengembangan Aplikasi BAGOPS

## Tujuan
Menyediakan sistem terpadu untuk penyusunan RENOPS/REN PAM, penugasan personel, pelaporan (Laphar/AAR), arsip dokumen, dan integrasi notifikasi serta peta.

## Ruang Lingkup Tahap Awal
1. Modul RENOPS/REN PAM digital + generator PDF (FPDF/TCPDF/DOMPDF)
2. Manajemen personel & penugasan (unsur pimpinan, negosiator, pam obvit, lalin, dokumentasi, waspers) - **DONE**
3. Kalender operasi (event: unras, razia, kunjungan, strong point) + reminder - **DONE**
4. Modul Intel & Risiko (perkiraan kerawanan, rekomendasi kekuatan)
5. Pelaporan & AAR digital (template siap pakai)
6. Arsip dokumen & lampiran (Sprin, Renpam, intel, AAR) dengan tagging - **DONE** (basic CRUD + 2MB upload limit)
7. Integrasi notifikasi (SMS/WA gateway) dan peta sektor/rute (GIS dasar)

## Deliverables Minimal (MVP)
- [x] Form web RENOPS dengan field: dasar hukum, intel singkat, waktu-lokasi, sasaran, skenario ancaman, kekuatan, rencana taktis/komunikasi/kontinjensi, koordinasi eksternal, logistik, dokumentasi.
- [ ] Generator PDF/Doc (pakai FPDF/TCPDF/DOMPDF) untuk Sprin/Renpam (merge data personel & peran).
- [x] Master data personel + assign ke sektor/peran; ekspor daftar tugas per event.
- [x] Kalender operasi + notifikasi H-3/H-1.
- [ ] Laphar dan AAR digital (pakai template yang sudah ada di docs).
- [x] Arsip terpusat per event dengan tag (jenis, lokasi, tanggal, risiko).

## Backlog Fase Berikutnya
- Dashboard KPI operasi (response time, insiden, kepatuhan SOP, kesiapan logistik, kepuasan stakeholder)
- Integrasi peta (plot sektor, rute utama/alternatif, objek vital)
- Upload bukti digital dengan geotag/waktu + watermark/QR
- Role-based access control (RBAC) + audit trail
- Analitik tren kerawanan dan rekomendasi kekuatan minimal

## Tahapan Implementasi (Sprint-level)
1) ~~Fondasi data & form~~ **DONE**
   - [x] Skema DB: personel, event/operasi, RENOPS, penugasan, dokumen, lampiran, log pelaporan.
   - [x] Form input RENOPS + validasi, simpan ke DB.
   - [x] Master personel + CRUD + import awal (csv/xls).

2) ~~Penugasan & dokumen~~ **DONE**
   - [x] Modul assignment per event (peran/unsur) + auto-generate Sprin/Renpam PDF (FPDF/TCPDF/DOMPDF).
   - [x] Template dokumen dapat disesuaikan (kop, nomor, pejabat, dasar, lampiran personel).
   - [x] Upload dokumen dengan batas 2MB dan validasi tipe file (PDF, JPEG, PNG, TXT)

3) ~~Kalender & notifikasi~~ **DONE**
   - [x] View kalender operasi, filter jenis/risiko.
   - [x] Reminder H-3/H-1 via SMS/WA gateway (configurable).

4) Pelaporan & AAR
   - [ ] Form Laphar harian; form AAR digital (template di docs).
   - [ ] Ekspor PDF laporan akhir; simpan lampiran foto/video (link/upload).

5) ~~Arsip & tagging~~ **DONE**
   - [x] Repositori per event; tag jenis, lokasi, tanggal, risiko.
   - [x] Pencarian cepat (tanggal, jenis, lokasi, kata kunci).
   - [x] CRUD lengkap untuk manajemen dokumen

6) Integrasi peta (awal)
   - [ ] Simpan koordinat lokasi/objek; tampilkan peta statis/embedded.
   - [ ] Plot sektor/rute utama-alternatif.

7) Keamanan & Validasi
   - [x] Validasi input form
   - [x] Pembatasan akses berdasarkan role
   - [x] Validasi ukuran dan tipe file upload

## Teknologi Disarankan
- Backend: PHP (Laravel) atau Node.js (Express) sesuai stack tim.
- DB: PostgreSQL/MySQL.
- PDF: FPDF/TCPDF/DOMPDF (hindari LaTeX).
- Frontend: HTML/JS + komponen UI sederhana (pilih framework jika perlu).
- Notifikasi: integrasi SMS/WA gateway (konfigurable).

## Risiko & Mitigasi
- Konsistensi data personel/peran → gunakan master data dan import terkontrol.
- Keamanan dokumen → RBAC, audit log, watermark/QR untuk dokumen keluar.
- Ketergantungan layanan gateway → fallback kanal email/SMS.
- Kinerja upload lampiran → batasi ukuran, gunakan storage terpisah bila perlu.

## Langkah Berikutnya
1. Setujui ruang lingkup MVP di atas.
2. Pilih stack backend (Laravel vs Express) dan library PDF (FPDF/TCPDF/DOMPDF).
3. Siapkan skema DB awal dan contoh data personel/event.
4. Bangun form RENOPS + generator PDF sebagai fitur pertama.
