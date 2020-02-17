<?php

//Update puzzle in database with new puzzle value
//
//$index = index of value to be updated
//$value = new value;
//$puzzle = current puzzle before update
//$roomname = puzzle room to update
//
//echoes new num if value is between 1 and 9, otherwise echoes ""

include 'dbconn.php';

$index = $_POST["index"];
$value = $_POST["value"];
$puzzle = $_POST["puzzle"];
$roomName = $_POST["roomName"];

// if value is a number between 1 and 9
if ($value and ord($value)>48 and ord($value)<58){
		$tempVal = chr(ord($value)+48);
		$puzzle[intval(substr($index, 5))] = $tempVal;
}else{
	$puzzle[intval(substr($index, 5))] = '0';
	$value = "";
}

$sql = "UPDATE rooms SET puzzle = ? WHERE roomName = ?";
$stmt = mysqli_stmt_init($conn);
if($stmt = mysqli_prepare($conn, $sql)){
	mysqli_stmt_bind_param($stmt, "ss", $puzzle, $roomName);
	mysqli_stmt_execute($stmt);
	if ($debug) {printf("new puzzle value saved<br>");}
	mysqli_stmt_close($stmt);
}else{
	if ($debug) {echo mysqli_stmt_error($stmt);}
}

echo $value;
?>
