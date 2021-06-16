<?php
//Mulai sesi (login)
session_start();
 
//cek login, jika tidak arahkan ke laman login.
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
        <li class="nav-item">
          <a class="nav-link" href="issues.php">Isu</a>
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
<div class="jumbotron">
  <h1 class="display-4">Selamat datang, <b><?php echo $_SESSION['username']?></h1>
  <p class="lead">Forum ini bertujuan untuk memfasilitasi diskusi mengenai isu-isu di masyarakat. Anda dapat mengajukan isu untuk didiskusikan
   pengguna dan dipertanggungjawabkan instansi pemerintah. Anda juga dapat membuat dan menjawab survei mengenai isu tersebut,
    serta berdiskusi melalui forum dan konsultasi.</p>
  <hr class="my-4">
  <p>Mari kita mulai.</p>
  <p class="lead">
    <a class="btn btn-dark btn-lg" href="issues.php" role="button">Pelajari Isu.</a>
  </p>
</div>
</div>
</body>
</html>