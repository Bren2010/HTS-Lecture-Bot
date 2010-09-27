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

$iniVar = array("%CHANNEL", "%LECTURER", "\\n");
$iniVal = array($config['server']['channel'], $config['lecture']['lector'], "\n");

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
require_once("ircMsg.php");

$modules = new modules();

$lecture = explode("\n", trim(file_get_contents("lecture.txt")));

$position = 0;
$mode = "q";
$initiated = FALSE;

if ($record == TRUE) {
	$startTime = time();
	$lectureHandle = fopen("recording.log", "w");
}

$socket = fsockopen($server, $port);
cmd_send("USER " . $nick . " " . $nick . " " . $nick . " : " . $nick); // Register user data.
cmd_send("NICK " . $nick); // Change nick.
			
cmd_send("JOIN " . $channel); // Join default channel.

while (1) {
	while ($data = fgets($socket)) {
		$pingCheck = substr($data, 0, strlen("PING :"));
		
		if ($pingCheck == "PING :") {
			$pong = substr($data, strlen("PING :"));
			cmd_send("PONG :" . $pong);
		} else {
			$message = new ircMsg($data);
			
			$command = strtolower($message->getCommand());
			
			$modules->hook($command, $message);

			
			$text = smartResponse($message);
			
			if ($output == TRUE) {
				echo $text['text'];
			}
			
			if ($record == TRUE && $initiated == TRUE && $text['record'] == TRUE && $text['text'] != "") {
				fwrite($lectureHandle, format(time() - $startTime) . " " . $text['text']);
			}
		}
	}
}
?>
