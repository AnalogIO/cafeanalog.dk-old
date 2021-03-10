<?php 
require("sqldbinfo.php");

$cookie_name = "analogbirthday";

// Connect to database
$con = mysqli_connect($server,$username,$password,$database) or die(mysqli_error());
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Get post values
$itu_mail = $_POST['itu_mail'];
$description = mysqli_real_escape_string($con, $_POST['description']);
echo "descrip:" . $description;

$idea_id = $_POST['idea_id'];

// Cookie values
$cookie_row = $_COOKIE[$cookie_name];
//All upvoted ideas, formatted for SQL Query
$trimarray = trim($cookie_row, ', ');

// Get idea's current points
if($_COOKIE[$cookie_name]) {
	$find_points_sql = "SELECT * FROM $table WHERE PID NOT IN($trimarray) " . "AND PID=$idea_id LIMIT 1";
}
else {
	$find_points_sql = "SELECT * FROM $table WHERE PID=$idea_id LIMIT 1";
}
$find_points = mysqli_query($con, $find_points_sql);
if(mysqli_num_rows($find_points) > 0) {
	$row = mysqli_fetch_array($find_points);
	$old_points = $row["points"];
	$new_points = $old_points + 1;
	echo "SUCCESS finding points";
}
else {
  echo "No players: <br>" . mysqli_error($con);
}

// Update players points
if($_COOKIE[$cookie_name]) {
	$update_points_sql = "UPDATE $table SET Points = $new_points WHERE PID NOT IN($trimarray) " . "AND PID=$idea_id LIMIT 1";
}
else {
	$update_points_sql = "UPDATE $table SET Points = $new_points WHERE PID=$idea_id LIMIT 1";
}
$update_points = mysqli_query($con, $update_points_sql);

if(mysqli_affected_rows($con) > 0) {
	echo "SUCCESS updating points" . $_COOKIE[$cookie_name];
}
else {
	echo "Error updating points: <br>" . mysqli_error($con);
}

?>