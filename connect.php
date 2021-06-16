<?php
//File ini bertujuan untuk melakukan koneksi
//database postgres menggunakan php
$db = pg_connect('host = localhost dbname=project user=postgres password=Kremlin1985');
if(!$db){
	die("Gagal terhubung dengan database: ".pg_connect_error());
}
?>