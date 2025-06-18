<?php
include '../db.php';

header('Content-Type: application/json');

$id_user = 12;

$query = "
    SELECT k.id, p.nama, p.harga_jual, k.jumlah, (p.harga_jual * k.jumlah) AS subtotal
    FROM keranjang k
    JOIN produk p ON k.id_produk = p.id
    WHERE k.id_user = $id_user
";

$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>