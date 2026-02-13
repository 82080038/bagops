# Diagram Alur Operasional BAGOPS (Ringkas)

```mermaid
graph TD
  A[Perintah/RENOPS Diterima] --> B[Analisis Ancaman & Kerawanan]
  B --> C[Rencana Taktis (kekuatan, rute, posko, komunikasi)]
  C --> D[Rencana Kontinjensi & Logistik]
  D --> E[Briefing & Gelar Pasukan]
  E --> F[Pelaksanaan Operasi]
  F --> G[Monitoring Real-time (CCTV/Radio/Posko)]
  G --> H[Penanganan Insiden & Pengamanan Obvit]
  H --> I[Laporan Harian (Laphar)]
  I --> J[After Action Review (AAR) & Laporan Akhir]
  J --> K[Pembaruan SOP & Rencana Kontinjensi]
```

## Diagram Khusus per Jenis Operasi

### Pengamanan Event/Keramaian
```mermaid
graph TD
  A[Survei Lokasi & Analisis Kerawanan] --> B[Site Plan & RENOPS]
  B --> C[Koordinasi EO/Dishub/Damkar/Nakes]
  C --> D[Uji Komunikasi & Integrasi CCTV]
  D --> E[Gelar Pasukan & Simulasi]
  E --> F[Pelaksanaan: Rekayasa Lalin, Sterilisasi, Patroli]
  F --> G[Monitoring Posko/CCTV/Radio]
  G --> H[Penanganan Insiden]
  H --> I[Laporan Harian]
  I --> J[AAR & Perbaikan SOP]
```

### Penanganan Bencana/SAR
```mermaid
graph TD
  A[Aktivasi Posko & ICS] --> B[Peta Risiko & Jalur Evakuasi]
  B --> C[Koordinasi BPBD/Basarnas/TNI/Dinkes/Relawan]
  C --> D[Logistik & APD Siap]
  D --> E[Pencarian & Evakuasi]
  E --> F[Perimeter & Pengamanan Aset]
  F --> G[Manajemen Pengungsi & Data Korban]
  G --> H[Rilis Situasi Berkala]
  H --> I[Laporan Harian]
  I --> J[AAR, Mitigasi, Update Peta Rawan]
```

### Pengamanan VVIP/VIP
```mermaid
graph TD
  A[Threat Assessment Rute & Venue] --> B[Rute Utama/Alternatif & Drop-off]
  B --> C[Koordinasi Paspampres/Denwal]
  C --> D[Screening Personel/Kendaraan]
  D --> E[Gelar & Gladi Lapangan]
  E --> F[Sterilisasi Berlapis & Ring 1-3]
  F --> G[Manajemen Arus & Quick Reaction/Medis]
  G --> H[Monitoring & Dokumentasi]
  H --> I[Escort Keluar]
  I --> J[Laporan Akhir & Evaluasi Kerawanan]
```

### Catatan Penggunaan
- Dapat dipakai sebagai referensi cepat untuk briefing.
- Sesuaikan simpul/rantai dengan jenis operasi (event, bencana, VVIP).
- Jika merender mermaid tidak tersedia, gunakan urutan langkah bernomor di bawah:
  1) Perintah/RENOPS diterima
  2) Analisis ancaman & kerawanan
  3) Rencana taktis (kekuatan, rute, posko, komunikasi)
  4) Rencana kontinjensi & logistik
  5) Briefing & gelar pasukan
  6) Pelaksanaan operasi
  7) Monitoring real-time (CCTV/Radio/Posko)
  8) Penanganan insiden & pengamanan obvit
  9) Laporan harian (Laphar)
  10) After Action Review (AAR) & laporan akhir
  11) Pembaruan SOP & rencana kontinjensi
