<?php
header('Content-Type: application/json');
include 'db.php';

// Validasi input tanggal
$start = isset($_GET['start']) ? $_GET['start'] : null;
$end = isset($_GET['end']) ? $_GET['end'] : null;

if (!$start || !$end) {
  echo json_encode([
    "success" => false,
    "message" => "Parameter tanggal tidak lengkap."
  ]);
  exit;
}

try {
  // Query untuk mengambil data laporan penjualan
  $query = "
    SELECT 
      t.created_at AS date,
      p.nama AS product,
      td.qty,
      p.harga_beli,
      p.harga_jual,
      (td.qty * p.harga_jual) AS subtotal,
      (p.harga_jual - p.harga_beli) * td.qty AS profit
    FROM transaksi t
    JOIN transaksi_detail td ON t.id = td.id_transaksi
    JOIN produk p ON td.id_produk = p.id
    WHERE DATE(t.created_at) BETWEEN ? AND ?
    ORDER BY t.created_at DESC
  ";

  $stmt = $conn->prepare($query);
  $stmt->bind_param("ss", $start, $end);
  $stmt->execute();
  $result = $stmt->get_result();

  $data = [];
  $omset = 0;
  $laba = 0;
  $produk_counter = [];

  while ($row = $result->fetch_assoc()) {
    // Format data sesuai kebutuhan frontend
    $data[] = [
      "date" => $row['date'],
      "product" => $row['product'],
      "qty" => (int)$row['qty'],
      "harga_beli" => (int)$row['harga_beli'],
      "harga_jual" => (int)$row['harga_jual'],
      "subtotal" => (int)$row['subtotal'],
      "profit" => (int)$row['profit']
    ];

    // Hitung total omset dan laba
    $omset += $row['subtotal'];
    $laba += $row['profit'];

    // Hitung produk terlaris
    if (!isset($produk_counter[$row['product']])) {
      $produk_counter[$row['product']] = 0;
    }
    $produk_counter[$row['product']] += $row['qty'];
  }

  // Cari produk terlaris
  $produk_terlaris = "-";
  if (!empty($produk_counter)) {
    arsort($produk_counter);
    $produk_terlaris = array_key_first($produk_counter);
  }

  // Response JSON
  echo json_encode([
    "success" => true,
    "data" => $data,
    "summary" => [
      "omset" => $omset,
      "laba" => $laba,
      "produk_terlaris" => $produk_terlaris
    ]
  ]);

} catch (Exception $e) {
  // Handle error
  echo json_encode([
    "success" => false,
    "message" => "Terjadi kesalahan: " . $e->getMessage()
  ]);
}

$conn->close();
?>