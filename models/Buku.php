<?php

require_once __DIR__ . '/../config/Database.php';

class Buku
{
    private $db;
    private $table = 'buku';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT b.*, k.nama_kategori FROM {$this->table} b LEFT JOIN kategori k ON b.kategori_id = k.id ORDER BY b.id DESC");
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT b.*, k.nama_kategori FROM {$this->table} b LEFT JOIN kategori k ON b.kategori_id = k.id WHERE b.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (judul, pengarang, penerbit, tahun_terbit, isbn, jumlah_stok, kategori_id) VALUES (:judul, :pengarang, :penerbit, :tahun_terbit, :isbn, :jumlah_stok, :kategori_id)");
        return $stmt->execute([
            'judul' => $data['judul'],
            'pengarang' => $data['pengarang'],
            'penerbit' => $data['penerbit'],
            'tahun_terbit' => $data['tahun_terbit'],
            'isbn' => $data['isbn'],
            'jumlah_stok' => $data['jumlah_stok'],
            'kategori_id' => $data['kategori_id']
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET judul = :judul, pengarang = :pengarang, penerbit = :penerbit, tahun_terbit = :tahun_terbit, isbn = :isbn, jumlah_stok = :jumlah_stok, kategori_id = :kategori_id WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'judul' => $data['judul'],
            'pengarang' => $data['pengarang'],
            'penerbit' => $data['penerbit'],
            'tahun_terbit' => $data['tahun_terbit'],
            'isbn' => $data['isbn'],
            'jumlah_stok' => $data['jumlah_stok'],
            'kategori_id' => $data['kategori_id']
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function count()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
        return $stmt->fetch()->total;
    }

    public function search($keyword)
    {
        $stmt = $this->db->prepare("SELECT b.*, k.nama_kategori FROM {$this->table} b LEFT JOIN kategori k ON b.kategori_id = k.id WHERE b.judul LIKE :keyword OR b.pengarang LIKE :keyword2 ORDER BY b.id DESC");
        $stmt->execute([
            'keyword' => "%{$keyword}%",
            'keyword2' => "%{$keyword}%"
        ]);
        return $stmt->fetchAll();
    }
}
