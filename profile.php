<?php
session_start();

/* ---------- PROTECT PAGE ---------- */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* ---------- DB CONNECTION ---------- */
include 'db.php';

/* ---------- USER DATA ---------- */
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? "Chef";

/* ---------- STATS ---------- */
$sessions_attended = rand(8, 25);
$progress = min(100, ($sessions_attended / 30) * 100);

/* ---------- DATA ---------- */
$certificates = [
    "Global Cuisine Basics",
    "Italian Masterclass",
    "Advanced Plating Techniques"
];

$upcoming_sessions = [
    ["chef"=>"Chef Luca Romano","dish"=>"Truffle Pasta","icon"=>"🍝","days"=>2],
    ["chef"=>"Chef Kenji Sato","dish"=>"Sushi Rolling","icon"=>"🍣","days"=>5],
    ["chef"=>"Chef Marie Dupont","dish"=>"French Desserts","icon"=>"🍰","days"=>7]
];

$badges = [
    ["name"=>"🍝 Pasta Pro","progress"=>100],
    ["name"=>"🍣 Sushi Master","progress"=>80],
    ["name"=>"🍰 Dessert Expert","progress"=>60]
];

$avatar = "assets/images/pro_avatar.jpg";

/* ---------- FETCH USER MEMES ---------- */
$stmt = $conn->prepare("SELECT meme_path, created_at FROM memes WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$memes_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile | Global Platter</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
/* ================= GLOBAL ================= */
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background: radial-gradient(circle at top,#6b0000,#0a0000);
    color:#fff;
    overflow-x:hidden;
}
.container{max-width:1200px;margin:80px auto;padding:30px;}
.section{margin-top:50px;}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:25px;}

/* ================= NAVBAR ================= */
.navbar{
    position:fixed;
    top:0;
    width:100%;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 40px;
    background: rgba(184,115,51,0.9);
    backdrop-filter: blur(10px);
    border-bottom:2px solid gold;
    z-index:100;
}
.navbar .logo{
    font-size:22px;
    font-weight:bold;
}
.nav-links{
    display:flex;           
    list-style:none;        
    margin:0;
    padding:0;
    gap:20px;               
}
.nav-links li{
    display:inline-block;
}
.nav-links a{
    color:#fff;
    text-decoration:none;
    font-weight:600;
    white-space:nowrap;
    padding:6px 12px;
    transition: color 0.3s, background 0.3s;
}
.nav-links a:hover,
.nav-links a.active{
    color:#ffd700;
}

