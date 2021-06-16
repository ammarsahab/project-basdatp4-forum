<?php
//mengecek login, jika tidak kembali ke login
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ){
    header("location: login.php");
    exit;
}
include('connect.php');
//mengecek apakah user ASN yang berizin mengubah status
$asncheck=pg_prepare("asncheck", 'SELECT asn_id from asn where asn_id=$1');
$asncheck=pg_execute("asncheck",array($_SESSION["id"]));
if(pg_num_rows($asncheck)==0){
    echo "Anda tidak memiliki izin mengubah status user.";
    exit;
}
//mengecek apakah tombol ubah sudah ditekan
if(isset($_POST['Ubah'])){
    $id=$_POST['id'];
    $facil=pg_prepare("facilinf", 'SELECT facil_id from facilitator where facil_id=$1');
    $facil=pg_execute("facilinf",array($id));
    $isfacil=pg_num_rows($facil);
    $asn=pg_prepare("asninf", 'SELECT asn_id, workplace_id from asn where asn_id=$1');
    $asn=pg_execute("asninf",array($id));
    //mengecek status user
    $isasn=pg_num_rows($asn);
    //jika berbeda, baru ubah. kasus pertama adalah jika dia bukan fasilitator dan ditekan
    //checkbox fasilitator (insert), kasus kedua adalah jika dia fasilitator dan tidak ditekan (delete)
    if(($isfacil==1 and empty($_POST['Facilitator'])) or ($isfacil==0 and isset($_POST['Facilitator']))){
        if(isset($_POST['Facilitator'])){
            $facilinsert=pg_prepare('facilupdate','INSERT INTO facilitator(facil_id) VALUES($1)');
            $facilinsert=pg_execute('facilupdate',array($id));
        }else{
            $facilalloc=pg_prepare('facilalloc','DELETE FROM alokasi_fasilitator WHERE facilitator_id=$1');
            $facilalloc=pg_execute('facilalloc',array($id));
            $facilrevoke=pg_prepare('facilrevoke','DELETE FROM facilitator WHERE facil_id=$1');
            $facilrevoke=pg_execute('facilrevoke',array($id));
        }
    }    //jika berbeda, baru ubah. kasus pertama adalah jika dia bukan asn dan ditekan
    //checkbox asn (insert), kasus kedua adalah jika dia asn dan checkbox tak ditekan (delete)
    if(($isasn==1 and empty($_POST['ASN'])) or ($isasn==0 and isset($_POST['ASN']))){
        if(isset($_POST['ASN'])){
            $asninsert=pg_prepare('asnupdate','INSERT INTO asn(asn_id) VALUES($1)');
            $asninsert=pg_execute('asnupdate',array($id));
        }else{
            $asnapproverevoke=pg_prepare('approverevoke','DELETE FROM approval_asn WHERE asnapprover_id=$1');
            $asnapproverevoke=pg_execute('approverevoke',array($id));
            $asnrevoke=pg_prepare('asnrevoke','DELETE FROM asn WHERE asn_id=$1');
            $asnrevoke=pg_execute('asnrevoke',array($id));
        }
    }
    //jika asn checkbox benar dan ada workplace untuk diubah, ubah workplace.
    if(isset($_POST['ASN']) and isset($_POST['workplace'])){
        $workplace_id=$_POST['workplace'];
        $workplace_update=pg_prepare('workupdate', 'UPDATE asn set workplace_id=$1 where asn_id=$2');
        $workplace_update=pg_execute('workupdate', array($workplace_id,$id));
    }
    header('Location: users.php?status=sukses');
}else{
    header('Location: users.php?status=gagal');
} ?>