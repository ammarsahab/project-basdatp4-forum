<?php
//cek login
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
    header("location: login.php");
    exit;
}
include('connect.php');
//cek isu
if(!isset($_GET['id'])){
    echo "Tidak ada isu untuk disetujui.";
    exit;}
$asncheck=pg_prepare("asncheck", 'SELECT asn_id from asn 
join govtagency
on asn.workplace_id=govtagency.govtagency_id
left join issues
on govtagency.govtagency_id=issues.respagency_id
where issue_id=$1 AND asn_id=$2');
$asncheck=pg_execute("asncheck",array($_GET['id'],$_SESSION["id"]));
$isasn=pg_num_rows($asncheck);
//cek apakah asn, dan dengan izin (tempat kerja sama)
if($isasn==0){
    echo "Anda tidak memiliki izin menyetujui.";
    exit;
}
//cek apakah telah disetujui
$hasbeenappr=pg_prepare("check", 'SELECT * from approval_asn where approvedissue_id=$1');
$hasbeenappr=pg_execute("check", array($_GET['id']));
$istappr=pg_num_rows($hasbeenappr);
if($istherappr==1){
    echo "Isu sudah disetujui.";
    exit;
}
//proses setuju
$prosesappr=pg_prepare("facilitate", 'INSERT INTO approval_asn(approvedissue_id, asnapprover_id)
VALUES ($1,$2)');
$prosesappr=pg_execute("facilitate", array($_GET['id'],$_SESSION['id']));
if($prosesappr){
    header('Location: issues.php?status=apprsukses');
}else{
    header('Location: issues.php?status=gagal');
}
?>