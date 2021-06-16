<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include('connect.php');
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
$udahjawab = pg_prepare('telahjawab','SELECT jawaban.jawaban_id FROM rekapitulasi_jawaban join jawaban on rekapitulasi_jawaban.jawaban_id=jawaban.jawaban_id where survey_id = $1 AND user_id=$2');
$udahjawab = pg_execute('telahjawab',array($poll,$_SESSION['id']));
$prevans = pg_fetch_array($udahjawab);
$jawaban = pg_prepare('jawaban','SELECT * FROM jawaban WHERE survey_id = $1');
$jawaban = pg_execute('jawaban',array($poll));
//cari jawaban, cari jawaban sebelumnya jika ada.
if (pg_num_rows($surveydata)==1) {
    if (isset($_POST['poll_answer'])) {
        $ceksama = pg_prepare('cek','SELECT * FROM rekapitulasi_jawaban WHERE jawaban_id = $1 AND user_id=$2');
        $ceksama = pg_execute('cek',array($_POST['poll_answer'],$_SESSION['id']));
        if(pg_num_rows($ceksama)==1){
            header("location:polls.php?issue=".$issue."&status=jwbsama");
            exit;
        }else{
            if(pg_num_rows($udahjawab)==1){
                $delete = pg_prepare('delete','DELETE FROM rekapitulasi_jawaban WHERE jawaban_id = $1 AND user_id=$2');
                $delete =pg_execute('delete',array($prevans['jawaban_id'],$_SESSION['id']));
                $masuk = pg_prepare('masuk','INSERT INTO rekapitulasi_jawaban(user_id,jawaban_id) VALUES($1,$2)');
                $masuk = pg_execute('masuk',array($_SESSION['id'],$_POST['poll_answer']));
                header("location:polls.php?issue=".$issue."&status=jwbbeda");
                exit;
            }else{
                    $masuk = pg_prepare('masuk','INSERT INTO rekapitulasi_jawaban(user_id,jawaban_id) VALUES($1,$2)');
                    $masuk = pg_execute('masuk',array($_SESSION['id'],$_POST['poll_answer']));
                    header("location:polls.php?issue=".$issue."&status=jwbbaru");
                    exit;
                }
            }
        }   
    }
//status berbeda tergantungjawaban sama, beda, atau belum dijawab
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
<div class="container">
<h2 class="text-center"><?php echo $sdata['survey_title'];?></h2>
<p><?php echo $sdata['survey_desc'];?></p>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST"> 
    <div class="d-none">
    <label for="Issue">Issue</label>
    <input type="number" title="issue" name="issue" value=<?php
    if(isset($issue)){
        echo $issue;
    }else{
        echo $_GET['issue'];
    }
    ?>>
    </div>
    <div class="d-none">
    <label for="Poll">Poll</label>
    <input type="number" title="poll" name="poll" value=<?php
    if(isset($poll)){
        echo $poll;
    }else{
        echo $_GET['poll'];
    }
    ?>></div>
    <?php  
    while($jwb=pg_fetch_array($jawaban)){
        echo "<div class='form-check'>";
        if(pg_num_rows($udahjawab)==1){
            if($jwb['jawaban_id']==$prevans['jawaban_id']){
                echo "<input class='form-check-input' type='radio' name ='poll_answer' id=".$jwb["jawaban_id"]." value=".$jwb['jawaban_id']." checked>";
                echo "<label class='form-check-label' for=".$jwb['jawaban_id'].">".$jwb['jawaban']."</label>";
            }else{
                echo "<input class='form-check-input' type='radio' name ='poll_answer' id=".$jwb["jawaban_id"]." value=".$jwb['jawaban_id'].">";
                echo "<label class='form-check-label' for=".$jwb['jawaban_id'].">".$jwb['jawaban']."</label>";
            }
        }else{
            echo "<input class='form-check-input' type='radio' name ='poll_answer' id=".$jwb["jawaban_id"]." value=".$jwb['jawaban_id'].">";
            echo "<label class='form-check-label' for=".$jwb['jawaban_id'].">".$jwb['jawaban']."</label>";
        }
        echo "</div>";
    }?>
        <p>
            <button class="btn btn-success" type="submit" value="Vote" name="Vote">Vote</button>
            <a class="btn btn-secondary" href="results.php?issue=<?php echo $_GET['issue'];?>&poll=<?php echo $_GET['poll']?>" role="button">Lihat hasil.</a>
       </p>
    </form>
</div>
</body>
</html>