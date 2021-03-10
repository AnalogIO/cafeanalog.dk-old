<?php
require("list_element.php");
require("sqldbinfo.php");

// Connect to database
$con = mysqli_connect($server,$username,$password,$database) or die(mysqli_error());
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Check if player is trying to delete
$find_player_sql = "SELECT * FROM $table ORDER BY rand()";
$find_player = mysqli_query($con, $find_player_sql);

while($row = mysqli_fetch_assoc($find_player)) {
	if(mysqli_num_rows($find_player) > 0) {
		$sendtoajax = "<li id='" . $row["PID"] . "'><span class='points_result'>" . $row["points"] . "</span><h3 class='headline_result'>" . $row["headline"] . "</h3><span class='descrip_result'>" . $row["description"] . "</span>";
		if($row["image_link"] == "") {
			$sendtoajax = $sendtoajax . "<div class='byline'>by <span class='itu_mail_result'>" . $row["itu_mail"] . "</span></div><div class='fb-share-button' data-href='http://cafeanalog.dk/library/?iid=" . $row["PID"] . "' data-layout='button'></div></li>";
		}
		else {
			$sendtoajax = $sendtoajax . "<div class='image_link_result'><a href='". $row["image_link"] . "' alt='Link to idea image'>Link to idea image</a></div><div class='byline'>by <span class='itu_mail_result'>" . $row["itu_mail"] . "</span></div><div class='fb-share-button' data-href='http://cafeanalog.dk/library/index.php?iid=" . $row["PID"] . "' data-layout='button'></div></li>";
		}
		echo $sendtoajax;
	}
	else {
	  echo "No players found: <br>" . mysqli_error($con);
	}
}

?>