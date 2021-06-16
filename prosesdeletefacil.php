<?php
//cek login
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
    header("location: login.php");
    exit;
}
//cek isu ada atau tidak
include('connect.php');
if(!isset($_GET['issue'])){
    echo "Tidak ada isu untuk ditangani.";
    header("location: home.php");
    exit;}
//cek apakah ada facil
if(!isset($_GET['facil'])){
    echo "Tidak ada fasilitator untuk ditangani.";
    header("location: home.php");
    exit;}
//cek apakah user memliki otoritas
$asnissuecheck=pg_prepare("asnissuecheck", 'SELECT asn_id from asn 
join govtagency
on asn.workplace_id=govtagency.govtagency_id
left join issues
on govtagency.govtagency_id=issues.respagency_id
where issue_id=$1 AND asn_id=$2');    
$asnissuecheck=pg_execute("asnissuecheck", array($_GET['issue'],$_SESSION['id']));
if(pg_numrows($asnissuecheck)==0){
    echo "Anda tidak memiliki izin memecat.";
    header("location: home.php");
    exit;
}
//cek kombinasi facil dan isu
$facil=pg_prepare("check", 'SELECT * from alokasi_fasilitator where allocissue_id=$1 AND facilitator_id=$2');
$facil=pg_execute("check", array($_GET['issue'],$_GET['facil']));
$isthere=pg_num_rows($facil);
if($isthere==1){
    $fire=pg_prepare("fire", 'DELETE from alokasi_fasilitator where allocissue_id=$1 AND facilitator_id=$2');
    $fire=pg_execute("fire", array($_GET['issue'],$_GET['facil']));
    header('location: issues.php?status=firesukses');
    exit;
}else{
    header('location: issues.php?status=gagal');
}
?>