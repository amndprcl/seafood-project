<?php
include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:home.php');
    exit();
}

// Mendapatkan total harga dari parameter URL
$total_price = isset($_GET['total_price']) ? (int)$_GET['total_price'] : 0;

// Ambil data pengguna dari database
$select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_user->execute([$user_id]);
$user = $select_user->fetch(PDO::FETCH_ASSOC);

// Konfigurasi Midtrans
require_once __DIR__ . '/midtrans/Midtrans.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-vLBQbljb-H0ro1b0pHjfW2VB';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Membuat ID transaksi unik
$transaction_id = 'ORDER-' . time();

// Set up parameter untuk Midtrans
$params = array(
    'transaction_details' => array(
        'order_id' => $transaction_id,
        'gross_amount' => $total_price,
    ),
    'customer_details' => array(
        'first_name' => $user['name'],
        'email' => $user['email'],
        'phone' => isset($user['number']) ? $user['number'] : '',
    ),
);

try {
    // Mendapatkan Snap Token
    $snapToken = \Midtrans\Snap::getSnapToken($params);
} catch (\Exception $e) {
    echo $e->getMessage();
    exit;
}

// Fungsi untuk mengupdate status pembayaran
function updatePaymentStatus($orderId, $status) {
    global $conn;
    $update_order = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
    $update_order->execute([$status, $orderId]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment with Midtrans</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-ZGt6-1ZaJYBV1AgN"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include 'components/user_header.php'; ?>

<div class="heading">
    <h3>Payment with Midtrans</h3>
    <p><a href="home.php">Home</a> <span> / Payment</span></p>
</div>

<section class="payment-info">
    <h2 class="payment-title">Payment Details</h2>
    <p class="total-amount">Total Amount: <span>Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span></p>
    <button id="pay-button" class="pay-button">Pay Now</button>
</section>

<?php include 'components/footer.php'; ?>

<script type="text/javascript">
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        snap.pay('<?php echo $snapToken; ?>', {
            onSuccess: function(result){
                alert("Payment success!");
                console.log(result);
                // Panggil fungsi untuk mengupdate status pembayaran
                updatePaymentStatus(result.order_id, 'success');
                window.location.href = 'order_success.php?order_id=' + result.order_id;
            },
            onPending: function(result){
                alert("Waiting for your payment!");
                console.log(result);
            },
            onError: function(result){
                alert("Payment failed!");
                console.log(result);
            },
            onClose: function(){
                alert('You closed the popup without finishing the payment');
            }
        });
    });

    function updatePaymentStatus(orderId, status) {
        $.ajax({
            url: 'update_payment_status.php',
            method: 'POST',
            data: {
                order_id: orderId,
                status: status
            },
            success: function(response) {
                console.log('Payment status updated successfully');
            },
            error: function(xhr, status, error) {
                console.error('Error updating payment status:', error);
            }
        });
    }
</script>

<script src="js/script.js"></script>
</body>
</html>
