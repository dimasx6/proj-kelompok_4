<?php
// Set header untuk respon JSON 
header('Content-Type: application/json');
session_start();

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "konter_uneq");

// Cek koneksi
if ($conn->connect_error) {
  echo json_encode([
    'success' => false, 
    'message' => 'Gagal koneksi database.'
  ]);
  exit;
}

// Ambil dan sanitasi input
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';

// Validasi input
if (empty($email) || empty($password)) {
  echo json_encode([
    'success' => false, 
    'message' => 'Email dan password harus diisi.'
  ]);
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode([
    'success' => false, 
    'message' => 'Format email tidak valid.'
  ]);
  exit;
}

// Ambil data user termasuk role
$stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  // Verifikasi password
  if (password_verify($password, $row['password'])) {
    // Regenerasi ID session untuk prevent hijacking
    session_regenerate_id(true);
    
    // Set session variables
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['role'] = $row['role'];
    $_SESSION['last_activity'] = time(); // Untuk timeout session
    
    // Response sukses
    echo json_encode([
      'success' => true,
      'message' => 'Login berhasil!',
      'role' => $row['role'] // Kirim role ke frontend
    ]);
    
  } else {
    // Password salah
    echo json_encode([
      'success' => false,
      'message' => 'Kombinasi email dan password salah.'
    ]);
  }
} else {
  // Email tidak ditemukan
  echo json_encode([
    'success' => false,
    'message' => 'Email tidak terdaftar.'
  ]);
}

// Tutup koneksi
$stmt->close();
$conn->close();
?>