<?php
require_once '../db.php';

$nama = $_POST['nama'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (nama, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nama, $email, $password);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registrasi berhasil!"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal registrasi. Email mungkin sudah digunakan."]);
}
?>
