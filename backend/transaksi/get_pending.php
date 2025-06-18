<?php
include '../db.php';

$id_user = 12;
$response = [
  "items" => [],
  "total" => 0,
  "id" => null,
  "success" => false
];

// Ambil transaksi pending terbaru
$q = mysqli_query($conn, "SELECT id FROM transaksi WHERE id_user = $id_user AND status = 'pending' ORDER BY id DESC LIMIT 1");
if ($row = mysqli_fetch_assoc($q)) {
  $id_transaksi = $row['id'];

  $q2 = mysqli_query($conn, "
    SELECT td.id_produk, p.nama, td.qty, p.harga_jual, td.subtotal
    FROM transaksi_detail td
    JOIN produk p ON p.id = td.id_produk
    WHERE td.id_transaksi = $id_transaksi
  ");

  $total = 0;
  $items = [];

  while ($row2 = mysqli_fetch_assoc($q2)) {
    $items[] = $row2;
    $total += $row2['subtotal'];
  }

  $response['items'] = $items;
  $response['total'] = $total;
  $response['id'] = $id_transaksi;
  $response['success'] = true;
}

header('Content-Type: application/json');
echo json_encode($response);
?>