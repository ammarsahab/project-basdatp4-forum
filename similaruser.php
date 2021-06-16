<?php
//cek login
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include('connect.php');
//cari jawaban
$jwb=pg_prepare('carijawab', 'SELECT jawaban.jawaban_id,jawaban from rekapitulasi_jawaban join jawaban on
rekapitulasi_jawaban.jawaban_id=jawaban.jawaban_id where survey_id=$1 and user_id=$2');
$jwb=pg_execute('carijawab', array($_GET['poll'],$_SESSION['id']));
$jawab=pg_fetch_array($jwb);
$ppl=pg_prepare('liatuser','SELECT * from rekapitulasi_jawaban join jawaban on
rekapitulasi_jawaban.jawaban_id=jawaban.jawaban_id join users 
on rekapitulasi_jawaban.user_id=users.user_id
where jawaban.jawaban_id=$1
');
$ppl=pg_execute('liatuser', array($jawab['jawaban_id']));
if(isset($_POST['poll'])){
    $poll=$_POST['poll'];
}else{
    $poll=$_GET['poll'];
}
if(isset($_POST['issue'])){
    $issue=$_POST['issue'];
}else{
    $issue=$_GET['issue'];
}
if(empty($issue)){
    header('location:issues.php');
    exit;
}//jika takda issue, pergi ke laman isu
if(empty($poll)){
    header('location:polls.php?issue='.$issue.'');
    exit;
}
$issuedata=pg_prepare("issuedata", 'SELECT * from issues where issue_id=$1');
$issuedata=pg_execute("issuedata",array($issue));
$isdata=pg_fetch_array($issuedata);
$surveydata=pg_prepare("surveydata", 'SELECT * from survey where survey_id=$1');
$surveydata=pg_execute("surveydata",array($poll));
$sdata=pg_fetch_array($surveydata);
?>
<!DOCTYPE html>
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
            <li><a class="dropdown-item" href="polls.php?issue=<?php echo $issue;?>">Survei</a></li>
            <li><a class="dropdown-item" href="topics.php?issue=<?php echo $issue;?>">Forum</a></li>
            <li><a class="dropdown-item" href="consul.php?issue=<?php echo $issue;?>">Konsultasi</a></li>
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
<?php
	//cek apakah status ada
	if(!isset($jawab['jawaban_id'])){ 
        header('location:results.php?issue='.$_GET['issue'].'&poll='.$_GET['poll'].'&status=belumjawab');
		exit;
	}
?>
<div class="container">
<h3>User yang menjawab <?php echo $jawab['jawaban'];?> di survei <?php echo $sdata['survey_title'];?>.</h3>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Username</th>
			<th>Email</th>
			<th>Jawaban</th>
		</tr>
	</thead>
	<tbody>
		
		<?php
            while($people=pg_fetch_array($ppl)){
				echo "<tr>";
          echo "<td><a href=profile.php?user=".$people['user_id']." class='link-dark'>".$people['username']."</td>";
          echo "<td>".$people['email']."</td>";
					echo "<td>".$people['jawaban']."</td>";
				echo"<tr>";
			}
		?>
	</tbody>
</table>
<p>
	Total: <?php echo pg_num_rows($ppl);?>
</p>
<a class="btn btn-secondary" href="results.php?issue=<?php echo $_GET['issue'];?>&poll=<?php echo $_GET['poll']?>" role="button">Kembali ke hasil.</a>
</div>
</body>
</html>
