<?php 
require("sqldbinfo.php");

$cookie_name = "analogbirthday";
$idea_id = -1;

// Connect to database
$con = mysqli_connect($server,$username,$password,$database) or die(mysqli_error());
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Make strings uppercase and sanitize them
$itu_mail = strtoupper( mysqli_escape_string($con, $_POST['itu_mail']) );
$description = mysqli_escape_string($con, $_POST['description']);

// Check if player is trying to delete
$find_player_sql = "SELECT * FROM $table WHERE itu_mail='$itu_mail' AND description='$description'";
$find_player = mysqli_query($con, $find_player_sql);

if(mysqli_num_rows($find_player) > 0) {
	$row = mysqli_fetch_array($find_player);
	$idea_id = $row["PID"];
//	echo "SUCCESS finding points";
}
else {
//  echo "No players: <br>" . mysqli_error($con);
}

if($_COOKIE[$cookie_name]) {
	setcookie($cookie_name, $idea_id . ", " . $_COOKIE[$cookie_name]);
}
else {
	setcookie($cookie_name, $idea_id . ", ");
}
echo "$idea_id";
echo "descrip" . $description . "mail" . $itu_mail;

?>