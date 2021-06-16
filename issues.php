<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
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
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Isu
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="approvedissues.php">Sudah disetujui</a></li>
            <li><a class="dropdown-item" href="unapprovedissues.php">Belum disetujui</a></li>
            <li><a class="dropdown-divider"></a></li>
            <li><a class="dropdown-item" href="issuereg.php">Ajukan Isu</a></li>
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
<br>
<div class="container">
<div class="jumbotron">
  <h1 class="display-4">Mari kita telaah, <b><?php echo $_SESSION['username']?></h1>
  <p class="lead">Isu-isu yang akan dibahas dibagi menjadi isu yang belum disetujui dan telah disetujui.
  Isu yang disetujui memiliki fasilitator dan instansi pemerintah yang bertanggung jawab. Isu
  yang belum disetujui tidak memiliki keduanya, tapi bisa saja memiliki salah satunya. Anda bisa 
  mengajukan isu.</p>
  <hr class="my-4">
  <p>Selamat berdiskusi.</p>
  <p class="lead">
    <a class="btn btn-dark btn-lg" href="approvedissues.php" role="button">Diskusikan isu yang telah disetujui.</a>
    <a class="btn btn-dark btn-lg" href="issuereg.php" role="button">Ajukan isu.</a>
  </p>
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