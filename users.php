<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include('connect.php')
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
<p>
<?php
	//cek apakah status ada
	if(isset($_GET['status'])){
			if($_GET['status']=='sukses'){
				echo '<div class="alert alert-success" role="alert">';
				echo 'Ubah status berhasil!';
				echo '</div>';}
			else{
				echo '<div class="alert alert-danger" role="alert">';
				echo 'Ubah status gagal!';
				echo '</div>';}
			}
?>
</p>
<h2 class="text-center">Pengguna</h2>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Username</th>
			<th>Email</th>
			<th>Status</th>
            <?php
			//jika asn, tindakan dapat terlihat.
			 $isasn=pg_prepare("isasn", 'SELECT asn_id from asn where asn_id=$1');
			 $isasn=pg_execute("isasn",array($_SESSION["id"]));				 
			 if(pg_num_rows($isasn)==1){
				echo "<th>Tindakan</th>";}?>
		</tr>
	</thead>
	<tbody>
		
		<?php
			//list user dicari
			$userlist=pg_prepare("userlist", 'SELECT user_id, username, email from users');
            $userlist=pg_execute("userlist",array());
            //mempersipakan prosedur apakah user fasilitator atau asn
			$facilcheck=pg_prepare("facilcheck", 'SELECT facil_id from facilitator where facil_id=$1');
            $asncheck=pg_prepare("asncheck", 'SELECT asn_id from asn where asn_id=$1');
            while($users=pg_fetch_array($userlist)){
                $facilcheck=pg_execute("facilcheck",array($users['user_id']));
                $asncheck=pg_execute("asncheck",array($users['user_id']));
                //mengisi status sesuai apakah user berada di tabel fasilitator dan asn.
				if(pg_num_rows($facilcheck)==1 && pg_num_rows($asncheck)==1){
                $status="Fasilitator, ASN";
                }elseif(pg_num_rows($facilcheck)==1 && pg_num_rows($asncheck)==0){
                $status= "Fasilitator";
                }elseif(pg_num_rows($facilcheck)==0 && pg_num_rows($asncheck)==1){
                $status= "ASN";
                }else{$status="User";}
				echo "<tr>";
					//profil tiap user dapat dilihat
					echo "<td><a href=profile.php?user=".$users['user_id']." class='link-dark'>".$users['username']."</td>";
					echo "<td>".$users['email']."</td>";
					echo "<td>".$status."</td>";
                    //jika asn, user dapat mengubah status user lain
					if(pg_num_rows($isasn)==1){
						echo "<td><p><a class='link-dark' href='userstatus.php?id=".$users['user_id']."'>Ubah Status</p>
					</td>";};
				echo"<tr>";
			}
		?>
	</tbody>
</table>
<p>
	Jumlah Pengguna: <?php echo pg_num_rows($userlist);?>
</p>
</div>
</body>
</html>
