<?php
/* ---------- Class dasar Pengguna Perpustakaan ---------- */
abstract class PenggunaPerpustakaan {
    protected string $kode;
    protected string $namaLengkap;
    protected int $totalPinjaman = 0;

    public function __construct(string $kode, string $namaLengkap) {
        $this->kode        = $kode;
        $this->namaLengkap = $namaLengkap;
    }

    // Wajib diimplementasikan oleh class turunan
    abstract public function kategori(): string;
    abstract public function maksBuku(): int;
    abstract public function durasiHari(): int;

    public function ambilKode(): string       { return $this->kode; }
    public function ambilNama(): string       { return $this->namaLengkap; }
    public function ambilTotalPinjaman(): int { return $this->totalPinjaman; }

    public function masihBisaMeminjam(): bool {
        return $this->totalPinjaman < $this->maksBuku();
    }

    public function catatPeminjaman(): void {
        if ($this->masihBisaMeminjam()) {
            $this->totalPinjaman++;
        }
    }

    public function catatPengembalian(): void {
        if ($this->totalPinjaman > 0) {
            $this->totalPinjaman--;
        }
    }

    public function cetakProfil(): void {
        echo sprintf(
            "[%s] %s | Kode: %s | Pinjaman: %d/%d buku | Durasi: %d hari<br>",
            $this->kategori(),
            $this->namaLengkap,
            $this->kode,
            $this->totalPinjaman,
            $this->maksBuku(),
            $this->durasiHari()
        );
    }
}

/* ---------- Class turunan ---------- */
class PenggunaMahasiswa extends PenggunaPerpustakaan {
    private const BATAS  = 3;
    private const DURASI = 7;

    public function kategori(): string { return "Mahasiswa"; }
    public function maksBuku(): int    { return self::BATAS; }
    public function durasiHari(): int  { return self::DURASI; }
}

class PenggunaDosen extends PenggunaPerpustakaan {
    private const BATAS  = 5;
    private const DURASI = 14;

    public function kategori(): string { return "Dosen"; }
    public function maksBuku(): int    { return self::BATAS; }
    public function durasiHari(): int  { return self::DURASI; }
}

class PenggunaUmum extends PenggunaPerpustakaan {
    private const BATAS  = 2;
    private const DURASI = 7;

    public function kategori(): string { return "Anggota Umum"; }
    public function maksBuku(): int    { return self::BATAS; }
    public function durasiHari(): int  { return self::DURASI; }
}
