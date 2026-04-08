# Fitur Pengembalian Buku dengan Sistem Hukuman/Denda

## Ringkasan Fitur
Sistem perpustakaan telah dilengkapi dengan fitur pengembalian buku yang otomatis menghitung denda untuk keterlambatan. Fitur ini membantu admin dalam mengelola pengembalian buku dan memberikan visual yang jelas tentang status keterlambatan.

---

## 🔄 Fitur-Fitur Baru yang Ditambahkan

### 1. **Perhitungan Denda Otomatis**
- Denda dihitung secara otomatis berdasarkan hari keterlambatan
- **Tarif Denda**: Rp 5.000 per hari (dapat disesuaikan di code)
- Dihitung saat form pengembalian diisi dan disubmit

### 2. **Form Pengembalian Dinamis**
- Form pengembalian menampilkan:
  - Informasi tanggal pinjam dan target kembali
  - Perhitungan denda real-time saat tanggal pengembalian diubah
  - Info detail jika ada keterlambatan
  - Field denda menjadi read-only (otomatis terisi)

### 3. **Dashboard Admin - Peringatan Keterlambatan**
- Card statistik menampilkan jumlah peminjaman terlambat
- Tabel khusus untuk peminjaman terlambat dengan:
  - Nama peminjam
  - Judul buku
  - Tanggal target kembali
  - Hari keterlambatan
  - Estimasi denda

### 4. **Daftar Peminjaman - Indikator Keterlambatan**
- Setiap peminjaman yang terlambat baris tabelnya berwarna merah
- Menampilkan jumlah hari terlambat
- Menampilkan estimasi denda (Rp)
- Tombol "Kembalikan" untuk pengembalian cepat

### 5. **Daftar Pengembalian - Info Keterlambatan**
- Tambahan kolom "Keterlambatan" yang menampilkan:
  - Status "Tepat Waktu" (badge hijau) atau jumlah hari terlambat (badge merah)
- Baris tabel berwarna kuning jika ada keterlambatan

---

## 📋 Alur Penggunaan

### Sebagai Admin:

#### Melihat Peminjaman Terlambat:
1. Masuk ke **Dashboard** → akan melihat statistik dan tabel peminjaman terlambat
2. Atau ke **Data Peminjaman** → Tab "Disetujui & Dipinjam" menampilkan indikator keterlambatan
3. Atau ke **Dashboard** → Bagian "Peringatan: Peminjaman Terlambat"

#### Mencatat Pengembalian Buku:
1. Klik menu **Pengembalian** → **Catat Pengembalian**
2. Pilih peminjaman dari dropdown
3. Input tanggal pengembalian aktual
4. **Denda akan otomatis terhitung** berdasarkan keterlambatan
5. (Opsional) Tambahkan keterangan
6. Klik **Simpan Pengembalian**
7. System akan:
   - Mencatat pengembalian
   - Update status peminjaman menjadi "Dikembalikan"
   - Return stok buku
   - Simpan denda (jika ada keterlambatan)

#### Melihat Riwayat Pengembalian:
1. Klik menu **Pengembalian**
2. Lihat daftar semua pengembalian
3. Kolom "Keterlambatan" menunjukkan apakah pengembalian tepat waktu atau terlambat
4. Kolom "Denda" menampilkan denda yang dikenakan (jika ada)

---

## 💻 File yang Dimodifikasi

### Models:
- **`models/Peminjaman.php`**
  - `checkIfLate($id)` - Cek apakah peminjaman terlambat
  - `getAllWithLateInfo()` - Get semua peminjaman dengan info keterlambatan

- **`models/Pengembalian.php`**
  - `calculateDenda($peminjamanId, $tanggalPengembalian, $dendaPerHari)` - Hitung denda
  - `getAllWithLateInfo()` - Get semua pengembalian dengan info keterlambatan
  - `getPeminjamanBelumDikembalikan()` - Get peminjaman yang belum dikembalikan + estimasi denda

### Controllers:
- **`controllers/PeminjamanController.php`**
  - Update method `index()` untuk menggunakan `getAllWithLateInfo()`

