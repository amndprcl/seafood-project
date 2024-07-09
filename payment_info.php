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

function updateOrderStatus($order_id, $status) {
    global $conn;
    $update_order = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
    $update_order->execute([$status, $order_id]);
}

// Get total price from URL parameter
$total_price = isset($_GET['total_price']) ? (float)$_GET['total_price'] : 0.00;
// Debug: Log received total_price
error_log("Received total_price: " . $total_price);

// Validate total_price (must be at least 0.01)
if ($total_price < 0.01) {
    showErrorAndRedirect("Error: Total price must be at least 0.01. Received: $total_price", 'cart.php');
}

// Convert total_price to format expected by Midtrans (no conversion to sen)
$gross_amount = $total_price;

// Debug: Log calculated gross_amount
error_log("Calculated gross_amount: " . $gross_amount);

// Fetch user data from database
$select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_user->execute([$user_id]);
$user = $select_user->fetch(PDO::FETCH_ASSOC);

// Configure Midtrans
require_once __DIR__ . '/midtrans/Midtrans.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-vLBQbljb-H0ro1b0pHjfW2VB';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Generate unique transaction ID
$transaction_id = 'ORDER-' . time();

// Set parameters for Midtrans
$params = array(
    'transaction_details' => array(
        'order_id' => $transaction_id,
        'gross_amount' => $gross_amount,
    ),
    'customer_details' => array(
        'first_name' => $user['name'],
        'email' => $user['email'],
        'phone' => isset($user['number']) ? $user['number'] : '',
    ),
);

try {
    // Get Snap Token from Midtrans
    $snapToken = \Midtrans\Snap::getSnapToken($params);
} catch (\Exception $e) {
    error_log("Midtrans Error: " . $e->getMessage());
    showErrorAndRedirect("Payment error: " . $e->getMessage(), 'cart.php');
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
    var snapToken = '<?php echo $snapToken; ?>';

    payButton.addEventListener('click', function () {
        snap.pay('<?php echo $snapToken; ?>', {
            onSuccess: function(result){
                alert("Pembayaran berhasil!");
                window.location.href = 'order_success.php?user_id=<?php echo $user_id; ?>&payment_status=success&redirect=home';
            },
            onPending: function(result){
                alert("Menunggu pembayaran Anda!");
                window.location.href = 'order_success.php?user_id=<?php echo $user_id; ?>&payment_status=pending';
            },
            onError: function(result){
                alert("Pembayaran gagal!");
                window.location.href = 'order_success.php?user_id=<?php echo $user_id; ?>&payment_status=failed';
            },
            onClose: function(){
                alert('Anda menutup popup tanpa menyelesaikan pembayaran');
            }
        });
    });
</script>

<script src="js/script.js"></script>
</body>
</html>
