<?php

include 'dbconn.php';

$newReset = $_GET["reset"];
$roomName = $_GET["roomName"];
$puzzleDif = $_GET["difficulty"];
$oldReset = $_SESSION["oldReset"];

$sql = "SELECT puzzle FROM rooms WHERE roomName=?";
$stmt = mysqli_stmt_init($conn);
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $roomName);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $puzzle);
mysqli_stmt_fetch($stmt);

$reset = $newReset - $oldReset;

if($reset){
	$puzzle = createSudoku($puzzleDif);
	
	$sql = "UPDATE rooms SET puzzle = ? WHERE roomName = ?";
	$stmt = mysqli_stmt_init($conn);
	if($stmt = mysqli_prepare($conn, $sql)){
		mysqli_stmt_bind_param($stmt, "ss", $puzzle, $roomName);
		mysqli_stmt_execute($stmt);
		if ($debug) {printf("new puzzle saved<br>");}
		mysqli_stmt_close($stmt);
	}else{
		if ($debug) {printf("new puzzle not saved<br>");}
	}
	echo "<div id='printP'>";
	displayPuzzle($puzzle);
	$_SESSION["oldReset"] = $newReset;
	if ($debug) {echo "reset: ".$reset."<br>";}
	echo "</div>";
}
else if ($puzzle){
	if ($debug) {printf("room: %s exists\n", $roomName);}
	echo "<div id='printP'>";
	displayPuzzle($puzzle);
	echo "</div>";
	mysqli_stmt_close($stmt);
	
}else{
	$puzzle = createSudoku($puzzleDif);

	$sql = "INSERT INTO rooms(roomName, puzzle) VALUES(?,?)";
	$stmt = mysqli_stmt_init($conn);
	if($stmt = mysqli_prepare($conn, $sql)){
		mysqli_stmt_bind_param($stmt, "ss", $roomName, $puzzle);
		mysqli_stmt_execute($stmt);
		if ($debug) {printf("room saved<br>");}
		mysqli_stmt_close($stmt);
	}else{
		if ($debug) {printf("room not saved");}
	}
	echo "<div id='printP'>";
	displayPuzzle($puzzle);
	echo "</div>";
}
?>
<script>
	oldPuzzle = <?php echo "'".$puzzle."'"; ?>;
</script>

<form class="reset" method=GET action=<?php echo $_SERVER['PHP_SELF']; ?> >
	<?php ?>
	<hr>
	<h2>Reset room:</h2>
	Select new difficulty:
	<input type="radio" name="difficulty" value="e">Easy
	<input type="radio" name="difficulty" value="m" checked>Medium
	<input type="radio" name="difficulty" value="h">Hard<br><br>
	<input type="hidden" name="reset" value=<?php echo $newReset+1 ?>>
	<input type="hidden" name="roomName" value="<?php echo $roomName; ?>">
	<input class="btn btn-primary" type="submit">
</form>

	
<?php
//TODO: make a real sudoku generator
//$puzzle = array(array(),array(),array(),array(),
//		array(),array(),array(),array(),array());
//for ($i=0; $i<9; $i++) {
//	$digits = array(1,2,3,4,5,6,7,8,9);
//	while($digits){
//		$col = 9-count($digits);
//		$row = $i;
//		$digits2 = $digits;
//		$used = array();
//		while($row%3>0){
//			$row-=1;
//			$col1 = floor($col/3)*3;
//			$col2 = floor($col/3)*3+1;
//			$col3 = floor($col/3)*3+2;
//			array_push($used, $puzzle[$row][$col1]); 
//			array_push($used, $puzzle[$row][$col2]);  
//			array_push($used, $puzzle[$row][$col3]); 	
//		}
//		while($row>0){
//			$row-=1;
//			array_push($used, $puzzle[$row][$col]);
//		}
//		$digits2 = array_diff($digits2, $used);
//        echo $digits2."<br>";
//		if (count($digits)<=0){
//			echo "puzzle impossible";
//			break;
//		}
//		$randIndex = array_rand($digits2);
//		array_push($puzzle[$i], $digits2[$randIndex]);
//		$digits = array_diff($digits, array($digits2[$randIndex]));
//	}
//}

