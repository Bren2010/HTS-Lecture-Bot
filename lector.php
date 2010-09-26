 <?php
/*
Title: HTS Lector Bot
Developer: Brendan Mc. (Bren2010)
Purpose: To record/give lectures via IRC for HTS

Date Started: Tuesday, September 7, 2010
Date Finished: Sunday, September 12, 2010
Version: 2.0
*/
/*************** CONFIGURATION ***************/
$config = parse_ini_file("config.ini", TRUE);

$nick = $config['nickserv']['nick'];
$registered = $config['nickserv']['registered'];
$password = $config['nickserv']['password'];

$accessArray = $config['access'];

$server = $config['server']['server'];
$port = $config['server']['port'];
$channel = $config['server']['channel'];

$iniVar = array("%CHANNEL", "%LECTURER");
$iniVal = array($config['server']['channel'], $config['lecture']['lector']);

$intro = str_replace($iniVar, $iniVal, $config['lecture']['intro']);
$postIntro = str_replace($iniVar, $iniVal, $config['lecture']['postIntro']);
$rules = str_replace($iniVar, $iniVal, $config['lecture']['rules']);

$lector = $config['lecture']['lector'];

// System Settings
$daemon = $config['system']['daemon']; // Run the bot as a daemon.
$output = $config['system']['output']; // Whether or not to output data sent to it.
$record = $config['system']['record']; // Used for recording the lecture.

/******************* CODE ********************/
//error_reporting(0);

if ($daemon == TRUE) {
	if(pcntl_fork()) die(); // This turns the bot into a daemon.
}

set_time_limit(0); // So PHP never times out

require_once("functions.php");
require_once("modules.php");

$modules = new modules();

$lecture = explode("\n", trim(file_get_contents("lecture.txt")));

$position = 0;
$mode = "q";
$initiated = TRUE;

$startTime = time();
$lectureHandle = fopen("recording.log", "w");

$socket = fsockopen($server, $port);
cmd_send("USER " . $nick . " " . $nick . " " . $nick . " : " . $nick); // Register user data.
cmd_send("NICK " . $nick); // Change nick.
			
cmd_send("JOIN " . $channel); // Join default channel.

while (1) {
	while ($data = fgets($socket)) {
		if ($output == TRUE) {
			echo $data;
		}
		
		$pingCheck = substr($data, 0, strlen("PING :"));
		
		if ($pingCheck == "PING :") { // Pings are isolated because of unusual format.
			$modules->hook("ping", $data);
		} else {
			$dataArray = array();
			
			// Extract values from data.
			$search = strpos($data, " :");
			
			if ($search != FALSE) {
				$message = trim(substr($data, strpos($data, " :") + 2));
				
				$header = trim(substr($data, 1, strpos($data, " :")));
				$headerArray = explode(" ", $header);
			} else {
				$message = "";
				
				$header = trim(substr($data, 0));
				$headerArray = explode(" ", $header);
			}
			
			$who = substr($headerArray[0], 0, strpos($headerArray[0], "!"));
			print_r($headerArray);
			$what = strtolower($headerArray[1]);
			
			// Put applicable values in array based on type.
			switch ($what) {
				case "join":
					$dataArray['who'] = $who;
					$dataArray['hostmask'] = $headerArray[0];
					$dataArray['where'] = $message;
					break;
					
				case "kick":
					$dataArray['who'] = $who;
					$dataArray['hostmask'] = $headerArray[0];
					$dataArray['where'] = $headerArray[2];
					$dataArray['person'] = $headerArray[3];
					$dataArray['reason'] = $message;
					break;
					
				case "notice":
					$dataArray['who'] = $who;
					$dataArray['hostmask'] = $headerArray[0];
					$dataArray['person'] = $headerArray[2];
					$dataArray['message'] = $message;
					break;
					
				case "privmsg":
					$dataArray['who'] = $who;
					$dataArray['hostmask'] = $headerArray[0];
					$dataArray['where'] = $headerArray[2];
					$dataArray['message'] = $message;
					break;
			}
			
			// Running the hooks.
			$modules->hook($what, $dataArray);
			
			// Run the recording hook.
			$dataArray['action'] = $what;
			$modules->hook('record', $dataArray);
		}
	}
}
?>
