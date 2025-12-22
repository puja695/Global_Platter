<?php 
include 'db.php'; // database connection

$message = "";

// For demo, assume logged-in user is ID=1
$user_id = 1;

// Get chef info from URL
$chef_name = $_GET['chef'] ?? '';
$chef_cost = $_GET['cost'] ?? '';

// Handle Add Card form
if (isset($_POST['add_card'])) {
    $card_number = $_POST['card_number'];
    $card_type   = $_POST['card_type'];
    $expiry_date = $_POST['expiry_date'];

    $masked = "**** **** **** " . substr($card_number, -4);
    $extra  = strtoupper($card_type) . " - " . $expiry_date;

    $stmt = $conn->prepare("INSERT INTO payments (user_id, payment_type, details, extra_info) VALUES (?, 'card', ?, ?)");
    $stmt->bind_param("iss", $user_id, $masked, $extra);

    $message = $stmt->execute() ? "✅ Card saved successfully!" : "❌ Error: " . $stmt->error;
}

// Handle Add UPI form
if (isset($_POST['add_vpa'])) {
    $vpa = $_POST['vpa'];

    $stmt = $conn->prepare("INSERT INTO payments (user_id, payment_type, details) VALUES (?, 'vpa', ?)");
    $stmt->bind_param("is", $user_id, $vpa);

    $message = $stmt->execute() ? "✅ UPI ID saved successfully!" : "❌ Error: " . $stmt->error;
}

// Fetch Cards
$cards = $conn->query("SELECT * FROM payments WHERE user_id=$user_id AND payment_type='card'");

