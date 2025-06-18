<?php
require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$id_user = $data['id_user'];
$produk = $data['produk'];

$conn->begin_transaction();

try {
  // Insert transaksi
  $query = "INSERT INTO transaksi (id_user, total, metode, status, created_at) VALUES (?, 0, '', 'Pending', NOW())";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $id_user);
  $stmt->execute();
  $id_transaksi = $stmt->insert_id;

  // Insert transaksi detail
  $total = 0;
  foreach ($produk as $item) {
    $id_produk = $item['id_produk'];
    $qty = $item['qty'];

    $hargaQuery = $conn->query("SELECT harga_jual FROM produk WHERE id = $id_produk");
    $hargaData = $hargaQuery->fetch_assoc();
    $harga = $hargaData['harga_jual'];
    $subtotal = $harga * $qty;
    $total += $subtotal;

    $stmt2 = $conn->prepare("INSERT INTO transaksi_detail (id_transaksi, id_produk, qty, subtotal) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("iiii", $id_transaksi, $id_produk, $qty, $subtotal);
    $stmt2->execute();

    // Update stok
    $conn->query("UPDATE produk SET stok = stok - $qty WHERE id = $id_produk");
  }

  // Update total transaksi
  $stmt3 = $conn->prepare("UPDATE transaksi SET total = ? WHERE id = ?");
  $stmt3->bind_param("ii", $total, $id_transaksi);
  $stmt3->execute();

  $conn->commit();
  echo json_encode(["status" => "success", "id_transaksi" => $id_transaksi]);

} catch (Exception $e) {
  $conn->rollback();
  echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
