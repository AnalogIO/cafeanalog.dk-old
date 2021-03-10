<?php
require("sqldbinfo.php");

// Connect to database
$con = mysqli_connect($server, $username, $password, $database) or die(mysqli_error());
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


//Create table sql
$create_sql = "CREATE TABLE $table(PID INT NOT NULL AUTO_INCREMENT COLLATE utf8_bin,
 PRIMARY KEY(PID), 
 itu_mail VARCHAR(150) NOT NULL COLLATE utf8_bin,
 description VARCHAR(1000) NOT NULL COLLATE utf8_bin, 
 headline VARCHAR(150) NOT NULL COLLATE utf8_bin,
 image_link VARCHAR(150) NOT NULL COLLATE utf8_bin,
 points INT NOT NULL)"; 


//Execute query with error message
if (mysqli_query($con, $create_sql)) {
  echo $table . " successfully installed!";
}
else {
  echo "Error creating table: <br>" . mysqli_error($con);
}
?>