- **`controllers/PengembalianController.php`**
  - Update method `index()` untuk menggunakan `getAllWithLateInfo()`
  - Update method `create()` dengan logika perhitungan denda otomatis

- **`controllers/DashboardController.php`**
  - Update method `admin()` untuk menambahkan data peminjaman terlambat

### Views:
- **`views/peminjaman/index.php`**
  - Tambah kolom "Keterlambatan" dengan indikator visual
  - Tambah tombol "Kembalikan" (warna info)
  - Highlight baris tabel untuk peminjaman terlambat

- **`views/pengembalian/create.php`**
  - Redesign form dengan info peminjaman
  - Tambah JavaScript untuk perhitungan denda real-time
  - Tampilkan berita keterlambatan

- **`views/pengembalian/index.php`**
  - Tambah kolom "Keterlambatan"
  - Highlight baris untuk pengembalian yang terlambat

- **`views/dashboard/admin.php`**
  - Tambah card statistik untuk peminjaman terlambat
  - Tambah tabel peringatan peminjaman terlambat

---

## 📊 Rumus Perhitungan Denda

```
Jika tanggal_pengembalian > tanggal_kembali_target:
  hari_terlambat = (tanggal_pengembalian - tanggal_kembali_target) / 86400
  denda = hari_terlambat × Rp 5.000
ElseIf:
  denda = Rp 0
```

---

## ⚙️ Konfigurasi

### Mengubah Tarif Denda:

**File**: `controllers/PengembalianController.php` → method `create()`

Cari baris:
```php
$hitungDenda = $this->pengembalianModel->calculateDenda(
    $data['peminjaman_id'],
    $data['tanggal_pengembalian'],
    5000 // ← Ubah nilai ini
);
```

Dan juga di **`views/pengembalian/create.php`**:
```javascript
const DENDA_PER_HARI = 5000; // ← Ubah nilai ini
```

---

## 🔍 Contoh Skenario

### Skenario 1: Pengembalian Tepat Waktu
- Tanggal target kembali: 15 Februari 2026
- Tanggal dikembalikan: 15 Februari 2026
- Hari terlambat: 0
- **Denda: Rp 0** ✅

### Skenario 2: Pengembalian Terlambat 3 Hari
- Tanggal target kembali: 15 Februari 2026
- Tanggal dikembalikan: 18 Februari 2026
- Hari terlambat: 3
- **Denda: Rp 15.000** (3 × Rp 5.000) ⚠️

### Skenario 3: Pengembalian Terlambat 7 Hari
- Tanggal target kembali: 10 Februari 2026
- Tanggal dikembalikan: 17 Februari 2026
- Hari terlambat: 7
- **Denda: Rp 35.000** (7 × Rp 5.000) ⚠️

---

## 🎯 Tips Penggunaan

1. **Pemeriksaan Rutin**: Admin sebaiknya rutin cek Dashboard untuk melihat peminjaman terlambat
2. **Pengembalian Cepat**: Gunakan tombol "Kembalikan" di daftar peminjaman untuk mempercepat penginputan
3. **Keterangan Denda**: Gunakan field "Keterangan" untuk mencatat alasan atau kondisi buku saat dikembalikan
4. **Backup Data**: Pastikan data denda tercatat untuk audit trail keuangan

---

## 🐛 Troubleshooting

### Denda Tidak Terlihat di Form
- Pastikan JavaScript tidak diblokir di browser
- Cek browser console untuk error (F12 → Console)
- Refresh halaman dan coba lagi

### Denda Tidak Tersimpan
- Pastikan database table `pengembalian` memiliki column `denda`
- Cek apakah koneksi database berfungsi dengan baik

### Info Keterlambatan Tidak Muncul
- Pastikan tanggal sistem komputer sudah benar
- Cek apakah query SQL berfungsi dengan baik (gunakan PhpMyAdmin untuk test)

---

**Dibuat**: 11 Februari 2026  
**Versi**: 1.0  
**Status**: ✅ Selesai dan siap digunakan
