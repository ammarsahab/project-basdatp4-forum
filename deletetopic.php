<?php
//cek login
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include('connect.php');
if(isset($_POST['topic'])){
    $topic=$_POST['topic'];
}else{
    $topic=$_GET['topic'];
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
if(empty($topic)){
    header('location:topics.php?issue='.$issue.'');
    exit;
}
$issuedata=pg_prepare("issuedata", 'SELECT * from issues where issue_id=$1');
$issuedata=pg_execute("issuedata",array($issue));
$isdata=pg_fetch_array($issuedata);
$topicdata=pg_prepare("topicdata", 'SELECT * from topik where topic_id=$1');
$topicdata=pg_execute("topicdata",array($topic));
$tdata=pg_fetch_array($topicdata);
$facilcheck=pg_prepare("facilcheck", 'SELECT *
from issues 
join alokasi_fasilitator on issue_id=allocissue_id
where issue_id=$1 AND facilitator_id=$2');
$facilcheck=pg_execute("facilcheck",array($issue,$_SESSION['id']));
$isfacil=pg_num_rows($facilcheck);
if($isfacil==0){
    echo "Anda tidak diizinkan melakukan ini";
    exit;
}
if (isset($_POST['Delete'])) {
    $stmt = pg_prepare('caritopic','SELECT * FROM topik WHERE topic_id=$1');
    $stmt = pg_execute('caritopic',array($topic));
    $sm = pg_num_rows($stmt);
    if ($sm==0) {
        echo 'Tidak ada topic dengan id tersebut';
        exit;
    }
    $delpost = pg_prepare('delpost','DELETE FROM post WHERE topic_id=$1');
    $delpost = pg_execute('delpost',array($topic));
    $deltopic = pg_prepare('deltopic','DELETE FROM topik WHERE topic_id=$1');
    $deltopic = pg_execute('deltopic',array($topic));
    header('location: topics.php?issue='.$issue.'&status=deletetopic');
    exit;
    }?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="bootstrap-5.0.1-dist/css/bootstrap.css"/>
    <script src="bootstrap-5.0.1-dist/js/bootstrap.bundle.js"></script>
    <title>topics</title>
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
            <li><a class="dropdown-item" href="topics.php?issue=<?php echo $issue;?>">Survei</a></li>
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
    <label for="topic">Topic</label>
    <input type="number" title="topic" name="topic" value=<?php
    if(isset($topic)){
        echo $topic;
    }else{
        echo $_GET['topic'];
    }
    ?>></div>
    <h2>Apakah anda yakin ingin delete topic <?php echo $tdata['topic_name'];?>?
        <p>
            <button class="btn btn-success" type="submit" value="Delete" name="Delete">Delete</button>
            <a class="btn btn-danger" href="topics.php?issue=<?php echo $_GET['issue'];?>" role="button">Tidak.</a>
       </p>
    </form>
</div>
</body>
</html>