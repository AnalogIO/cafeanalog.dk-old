<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/libraries/shiftplanning.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/libraries/cache.inc');
	
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

	echo "<h1>From Cache</h1>";
	print_r(get_cached_opening_hours());
	
	
	echo "<h1>From API</h1>";

	print_r($shifts);

	echo "<h1>From API, after USort</h1>";
	
	$testArray = $shifts["data"];

	usort($testArray, "cmp");
	print_r($testArray);
	?>