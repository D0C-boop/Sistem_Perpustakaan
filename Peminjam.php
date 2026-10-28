<?php
/* ---------- Class Transaksi Peminjaman ---------- */
// Membutuhkan class KoleksiBuku dan PenggunaPerpustakaan, jadi kedua file itu dimuat dulu
require_once __DIR__ . '/Buku.php';
require_once __DIR__ . '/Anggota.php';

class Peminjaman {
    private const TARIF_DENDA_HARIAN = 1000;

    private const STATUS_BARU          = "baru";
    private const STATUS_DIPINJAM      = "dipinjam";
    private const STATUS_DIKEMBALIKAN  = "dikembalikan";

    private KoleksiBuku $buku;
    private PenggunaPerpustakaan $peminjam;
    private DateTimeImmutable $waktuPinjam;
    private DateTimeImmutable $batasKembali;
    private ?DateTimeImmutable $waktuKembali = null;
    private string $status = self::STATUS_BARU;

    public function __construct(KoleksiBuku $buku, PenggunaPerpustakaan $peminjam, string $tanggalPinjam) {
        $this->buku        = $buku;
        $this->peminjam    = $peminjam;
        $this->waktuPinjam = new DateTimeImmutable($tanggalPinjam);

        // Durasi pinjam mengikuti kategori pengguna (polimorfisme)
        $durasi = $peminjam->durasiHari();
        $this->batasKembali = $this->waktuPinjam->modify("+{$durasi} days");
    }

    public function ambilStatus(): string {
        return $this->status;
    }

    // Menjalankan proses peminjaman; true jika berhasil
    public function jalankan(): bool {
        if (!$this->peminjam->masihBisaMeminjam()) {
            echo "Ditolak: {$this->peminjam->ambilNama()} sudah mencapai batas peminjaman.<br>";
            return false;
        }
        if (!$this->buku->pinjamSatu()) {
            echo "Ditolak: buku \"{$this->buku->ambilJudul()}\" sedang kosong.<br>";
            return false;
        }

        $this->peminjam->catatPeminjaman();
        $this->status = self::STATUS_DIPINJAM;

        echo "{$this->peminjam->ambilNama()} berhasil meminjam \"{$this->buku->ambilJudul()}\" "
           . "(batas kembali: {$this->batasKembali->format('d/m/Y')})<br>";
        return true;
    }

    public function selesaikan(string $tanggalKembali): void {
        if ($this->status !== self::STATUS_DIPINJAM) {
            echo "Ditolak: transaksi ini tidak dalam status dipinjam.<br>";
            return;
        }

        $this->waktuKembali = new DateTimeImmutable($tanggalKembali);
        $this->buku->kembalikanSatu();
        $this->peminjam->catatPengembalian();
        $this->status = self::STATUS_DIKEMBALIKAN;

        echo "{$this->peminjam->ambilNama()} mengembalikan \"{$this->buku->ambilJudul()}\" "
           . "pada {$this->waktuKembali->format('d/m/Y')}<br>";
    }

    // Denda = jumlah hari terlambat x tarif harian; 0 jika tepat waktu
    public function hitungTotalDenda(): int {
        if ($this->waktuKembali === null || $this->waktuKembali <= $this->batasKembali) {
            return 0;
        }
        $selisihHari = $this->batasKembali->diff($this->waktuKembali)->days;
        return $selisihHari * self::TARIF_DENDA_HARIAN;
    }

    public function cetakRingkasan(): void {
        echo sprintf(
            "Peminjam: %s | Buku: %s | Status: %s | Denda: Rp %s<br>",
            $this->peminjam->ambilNama(),
            $this->buku->ambilJudul(),
            $this->status,
            number_format($this->hitungTotalDenda(), 0, ',', '.')
        );
    }
}