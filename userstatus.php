<?php
// Mulai sesi
session_start();
 
// Cek login, jika tidak pergi ke laman login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
    header("location: login.php");
    exit;
}
include('connect.php');
//cek apakah user ASN yang memiliki otoritas
$asncheck=pg_prepare("asncheck", 'SELECT asn_id from asn where asn_id=$1');
$asncheck=pg_execute("asncheck",array($_SESSION["id"]));
if(pg_num_rows($asncheck)==0){
    echo "Anda tidak memiliki izin mengubah status user.";
    exit;
}
//cek apakah ubah sudah ada.
if(isset($_POST['Ubah'])){
    header("location: prosesubah.php");
    exit;
}
//cek apakah user ada
if(!isset($_GET['id'])){
    echo "Tidak ada user untuk diubah statusnya.";
    exit;}
//cari informasi mengenai user, apakah asn atau fasilitator
elseif(isset($_GET['id']))
    {$id=$_GET['id'];}
$facil=pg_prepare("facilinf", 'SELECT facil_id from facilitator where facil_id=$1');
$facil=pg_execute("facilinf",array($id));
$isfacil=pg_num_rows($facil);
$asn=pg_prepare("asninf", 'SELECT asn_id, workplace_id from asn where asn_id=$1');
$asn=pg_execute("asninf",array($id));
$isasn=pg_num_rows($asn);
$worksin="";
//jika asn, cari temoat kerja
if($isasn==1){
    $infoasn=pg_fetch_array($asn);
    $workplace=pg_prepare("asnworkplace", 'SELECT govtagency_id, govtagency_name from govtagency where govtagency_id=$1');
    $workplace=pg_execute("asnworkplace", array($infoasn['workplace_id']));
    $workpace=pg_fetch_array($workplace);
    $worksin=$workpace['govtagency_name'];
}
//cari data tempat kerja
$agencies=pg_prepare("workplaces", 'SELECT govtagency_id, govtagency_name from govtagency');
$agencies=pg_execute("workplaces",array());
//cari data user yang ingin diubah
$users=pg_prepare("userinf", 'SELECT user_id, username from users where user_id=$1');
$users=pg_execute("userinf",array($id));
$user=pg_fetch_array($users); 
?>
<!DOCTYPE HTML>
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
<form action="prosesubah.php" method="POST">
        <div class="d-none"><label for="id">User ID: </label>
        <input class="form-control" type="text" name="id" value=<?php echo $user['user_id']?>>
        </div>
        <h3>Edit Status User: <b><?php echo $user['username'];?></b></h3>
    	<div class="mb-3">
			<label for="status">User ini adalah: </label>
			<div class="form-check">
                <input class="form-check-input" type="checkbox" class="form-control" name="Facilitator" value="Facilitator" <?php
                //jika user fasilitator atau asn, sudah checkbox sudah dicheck.
                if($isfacil==1){
                    echo "checked";
                }
            ?>>
            <label class ="form-check-label" for="Facilitator">Fasilitator</label>
            <br>
            <input class="form-check-input" type="checkbox" class="form-control" name="ASN" value="ASN" <?php
            if($isasn==1){
                echo "checked";
            } 
            ?>>
            <label class="form-check-label"  for="asn">ASN</label>
         </div>
         </div>
            <?php //jika user asn, tempat kerja diganti. secara default ASN baru bekerja di kementrian latihan
            if($isasn==1){
                echo "<div class='form-group'>";
                echo "<label for='workplace'>Pilih tempat kerja </label>";
                echo "<select class ='form-control' name='workplace' id='workplace'>";
                echo "<option value='none' disabled>Pilih</option>";
                while($agency=pg_fetch_array($agencies)){
                    echo '<option value="'.$agency['govtagency_id'].'">'.$agency['govtagency_name'].'</option>';
                }
                echo "</select>";
                echo "</div>";
            }
            ?>
         <br>
         <button type="submit" class="btn btn-success" value="Ubah" name="Ubah">Ubah</button>
</form>
</div>
</div>
</div>
</body>
</html>