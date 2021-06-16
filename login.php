<?php
session_start();
//jika sudah ter-login, langsung ke home.
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: home.php");
    exit;
}
include('connect.php');

//username, password, dan kesalahan-kesalahannya disetting kosong.
$username = $password ="";
$username_err = $password_err = $login_err = "";
//jika login telah ditekan.
if(isset($_POST['Login'])){
        //mengecek jika username kosong. jika ya, ada error didefinsikan.
        if(empty(trim($_POST["username"]))){
        $username_err = "Harap masukkan username.";
    } else{
        //jika tidak kosong, username didefinisikan sebagai post username.
        $username = trim($_POST["username"]);
    }
    if(empty(trim($_POST["password"]))){
        //mengecek jika password kosong. jika ya, ada error didefinsikan.
        $password_err = "Harap masukkan password.";
    } else{
        //jika tidak kosong, username didefinisikan sebagai post username.
        $password = trim($_POST["password"]);
    }
    //login hanya dilakukan jika tak ada error.
    if(empty($username_err) && empty($password_err)){
        //prepared statement untuk mencegah SQL injection.
        $login=pg_prepare("login", 'SELECT user_id, username, password FROM users WHERE username = $1');
        $login=pg_execute("login",array(trim($_POST["username"])));
        $count=pg_num_rows($login);
        //mencari apakah ada user dengan username tersebut
        if($count==1){
            //memverifikasi apakah password merupakan milik user.
            $arr = pg_fetch_array($login, 0, PGSQL_NUM);
            if(password_verify($password,$arr[2])){
                session_start();
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $arr[0];
                $_SESSION["username"] = $username;                            
                header("location: home.php");
            }else{
                $login_err = "Username atau password tidak valid.";}
            }else{
                $login_err = "Username atau password tidak valid.";
                }
            }else{
                $login_err= "Terjadi kesalahan, coba lagi beberapa waktu ke depan.";
            }}          
?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="bootstrap-5.0.1-dist/css/bootstrap.css" />
    <title>Login</title>
</head>
<body>
<div class="container">
<header>
<h2 class="text-center">Login</h2>
</header>
<?php //menampilkan registrasi sukses atau pesan error. 
if(isset($_GET['status'])){
    if($_GET['status']='regsukses'){
        echo '<div class="alert alert-success" role="alert">';
        echo 'Registrasi sukses!';
        echo '</div>';}
    }
if(!empty($login_err)){
    echo '<div class="alert alert-danger" role="alert">';
    echo $login_err;
    echo '</div>';}
?>
<div class="card text-black bg-light">
<div class="card-body">
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
	<div class="mb-3">
		<label for="username">Username: </label>
		<input type="text" name="username" placeholder="Username" class="form-control" id="username" aria-describedy="unameerror"/>
        <div id="unameerror" class="form-text"><?php echo $username_err;?></div>
    </div>
    <div class="mb-3">
		<label for="password">Password: </label>
        <input type="text" name="password" placeholder="Password" class="form-control" id="password" aria-describedy="passerror"/>
        <div id="passerror" class="form-text"><?php echo $password_err;?></div>
    </div>
    <button type="submit" class="btn btn-success" value="Login" name="Login">Login</button>
    <button type="reset" class="btn btn-primary" value="Ulang" name="Ulang">Ulang</button>
</form>
<a href="registration.php" class="link-dark">Belum terdaftar?</a>
</div>
</div>
</div>
</body>
</html>