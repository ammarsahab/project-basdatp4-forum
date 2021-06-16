<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include("connect.php");
if(isset($_POST['Jadwalkan'])){
    $issue=$_POST['issue'];
}else{
    $issue=$_GET['issue'];
}
//sangat mirip seperti form pendaftara sebelumnya
$issuedata=pg_prepare("issuedata", 'SELECT * from issues where issue_id=$1');
$issuedata=pg_execute("issuedata",array($issue));
$isdata=pg_fetch_array($issuedata);
$time = $platform=$link="";
$time_err=$platform_err=$link_err="";
if(isset($_POST['Jadwalkan'])){
    if(empty(trim($_POST["waktu"]))){
        $time_err = "Waktu tidak boleh kosong.";
    }
    else{
    $time=$_POST['waktu'];
    }
    }    
    if(empty($_POST["platform"])){
        $platform_err = "Harus ada platform.";
    } 
    else{
        $platform = $_POST["platform"];
    }   
    if(empty($_POST["address"])){
        $link_err = "Harus ada alamat atau tautan.";
    } 
    else{
        $link = $_POST["address"];
    }   
    if(empty($time_err) && empty($platform_err)&& empty($link_err)){
        $facilitator=$_SESSION["id"];
        $insert=pg_prepare("insert", 'INSERT INTO konsultasi(issue_id,waktu_konsultasi,platform_konsultasi
        ,alamat_tautan_konsultasi,facil_id) values($1,$2,$3,$4,$5)');
        $insert=pg_execute("insert",array($issue,$time,$platform,$link,$facilitator));
        header('Location: consul.php?status=sukses&issue='.$issue.'');
        exit;
        }

?>

<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="bootstrap-5.0.1-dist/css/bootstrap.css"/>
    <script src="bootstrap-5.0.1-dist/js/bootstrap.bundle.js"></script>
    <title>Konsultasi</title>
<body>
<header>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="#">Forum</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="home.php">Home</a>
        </li>
        <li class="nav-item dropdown"> 
        <a class="nav-link dropdown-toggle" href="issues.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Isu
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="approvedissues.php">Sudah disetujui</a></li>
            <li><a class="dropdown-item" href="unapprovedissues.php">Belum disetujui</a></li>
            <li><a class="dropdown-divider"></a></li>
            <li><a class="dropdown-item" href="issuereg.php">Ajukan Isu</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
        <!-- dibuat dropdown baru di navbar untuk isu ini -->
        <a class="nav-link dropdown-toggle" href=# id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?php echo($isdata['issue_name']);?>
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="polls.php?issue=<?php echo $_GET['issue'];?>">Survei</a></li>
            <li><a class="dropdown-item" href="topics.php?issue=<?php echo $_GET['issue'];?>">Forum</a></li>
            <li><a class="dropdown-item" href="consul.php?issue=<?php echo $_GET['issue'];?>">Konsultasi</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="users.php">Pengguna</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">Profil</a>
        </li>
        <li class="nav-item dropdown"> 
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Akun
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="logout.php">Log out</a></li>
            <li><a class="dropdown-item" href="resetpassword.php">Reset sandi</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
</header>
<div class="container">
<h2 class="text-center">Buat konsultasi untuk <?php echo($isdata['issue_name']);?></h2><br/>
<div class="card bg-light">
<div class="card-body">
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
	<div class='d-none'>
    <label for="Issue">Issue</label>
    <input type="number" name="issue" name="issue" value=<?php
    if(isset($issue)){
        echo $issue;
    }else{
        echo $_GET['issue'];
    }
    ?>>
    </div>
    <div class="form-group">
    <label for="waktu">Waktu Konsultasi:</label>
    <input class="form-control" type="datetime-local" id="waktu" name="waktu">
    <div class="form-text"><?php echo $time_err;?></div>
    </div>
    <div class="mb-3">
    <label for="platform">Platform Konsultasi</label>
    <input type="text" class="form-control" name="platform" placeholder="platform"></textarea>
    <div class="form-text"><?php echo $platform_err;?></div>
    </div>
    <div class="mb-3">
    <label for="address">Tautan/Alamat Konsultasi</label>
    <input class="form-control" type="text" name="address" placeholder="address"></textarea>
    <div class="form-text"><?php echo $link_err;?></div>
    </div>
    <br>
    <button class="btn btn-success" type="submit" value="Jadwalkan" name="Jadwalkan">Jadwalkan</button>
    <button class="btn btn-primary" type="reset" value="Ulang">Ulang</button>
</form>
</div>
</div>
</div>
</body>
</html>