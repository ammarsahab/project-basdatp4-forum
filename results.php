<?php
// cek login
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include('connect.php');
$poll=$_GET['poll'];
$issue=$_GET['issue'];

if(empty($issue)){
    header('location:issues.php');
    exit;
}//jika takda issue, pergi ke laman isu
if(empty($poll)){
    header('location:polls.php?issue='.$issue.'');
	exit;
}//jika takda poll, pergi ke laman poll
$issuedata=pg_prepare("issuedata", 'SELECT * from issues where issue_id=$1');
$issuedata=pg_execute("issuedata",array($issue));
$isdata=pg_fetch_array($issuedata);
$surveydata=pg_prepare("surveydata", 'SELECT * from survey where survey_id=$1');
$surveydata=pg_execute("surveydata",array($poll));
$sdata=pg_fetch_array($surveydata);
//cari informasi rekapitulasi jawaban
$rekap=pg_prepare("rekap", 'SELECT jawaban.jawaban_id, count(user_id), jawaban from jawaban 
left join rekapitulasi_jawaban on jawaban.jawaban_id=rekapitulasi_jawaban.jawaban_id where
survey_id=$1 group by jawaban.jawaban_id');
$rekap=pg_execute("rekap",array($poll));
$total=pg_prepare("total", 'SELECT count(user_id) from jawaban 
left join rekapitulasi_jawaban on jawaban.jawaban_id=rekapitulasi_jawaban.jawaban_id where
survey_id=$1');
//hitung total jawaban
$total=pg_execute("total",array($poll));
$num=pg_fetch_array($total);
?>
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
<div class='container'>
<h3 class="text-center">Hasil <?php echo $sdata['survey_title'];?></h3>
<p>
    <?php echo $sdata['survey_desc']?>
</p>
<br/>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Jawaban</th>
			<th>Jumlah</th>
            <th>Persentase</th>
		</tr>
	</thead>
	<tbody>
		<?php
      while($rekapit=pg_fetch_array($rekap)){
				echo "<tr>";
					echo "<td>".$rekapit['jawaban']."</td>";
					echo "<td>".$rekapit['count']."</td>";
					if($num['count']==0){
						echo"<td>0</td>";
					}else
					{echo "<td>".round(100*($rekapit['count'])/($num['count']))."</td>";	
					//jika total 0, hasil tabel 0. jika tidak, akan ada error pembagian oleh nol
				}
			}
		?>
	</tbody>
</table>
<br>
    <a class="btn btn-dark" href="similaruser.php?poll=<?php echo $poll;?>&issue=<?php echo $issue;?>">Cari User yang menjawab sama</a>
<?php if(isset($_GET['status'])){
        if($_GET['status']=="belumjawab"){
          echo '<div class="alert alert-success" role="alert">';
          echo 'Survey belum dijawab!';
          echo '</div>';
    }
  }?>
</div>
</body>
</html>
