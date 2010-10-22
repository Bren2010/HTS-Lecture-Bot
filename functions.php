<?php
function cmd_send($command) {
	global $socket;
	$code = fputs($socket, $command."\n\r");
	
	global $nick;
	global $output;
	global $rawOutput;
	
	$ircMsg = new ircMsg(":" . $nick . "!" . $nick . "@plz.rewt.me " . $command);
	
	$command = strtolower($ircMsg->getCommand());
	
	if ($rawOutput == TRUE && $output == TRUE && $command == "privmsg") {
		echo $nick . "!" . $nick . "@plz.rewt.me " . $command . "\n";
	} elseif ($output == TRUE && $rawOutput == FALSE && $command == "privmsg") {
		global $modules;
		$modules->hook("smartOutput", array('command' => $command, 'message' => $ircMsg));
	}

	return $code;
}

function searchAccess($hostmask, $accessArray) {
	$return = FALSE;
	
	foreach (array_keys($accessArray) as $userPos) {
		if ($accessArray[$userPos]['hostmask'] == $hostmask) {
			$return = $userPos;
			break;
		}
	}
	
	return $return;
}

function say($message) {
	global $accessArray;
	
	foreach ($accessArray as $person) {
		cmd_send("PRIVMSG " . substr($person['hostmask'], 0, strpos($person['hostmask'], "!")) . " :" . $message);
	}
}

function talk($where, $message) {
	$array = explode("\n", $message);
	
	foreach ($array as $msg) {
		cmd_send("PRIVMSG " . $where . " :" . $msg);
	}
}
?>
