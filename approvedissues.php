<?php
// cek apakah user telah login
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include('connect.php');
//cek apakah user asn
$asncheck=pg_prepare("asncheck", 'SELECT asn_id from asn where asn_id=$1');
$asncheck=pg_execute("asncheck", array($_SESSION['id']));
$isasn=pg_num_rows($asncheck);
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
<h3 class="text-center"><b>Isu yang disetujui</b></h3>
<table class="table table-hover">
    <thead>
		<tr>
			<th>Nama</th>
			<th>Deskripsi</th>
            <th>Fasilitator</th>
			<th>Instansi penanggung jawab</th>
            <?php if($isasn==1){//jika asn, buka menu tindalam
                echo "<th>Tindakan</th>";
                //siapkan check apakah tempat kerja asn relevan
                $asnissuecheck=pg_prepare("asnissuecheck", 'SELECT asn_id from asn 
                join govtagency
                on asn.workplace_id=govtagency.govtagency_id
                left join issues
                on govtagency.govtagency_id=issues.respagency_id
                where issue_id=$1 AND asn_id=$2');    
            }?>
		</tr>
	</thead>
	<tbody>
		
		<?php
            //query untuk mencari semua issue yang disetujui
			$approissuelist=pg_prepare("approvedissuelist", 'SELECT 
            issues.issue_id,
            issues.issue_name, 
            issues.issue_desc, 
            facilitator.facil_id,
            users.username, 
            approval_asn.asnapprover_id,
            govtagency.govtagency_name 
            from 
            issues 
            left join alokasi_fasilitator on issue_id=allocissue_id
            left join approval_asn on issue_id=approvedissue_id
            left join facilitator on facilitator_id=facil_id
            left join users on facil_id=user_id
            left join govtagency on respagency_id=govtagency_id
            where
            alokasi_fasilitator.facilitator_id IS NOT NULL AND approval_asn.asnapprover_id IS NOT NULL');
            //isu memiliki fasilitator DAN penyetuju
            $approissuelist=pg_execute("approvedissuelist",array());
            while($issues=pg_fetch_array($approissuelist)){
				echo "<td><a class='link-dark' href='issuehome.php?issue=".$issues['issue_id']."'>".$issues['issue_name']."</td>";
				echo "<td>".$issues['issue_desc']."</td>";
				echo "<td>".$issues['username']."</td>";
                echo "<td>".$issues['govtagency_name']."</td>";
                if($isasn==1){//jika tempat kerja sesuai, dapat memecat fasilitator
                    $asnissuecheck=pg_execute("asnissuecheck", array($issues['issue_id'],$_SESSION['id']));
                    if(pg_num_rows($asnissuecheck)==1){
                        echo "<td><a class='link-dark' href='prosesdeletefacil.php?issue=".$issues['issue_id']."&facil=".$issues['facil_id']."'>Pecat fasilitator.</td>";
                    }else{
                        echo "<td>Isu ini tidak di bawah agensi Anda.</td>";
                        }
                    }
                echo"</tr>";
                }            
		?>
	</tbody>
</table>
<p>
	Jumlah Isu yang disetujui: <?php echo pg_num_rows($approissuelist);?>
</p>
</div>
</body>
</html>