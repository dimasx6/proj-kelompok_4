<?php
include '../db.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

if (!$id) {
  echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
  exit;
}

$query = $conn->prepare("DELETE FROM keranjang WHERE id = ?");
$query->bind_param("i", $id);
$success = $query->execute();

echo json_encode(['success' => $success]);
?>