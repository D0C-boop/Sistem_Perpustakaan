<?php
/*
 * Pengujian sistem peminjaman buku perpustakaan.
 * File ini hanya berisi skenario uji; semua class ada di file terpisah.
 * Jalankan dengan: php -S localhost:8000, lalu buka index.php di browser.
 */
require_once __DIR__ . '/Buku.php';        // berisi KoleksiBuku
require_once __DIR__ . '/Anggota.php';     // berisi PenggunaPerpustakaan, PenggunaMahasiswa, PenggunaDosen, PenggunaUmum
require_once __DIR__ . '/Peminjam.php';

// Inisialisasi koleksi buku
$bukuPhp    = new KoleksiBuku("B001", "Pemrograman PHP Dasar", "Penulis A", 2);
$bukuBasdat = new KoleksiBuku("B002", "Basis Data", "Penulis B", 1);

// Inisialisasi pengguna
$mhsWayan  = new PenggunaMahasiswa("M001", "Alonso");
$dosenKetut = new PenggunaDosen("D001", "Lewis Hamilton");
$tamu      = new PenggunaUmum("U001", "Tamu");

echo "<b>== Kondisi Awal ==</b><br>";
foreach ([$bukuPhp, $bukuBasdat] as $item) {
    $item->cetakDetail();
}
foreach ([$mhsWayan, $dosenKetut] as $pengguna) {
    $pengguna->cetakProfil();
}

// Polimorfisme: method yang sama, hasil berbeda untuk tiap jenis pengguna
echo "<br><b>== Aturan Peminjaman per Kategori ==</b><br>";
foreach ([$tamu, $mhsWayan, $dosenKetut] as $pengguna) {
    printf(
        "%s -> batas %d buku, durasi %d hari<br>",
        $pengguna->kategori(),
        $pengguna->maksBuku(),
        $pengguna->durasiHari()
    );
}

// Enkapsulasi: akses langsung ke properti private akan menimbulkan error
// $bukuPhp->jumlahStok = 99;
echo "<br><b>== Proses Transaksi ==</b><br>";
$transaksiA = new Peminjaman($bukuPhp, $mhsWayan, "2026-10-01");
$transaksiB = new Peminjaman($bukuBasdat, $dosenKetut, "2026-10-01");
$transaksiC = new Peminjaman($bukuBasdat, $mhsWayan, "2026-10-02"); // stok B002 sudah habis

$transaksiA->jalankan();
$transaksiB->jalankan();
$transaksiC->jalankan(); // diharapkan gagal

// Wayan terlambat 4 hari (jatuh tempo 2026-10-08, dikembalikan 2026-10-12)
$transaksiA->selesaikan("2026-10-12");
// Dr. Ketut tepat waktu (jatuh tempo 2026-10-15, dikembalikan 2026-10-10)
$transaksiB->selesaikan("2026-10-10");

echo "<br><b>== Rekap Akhir ==</b><br>";
$transaksiA->cetakRingkasan();
$transaksiB->cetakRingkasan();
$bukuPhp->cetakDetail();
$bukuBasdat->cetakDetail();