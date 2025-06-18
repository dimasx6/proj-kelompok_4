<?php
header('Content-Type: application/json');
require_once 'db.php'; // Include your database connection

// Function to get product count
function getProductCount($conn) {
    $query = "SELECT COUNT(*) as count FROM produk";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

// Function to get today's transactions
function getTodayTransactions($conn) {
    $today = date('Y-m-d');
    $query = "SELECT COUNT(*) as count FROM transaksi WHERE DATE(tanggal) = '$today'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

// Function to get today's sales
function getTodaySales($conn) {
    $today = date('Y-m-d');
    $query = "SELECT SUM(total) as total FROM transaksi WHERE DATE(tanggal) = '$today'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

// Function to get low stock notifications
function getLowStockNotifications($conn) {
    $query = "SELECT nama, stok FROM produk WHERE stok < 5 ORDER BY stok ASC";
    $result = mysqli_query($conn, $query);
    $notifications = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = [
            'name' => $row['nama'],
            'stock' => $row['stok']
        ];
    }
    return $notifications;
}

// Main response
try {
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    $response = [
        'success' => true,
        'data' => [
            'product_count' => getProductCount($conn),
            'today_transactions' => getTodayTransactions($conn),
            'today_sales' => getTodaySales($conn),
            'notifications' => getLowStockNotifications($conn)
        ]
    ];

    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>