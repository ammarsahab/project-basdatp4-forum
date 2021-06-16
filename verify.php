<?phpinclude("connect.php")
if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
    // Verify data
    $search=pg_prepare("searchverify", 'SELECT email, hash, active FROM users WHERE email=$1 AND hash=$2 AND active=0'));
    $search=pg_execute("searchverify",array($_GET['email'],$_GET['hash']));
    $match=pg_num_rows($search)
    echo $match;
}else{
    $msg ="Dari mana lo anjing";
}
?>
<!DOCTYPE html>
<head>
	<title>verification</title>
<head>
<body>
<header>
	<h3>Registration</h3>
    <h3><?php 
    if(isset($msg)){ 
        echo $msg;} ?></h3>
</header>
</body>
</html>