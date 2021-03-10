<?php 
/* provides a way to sort the shifts, based on the start_timestamp */
function cmp($a, $b)
{
    if ($a[start_timestamp] == $b[start_timestamp]) {
        return 0;
    }
    return ($a[start_timestamp] < $b[start_timestamp]) ? -1 : 1;
}

/* gets the contents of a file if it exists, otherwise grabs and caches */
function get_cached_content($file,$url,$minutes = 60,$fn = '',$fn_args = '') {
  //vars
  $current_time = time(); $expire_time = $minutes * 60; $file_time = filemtime($file);
  //decisions, decisions
  if(file_exists($file) && ($current_time < $file_time)) {
    //echo 'returning from cached file';
    return file_get_contents($file);
  }
  else {
    //echo 'returning from cached file';
    return file_get_contents($file);
  }
}

/* gets content from a URL via curl */
function get_url($url) {
  $ch = curl_init();
  curl_setopt($ch,CURLOPT_ENCODING,""); 
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
  $content = curl_exec($ch);
  curl_close($ch);
  return $content;
}

function get_cached_opening_hours($minutes = 5) 
{
	$openingHours_cachefile = $_SERVER['DOCUMENT_ROOT'].'/libraries/cache/openingHours.txt';
	
	//cache vars
	$current_time = time();
	$expire_time = $minutes * 60;
	
	//Do we use the cache or get fresh content?
	if(!file_exists($openingHours_cachefile) || (filemtime($openingHours_cachefile) < $current_time - $expire_time)) 
	{
		// echo 'Refreshing cached file from API';
		get_opening_hours_from_api($openingHours_cachefile);
	}

	return unserialize(file_get_contents($openingHours_cachefile));
}

function get_opening_hours_from_api($openingHours_cachefile)
{
	require_once($_SERVER['DOCUMENT_ROOT'].'/libraries/shiftplanning.php');

	$shiftplanning = new shiftplanning(
	    array(
	        'key' => 'e12e131a14666b106d1d0f0fec2c9d17b3a7edfa' // enter your developer key
	    )
	);

	// check for a current active session
	// if a session exists, $session will now hold the user's information
	$session = $shiftplanning->getSession( );
	if( !$session )
	{// if a session hasn't been started, create one

		// perform a single API call to authenticate a user
		$response = $shiftplanning->doLogin(
			array(// these fields are required to login
				'username' => 'cafeanalog.dk',
				'password' => 'index.php',
			)
		);

		if( $response['status']['code'] == 1 )
		{// check to make sure that login was successful
			$session = $shiftplanning->getSession( );	// return the session data after successful login
		}
		else
		{// display the login error to the user
			echo $response['status']['text'] . "--" . $response['status']['error'];
		}
	}

	$shifts = $shiftplanning->setRequest(
	    array(
	        'module' => 'schedule.shifts',
	        'start_date' => 'today',
	        'mode' => 'overview'
	    )
	);

	if ($shifts["status"]["code"] != 1)
	{
		print_r($shifts["status"]); //for test purposes only
		exit;
	}

	$openingHours = array(
					"openOrClosed" => "closed"
					, "openOrClosedMessage" => "We're Cløsed!"
					, "open" => array()
					, "close" => array()
					, "employees" => array()
					);
	$employeeSeparator = " &amp; ";

	//TODO: Sort by start_timestamp, ascending
	
	$shiftsSorted = $shifts["data"];

	usort($shiftsSorted, "cmp");
	
	foreach ($shiftsSorted as $shift)
	{
		if ($shift["working"] > 0 && $shift["schedule_name"] == "Barista")
		{
			$employeesOnShift = getFirstNames($shift["employees"], $employeeSeparator);
			
			$nowDateTime = new DateTime("now");
			$currentOpenDateTime = new DateTime($shift["start_timestamp"]);
			$currentCloseDateTime = new DateTime($shift["end_timestamp"]);
			$employeesOnOpenShift = "";
		
			if ($currentOpenDateTime <= $nowDateTime && $nowDateTime <= $currentCloseDateTime)
			{
				$openingHours["openOrClosed"] = "open";
				$openingHours["openOrClosedMessage"] = "<h1>We're Åpen!</h1> On shift right now: " . getFirstNames($shift["employees"], $employeeSeparator);
			}		
			
			$shiftFound = false;
			for ($i = 0; $i < count($openingHours["close"]); $i++)
			{
				if ($shift["start_timestamp"] == $openingHours["close"][$i])
				{
					$shiftFound = true;
					$openingHours["close"][$i] = $shift["end_timestamp"];
				}
				elseif ($shift["end_timestamp"] == $openingHours["open"][$i])
				{
					$shiftFound = true;
					$openingHours["open"][$i] = $shift["start_timestamp"];
				}
				
				if ($shiftFound)
				{
					$openingHours["employees"][$i] .= $employeeSeparator . $employeesOnShift;
				}
			}
			
				
			if (!$shiftFound)
			{
				$openingHours["open"][] = $shift["start_timestamp"];
				$openingHours["close"][] = $shift["end_timestamp"];
				$openingHours["employees"][] = $employeesOnShift;	
			}
		}
	}
	// echo "Putting info into the file: '" . $openingHours_cachefile . "'";
	file_put_contents($openingHours_cachefile, serialize($openingHours));
}

function getFirstNames($employees, $employeeSeparator)
{
	$returnString = "";
	
	foreach ($employees as $employee)
	{
		$employeeNames = explode(" ", $employee["name"]);
		$employeeFirstname = $employeeNames[0];
		$returnString .= $employeeSeparator . $employeeFirstname;
	}
	
	$returnString = substr($returnString, strlen($employeeSeparator));

	return $returnString;
}

function get_cached_areWeOpen($minutes = 5) {
	//Get an array with opening hours from cache or directly Shiftplanning. Times are in UTC.
	$openingHours = get_cached_opening_hours();
	
	if ($openingHours["openOrClosed"] == "open")
	{
		return true;
	}
	
	return false;
}
?>