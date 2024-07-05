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

$sql = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$bookResult = $stmt->get_result();
$book = $bookResult->fetch_assoc();

if (!$book) {
    die("Buku tidak ditemukan.");
}

$rental_price = $book['rental_price'];
$author_id = $book['author_id'];
$admin_fee = $rental_price * 0.30;
$author_income = $rental_price * 0.70;

$transaction_details = array(
    'order_id' => uniqid(),
    'gross_amount' => $rental_price,
);

$item_details = array(
    array(
        'id' => $book['id'],
        'price' => $rental_price,
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
  <link rel="stylesheet" type="text/css" href="../css/payment.css">
  <title>Pembayaran</title>
  <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-wGHeB_77sjrLoW2O"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .payment-container {
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      width: 100%;
      text-align: center;
    }
    .payment-container h1 {
      font-size: 24px;
      margin-bottom: 10px;
    }
    .payment-container p {
      font-size: 18px;
      margin-bottom: 20px;
    }
    .pay-button {
      background-color: #4CAF50;
      color: white;
      padding: 10px 20px;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .pay-button:hover {
      background-color: #45a049;
    }
    .alert {
      margin-top: 20px;
      padding: 10px;
      color: #fff;
      border-radius: 5px;
    }
    .alert-success {
      background-color: #4CAF50;
    }
    .alert-error {
      background-color: #f44336;
    }
  </style>
</head>
<body>
  <div class="payment-container">
    <h1>Pembayaran untuk "<?php echo htmlspecialchars($book['title']); ?>"</h1>
    <p>Harga sewa: Rp<?php echo number_format($book['rental_price'], 0, ',', '.'); ?></p>
    <button id="pay-button" class="pay-button">Bayar</button>
  </div>

  <script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
      snap.pay('<?php echo $snapToken; ?>', {
        onSuccess: function(result){
          const successAlert = document.createElement('div');
          successAlert.className = 'alert alert-success';
          successAlert.innerText = 'Pembayaran berhasil!';
          document.body.appendChild(successAlert);
          setTimeout(() => {
            window.location.href = 'payment_success.php?book_id=<?php echo $book_id; ?>&author_id=<?php echo $author_id; ?>&amount=<?php echo $author_income; ?>&admin_fee=<?php echo $admin_fee; ?>&result=' + encodeURIComponent(JSON.stringify(result));
          }, 2000);
        },
        onPending: function(result){
          alert('Menunggu pembayaran!');
          console.log(result);
        },
        onError: function(result){
          const errorAlert = document.createElement('div');
          errorAlert.className = 'alert alert-error';
          errorAlert.innerText = 'Pembayaran gagal!';
          document.body.appendChild(errorAlert);
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
