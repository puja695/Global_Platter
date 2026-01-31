<?php
session_start();

/* ---------- PROTECT CHEF PAGE ---------- */
if (!isset($_SESSION['chef_id'])) {
    header("Location: login.php");
    exit;
}

$chef_name = $_SESSION['chef_name'] ?? "Chef";

/* ---------- RANDOM BOOKINGS ---------- */
$random_bookings = [
    ["user"=>"Amit","dish"=>"Truffle Pasta","date"=>"2 days ago"],
    ["user"=>"Sam","dish"=>"Sushi Platter","date"=>"4 days ago"],
    ["user"=>"Rebeka","dish"=>"Mexican Tacos","date"=>"1 week ago"],
    ["user"=>"Nathan","dish"=>"French Desserts","date"=>"5 days ago"],
    ["user"=>"Kate","dish"=>"Butter Chicken","date"=>"3 days ago"]
];

/* ---------- RANDOM RATINGS & REVIEWS ---------- */
$sample_reviews = [
    ["name"=>"Aarav","rating"=>5,"review"=>"Outstanding food and presentation!"],
    ["name"=>"Charlie","rating"=>4,"review"=>"Loved the flavors, will book again!"],
    ["name"=>"Talwiinder","rating"=>5,"review"=>"One of the best chefs I’ve experienced."],
    ["name"=>"Peter","rating"=>4,"review"=>"Very professional and tasty dishes."],
    ["name"=>"Karina","rating"=>3,"review"=>"Good, but room for improvement."],
    ["name"=>"Samuel","rating"=>5,"review"=>"Absolutely amazing service!"],
    ["name"=>"Victor","rating"=>4,"review"=>"Great cooking and hygiene."]
];

shuffle($sample_reviews);
$random_reviews = array_slice($sample_reviews, 0, 3);
$avg_rating = array_sum(array_column($random_reviews, 'rating')) / count($random_reviews);

/* ---------- RANDOM STATS ---------- */
$total_bookings = rand(15, 60);
$experience_years = rand(1, 10);

$avatar = "assets/images/chef_avatar.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chef Profile | Global Platter</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background: radial-gradient(circle at top,#3b0000,#0a0000);
    color:#fff;
}
.container{max-width:1200px;margin:80px auto;padding:30px;}
.section{margin-top:50px;}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:25px;}

.navbar{
    position:fixed;top:0;width:100%;
    display:flex;justify-content:space-between;align-items:center;
    padding:16px px;background: rgba(184,115,51,0.9);
    backdrop-filter: blur(10px);border-bottom:2px solid gold;z-index:100;
}
.logo{font-size:22px;font-weight:bold;}
.nav-links{display:flex;gap:20px;list-style:none;}
.nav-links a{color:#fff;text-decoration:none;font-weight:600;}
.nav-links a.active{color:#ffd700;}

.hero{
    display:flex;flex-wrap:wrap;align-items:center;gap:25px;
    background: rgba(255,255,255,0.08);
    padding:30px;border-radius:20px;
}
.avatar{
    width:110px;height:110px;border-radius:50%;
    background:url('<?php echo $avatar; ?>') center/cover no-repeat;
}
.hero h1{
    font-size:34px;
    background:linear-gradient(90deg,#ffd700,#ffda75,#ffd700);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

.card{
    background:rgba(0,0,0,0.35);
    padding:25px;border-radius:18px;
    box-shadow:0 20px 50px rgba(0,0,0,0.6);
}
.label{font-size:14px;color:#ffd700;}
.value{font-size:28px;font-weight:700;margin-top:8px;}

.list-item{
    padding:10px;
    border-bottom:1px solid rgba(255,255,255,0.1);
}
.list-item:last-child{border:none;}

.rating-stars{
    color:gold;font-size:18px;
}

.logout{
    display:inline-block;margin:60px auto 0;
    background:#ffd700;color:#000;
    padding:14px 35px;border-radius:10px;
    text-decoration:none;font-weight:bold;
}
</style>
</head>
<body>

<header>
<nav class="navbar">
    <div class="logo">
        <a href="LandingPage.php" style="color:inherit;text-decoration:none;">GLOBAL PLATTER</a>
    </div>
    <ul class="nav-links">
        <li><a href="chef_profile.php" class="active">My Profile</a></li>
        
        
    </ul>
</nav>
</header>

<div class="container">

<!-- HERO -->
<div class="hero">
    <div class="avatar"></div>
    <div>
        <h1>Welcome Chef <?php echo htmlspecialchars($chef_name); ?> 👨‍🍳</h1>
        <p><?php echo $experience_years; ?>+ years of culinary excellence</p>
    </div>
</div>

<!-- STATS -->
<div class="section grid">
<div class="card">
    <div class="label">Total Bookings</div>
    <div class="value"><?php echo $total_bookings; ?></div>
</div>

<div class="card">
    <div class="label">Average Rating</div>
    <div class="value"><?php echo number_format($avg_rating,1); ?> ⭐</div>
</div>
</div>

<!-- RECENT BOOKINGS -->
<div class="section grid">
<div class="card">
    <div class="label">Recent Bookings</div>
    <?php foreach(array_slice($random_bookings,0,4) as $b): ?>
        <div class="list-item">
            🍽 <?php echo $b['dish']; ?>
            <small>by <?php echo $b['user']; ?> • <?php echo $b['date']; ?></small>
        </div>
    <?php endforeach; ?>
</div>

<!-- RATINGS -->
<div class="card">
    <div class="label">Recent Ratings</div>
    <?php foreach($random_reviews as $r): ?>
        <div class="list-item">
            <div class="rating-stars">
                <?php echo str_repeat("⭐", $r['rating']); ?>
            </div>
            <small>
                "<?php echo $r['review']; ?>" — by <?php echo $r['name']; ?>
            </small>
        </div>
    <?php endforeach; ?>
</div>
</div>

<a href="logout.php" class="logout">Logout</a>
</div>

</body>
</html>
