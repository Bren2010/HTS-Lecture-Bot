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

// NickServ data.
$nick = $config['nickserv']['nick'];
$registered = $config['nickserv']['registered'];
$NSpassword = $config['nickserv']['password'];

// Access list.
$accessArray = $config['access'];

// Lecture Specific Info
$iniVar = array("%CHANNEL", "%LECTURER", "\\n");
$iniVal = array($config['server']['channel'], $config['lecture']['lector'], "\n");

$intro = str_replace($iniVar, $iniVal, $config['lecture']['intro']);
$postIntro = str_replace($iniVar, $iniVal, $config['lecture']['postIntro']);
$rules = str_replace($iniVar, $iniVal, $config['lecture']['rules']);

$lector = $config['lecture']['lector'];

// Server Settings
$server = $config['server']['server'];
$port = $config['server']['port'];
$channel = $config['server']['channel'];
$serverPass = $config['server']['password'];

// System Settings
$daemon = $config['system']['daemon']; // Run the bot as a daemon.
$output = $config['system']['output']; // Whether or not to output data sent to it.
$rawOutput = $config['system']['rawOutput']; // Whether or not to show raw output.
$errors = $config['system']['errors']; // Used to determine if errors should be outputted.

/******************* CODE ********************/
if ($errors) {
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}

if ($daemon) {
	if(pcntl_fork()) die(); // This turns the bot into a daemon.
}

set_time_limit(0); // So PHP never times out

require_once("functions.php");
require_once("modules.php");
require_once("ircMsg.php");

$modules = new modules();

$quietArray = array(); // Array to not accept commands from (May be users/channels).
$globalAction = "";  // For trans-module actions.

$lecture = explode("\n\n", trim(file_get_contents("lecture.txt")));

$position = 0;
$mode = "q";
$initiated = FALSE;

$socket = fsockopen($server, $port) or die ("Could not connect.\n");

sleep(1);

if (!empty($serverPass)) {
	cmd_send("PASS :" . $serverPass);
}

cmd_send("USER " . $nick . " " . $nick . " " . $nick . " : " . $nick); // Register user data.
cmd_send("NICK " . $nick); // Change nick.
cmd_send("JOIN " . $channel); // Join default channel.

while (1) {
	while ($data = fgets($socket)) {
		$pingCheck = substr($data, 0, strlen("PING :"));
			
		if ($pingCheck == "PING :") { // Play ping-pong.
			$pong = substr($data, strlen("PING :"));
			
			cmd_send("PONG :" . $pong);
		} elseif (!empty($data)) {
			$message = new ircMsg($data);

			if ($output && $rawOutput == FALSE) {
				$smartData = array('command' => strtolower($message->getCommand()), 'message' => $message);
				
				$modules->hook('smartOutput', $smartData);
			} elseif ($output == TRUE) {
				echo $data;
			}
		
			$command = trim(strtolower($message->getCommand()));
			
			if ($command == "privmsg") {
				// First part of this is ensuring that the user/channel isn't on the ignore list.
				$parameters = $message->getParameters();
				
				$preWhere = trim($parameters[0]);
				
				if ($preWhere == $message->getNick()) {
					$where = $message->getNick();
				} else {
					$where = $parameters[0];
				}
				
				$chanSearch = array_search($where, $quietArray);
				$nickSearch = array_search($message->getNick(), $quietArray);
			
				if ($chanSearch === FALSE && $nickSearch === FALSE) {					
					$modules->hook($command, $message);
				}
			} else {
				$modules->hook($command, $message);
			}
		} 
	}
}
?>
