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
<div class='container'>
<h3 class="text-center"> Topik <?php echo $tdata['topic_name'];?>, isu <?php echo $isdata['issue_name'];?>.</h3>
<table class="table table-hover">
	<thead>
		<tr>
			<th>User</th>
            <th>Timestamp</th>
            <th>Post</th>
            <?php if($isfacil==1){
            //jika fasilitator, dapat delete
            echo "<th>Delete</th>";
        }?>
		</tr>
	</thead>
	<tbody>
		<?php
			$postlist=pg_prepare("postlist", 'SELECT * from post 
            join users on post.post_creator_id=users.user_id
            where topic_id=$1 order by post_date asc');
            $postlist=pg_execute("postlist",array($topic));
            while($posts=pg_fetch_array($postlist)){
				echo "<tr>";
                echo "<td><a href=profile.php?user=".$posts['user_id']." class='link-dark'>".$posts['username']."</td>";
                echo "<td>".$posts['post_date']."</td>";
                    echo "<td>".$posts['post']."</td>";
                    if($isfacil==1){
						//jika fasilitator isu, dapat delete post
						echo "<td><p><a class='link-dark' href='deletepost.php?post=".$posts['post_id']."&topic=".$posts['topic_id']."&issue=".$issue."'>Delete</p></td>";
						}
                echo"<tr>";
			}
        ?>
	</tbody>
</table>
<div class="card bg-light">
<div class="card-body">
<form action="reply.php" method="GET">
<fieldset>
	<div class="d-none"> 
    <label for="Issue">Issue</label>
    <input type="number" name="issue" name="issue" value=<?php
        echo $_GET['issue'];
    ?>>
    </div>
    <div class="d-none">
    <label for="Issue">Topic</label>
    <input type="number" name="topic" name="topic" value=<?php
        echo $_GET['topic'];
    ?>>
    </div>
    <div class="form-group">
    <label for="post">Post</label>
    <textarea class="form-control" title="post" name="post" placeholder="Post"></textarea>
    </div>
    <br>
    <button class="btn btn-dark" type="submit" name="Reply" value="Reply">Balas</button>
</fieldset>
</form>
</div>
</div>
<p>
	Total post: <?php echo pg_num_rows($postlist);?>
</p>
<p>
<?php
if(isset($_GET['status'])){
    if($_GET['status']=="reply"){
        echo '<div class="alert alert-success" role="alert">';
        echo 'Reply sukses!';
        echo '</div>';
    }elseif($_GET['status']=="none"){
        echo '<div class="alert alert-danger" role="alert">';
        echo 'Tidak ada reply!';
        echo '</div>';
    }
    elseif($_GET['status']=="deletepost"){
        echo '<div class="alert alert-success" role="alert">';
        echo 'Post berhasil di-delete!';
        echo '</div>';
    }
}?>
</p>
<p>
<a href='topics.php?issue=<?php echo $issue;?>' class="btn btn-dark">Kembali ke List Topik</a>
</p>
</div>
</body>
</html>