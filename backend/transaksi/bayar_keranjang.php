<?php
header('Content-Type: application/json');
include '../db.php';

// Hardcoded user
$id_user = 12;

// Ambil metode dari form atau hardcoded juga bisa
$metode = $_POST['metode'] ?? 'Tunai'; // default "Tunai" kalau kosong

// Ambil isi keranjang
$q = mysqli_query($conn, "
  SELECT k.id_produk, k.jumlah, p.harga_jual
  FROM keranjang k
  JOIN produk p ON p.id = k.id_produk
  WHERE k.id_user = $id_user
");

$items = [];
$total = 0;

while ($row = mysqli_fetch_assoc($q)) {
  $subtotal = $row['harga_jual'] * $row['jumlah'];
  $total += $subtotal;

  $items[] = [
    "id_produk" => $row['id_produk'],
    "jumlah" => $row['jumlah'],
    "subtotal" => $subtotal
  ];
}

if (count($items) === 0) {
  echo json_encode(["success" => false, "message" => "Keranjang kosong"]);
  exit;
}

// Simpan transaksi
$tanggal = date("Y-m-d H:i:s");
$insert = mysqli_query($conn, "
  INSERT INTO transaksi (id_user, total, metode, status, created_at)
  VALUES ($id_user, $total, '$metode', 'selesai', '$tanggal')
");

if (!$insert) {
  echo json_encode(["success" => false, "message" => "Gagal menyimpan transaksi"]);
  exit;
}

$id_transaksi = mysqli_insert_id($conn);

// Simpan detail dan kurangi stok
foreach ($items as $item) {
  $id_produk = $item['id_produk'];
  $jumlah = $item['jumlah'];
  $subtotal = $item['subtotal'];

  mysqli_query($conn, "
    INSERT INTO transaksi_detail (id_transaksi, id_produk, qty, subtotal)
    VALUES ($id_transaksi, $id_produk, $jumlah, $subtotal)
  ");

  mysqli_query($conn, "
    UPDATE produk SET stok = stok - $jumlah WHERE id = $id_produk
  ");
}

// Hapus keranjang
mysqli_query($conn, "DELETE FROM keranjang WHERE id_user = $id_user");

// Berhasil
echo json_encode(["success" => true, "message" => "Pembayaran berhasil"]);