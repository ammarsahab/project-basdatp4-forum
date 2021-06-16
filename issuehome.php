<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include('connect.php');
$is=pg_prepare("wutis", 'SELECT issues.issue_name, issue_desc from issues where issue_id=$1');
$is=pg_execute("wutis",array($_GET['issue']));
$name=pg_fetch_array($is);
//cari isu dengan id tersebur
?>
 
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="bootstrap-5.0.1-dist/css/bootstrap.css"/>
    <script src="bootstrap-5.0.1-dist/js/bootstrap.bundle.js"></script>
    <title>Home</title>
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
            <?php echo($name['issue_name']);?>
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
<br/>
<div class="container">
<div class="jumbotron">
  <h1 class="display-4">Laman <b><?php echo ($name['issue_name']);?></h1>
  <p class="lead"><?php echo ($name['issue_desc']);?></p>
  <hr class="my-4">
  <p>Apa yang ingin Anda lakukan, <?php echo $_SESSION['username'];?>?</p>
  <p class="lead">
    <a class="btn btn-dark btn-lg" href="polls.php?issue=<?php echo $_GET['issue'];?>" role="button">Lihat survey.</a>
    <a class="btn btn-dark btn-lg" href="topics.php?issue=<?php echo $_GET['issue'];?>" role="button">Lihat forum.</a>
    <a class="btn btn-dark btn-lg" href="consul.php?issue=<?php echo $_GET['issue'];?>" role="button">Lihat jadwal konsultasi.</a>
  </p>
    <!-- tautan php diberi id issue agar dapat dilokasikan dengan tepat. -->
<p>
<?php
//cek status dan pesan
if(isset($_GET['status'])){
    if($_GET['status']=="regsukses"){
        echo '<div class="alert alert-success" role="alert">';
        echo 'Pengajuan sukses!';
        echo '</div>';
    }
    elseif($_GET['status']=="fasilsukses"){
        echo '<div class="alert alert-success" role="alert">';
        echo 'Fasilitasi sukses!';
        echo '</div>';
    }
    elseif($_GET['status']=="apprsukses"){
        echo '<div class="alert alert-success" role="alert">';
        echo 'Approval sukses!';
        echo '</div>';
        }    
    elseif($_GET['status']=="firesukses"){
        echo '<div class="alert alert-success" role="alert">';
        echo 'Pemecatan sukses!';
        echo '</div>';
        }
    else{
        echo '<div class="alert alert-danger" role="alert">';
        echo 'Gagal';
        echo '</div>';
    }
}
?>
</p>
</div>
</div>
</div>
</body>
</html>