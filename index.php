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
$bukuCar    = new KoleksiBuku("B001", "How to build a car", "Adrian Newey", 2);
$bukuMie = new KoleksiBuku("B002", "Seporsi Mie ayam sebelum mati", "Brian Khrisna", 1);

// Inisialisasi pengguna
$mhsAlonso  = new PenggunaMahasiswa("M001", "Alonso");
$dosenLewis = new PenggunaDosen("D001", "Sir. Lewis Hamilton");
$tamu      = new PenggunaUmum("U001", "Tamu");

echo "<b>== Kondisi Awal ==</b><br>";
foreach ([$bukuCar, $bukuMie] as $item) {
    $item->cetakDetail();
}
foreach ([$mhsAlonso, $dosenLewis] as $pengguna) {
    $pengguna->cetakProfil();
}

// Polimorfisme: method yang sama, hasil berbeda untuk tiap jenis pengguna
echo "<br><b>== Aturan Peminjaman per Kategori ==</b><br>";
foreach ([$tamu, $mhsAlonso, $dosenLewis] as $pengguna) {
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
$transaksiA = new Peminjaman($bukuMie, $mhsAlonso, "2026-09-02");
$transaksiB = new Peminjaman($bukuCar, $dosenLewis, "2026-09-02");
$transaksiC = new Peminjaman($bukuCar, $mhsAlonso, "2026-09-03"); // stok B002 sudah habis

$transaksiA->jalankan();
$transaksiB->jalankan();
$transaksiC->jalankan(); // diharapkan gagal

// Alonso terlambat 7 hari (jatuh tempo 2026-10-07, dikembalikan 2026-10-14)
$transaksiA->selesaikan("2026-09-14");
// Sir. Lewis Hamilton tepat waktu (jatuh tempo 2026-09-10, dikembalikan 2026-09-10)
$transaksiB->selesaikan("2026-09-10");

echo "<br><b>== Rekap Akhir ==</b><br>";
$transaksiA->cetakRingkasan();
$transaksiB->cetakRingkasan();
$bukuCar->cetakDetail();
$bukuMie->cetakDetail();