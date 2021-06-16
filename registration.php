<?php
//koneksi dengan database sebelum menunjukkan atau memproses form.
include("connect.php");
//username, password, email, dan kesalahan-kesalahannya disetting kosong.
$username = $password = $confirm_password = $email ="";
$username_err = $password_err = $confirm_password_err = $email_err ="";
//form hanya akan diproses jika tombol daftar ditekan.
if(isset($_POST['Daftar'])){
    //mengecek apakah username kosong.
    if(empty(trim($_POST["username"]))){
        $username_err = "Username tidak boleh kosong.";
    }
    //mengecek apakah username mengikuti karakter tertentu (menggunakan regex)
    elseif(!preg_match('/^[A-Za-z0-9_]+$/',trim($_POST["username"]))){
        $username_err = "Username hanya boleh mengandung huruf, angka, dan underscore.";
    }
    //mengecek apakah username telah diambil. jika tidak, username yang kosong
    //diganti oleh username di POST. jika ya, error username akan didefinisikan
    else{
        $checku=pg_prepare("checkuname", 'SELECT user_id FROM users WHERE username = $1');
        $checku=pg_execute("checkuname",array(trim($_POST["username"])));
        if(pg_num_rows($checku)==1){
            $username_err = "Username sudah diambil";
        }
        else{
            $username=trim($_POST["username"]);
        }
    }    
    //mengecek apakah password kosong
    if(empty(trim($_POST["password"]))){
        $password_err = "Password tidak boleh kosong.";
    }//mengecek panjang string dari password, harus minimal 6 karakter.. 
    elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password harus memiliki minimal 6 karakter.";
    }//jika cek terlewati, passsword kosong diganti password di POST. jika tidak, error akan didefinisikan.
     else{
        $password = trim($_POST["password"]);
    }
    //mengecak apakah konfirmasi password ada.
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Harap konfirmasi password.";     
    }//mengecek apakah password sama dengan password konfirmasi
    //confirm password didefinisikan terlebih dahulu sebagai confirm yang ada di post, lalu dibandingkan. 
    //jika beda, error akan didefinisikan
    else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password tidak match.";
        }
    }//mengecek apakah email kosong
    if(empty(trim($_POST["email"]))){
        $email_err = "Email tidak boleh kosong.";     
    }//mengecek format email
    elseif(!preg_match('/^[A-Za-z0-9-_.+%]+@[A-Za-z0-9-.]+.[A-Za-z]{2,4}$/', trim($_POST["email"]))){
        $email_err = "Email diformat dengan salah";
    }//mengecek keunikan email
    else{
        $checke=pg_prepare("checkemail", 'SELECT user_id FROM users WHERE email = $1');
        $checke=pg_execute("checkemail",array(trim($_POST["email"])));
        if(pg_num_rows($checke) == 1){
        $email_err = "Email sudah diambil";}
        else{
            $email=trim($_POST["email"]);
        }
    }//jika terlewati, email didefinisikan sebagai email yang di post. jika tidak, akan muncul error email.
    //query hanya terjadi jika semua data tidak error.
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)){
        //password di-hash untuk keamanan.
        $insert_password = password_hash($password, PASSWORD_DEFAULT);
        //menggunakan prepared statement untuk mencegah SQL injection.
        $insert=pg_prepare("insert", 'INSERT INTO users(username,password,email) values($1,$2,$3)');
        $insert=pg_execute("insert",array($username,$insert_password,$email));
        header('location:login.php?status=regsukses'); 
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="bootstrap-5.0.1-dist/css/bootstrap.css" />
	<title>Registration</title>
<head>
<body>
<header>
    <div class="container">
	<h2 class="text-center">Pendaftaran</h2>
    </div>
</header>
<!-- form action menuju ke dokumen sendiri -->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
<div class="container">
<div class="card bg-light text-black">
<div class="card-body">
    <div class="mb-3">
		<label for="username">Username: </label>
		<input type="text" name="username" placeholder="Username" class="form-control" aria-describedby="unameerror" id="username"/>
        <div id="unameerror" class="form-text"><?php echo $username_err;?></div>
    </div>
    <div class="mb-3">
        <label for="email">Email: </label>
        <input type="text" name="email" placeholder="example@site.com" class="form-control" aria-describedby="emailerror" id="email"/>
        <div id="emailerror" class="form-text"><?php echo $email_err; ?></div>
    </div>
    <div class="mb-3">
		<label for="password">Password: </label>
        <input type="text" name="password" placeholder="password" class="form-control" aria-describedby="passworderror" id="password"/>
        <div id="passworderror" class="form-text col-md-6"><?php echo $password_err; ?></div>
    </div>
    <div class="mb-3">
		<label for="confirm_password">Ulangi Password: </label>
        <input type="text" name="confirm_password" class="form-control" aria-describedby="conferror" id="confpass"/>
        <div id="conferror" class="form-text col-md-6"><?php echo $confirm_password_err; ?></div>
    </div>
    <button type="submit" class="btn btn-success" value="Daftar" name="Daftar">Daftar</button>
    <button type="reset" class="btn btn-primary" value="Ulang" name="Ulang">Ulang</button>
</form>
</div>
</div>
</div>
</body>
</html>