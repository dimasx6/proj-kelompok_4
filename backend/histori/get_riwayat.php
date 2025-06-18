<?php
header('Content-Type: application/json');
include '../db.php';

// For testing, hardcoded user ID (remove in production)
$id_user = 12;

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("
    SELECT id, total, metode, status, created_at 
    FROM transaksi 
    WHERE id_user = ? 
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = [
        "date" => date("d-m-Y H:i", strtotime($row['created_at'])),
        "paymentMethod" => $row['metode'],
        "total" => "Rp " . number_format($row['total'], 0, ',', '.'),
        "status" => ucfirst($row['status'])
    ];
}

// Fixed the json_encode typo
echo json_encode(["success" => true, "data" => $results]);

$stmt->close();
$conn->close();
?>