// createSudoku generates the sudoku puzzle.
//	Args:
//		$diff: (string) difficulty of puzzle. 
//			Takes "e", "m", or "h".
//	Returns:
//		$pStr: (string) generated puzzle.
function createSudoku($diff){
	$puzzle = array(array(),array(),array(),array(),
				array(),array(),array(),array(),array());
	$digits = array(1,2,3,4,5,6,7,8,9);
	
	// shuffle the items of $digits into the first row of $puzzle.
	while ($digits) {
		$randIndex = array_rand($digits);
		array_push($puzzle[0], $digits[$randIndex]);
		$digits = array_diff($digits, array($digits[$randIndex]));
	}
	// Randomly generate an offset to shift row 0 by
	// to generate the remainder of the puzzle.
	$shift1 = rand(1,2);
	$a1 = array_slice($puzzle[0], 3*$shift1);
	$a2 = array_slice($puzzle[0], 0, 3*$shift1);
	$puzzle[1] = array_merge($a1, $a2);
	$shift2 = 3-$shift1;
	$a1 = array_slice($puzzle[0], 3*$shift2);
	$a2 = array_slice($puzzle[0], 0, 3*$shift2);
	$puzzle[2] = array_merge($a1, $a2);

	$shift3 = rand(1,2);
	$a1 = array_slice($puzzle[0], $shift3);
	$a2 = array_slice($puzzle[0], 0, $shift3);
	$puzzle[3] = array_merge($a1, $a2);
	$shift1 = rand(1,2);
	$a1 = array_slice($puzzle[3], 3*$shift1);
	$a2 = array_slice($puzzle[3], 0, 3*$shift1);
	$puzzle[4] = array_merge($a1, $a2);
	$shift2 = 3-$shift1;
	$a1 = array_slice($puzzle[3], 3*$shift2);
	$a2 = array_slice($puzzle[3], 0, 3*$shift2);
	$puzzle[5] = array_merge($a1, $a2);

	$shift4 = 3-$shift3;
	$a1 = array_slice($puzzle[0], $shift4);
	$a2 = array_slice($puzzle[0], 0, $shift4);
	$puzzle[6] = array_merge($a1, $a2);
	$shift1 = rand(1,2);
	$a1 = array_slice($puzzle[6], 3*$shift1);
	$a2 = array_slice($puzzle[6], 0, 3*$shift1);
	$puzzle[7] = array_merge($a1, $a2);
	$shift2 = 3-$shift1;
	$a1 = array_slice($puzzle[6], 3*$shift2);
	$a2 = array_slice($puzzle[6], 0, 3*$shift2);
	$puzzle[8] = array_merge($a1, $a2);

	// Erase certain amount of elements depending 
	// on difficulty setting.
	if ($diff == 'e' or $diff == 'easy'){
		for ($i=0; $i<9; $i++) {
			for ($j=0; $j<9; $j++) {
				if(rand(1,5)>3){
					$puzzle[$i][$j] = 0;
				}
			}
		}
	}elseif($diff == 'm' or $diff == 'medium'){
		for ($i=0; $i<9; $i++) {
			for ($j=0; $j<9; $j++) {
				if(rand(1,5)>2){
					$puzzle[$i][$j] = 0;
				}
			}
		}
		
	}elseif($diff == 'h'or $diff == 'hard'){
		for ($i=0; $i<9; $i++) {
			for ($j=0; $j<9; $j++) {
				if(rand(1,9)>2){
					$puzzle[$i][$j] = 0;
				}
			}
		}	
	}else{
		echo "Difficulty not selected?<br>";
		echo $diff."<br>";
		echo "...<br>";
	}
	// change the puzzle from a multidementional 
	// array to a string.
	$p1 = array();
	foreach($puzzle as $i) {
		$p2 = implode("", $i);
		array_push($p1, $p2);
	}
	$pStr = implode("", $p1);
	return $pStr;
	
}

// Print out Sudoku in a HTML table
function displayPuzzle($puzzle){
	echo "<table>";
	for($h=0; $h<9; $h++){
		// if 3rd or 6th row print seperator
		if ($h==3 or $h==6){
			echo "<tr>";
			for($j=0;$j<11;$j++){
				if ($j==3 or $j==7) {
					echo "<td></td>";
				}else{
					echo "<td>-------</td>";
				}
			}	
			echo "</tr>";
		}
		echo "<tr>";
		for($i=0; $i<9; $i++) {
			if ($i==3 or $i==6) {
				// if 3rd or 6th column print seperator
				echo "<td>|</td>";
			}
			echo "<td>";
			// if puzzle datum is not zero it must be a letter or a number
			if ($puzzle[$h*9+$i]){
				$chr = ord($puzzle[$h*9+$i]);
				// if puzzle datum is a-i print its number value in an input field
				if($chr>96 and $chr<106) {
					$chrToNum = chr($chr-48);
					printf ('<input value="%s" id="index%s" type="text" 
					maxlength="1" size="1" onchange="storeVal(%s, oldPuzzle)">', 
					$chrToNum, strval($h*9+$i), strval($h*9+$i));
				}else{ // if datum is a number print the number
					print($puzzle[$h*9+$i]);
				}				
			}else{ // if datum is '0' print empty input field
				printf ('<input id="index%s" type="text" 
				maxlength="1" size="1" onchange="storeVal(%s, oldPuzzle)">', 
				strval($h*9+$i), strval($h*9+$i));			
			}
			echo "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
}

?>

<script>
// uses ajax to send new puzzle data to database
// gets called when input field detects change
// index = index to be modified
// puzzle = the global puzzle variable, oldPuzzle
// sets value of modified field to 1-9 or ""
function storeVal(index, puzzle) {
	id = "index"+index;
	//alert("storing value: "+puzzle+"at index: "+index);
	if(!document.getElementById(id)) {
		alert("id not found: "+id);
	}
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById(id).value = this.responseText;	
		}
	};
	xhttp.open("POST", "setboard.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("index="+id+"&value="+document.getElementById(id).value+
			   "&puzzle="+puzzle+"&roomName="+"<?php echo $roomName ?>");
	
}
	
// obtains most recent puzzle data newP from database with ajax
// compares global variable oldPuzzle to newP
// updates only the new values found
// updates oldPuzzle to new puzzle data
function getVal() {
	var newP;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			newP = this.responseText;
			let oldP = oldPuzzle;
			for (let i=0; i<oldP.length; i++){
				if (newP[i] != oldP[i]) {
					//alert(i+" used to be "+oldP[i]+" now is "+newP[i]+" "+newP);
					let index = 'index'+i;
					document.getElementById(index).value = String.fromCharCode(newP[i].charCodeAt(0)-48);
					
					// if new value is backspace
					if (newP[i]=='0'){
						document.getElementById(index).value = "";
					}
				}	
			}
			oldPuzzle = newP;
		}
	};
	xhttp.open("POST", "getboard.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("roomName="+"<?php echo $roomName; ?>");
}

// update browser using getVal every 2 seconds
setInterval(getVal, 2000);

</script>



