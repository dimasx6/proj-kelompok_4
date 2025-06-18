<?php
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['id'] ?? 0;
$currentPassword = $data['current_password'] ?? '';
$newPassword = $data['new_password'] ?? '';

// Validasi input
if (!$userId || !$currentPassword || !$newPassword) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    exit;
}

if (strlen($newPassword) < 8) {
    echo json_encode(["success" => false, "message" => "Password minimal 8 karakter"]);
    exit;
}

try {
    // 1. Verifikasi password saat ini
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "User tidak ditemukan"]);
        exit;
    }
    
    $user = $result->fetch_assoc();
    if (!password_verify($currentPassword, $user['password'])) {
        echo json_encode(["success" => false, "message" => "Password saat ini salah"]);
        exit;
    }
    
    // 2. Update password baru
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $userId);
    $stmt->execute();
    
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$conn->close();
?>