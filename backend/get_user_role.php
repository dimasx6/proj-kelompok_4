<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

// Koneksi database (sesuaikan)
$conn = new mysqli("localhost", "root", "", "konter_uneq");

// Ambil role user
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode([
    'success' => true,
    'role' => $result->fetch_assoc()['role']
]);
?>