/* ================= HERO ================= */
.hero{
    display:flex;
    flex-wrap: wrap;
    align-items:center;
    gap:25px;
    background: rgba(255,255,255,0.08);
    padding:30px;
    border-radius:20px;
    box-shadow:0 25px 60px rgba(0,0,0,0.6);
}
.avatar{
    width:110px;
    height:110px;
    border-radius:50%;
    background:url('<?php echo $avatar; ?>') center/cover no-repeat;
}
.hero h1{
    font-size:36px;
    background:linear-gradient(90deg,#ffd700,#ffda75,#ffd700);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}
.hero p{opacity:0.9;}

/* ================= CARDS ================= */
.card{
    background:rgba(0,0,0,0.35);
    padding:25px;
    border-radius:18px;
    box-shadow:0 20px 50px rgba(0,0,0,0.6);
    transition:0.4s;
}
.card:hover{transform:translateY(-10px);}
.label{font-size:14px;color:#ffd700;}
.value{font-size:28px;font-weight:700;margin-top:8px;}

/* ================= PROGRESS ================= */
.progress-circle{
    position:relative;
    width:140px;
    height:140px;
    margin:auto;
}
.progress-circle svg{transform:rotate(-90deg);}
.progress-circle circle{fill:none;stroke-width:12;}
.bg{stroke:#2b0000;}
.fg{stroke:url(#grad);stroke-dasharray:314;stroke-dashoffset:314;transition:1.5s;}
.center{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    font-size:22px;
    font-weight:bold;
    color:#ffd700;
}

/* ================= UPCOMING ================= */
.session{text-align:center;font-size:22px;}
.session small{display:block;font-size:14px;opacity:0.8}

/* ================= CERTIFICATES ================= */
.cert li{
    list-style:none;
    background:rgba(255,215,0,0.08);
    border-left:4px solid gold;
    padding:10px;
    border-radius:8px;
    margin-bottom:10px;
}

/* ================= BADGES ================= */
.badge{margin-bottom:15px;}
.bar{
    height:8px;
    background:#2b0000;
    border-radius:10px;
    overflow:hidden;
    margin-top:5px;
}
.fill{height:100%;background:linear-gradient(90deg,#ffd700,#ffda75);}

/* ================= GOAL ================= */
.goal{
    background:linear-gradient(135deg,#b87333,#ffd700);
    color:#000;
    padding:25px;
    border-radius:18px;
    font-weight:bold;
    text-align:center;
}

/* ================= LOGOUT ================= */
.logout{
    display:inline-block;
    margin:60px auto 0;
    background:#ffd700;
    color:#000;
    padding:14px 35px;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
    text-align:center;
    transition: background 0.3s;
}
.logout:hover{background:#ffda75;}

/* ================= MY MEMES ================= */
.my-memes{
    margin-top:50px;
    padding:20px;
    background:rgba(0,0,0,0.35);
    border-radius:18px;
    box-shadow:0 20px 50px rgba(0,0,0,0.6);
}
.my-memes h2{
    color:#ffd700;
    border-bottom:2px solid gold;
    display:inline-block;
    padding-bottom:6px;
    margin-bottom:20px;
}
.meme-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}
.meme-card{
    background:#fff8e7;
    color:#2d2d2d;
    padding:10px;
    border-radius:12px;
    text-align:center;
}
.meme-card img{width:100%;border-radius:10px;}
.meme-card p{margin-top:5px;font-size:14px;color:#555;}
.no-memes{font-style:italic;color:#ddd;}
</style>
</head>
<body>
<header>
    <nav class="navbar">
      <!-- CLICKABLE LOGO -->
      <div class="logo">
        <a href="LandingPage.php" style="text-decoration:none; color:inherit;">
          <b>GLOBAL PLATTER</b>
        </a>
      </div>

      <ul class="nav-links">
        <li><a href="login.php">Login/Signup</a></li>
        <li><a href="profile.php" class="active">My Profile</a></li>
        <li><a href="Chefs.php">Chefs</a></li>
        <li><a href="meme_generator.php">Make a Meme</a></li>
        <li><a href="about.php">About Us</a></li>
      </ul>
    </nav>
</header>

<div class="container">

<!-- HERO -->
<div class="hero">
    <div class="avatar"></div>
    <div>
        <h1>Welcome back, <?php echo $username; ?> 👋</h1>
        <p>You’ve attended <?php echo $sessions_attended; ?> sessions so far — keep cooking 🔥</p>
    </div>
</div>

<!-- STATS -->
<div class="section grid">
<div class="card">
    <div class="label">Sessions Attended</div>
    <div class="value" id="count"><?php echo $sessions_attended; ?></div>
</div>

<div class="card">
    <div class="label">Learning Progress</div>
    <div class="progress-circle">
        <svg width="140" height="140">
            <defs>
                <linearGradient id="grad">
                    <stop offset="0%" stop-color="#ffd700"/>
                    <stop offset="100%" stop-color="#ffda75"/>
                </linearGradient>
            </defs>
            <circle class="bg" cx="70" cy="70" r="50"></circle>
            <circle class="fg" cx="70" cy="70" r="50"></circle>
        </svg>
        <div class="center"><?php echo intval($progress); ?>%</div>
    </div>
</div>

<div class="card">
    <div class="label">Upcoming Session</div>
    <?php $s=$upcoming_sessions[0]; ?>
    <div class="session">
        <?php echo $s['icon']; ?> <?php echo $s['dish']; ?>
        <small><?php echo $s['chef']; ?> • in <?php echo $s['days']; ?> days</small>
    </div>
</div>
</div>

<!-- NEXT GOAL -->
<div class="section goal">
🎯 Complete <b><?php echo max(0,30-$sessions_attended); ?></b> more sessions to unlock a new Chef Badge!
</div>

<!-- CERTIFICATES -->
<div class="section grid">
<div class="card">
    <div class="label">Certificates Earned</div>
    <ul class="cert">
        <?php foreach($certificates as $c): ?>
        <li>🏅 <?php echo $c; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
</div>

<!-- BADGES -->
<div class="section grid">
<div class="card">
    <div class="label">Chef Badges</div>
    <?php foreach($badges as $b): ?>
    <div class="badge">
        <?php echo $b['name']; ?> (<?php echo $b['progress']; ?>%)
        <div class="bar">
            <div class="fill" style="width:<?php echo $b['progress']; ?>%"></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</div>

<!-- MY MEMES SECTION -->
<div class="section my-memes">
    <h2>😂 My Memes</h2>
    <?php if($memes_result->num_rows>0): ?>
        <div class="meme-grid">
            <?php while($row = $memes_result->fetch_assoc()): ?>
            <div class="meme-card">
                <img src="<?php echo $row['meme_path']; ?>" alt="My Meme">
                <p>Created on: <?php echo date("d M Y", strtotime($row['created_at'])); ?></p>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="no-memes">You haven’t created any memes yet 😅</p>
    <?php endif; ?>
</div>

<!-- LOGOUT BUTTON -->
<a href="logout.php" class="logout">Logout</a>
</div>

<script>
/* COUNT UP */
let c=0,target=<?php echo $sessions_attended; ?>;
const el=document.getElementById('count');
const i=setInterval(()=>{c++;el.textContent=c;if(c>=target)clearInterval(i);},40);

/* PROGRESS CIRCLE */
const circle=document.querySelector('.fg');
const r=50,circ=2*Math.PI*r;
circle.style.strokeDasharray=circ;
circle.style.strokeDashoffset=circ;
setTimeout(()=>{circle.style.strokeDashoffset=circ-(<?php echo intval($progress); ?>/100)*circ;},500);
</script>
</body>
</html>
