  <?php

  date_default_timezone_set('Europe/Copenhagen');
  $isOpen = json_decode(file_get_contents('http://cafeanalog.dk/api/open'))->open;
  $openingHours["openOrClosed"] = $isOpen ? "open" : "closed";
  //Get a random image from a directory of backgrounds
  $imagesDir = "img/bg/";
  $images = glob($imagesDir.$openingHours["openOrClosed"]."/" . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

  $randomImage = (empty($images) ? $imagesDir."/default.jpg" : $images[array_rand($images)]);

  $shifts = array_map("to_date", json_decode(file_get_contents('http://cafeanalog.dk/api/shifts')));

  $openHours = array();

  function to_date($elem) {
    $result = new StdClass();
    $result->Open = strtotime($elem->start);
    $result->Close = strtotime($elem->end);
    $result->Employees = array_column($elem->employees, 'firstName');
    return $result;
  }

  for ($i = 0; $i < count($shifts); $i++) {
    $shift = $shifts[$i];
    if (empty($openHours)) { array_push($openHours, $shift); continue; }
    $last = $openHours[count($openHours)-1];
    if ($shift->Open <= $last->Open && $shift->Close <= $last->Close && $last->Open <= $shift->Close) {
      $last->Open = $shift->Open;
      $openHours[count($openHours)-1] = $last;
    } else if ($last->Open <= $shift->Open && $last->Close <= $shift->Close && $shift->Open <= $last->Close) {
      $last->Close = $shift->Close;
      $openHours[count($openHours)-1] = $last;
    } else if ($shift->Open <= $last->Open && $last->Close <= $shift->Close) {
      $last->Open = $shift->Open; $last->Close = $shift->Close;
      $openHours[count($openHours)-1] = $last;
    } else if ($last->Open <= $shift->Open && $shift->Close <= $last->Close) {
      // Do nothing
    } else {
      array_push($openHours, $shift);
    }
  }

  function after_now($elem) {
    return $elem->Close > $_SERVER['REQUEST_TIME'];
  }

  //$openHours = array_values(array_filter($openHours, "after_now"));
  ?>
<!DOCTYPE html>
  <!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
  <!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
  <!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
  <!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Cafe Analog</title>
    <meta name="description" content="When is Cafe Analog open? This site will show you when Analog is open.">
    <meta name="tags" content="cafe analog open ITU IT-university close analogue caffe">
    <meta name="viewport" content="width=device-width">

    <link rel="shortcut icon" type="image/png" href="/favicon.png"/>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">
    <link href='http://fonts.googleapis.com/css?family=Gudea%7CPathway+Gothic+One' rel='stylesheet' type='text/css'>
    <script src="js/vendor/modernizr-2.6.2.min.js"></script>
  </head>
  <body>
    <div id="background">
      <img src="<?=$randomImage?>" alt="">
    </div>
    <div id="openingHours" class="longCaptions">
      <h1>We're <? echo $isOpen ? "ÅPEN" : "CLØSED"; ?>!</h1>
      <?php
        if ($isOpen) {
          $emp = implode(" & ", array_values(array_filter(array_map("to_date", json_decode(file_get_contents('http://cafeanalog.dk/api/shifts'))), "after_now"))[0]->Employees);
          echo "On shift right now: " . $emp . "\n";
        }
      ?>
      	<? if(empty($openHours)) {
  				echo "<h2>Analog is closed this week.</h2>";
			} else {
  				echo "<h2>Scheduled opening hours "; 
  				echo date("w")==0?"the next" :"this"; 
      			echo " week:</h2>";
              	
  				echo "<ol>";
  					$now = new DateTime('NOW');

                  	foreach ($openHours as $shift)
                  	{
                    	echo "<li>";
                    	echo date('D jS: H:i - ', $shift->Open);

                    	echo date('H:i', $shift->Close);

                    	/*echo " ("
                    	. $openingHours["employees"][$i] . ")";*/
                    	echo "</li>";
                  	}
              	echo "</ol>";
  				}
      	?>
      
      <br />
      <br />
      
      <div class="email">
      	Get in contact with the Analog Board <br />
        E-mail us at <a href="mailto:analog@cafeanalog.dk" style="color:black">analog(at)cafeanalog.dk</a>
      </div>
      
      
      	<!--
			Outcomment LastFm
      		<div class="music-information"></div>
      		<a class="lastfm-link" href="http://last.fm/user/AnalogIO">View our previous tracklist</a>
       		<br />
    	-->
      
    	<div class="footer">
          
          	<!-- Social media buttons -->
          	<div style="display: table; margin: 0 auto;">
            <div style="display:flex;flex-wrap:wrap">
              	<a href="https://www.facebook.com/cafeanalog" target="_blank" rel="noopener noreferrer" style="text-decoration:none;border:0;width:40px;height:40px;padding:2px;margin:5px;color:#e9e9e9;border-radius:15%;background-color:#38251a;">
                  <svg class="niftybutton-facebook" style="display:block;fill:currentColor" data-tag="fac" data-name="Facebook" viewBox="0 0 512 512" preserveAspectRatio="xMidYMid meet">
              		<path d="M211.9 197.4h-36.7v59.9h36.7V433.1h70.5V256.5h49.2l5.2-59.1h-54.4c0 0 0-22.1 0-33.7 0-13.9 2.8-19.5 16.3-19.5 10.9 0 38.2 0 38.2 0V82.9c0 0-40.2 0-48.8 0 -52.5 0-76.1 23.1-76.1 67.3C211.9 188.8 211.9 197.4 211.9 197.4z">
                    </path>
              	  </svg>
              	</a>
              <a href="https://www.instagram.com/cafeanalog" target="_blank" rel="noopener noreferrer" style="text-decoration:none;border:0;width:40px;height:40px;padding:2px;margin:5px;color:#e9e9e9;border-radius:15%;background-color:#38251a;">
                <svg class="niftybutton-instagram" style="display:block;fill:currentColor" data-tag="ins" data-name="Instagram" viewBox="0 0 512 512" preserveAspectRatio="xMidYMid meet">
              		<path d="M256 109.3c47.8 0 53.4 0.2 72.3 1 17.4 0.8 26.9 3.7 33.2 6.2 8.4 3.2 14.3 7.1 20.6 13.4 6.3 6.3 10.1 12.2 13.4 20.6 2.5 6.3 5.4 15.8 6.2 33.2 0.9 18.9 1 24.5 1 72.3s-0.2 53.4-1 72.3c-0.8 17.4-3.7 26.9-6.2 33.2 -3.2 8.4-7.1 14.3-13.4 20.6 -6.3 6.3-12.2 10.1-20.6 13.4 -6.3 2.5-15.8 5.4-33.2 6.2 -18.9 0.9-24.5 1-72.3 1s-53.4-0.2-72.3-1c-17.4-0.8-26.9-3.7-33.2-6.2 -8.4-3.2-14.3-7.1-20.6-13.4 -6.3-6.3-10.1-12.2-13.4-20.6 -2.5-6.3-5.4-15.8-6.2-33.2 -0.9-18.9-1-24.5-1-72.3s0.2-53.4 1-72.3c0.8-17.4 3.7-26.9 6.2-33.2 3.2-8.4 7.1-14.3 13.4-20.6 6.3-6.3 12.2-10.1 20.6-13.4 6.3-2.5 15.8-5.4 33.2-6.2C202.6 109.5 208.2 109.3 256 109.3M256 77.1c-48.6 0-54.7 0.2-73.8 1.1 -19 0.9-32.1 3.9-43.4 8.3 -11.8 4.6-21.7 10.7-31.7 20.6 -9.9 9.9-16.1 19.9-20.6 31.7 -4.4 11.4-7.4 24.4-8.3 43.4 -0.9 19.1-1.1 25.2-1.1 73.8 0 48.6 0.2 54.7 1.1 73.8 0.9 19 3.9 32.1 8.3 43.4 4.6 11.8 10.7 21.7 20.6 31.7 9.9 9.9 19.9 16.1 31.7 20.6 11.4 4.4 24.4 7.4 43.4 8.3 19.1 0.9 25.2 1.1 73.8 1.1s54.7-0.2 73.8-1.1c19-0.9 32.1-3.9 43.4-8.3 11.8-4.6 21.7-10.7 31.7-20.6 9.9-9.9 16.1-19.9 20.6-31.7 4.4-11.4 7.4-24.4 8.3-43.4 0.9-19.1 1.1-25.2 1.1-73.8s-0.2-54.7-1.1-73.8c-0.9-19-3.9-32.1-8.3-43.4 -4.6-11.8-10.7-21.7-20.6-31.7 -9.9-9.9-19.9-16.1-31.7-20.6 -11.4-4.4-24.4-7.4-43.4-8.3C310.7 77.3 304.6 77.1 256 77.1L256 77.1z">
                  	</path>
              		<path d="M256 164.1c-50.7 0-91.9 41.1-91.9 91.9s41.1 91.9 91.9 91.9 91.9-41.1 91.9-91.9S306.7 164.1 256 164.1zM256 315.6c-32.9 0-59.6-26.7-59.6-59.6s26.7-59.6 59.6-59.6 59.6 26.7 59.6 59.6S288.9 315.6 256 315.6z">
                  	</path>
              		<circle cx="351.5" cy="160.5" r="21.5">
                  </circle>
              	</svg>
              </a>
            </div>
          </div>
          
          <br />
          	
          	Cafe Analog <br />
      		Rued Langgaards Vej 7, 2300 Copenhagen S <br /> 
      		CVR: DK-34657343
      	</div>

  </div>
      
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.9.0.min.js"><\/script>')</script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>
  </body>
  </html>
