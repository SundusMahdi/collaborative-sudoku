<?php
//establish connection with mysql database
//and set global debug variable

$serverName = "localhost";
$dBUser = "sundus_actual";
$dBPwd = "PurpleWebWatch!";
$dBName = "sundus_sudoku_room";

$debug = false;

$conn = mysqli_connect($serverName, $dBUser, $dBPwd, $dBName);
if (mysqli_connect_errno()) {
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}

?>