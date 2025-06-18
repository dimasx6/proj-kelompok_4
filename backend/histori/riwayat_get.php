<?php
header('Content-Type: application/json');
include '../db.php';

$id_user = 12;

$q = mysqli_query($conn, "
  SELECT id, total, metode, status, created_at 
  FROM transaksi 
  WHERE id_user = $id_user 
  ORDER BY created_at DESC
");

$results = [];
while ($row = mysqli_fetch_assoc($q)) {
  $results[] = [
    "date" => date("d-m-Y H:i", strtotime($row['created_at'])),
    "paymentMethod" => $row['metode'],
    "total" => "Rp " . number_format($row['total'], 0, ',', '.'),
    "status" => ucfirst($row['status'])
  ];
}

echo json_encode(["success" => true, "data" => $results]);
?>