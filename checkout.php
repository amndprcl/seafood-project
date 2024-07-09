<?php
// Aktifkan error reporting untuk membantu debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fungsi untuk logging
function debug_log($message) {
    error_log("[DEBUG] " . $message);
}

debug_log("Memulai proses checkout dan pembayaran");

// Koneksi database dan inisialisasi session
include 'components/connect.php';
session_start();

// Cek apakah user sudah login
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:home.php');
    exit();
}

debug_log("User ID: " . $user_id);

// Variabel untuk notifikasi
$notification = '';

// Proses form checkout jika di-submit
if (isset($_POST['submit'])) {
    debug_log("Form checkout di-submit");
    
    // Sanitasi input
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $total_products = $_POST['total_products'];
    $total_price = $_POST['total_price'];

    // Cek apakah keranjang kosong
    $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
    $check_cart->execute([$user_id]);

    if ($check_cart->rowCount() > 0) {
        if (empty($address)) {
            $message[] = 'Silakan tambahkan alamat Anda!';
        } else {
            try {
                $conn->beginTransaction();

                // Masukkan order ke database
                $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
                $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);

                // Hapus item dari keranjang
                $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
                $delete_cart->execute([$user_id]);

                $conn->commit();
                $message[] = 'Pesanan berhasil dibuat!';

                // Jika metode pembayaran adalah COD, tampilkan notifikasi tanpa redirect
                if ($method == 'cash on delivery') {
                    $notification = 'Pesanan telah terkirim';
                }

                // Redirect ke proses pembayaran jika metode adalah Qriss atau Bank BCA
                if ($method == 'Qriss' || $method == 'Bank BCA') {
                    debug_log("Redirect ke payment_info.php untuk pembayaran");
                    header("Location: payment_info.php?total_price=$total_price");
                    exit();
                }
            } catch (Exception $e) {
                $conn->rollBack();
                debug_log("Error dalam transaksi: " . $e->getMessage());
                $message[] = 'Terjadi kesalahan saat memproses pesanan Anda. Silakan coba lagi.';
            }
        }
    } else {
        $message[] = 'Keranjang Anda kosong';
        debug_log("Keranjang kosong");
    }
}

// Ambil item keranjang
$grand_total = 0;
$cart_items = [];
$select_cart = $conn->prepare("SELECT c.*, p.name, p.price FROM `cart` c JOIN `products` p ON c.pid = p.id WHERE c.user_id = ?");
$select_cart->execute([$user_id]);

debug_log("Mengambil item keranjang");

if($select_cart->rowCount() > 0){
    while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
        $cart_items[] = $fetch_cart['name'].' ('.($fetch_cart['price'] * $fetch_cart['quantity']).')';
        $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
    }
    $total_products = implode(', ', $cart_items);
    $formatted_grand_total = "Rp " . number_format($grand_total, 0, ',', '.');
    debug_log("Item keranjang diproses. Total: " . $formatted_grand_total);
} else {
    $total_products = '';
    $formatted_grand_total = "Rp 0";
    debug_log("Keranjang kosong");
}

debug_log("Proses checkout dan pembayaran selesai");
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'components/user_header.php'; ?>

<div class="heading">
   <h3>Checkout</h3>
   <p><a href="home.php">Home</a> <span> / Checkout</span></p>
</div>

<section class="checkout">
   <h1 class="title">Order Summary</h1>

   <?php if (!empty($notification)): ?>
   <div class="notification">
      <p><?= htmlspecialchars($notification); ?></p>
   </div>
   <?php endif; ?>

   <form action="" method="post">
      <div class="cart-items">
         <h3>Cart Items</h3>
         <?php
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $formatted_price = "Rp. " . number_format($fetch_cart['price'], 0, ',', '.');
               $formatted_total = "Rp. " . number_format(($fetch_cart['price'] * $fetch_cart['quantity']), 0, ',', '.');
         ?>
         <p>
            <span class="name"><?= htmlspecialchars($fetch_cart['name']); ?></span>
            <span class="price"><?= $formatted_price; ?> x <?= htmlspecialchars($fetch_cart['quantity']); ?></span>
         </p>
         <?php
            }
         ?>
         <p class="grand-total">
            <span class="name">Jumlah total :</span>
            <span class="price"><?= $formatted_grand_total; ?></span>
         </p>
         <?php
         } else {
            echo '<p class="empty">Keranjang Anda kosong!</p>';
         }
         ?>
      </div>
      
      <input type="hidden" name="total_products" value="<?= htmlspecialchars($total_products); ?>">
      <input type="hidden" name="total_price" value="<?= $grand_total; ?>">
      <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_profile['name']); ?>">
      <input type="hidden" name="number" value="<?= htmlspecialchars($fetch_profile['number']); ?>">
      <input type="hidden" name="email" value="<?= htmlspecialchars($fetch_profile['email']); ?>">
      <input type="hidden" name="address" value="<?= htmlspecialchars($fetch_profile['address']); ?>">
      
      <div class="user-info">
         <h3>Your Info</h3>
         <p><i class="fas fa-user"></i><span><?= htmlspecialchars($fetch_profile['name']); ?></span></p>
         <p><i class="fas fa-phone"></i><span><?= htmlspecialchars($fetch_profile['number']); ?></span></p>
         <p><i class="fas fa-envelope"></i><span><?= htmlspecialchars($fetch_profile['email']); ?></span></p>
         <a href="update_profile.php" class="btn">Update Info</a>
         <h3>Delivery Address</h3>
         <p><i class="fas fa-map-marker-alt"></i><span><?php if($fetch_profile['address'] == ''){echo 'Please enter your address';}else{echo htmlspecialchars($fetch_profile['address']);} ?></span></p>
         <a href="update_address.php" class="btn">Update Address</a>
         <select name="method" class="box" required>
            <option value="" disabled selected>Select Payment Method --</option>
            <option value="cash on delivery">Cash on Delivery</option>
            <option value="Qriss">Qriss</option>
            <option value="Bank BCA">Bank BCA</option>
         </select>
         <input type="submit" value="Place Order" class="btn <?php if($fetch_profile['address'] == ''){echo 'disabled';} ?>" style="width:100%; background:var(--red); color:var(--white);" name="submit">
      </div>
   </form>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
