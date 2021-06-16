<?php
//cek login
session_start();
 if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include("connect.php");
$issue="";
//ambil isu dari post jika ada (kasus sudah menekan buat), atau dari get jika ada (kasus baru dari link).
if(isset($_POST['Buat'])){
    $issue=$_POST['issue'];
}elseif(isset($_GET['issue'])){
    $issue=$_GET['issue'];
}
if(empty($issue)){
    header("location: issues.php");
    exit;
}
$issuedata=pg_prepare("issuedata", 'SELECT * from issues where issue_id=$1');
$issuedata=pg_execute("issuedata",array($issue));
$isdata=pg_fetch_array($issuedata);
//cari data tentang isu
$survey_title = $survey_desc="";
$title_err=$desc_err=$ans_err="";
//proses jika tombol buat ditekan
if(isset($_POST['Buat'])){
    //cek kekosongan nama
    if(empty(trim($_POST["title"]))){
        $title_err = "Nama survei tidak boleh kosong.";
    }
    else{
        $checktitle=pg_prepare("checkisstitle", 'SELECT survey_id FROM survey WHERE survey_title = $1');
        $checktitle=pg_execute("checkisstitle",array(trim($_POST["title"])));
        if(pg_num_rows($checktitle)==1){
            $title_err = "Survei yang sama sudah ada";
        }
        //cek apakah ada survei dengan judul sama
        else{
            $survey_title=trim($_POST["title"]);
        }
        //judul ditetapkan jika tak ada salah
    }    
    if(empty(trim($_POST["desc"]))){
        $desc_err = "Deskripsi tidak boleh kosong.";
    } 
    //cek jika deskripsi kosong
    else{
        $survey_desc = $_POST["desc"];
    //deskripsi ditetapkan jika tak ada salah
    }   
    if(empty($_POST["ans"])){
        $ans_err= "Jawaban tidak boleh kosong.";
    //cek kesalahan jawaban
    }
    //insert jika tak ada salah
    if(empty($title_err) && empty($desc_err) && empty($ans_err)){
        //pembuat survey adalah user
        $creator=$_SESSION["id"];
        $insert=pg_prepare("insert", 'INSERT INTO survey(sur_issue_id,survey_title,survey_desc,sur_creator_id) values($1,$2,$3,$4)');
        $insert=pg_execute("insert",array($issue,$survey_title,$survey_desc,$creator));
        //masukkan survey
        //cari survey dengan nama tersebut
        $lookup=pg_prepare("lookup", 'SELECT survey_id from survey where survey_title=$1');
        $lookup=pg_execute("lookup",array($survey_title));
        $id=pg_fetch_array($lookup);
        if(pg_num_rows($lookup)==0){
            echo "Tidak ada survei untuk dimasukkan jawaban";
            exit;
        }else{
            //pecahkan input string jadi array
            $answers =explode(',', $_POST['ans']);
            foreach($answers as $answer) {
                if (empty($answer)) continue;
                $insrt = pg_prepare('ans', 'INSERT INTO jawaban (survey_id, jawaban) VALUES ($1, $2)');
                $insrt = pg_execute('ans',array($id['survey_id'],$answer));
            }
        //masukkan jawaban
        header('Location: polls.php?status=buatsukses&issue='.$issue.'');
        exit;
        }
    }}
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
<h3 class="text-center">Buat Survei untuk <?php echo($isdata['issue_name']);?></h3>
<div class="card text-black bg-light">
<div class="card-body">
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
    <div class="mb-3">
        <label for="title">Judul</label>
        <input class="form-control" type="text" title="title" name="title" placeholder="Judul" aria-explainedby="titleerr" >
        <div class="form-text" name="titleerr"><?php echo $title_err;?></div>
    <div>
    <div class="form-group">
        <label for="desc">Deskripsi</label>
        <textarea class="form-control" aria-explainedby="descerr" type="text" title="desc" name="desc" placeholder="Deskripsi"></textarea>
        <div class="form-text" name="descerr"><?php echo $desc_err;?></div>
    </div>
    <div class="form-group">
    <div class="row">
        <div class="col">
        <label for="ans">Jawaban</label>
        <textarea class="form-control" aria-explainedby="anserr"title="ans" name="ans" placeholder="Jawaban" ></textarea>
        </div>
        <div class="col">
        <div class="form-text">Pisahkan jawaban dengan koma. Akhiri tapa tanda baca (misal: jawaban a, jawaban b, jawaban c)
        </div>
        </div>
    </div>
    <div class="form-text" name="anserr"><?php echo $ans_err;?></div>
    <br>
    <button class="btn btn-success" type="submit" value="Buat" name="Buat">Buat</button>
    <button class="btn btn-primary" type="reset" value="Ulang">Ulang</button>
    </fieldset>
</div>
</form>
</body>
</html>