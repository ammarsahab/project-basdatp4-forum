<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
if(!isset($_GET["issue"])){
    echo "Tidak ada isu";
    exit;
}
$issue=$_GET['issue'];
include('connect.php');
$facilcheck=pg_prepare("facilcheck", 'SELECT *
from issues 
join alokasi_fasilitator on issue_id=allocissue_id
where issue_id=$1 AND facilitator_id=$2');
$facilcheck=pg_execute("facilcheck",array($issue,$_SESSION['id']));
$isfacil=pg_num_rows($facilcheck);
//cek apakah user fasilitator di isu tersebut
$issuedata=pg_prepare("issuedata", 'SELECT * from issues where issue_id=$1');
$issuedata=pg_execute("issuedata",array($issue));
$isdata=pg_fetch_array($issuedata);
//cari data tentang isu?>
<!DOCTYPE HTML>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="bootstrap-5.0.1-dist/css/bootstrap.css"/>
    <script src="bootstrap-5.0.1-dist/js/bootstrap.bundle.js"></script>
    <title>Polls</title>
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
<br/>
<p>
<div class="container">
<h2 class="text-center">Konsultasi untuk <?php echo($isdata['issue_name']);?></h2><br/>
<p>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Waktu Konsultasi</th>
            <th>Platform Konsultasi</th>
            <th>Tautan/Alamat Konsultasi</th>
            <?php if($isfacil==1){
            //jika fasilitator, dapat delete
            echo "<th>Delete</th>";
        }?>
		</tr>
	</thead>
	<tbody>
		
		<?php
			$conslist=pg_prepare("conslist", 'SELECT * from konsultasi where issue_id=$1');
            $conslist=pg_execute("conslist",array($issue));
            while($cons=pg_fetch_array($conslist)){
				echo "<tr>";
                    echo "<td>".$cons['waktu_konsultasi']."</td>";
                    echo "<td>".$cons['platform_konsultasi']."</td>";
                    echo "<td><a href=".$cons['alamat_tautan_konsultasi']." class='link-success'>".$cons['alamat_tautan_konsultasi']."</td>";
                    if($isfacil==1){
                        //jika fasilitator isu, dapat delete survei
                        echo "<td><p><a class='link-dark' href='deletekonsultasi.php?konsultasi=".$cons['konsultasi_id']."&issue=".$issue."'>Delete</p></td>";
        			}
                echo"<tr>";
            }
        ?>
	</tbody>
</table>
<p>
<p>
    <?php if($isfacil==1){
    echo "<a href='consulcreate.php?issue=".$issue."' class='btn btn-dark'>Buat Konsultasi</a>";
    }?>
</p>
	Total: <?php echo pg_num_rows($conslist);?>
</p>
<?php
if(isset($_GET['status'])){
    if($_GET['status']=="sukses"){
        echo "<div class='alert alert-success' role='alert'>";
        echo "Penjadwalan konsultasi sukses!";
        echo "</div>";
    }
    if($_GET['status']=="deletekonsultasi"){
        echo "<div class='alert alert-success' role='alert'>";
        echo "Delete konsultasi sukses!";
        echo "</div>";
    }

}
?>
</div>
</body>
</html>