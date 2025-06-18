<?php
include '../db.php';
header('Content-Type: application/json');

$id_user = 12;
$id_produk = $_POST['id_produk'];
$jumlah = 1;

// Cek apakah produk sudah ada di keranjang
$cekQuery = "SELECT * FROM keranjang WHERE id_user = $id_user AND id_produk = $id_produk";
$cekResult = mysqli_query($conn, $cekQuery);

if (mysqli_num_rows($cekResult) > 0) {
    // Jika sudah ada, update jumlah
    $updateQuery = "UPDATE keranjang SET jumlah = jumlah + $jumlah WHERE id_user = $id_user AND id_produk = $id_produk";
    $result = mysqli_query($conn, $updateQuery);
} else {
    // Jika belum ada, insert baru
    $insertQuery = "INSERT INTO keranjang (id_user, id_produk, jumlah) VALUES ($id_user, $id_produk, $jumlah)";
    $result = mysqli_query($conn, $insertQuery);
}

if ($result) {
    echo json_encode([
        "success" => true,
        "message" => "Berhasil ditambahkan ke keranjang"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Gagal menambahkan: " . mysqli_error($conn)
    ]);
}
?>