<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pembaca') {
    header('Location: ../index.php');
    exit();
}

require_once '../vendor/autoload.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-GU546525R_oLjPN6KMSDedER';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ilib3";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$user_id = $_SESSION['user_id'];

// Get book details
$sql = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$bookResult = $stmt->get_result();
$book = $bookResult->fetch_assoc();

if (!$book) {
    die("Buku tidak ditemukan.");
}

// Prepare transaction details
$transaction_details = array(
    'order_id' => uniqid(),
    'gross_amount' => $book['rental_price'],
);

// Optional
$item_details = array(
    array(
        'id' => $book['id'],
        'price' => $book['rental_price'],
        'quantity' => 1,
        'name' => $book['title']
    )
);

$customer_details = array(
    'first_name' => $_SESSION['username'],
    'email' => $_SESSION['email'],
);

$transaction = array(
    'transaction_details' => $transaction_details,
    'customer_details' => $customer_details,
    'item_details' => $item_details,
);

$snapToken = \Midtrans\Snap::getSnapToken($transaction);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pembayaran</title>
  <link rel="stylesheet" type="text/css" href="../css/payment.css">
  <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="YOUR_CLIENT_KEY"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .payment-container {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
    }
    .payment-container h1 {
      font-size: 24px;
      margin-bottom: 20px;
    }
    .payment-container p {
      font-size: 18px;
      margin-bottom: 20px;
    }
    .payment-container button {
      background-color: #4CAF50;
      color: white;
      padding: 15px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }
    .payment-container button:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
  <div class="payment-container">
    <h1>Pembayaran untuk "<?php echo htmlspecialchars($book['title']); ?>"</h1>
    <p>Harga sewa: Rp <?php echo number_format($book['rental_price'], 0, ',', '.'); ?></p>
    <button id="pay-button">Bayar</button>
  </div>

  <script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
      snap.pay('<?php echo $snapToken; ?>', {
        onSuccess: function(result){
          window.location.href = 'payment_success.php?book_id=<?php echo $book_id; ?>&result=' + encodeURIComponent(JSON.stringify(result));
        },
        onPending: function(result){
          alert('Menunggu pembayaran!');
          console.log(result);
        },
        onError: function(result){
          alert('Pembayaran gagal!');
          console.log(result);
        },
        onClose: function(){
          alert('Anda menutup popup tanpa menyelesaikan pembayaran.');
        }
      });
    };
  </script>
</body>
</html>
