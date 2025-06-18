<?php
include '../db.php';

$id_user = 12;

// Ambil transaksi terakhir yang sudah dibayar
$q = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_user = $id_user AND status != 'pending' ORDER BY id DESC LIMIT 1");

if ($row = mysqli_fetch_assoc($q)) {
  $id_transaksi = $row['id'];
  $tanggal = $row['created_at']; // sesuai struktur
  $metode = $row['metode'];      // sesuai struktur
  $total = $row['total'];

  $items = [];
  $q2 = mysqli_query($conn, "
    SELECT p.nama, td.subtotal
    FROM transaksi_detail td
    JOIN produk p ON p.id = td.id_produk
    WHERE td.id_transaksi = $id_transaksi
  ");

  while ($r = mysqli_fetch_assoc($q2)) {
    $items[] = $r;
  }

  echo json_encode([
    "status" => "success",
    "tanggal" => $tanggal,
    "metode" => $metode,
    "total" => $total,
    "items" => $items
  ]);
} else {
  echo json_encode([
    "status" => "error",
    "message" => "Struk tidak ditemukan."
  ]);
}
?>