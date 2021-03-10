<?php
require("sqldbinfo.php");

// Connect to database
$con = mysqli_connect($server,$username,$password,$database) or die(mysqli_error());
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Make strings uppercase and sanitize them
$itu_mail = strtoupper( mysqli_escape_string($con, $_POST['itu_mail']) );
$description = mysqli_escape_string($con, nl2br($_POST['description']));
$headline = mysqli_escape_string($con, $_POST['headline']);
$image_link = mysqli_escape_string($con, $_POST['image_link']);

// Check if user is already in the database 
$check_player_sql = mysqli_query($con, "SELECT * FROM $table WHERE description='$description' LIMIT 1");

if(mysqli_num_rows($check_player_sql) == 0) {
	
	// If no users found
	if(mysqli_affected_rows($con) == 0) {
		//New user sql
		$new_sub_sql = "INSERT INTO $table (itu_mail, description, headline, image_link, points) VALUES ('$itu_mail', '$description', '$headline', '$image_link', 0)"; 
		
		// If user is not in the database, add them
		if (mysqli_query($con, $new_sub_sql)) {
		  echo mysqli_insert_id($con);
		}
		else {
		  echo "Error adding player: <br>" . mysqli_error($con);
		}
	}
	else {
		echo "It seems you're already registered!";
	}
}

?>