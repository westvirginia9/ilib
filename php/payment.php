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
  <title>Pembayaran</title>
  <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-wGHeB_77sjrLoW2O"></script>
</head>
<body>
  <h1>Pembayaran untuk "<?php echo htmlspecialchars($book['title']); ?>"</h1>
  <p>Harga sewa: <?php echo htmlspecialchars($book['rental_price']); ?></p>
  <button id="pay-button">Bayar</button>

  <script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
      snap.pay('<?php echo $snapToken; ?>', {
        onSuccess: function(result){
          window.location.href = 'payment_success.php?book_id=<?php echo $book_id; ?>&author_id=<?php echo $author_id; ?>&amount=<?php echo $author_income; ?>&admin_fee=<?php echo $admin_fee; ?>&result=' + encodeURIComponent(JSON.stringify(result));
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
