<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include("connect.php");
if(isset($_POST['Unggah'])){
    $issue=$_POST['issue'];
}else{$issue=$_GET['issue'];
}
if(empty($issue)){
    header('location:issues.php');
    exit;
}//jika takda issue, pergi ke laman issue
$issuedata=pg_prepare("issuedata", 'SELECT * from issues where issue_id=$1');
$issuedata=pg_execute("issuedata",array($issue));
$isdata=pg_fetch_array($issuedata);
$topic_name = $post="";
$name_err=$post_err="";
//pengecekan nama - kosong dan uniqueness
if(isset($_POST['Unggah'])){
    if(empty(trim($_POST["name"]))){
        $name_err = "Nama topik tidak boleh kosong.";
    }
    else{
        $checkname=pg_prepare("checkissname", 'SELECT topic_id FROM topik WHERE topic_name = $1');
        $checkname=pg_execute("checkissname",array(trim($_POST["name"])));
        if(pg_num_rows($checkname)==1){
            $name_err = "Topik yang sama sudah ada";
        }
        else{
            $topic_name=trim($_POST["name"]);
        }
    }    
    //pengecekan post
    if(empty($_POST["post"])){
        $post_err = "Harus ada post pertama.";
    } 
    else{
        $post = $_POST["post"];
    }   
    //insert hanya jika post dan topik ada
    if(empty($name_err) && empty($post_err)){
        $creator=$_SESSION["id"];
        $insert=pg_prepare("insert", 'INSERT INTO topik(issue_id,topic_name,topic_creator_id) values($1,$2,$3)');
        $insert=pg_execute("insert",array($issue,$topic_name,$creator));
        //insert ke topik, cari topik
        $lookup=pg_prepare("lookup", 'SELECT topic_id from topik where topic_name=$1');
        $lookup=pg_execute("lookup",array($topic_name));
        $id=pg_fetch_array($lookup);
        if(pg_num_rows($lookup)==0){
            echo "Tidak ada topik untuk dimasukkan post";
            exit;
        }else{
            //insert ke post
            $insrt = pg_prepare('posts', 'INSERT INTO post(topic_id, post, post_creator_id) VALUES ($1, $2,$3)');
            $insrt = pg_execute('posts',array($id['topic_id'],$post,$creator));
            }
        header('Location: topics.php?status=topicsukses&issue='.$issue.'');
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
<div class="container">
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
    <div class="d-none">
    <label for="Issue">Issue</label>
    <input type="number" name="issue" name="issue" value=<?php
    if(isset($issue)){
        echo $issue;
    }else{
        echo $_GET['issue'];
    }
    ?>>
    </div>
    <div class='mb-3'>
    <label for="name">Nama Topik</label>
    <input class="form-control" type="text" name="name" placeholder="Nama" >
    <div class="form text"><?php echo $name_err;?></div>
    </div>
    <div class='form group'>
    <label for="post">Post</label>
    <textarea class="form-control"title="post" name="post" placeholder="Post"></textarea>
    <div class="form text"><?php echo $post_err;?></div>
    </div>
    <br>
    <p>
    <button class="btn btn-success" type="submit" value="Unggah" name="Unggah">Unggah</button>
    <button class="btn btn-primary" type="reset" value="Ulang">Ulang</button>
	</p>
</form>
</div>
</body>
</html>