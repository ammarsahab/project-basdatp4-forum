<?php
// Cek login
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include("connect.php");
//cari semua instansi pemerintah
$agencies=pg_prepare("workplaces", 'SELECT govtagency_id, govtagency_name from govtagency');
$agencies=pg_execute("workplaces",array());
//nama, deskripsi isu didefinisikan kosong, begitu juga error
$issue_name = $issue_desc="";
$name_err=$desc_err="";
//mengecek apakah tombol ajukan telah ditekan
if(isset($_POST['Ajukan'])){
    //mengecek apakah nama isu ada
    if(empty(trim($_POST["name"]))){
        $name_err = "Nama Isu tidak boleh kosong.";
    }
    else{
        //mengecek apakah sudah ada isu dengan nama sama.
        $checkname=pg_prepare("checkissname", 'SELECT issue_id FROM issues WHERE issue_name = $1');
        $checkname=pg_execute("checkissname",array(trim($_POST["name"])));
        if(pg_num_rows($checkname)==1){
            $name_err = "Isu yang sama sudah ada";
        }
        else{
        //jika cek dilewati, nama isu adalah nama di form
            $issue_name=trim($_POST["name"]);
        }
    }    
    //mengecek apakah deskripsi isu ada
    if(empty(trim($_POST["desc"]))){
        $desc_err = "Deskripsi tidak boleh kosong.";
    } 
    else{
    //jika cek dilewati, deskripsi isu adalah deskripsi di form
        $issue_desc = trim($_POST["desc"]);
    }   
    //jika tidak ada error dan agensi dipilih, masukan ke isu
    if(empty($name_err) && empty($desc_err) && isset($_POST["agency"])){
        //post agency adalah instansi yang bertanggung jawab
        $respfor=$_POST['agency'];
        //proposer adalah user yang mengajukan.
        $proposer=$_SESSION["id"];
        $insert=pg_prepare("insert", 'INSERT INTO issues(issue_name,issue_desc,proposer_id,respagency_id) values($1,$2,$3,$4)');
        $insert=pg_execute("insert",array($issue_name,$issue_desc,$proposer,$respfor));
        header('Location: issues.php?status=regsukses');
        exit;
    }
}
?>

<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="bootstrap-5.0.1-dist/css/bootstrap.css"/>
    <script src="bootstrap-5.0.1-dist/js/bootstrap.bundle.js"></script>
    <title>Home</title>
</head>
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
<div class="container">
<h3 class="text-center"><b>Ajukan Isu</b></h3>
<br>
<div class="card text-black bg-light">
<div class="card-body">
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
	<div class="mb-3">
        <label for="name">Issue Name: </label>
		<input class="form-control" type="text" name="name" placeholder="Issue Name" aria-describedby="nameerror"/>
        <div class="form-text" name="nameerror"><?php echo $name_err; ?></div>
    </div>
 	<div class="form-group">
        <label for="desc">Issue Description: </label>
        <textarea name="desc" class="form-control" rows="3" aria-describedby="descerr"></textarea>
        <div class="form-text" name="descerr"><?php echo $desc_err; ?></div>
    </div>
    <div class="form-group">
        <?php
            echo "<label for='agency'>Instansi yang bertanggung jawab adalah: </label>";
            echo "<select class='form-control' name='agency' id='agency'>";
            echo "<option selected='true' disabled='disabled' value='none'>Pilih</option>";
            while($agency=pg_fetch_array($agencies)){
                echo '<option value="'.$agency['govtagency_id'].'">'.$agency['govtagency_name'].'</option>';
                }
            ?>
    </select>
    </div>
    <br>
    <div class="form-row">
		<button class="btn btn-success" type="submit" value="Ajukan" name="Ajukan">Ajukan</button>
		<button class="btn btn-primary" type="reset" value="Ulang" name="ulang">Ulang</button>
	</div>
</div>
</div>
</div>
</form>
</body>
</html>