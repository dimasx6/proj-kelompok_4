<?php
include '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$nama = $data['nama'];
$harga_beli = $data['harga_beli'];
$harga_jual = $data['harga_jual'];
$stok = $data['stok'];
$id_kategori = 1; // sementara default

$query = $conn->prepare("INSERT INTO produk (nama, harga_beli, harga_jual, stok, id_kategori) VALUES (?, ?, ?, ?, ?)");
$query->bind_param("sddii", $nama, $harga_beli, $harga_jual, $stok, $id_kategori);
$success = $query->execute();

echo json_encode(["success" => $success]);
