<?php
session_start();
// filter input
$openFileName = $_POST["see"];
if( !preg_match('/^[\w_\.\-]+$/', $openFileName) ){
	echo "Invalid filename";
	exit;
}
// path to file
$filePath = $_SESSION["userPath"] . "/" . $_POST["see"];
// gets file MIME type, sets header to type
// from 330 wiki
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($filePath);
header("Content-Type: ".$mime);
// display file contents
readfile($filePath);
?>