<?php
/* ---------- Class Koleksi Buku ---------- */
class KoleksiBuku {
    private string $kode;
    private string $judul;
    private string $pengarang;
    private int $jumlahStok = 0;

    public function __construct(string $kode, string $judul, string $pengarang, int $jumlahStok) {
        $this->kode      = $kode;
        $this->judul     = $judul;
        $this->pengarang = $pengarang;
        $this->ubahStok($jumlahStok);
    }

    public function ambilKode(): string      { return $this->kode; }
    public function ambilJudul(): string     { return $this->judul; }
    public function ambilPengarang(): string { return $this->pengarang; }
    public function ambilStok(): int         { return $this->jumlahStok; }

    // Validasi: stok tidak boleh bernilai negatif
    public function ubahStok(int $jumlah): bool {
        if ($jumlah < 0) {
            echo "Gagal: stok {$jumlah} tidak valid.<br>";
            return false;
        }
        $this->jumlahStok = $jumlah;
        return true;
    }

    public function adaStok(): bool {
        return $this->jumlahStok > 0;
    }

    // Mengurangi stok satu eksemplar; false jika stok kosong
    public function pinjamSatu(): bool {
        if (!$this->adaStok()) {
            return false;
        }
        $this->jumlahStok--;
        return true;
    }

    public function kembalikanSatu(): void {
        $this->jumlahStok++;
    }

    public function cetakDetail(): void {
        echo sprintf(
            "Buku #%s: \"%s\" oleh %s | Stok: %d (%s)<br>",
            $this->kode,
            $this->judul,
            $this->pengarang,
            $this->jumlahStok,
            $this->adaStok() ? "tersedia" : "habis"
        );
    }
}