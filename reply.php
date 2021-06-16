<?php
// cek loffin
session_start(); 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include("connect.php");
$post ="";
$post_err="";
//masukkan poost
if(isset($_GET['Reply'])){    
    if(empty(trim($_GET["post"]))){
        header('Location: posts.php?status=none&issue='.$_GET['issue'].'&topic='.$_GET['topic'].'');
        exit;
    }
    else{
        $post = $_GET["post"];
    }  
    $creator=$_SESSION["id"];
    $insrt = pg_prepare('posts', 'INSERT INTO post(topic_id, post, post_creator_id) VALUES ($1, $2,$3)');
    $insrt = pg_execute('posts',array($_GET['topic'],$post,$creator));    
    header('Location: posts.php?status=reply&issue='.$_GET['issue'].'&topic='.$_GET['topic'].'');
    exit;
}
?>