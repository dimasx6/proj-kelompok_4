<?php
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['id'] ?? 0;
$field = $data['field'] ?? '';
$value = $data['value'] ?? '';

// Validasi input
if (!$userId || !$field || $value === '') {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    exit;
}

// Validasi field yang boleh diupdate
$allowedFields = ['nama', 'email'];
if (!in_array($field, $allowedFields)) {
    echo json_encode(["success" => false, "message" => "Field tidak diizinkan"]);
    exit;
}

// Validasi khusus untuk email
if ($field === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Format email tidak valid"]);
    exit;
}

try {
    // Update data user
    $stmt = $conn->prepare("UPDATE users SET $field = ? WHERE id = ?");
    $stmt->bind_param("si", $value, $userId);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Tidak ada perubahan data"]);
    }
} catch (Exception $e) {
    // Handle duplicate email error
    if ($e->getCode() == 1062) {
        echo json_encode(["success" => false, "message" => "Email sudah digunakan"]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Error: " . $e->getMessage()
        ]);
    }
}

$conn->close();
?>