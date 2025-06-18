<?php
include '../db.php';

$data = json_decode(file_get_contents("php://input"), true);


$id = $data['id'];
$nama = $data['nama'];
$harga_beli = $data['harga_beli'];
$harga_jual = $data['harga_jual'];
$stok = $data['stok'];

$query = $conn->prepare("UPDATE produk SET nama=?, harga_beli=?, harga_jual=?, stok=? WHERE id=?");
$query->bind_param("sddii", $nama, $harga_beli, $harga_jual, $stok, $id);
$success = $query->execute();

echo json_encode(["success" => $success]);
