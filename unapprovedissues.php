<?php
//cek login
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include('connect.php');
//cek jika user asn/fasilitator
$asncheck=pg_prepare("asncheck", 'SELECT asn_id from asn where asn_id=$1');
$asncheck=pg_execute("asncheck", array($_SESSION['id']));
$isasn=pg_num_rows($asncheck);
$facilcheck=pg_prepare("facilcheck", 'SELECT facil_id from facilitator where facil_id=$1');
$facilcheck=pg_execute("facilcheck",array($_SESSION["id"]));
$isfacil=pg_num_rows($facilcheck);
?>
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
<br>
<div class="container">
<h3 class="text-center"><b>Isu yang belum disetujui</b></h3>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Nama</th>
			<th>Deskripsi</th>
            <th>Fasilitator</th>
			<th>Agensi yang bertanggung jawab</th>
            <?php if($isasn==1){//jika asn, buka menu tindakan
                echo "<th>Tindakan</th>";
            }
            $asnissuecheck=pg_prepare("asnissuecheck", 'SELECT asn_id from asn 
            join govtagency
            on asn.workplace_id=govtagency.govtagency_id
            left join issues
            on govtagency.govtagency_id=issues.respagency_id
            where issue_id=$1 AND asn_id=$2');    
?>
		</tr>
	</thead>
	<tbody>
		
		<?php
			$unapprois=pg_prepare("unapprovedissuelist", 'SELECT 
            issues.issue_id,
            issues.issue_name, 
            issues.issue_desc,
            facilitator.facil_id,
            users.username, 
            approval_asn.asnapprover_id,
            issues.respagency_id,
            govtagency.govtagency_name
            from 
            issues 
            left join alokasi_fasilitator on issue_id=allocissue_id
            left join approval_asn on issue_id=approvedissue_id
            left join facilitator on facilitator_id=facil_id
            left join users on facil_id=user_id
            left join govtagency on govtagency_id=respagency_id
            where
            alokasi_fasilitator.facilitator_id IS NULL OR approval_asn.asnapprover_id IS NULL');
            //tidak ada fasilitator ATAU tidak ada asn yang menyetujui. satu ada boleh, kedua tak ada boleh
            $unapprois=pg_execute("unapprovedissuelist",array());
            while($issues=pg_fetch_array($unapprois)){
				echo "<tr>";
					echo "<td>".$issues['issue_name']."</td>";
					echo "<td>".$issues['issue_desc']."</td>";
					if(empty($issues['username'])){
                        if($isfacil==1){
                            echo "<td><a class='link-dark' href='prosesfacil.php?id=".$issues['issue_id']."'>Fasilitasi</td>";
                        }else{
                            echo "<td>Belum ada fasilitator.</td>";}
                        }
                    else{
                        echo "<td>".$issues['username']."</td>";
                        }
                    if(empty($issues['asnapprover_id'])){
                        $asnissuecheck=pg_execute("asnissuecheck",array($issues['issue_id'],$_SESSION["id"]));
                        $isissue=pg_num_rows($asnissuecheck);
                        if($isissue==1){
                            echo "<td><a class='link-dark' href='prosesapproval.php?id=".$issues['issue_id']."'>Setujui</td>";
                            }else{
                            echo "<td>Belum disetjui.</td>";}
                        }
                    else{
                        echo "<td>".$issues['govtagency_name']."</td>";
                        }
                    if($isasn){
                        if($isissue==1 && !empty($issues['username'])){
                            echo "<td><a class='link-dark' href='prosesdeletefacil.php?issue=".$issues['issue_id']."&facil=".$issues['facil_id']."'>Pecat fasilitator.</td>";
                        }else{
                            echo "<td>Anda tidak dapat memecat fasilitator pada isu ini.</td>";
                        }
                    }
				echo"</tr>";
			}
		?>
	</tbody>
</table>
<p>
	Jumlah Isu yang belum disetujui: <?php echo pg_num_rows($unapprois);?>
</p>
</div>
</body>
</html>
