<?php
//cek login
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
    header("location: login.php");
    exit;
}
//cek isu ada atau tidak
include('connect.php');
if(!isset($_GET['id'])){
    echo "Tidak ada isu untuk difasilitasi.";
    header("location: home.php");
    exit;}
$facilcheck=pg_prepare("facilcheck", 'SELECT facil_id from facilitator where facil_id=$1');
$facilcheck=pg_execute("facilcheck",array($_SESSION["id"]));
$isfacil=pg_num_rows($facilcheck);
//cek apakah user fasilitator
if($isfacil==0){
    echo "Anda tidak memiliki izin memfasilitasi.";
    header("location: home.php");
    exit;
}
$hasbeenfaciled=pg_prepare("check", 'SELECT * from alokasi_fasilitator where allocissue_id=$1');
$hasbeenfaciled=pg_execute("check", array($_GET['id']));
$istherfacil=pg_num_rows($hasbeenfaciled);
//cek apakah isu telah ada
if($istherfacil==1){
    echo "Isu sudah terfasilitasi.";
    header("location: home.php");
    exit;
}
//masukkan isu.
$prosesfacil=pg_prepare("facilitate", 'INSERT INTO alokasi_fasilitator(allocissue_id, facilitator_id)
VALUES ($1,$2)');
$prosesfacil=pg_execute("facilitate", array($_GET['id'],$_SESSION['id']));
if($prosesfacil){
    header('Location: issues.php?status=fasilsukses');
}else{
    header('Location: issues.php?status=fasilgagal');
}
?>