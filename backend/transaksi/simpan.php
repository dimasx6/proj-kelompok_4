<?php
include '../db.php'; // koneksi ke database

header('Content-Type: application/json');

// Ambil data dari body request
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Data tidak ditemukan."]);
    exit;
}

// Ambil nilai dari request
$id = $data['id']; // ID dari tabel transaksi
$total = $data['total'];
$metode = $data['metode'];
$status = 'sukses'; // Set status sukses

if (!$id || !$total || !$metode) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap."]);
    exit;
}

// Update data transaksi
$update = mysqli_query($conn, "
    UPDATE transaksi 
    SET 
        total = $total,
        metode = '$metode',
        status = '$status'
    WHERE id = $id
");

if (!$update) {
    echo json_encode(["success" => false, "message" => "Gagal update transaksi: " . mysqli_error($conn)]);
    exit;
}

// Ambil data keranjang berdasarkan id_user = 12
$id_user = 12;
$get_keranjang = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_user = $id_user");

while ($item = mysqli_fetch_assoc($get_keranjang)) {
    $id_produk = $item['id_produk'];
    $jumlah = $item['jumlah'];

    // Ambil harga jual produk
    $get_produk = mysqli_query($conn, "SELECT harga_jual FROM produk WHERE id = $id_produk");
    $produk = mysqli_fetch_assoc($get_produk);
    $harga = $produk['harga_jual'];
    $subtotal = $jumlah * $harga;

    // Simpan ke transaksi_detail
    mysqli_query($conn, "
        INSERT INTO transaksi_detail (id_transaksi, id_produk, qty, subtotal)
        VALUES ($id, $id_produk, $jumlah, $subtotal)
    ");

    // Kurangi stok produk
    mysqli_query($conn, "
        UPDATE produk SET stok = stok - $jumlah WHERE id = $id_produk
    ");
}

// Hapus semua isi keranjang setelah transaksi
mysqli_query($conn, "DELETE FROM keranjang WHERE id_user = $id_user");

echo json_encode(["success" => true, "message" => "Pembayaran berhasil!"]);
?>