// Fetch VPAs
$vpas = $conn->query("SELECT * FROM payments WHERE user_id=$user_id AND payment_type='vpa'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payments - Global Platter</title>
<style>
body { font-family: 'Poppins', sans-serif; background: #fafafa; margin: 20px; }
.container { background: #fff; padding: 25px; border-radius: 10px; max-width: 700px; margin: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
h1, h2 { text-align: center; color: #333; }
h2 { border-bottom: 2px solid #ffa500; padding-bottom: 5px; margin-top: 25px; }
.payment-info { text-align:center; margin-bottom:20px; font-size:18px; background:#fff3e6; padding:15px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
.item { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid #eee; cursor:pointer; }
.delete { color:red; cursor:pointer; }
form { margin-top:15px; }
input, select, button { padding:10px; margin:6px 0; width:100%; border:1px solid #ccc; border-radius:5px; font-size:14px; }
button { background: linear-gradient(90deg,#ff8c00,#ffd700); color: #000; border: none; cursor: pointer; font-weight: bold; transition: 0.3s; }
button:hover { transform:scale(1.05); }
.msg { margin: 10px 0; font-weight: bold; color: green; text-align: center; }
.upi-options { display:flex; justify-content:space-around; align-items:center; flex-wrap:wrap; margin-top:15px; gap:15px; }
.upi-options img { width:70px; height:70px; object-fit:contain; transition:0.3s; border-radius:8px; cursor:pointer; }
.upi-options img:hover { transform: scale(1.1); background:#ffe5b4; }
.banks, .paylater { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:10px; }
.bank, .later { border:1px solid #ddd; padding:10px; text-align:center; border-radius:5px; cursor:pointer; transition:0.3s; }
.bank:hover, .later:hover { background:#ffe5b4; }
.back-btn { display:inline-block; padding:12px 25px; background: #800000; color:#fff; font-weight:bold; border-radius:8px; text-decoration:none; transition:0.3s; margin-top:30px; }
.back-btn:hover { background:#a00000; }
</style>
</head>
<body>
<div class="container">
<h1>Payment Methods</h1>

<?php if($chef_name && $chef_cost): ?>
<div class="payment-info">
💰 You are booking <b><?php echo htmlspecialchars($chef_name); ?></b><br>
Total Cost: <b>₹<?php echo htmlspecialchars($chef_cost); ?></b>
</div>
<?php endif; ?>

<?php if($message != ""): ?>
<p class="msg"><?php echo $message; ?></p>
<?php endif; ?>

<h2>Pay by UPI</h2>
<div class="upi-options">
    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRl1tsNbn9_XqAUgNcbCYCmJ1absNIlyqOa3g&s" alt="GPay" onclick="confirmPayment('UPI - GPay')">
    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTVf6nm-Dk1ELbj1aBfeN_vJH6R1wAqYWnltw&s" alt="PhonePe" onclick="confirmPayment('UPI - PhonePe')">
    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQuf5KuJA7uACyaOETjfiAX3FYTL6BGdYr2ZA&s" alt="Paytm" onclick="confirmPayment('UPI - Paytm')">
    <img src="https://www.presentations.gov.in/wp-content/uploads/2020/06/BHIM_Preview.png" alt="BHIM" onclick="confirmPayment('UPI - BHIM')">
</div>

<?php if ($vpas->num_rows > 0) { 
    while($row = $vpas->fetch_assoc()) { ?>
<div class="item" onclick="confirmPayment('UPI - <?php echo $row['details']; ?>')">
    <span>🟢 <?php echo $row['details']; ?></span>
</div>
<?php } } else { ?>
<p>No UPI IDs saved yet.</p>
<?php } ?>

<form method="POST" action="">
<input type="text" name="vpa" placeholder="Enter UPI ID (e.g. username@upi)" required>
<button type="submit" name="add_vpa">+ Add UPI ID</button>
</form>

<h2>Cards</h2>
<?php if ($cards->num_rows > 0) { 
    while($row = $cards->fetch_assoc()) { ?>
<div class="item" onclick="confirmPayment('Card - <?php echo $row['details']; ?>')">
    <span>💳 <?php echo $row['details']; ?> (<?php echo $row['extra_info']; ?>)</span>
</div>
<?php } } else { ?>
<p>No cards saved yet.</p>
<?php } ?>

<form method="POST" action="">
<input type="text" name="card_number" placeholder="Enter Card Number" required>
<select name="card_type" required>
    <option value="">Select Card Type</option>
    <option value="Visa">Visa</option>
    <option value="MasterCard">MasterCard</option>
    <option value="Rupay">Rupay</option>
</select>
<input type="text" name="expiry_date" placeholder="MM/YY" required>
<button type="submit" name="add_card">+ Add Card</button>
</form>

<h2>Netbanking</h2>
<p>Select your bank to proceed:</p>
<div class="banks">
    <div class="bank" onclick="confirmPayment('Netbanking - HDFC Bank')">🏦 HDFC Bank</div>
    <div class="bank" onclick="confirmPayment('Netbanking - ICICI Bank')">🏦 ICICI Bank</div>
    <div class="bank" onclick="confirmPayment('Netbanking - SBI Bank')">🏦 SBI Bank</div>
    <div class="bank" onclick="confirmPayment('Netbanking - Axis Bank')">🏦 Axis Bank</div>
    <div class="bank" onclick="confirmPayment('Netbanking - Kotak Bank')">🏦 Kotak Bank</div>
    <div class="bank" onclick="confirmPayment('Netbanking - PNB Bank')">🏦 PNB Bank</div>
</div>

<h2>Pay Later</h2>
<p>Use Pay Later options for convenient deferred payments:</p>
<div class="paylater">
    <div class="later" onclick="confirmPayment('Simpl Pay Later')">🕒 Simpl Pay Later</div>
    <div class="later" onclick="confirmPayment('LazyPay')">🕒 LazyPay</div>
    <div class="later" onclick="confirmPayment('ICICI PayLater')">🕒 ICICI PayLater</div>
    <div class="later" onclick="confirmPayment('HDFC FlexiPay')">🕒 HDFC FlexiPay</div>
</div>

<!-- Back to Chefs Button -->
<div style="text-align:center;">
    <a href="Chefs.php" class="back-btn">⬅ Back to Chefs</a>
</div>

<script>
function confirmPayment(method){
    let chef = "<?php echo addslashes($chef_name); ?>";
    let cost = "<?php echo addslashes($chef_cost); ?>";
    if(chef && cost){
        alert(`✅ Payment Completed: ${method}\nYou are paying ₹${cost} for booking ${chef}!`);
    } else {
        alert(`✅ Payment Method: ${method}\nYou have not selected a chef.`);
    }
}
</script>

</div>
</body>
</html>
