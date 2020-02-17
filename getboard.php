<?php
// pulls new puzzle data from database and echoes it

include 'dbconn.php';

$sql = "SELECT puzzle FROM rooms WHERE roomName='".$_POST["roomName"]."'";
$result = mysqli_query($conn, $sql) or die(mysqli_error($con));
$p = implode("",mysqli_fetch_row($result));
if ($p){
	echo $p;
}

?>