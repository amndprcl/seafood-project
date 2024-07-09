<?php
include 'components/connect.php';

// Mengambil parameter dari URL
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
$payment_status = isset($_GET['payment_status']) ? $_GET['payment_status'] : '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

if (!empty($user_id) && !empty($payment_status)) {
    // Memperbarui status pesanan
    updateOrderStatus($user_id, $payment_status);

    // Jika redirect parameter ada dan bernilai 'home', arahkan ke home.php
    if ($redirect === 'home' && $payment_status === 'success') {
        header('Location: home.php');
        exit();
    } else {
        header('Location: home.php');
        exit();
    }
} else {
    header('Location: cart.php');
    exit();
}

function updateOrderStatus($user_id, $status) {
    global $conn;
    $update_order = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE user_id = ?");
    $update_order->execute([$status, $user_id]);
}
?>
