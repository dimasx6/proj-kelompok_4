<?php
include '../db.php';

$sql = "SELECT p.id, p.nama, p.deskripsi, p.harga_jual, p.gambar, k.nama AS kategori 
        FROM produk p
        LEFT JOIN kategori k ON p.id_kategori = k.id
        WHERE p.stok > 0"; // Tampilkan hanya produk yang tersedia

$result = mysqli_query($conn, $sql);

$produk = [];
while ($row = mysqli_fetch_assoc($result)) {
  $produk[] = $row;
}

header('Content-Type: application/json');
echo json_encode($produk);
?>
