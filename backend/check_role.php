<?php
session_start();

// Jika belum login, redirect ke login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

// Daftar halaman yang diizinkan
$allowed_pages = [
    'admin' => [
        'dashboard.html',
        'laporan-penjualan.html',
        'pengaturan.html',
        'katalog.html',
        'keranjang.html'
    ],
    'user' => [
        'katalog.html',
        'keranjang.html',
        'pengaturan.html'
    ]
];

// Dapatkan halaman saat ini
$current_page = basename($_SERVER['REQUEST_URI']);

// Jika mencoba akses halaman yang tidak diizinkan
if (!in_array($current_page, $allowed_pages[$_SESSION['role']])) {
    header("Location: ../katalog.html");
    exit();
}
?>