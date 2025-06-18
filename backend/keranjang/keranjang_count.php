<?php
include '../koneksi.php';
$id_user = $_GET['id_user'];
$q = mysqli_query($conn, "SELECT SUM(jumlah) as count FROM keranjang WHERE id_user=$id_user");
$row = mysqli_fetch_assoc($q);
echo json_encode(['count' => $row['count'] ?? 0]);
?>