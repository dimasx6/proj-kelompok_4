<?php
header('Content-Type: application/json');
include 'db.php';

// Ambil ID user dari session atau parameter
$userId = $_GET['id'] ?? 0;

if (!$userId) {
    echo json_encode([
        "success" => false,
        "message" => "ID User tidak valid"
    ]);
    exit;
}

try {
    // Query untuk mengambil data user
    $stmt = $conn->prepare("SELECT id, nama, email, role, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "data" => $user
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "User tidak ditemukan"
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$conn->close();
?>