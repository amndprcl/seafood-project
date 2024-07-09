<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
   exit();
}

$message = [];

if(isset($_POST['submit'])){
   $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
   $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
   $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_STRING);

   if(!empty($name) && !empty($address) && !empty($number)) {
      if(strlen($name) > 50 || strlen($address) > 100 || strlen($number) > 20) {
         $message[] = 'Input terlalu panjang!';
      } elseif(!preg_match("/^(\+62|62|0)8[1-9][0-9]{6,9}$/", $number)) {
         $message[] = 'Format nomor telepon tidak valid! Gunakan format 08xx, 62xx, atau +62xx.';
      } else {
         // Ubah format nomor telepon ke 62xxxxxxxxxx
         $number = preg_replace("/^(\+62|62|0)/", "62", $number);
         
         try {
            $update_address = $conn->prepare("UPDATE `users` SET name = ?, address = ?, number = ? WHERE id = ?");
            $result = $update_address->execute([$name, $address, $number, $user_id]);
            
            if($result) {
               $message[] = 'Data berhasil diperbarui!';
            } else {
               $error_info = $update_address->errorInfo();
               error_log("Database error: " . implode(", ", $error_info));
               $message[] = 'Gagal memperbarui data. Error: ' . $error_info[2];
            }
         } catch(PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            $message[] = 'Terjadi kesalahan database: ' . $e->getMessage();
         }
      }
   } else {
      $message[] = 'Mohon isi semua field!';
   }
}

// Ambil data user yang ada
try {
    $select_user = $conn->prepare("SELECT name, address, number FROM `users` WHERE id = ?");
    $select_user->execute([$user_id]);
    $user_data = $select_user->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    $message[] = 'Gagal mengambil data user.';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Data</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'components/user_header.php' ?>

<section class="form-container">
   <?php
   if(is_array($message) && !empty($message)){
      foreach($message as $msg){
         echo '<div class="message">'.htmlspecialchars($msg).'</div>';
      }
   }
   ?>

   <form action="" method="post">
      <h3>Update Data Anda</h3>
      <input type="text" class="box" placeholder="Masukkan Nama Anda" required maxlength="50" name="name" value="<?php echo htmlspecialchars($user_data['name'] ?? ''); ?>">
      <input type="text" class="box" placeholder="Masukkan Alamat Lengkap Anda" required maxlength="100" name="address" value="<?php echo htmlspecialchars($user_data['address'] ?? ''); ?>">
      <input type="tel" class="box" placeholder="Masukkan Nomor Telepon Anda (contoh: 081234567890)" required maxlength="20" name="number" value="<?php echo htmlspecialchars($user_data['number'] ?? ''); ?>">
      <input type="submit" value="Simpan Data" name="submit" class="btn">
   </form>
</section>

<?php include 'components/footer.php' ?>

<script src="js/script.js"></script>

</body>
</html>