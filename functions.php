<?php
function cmd_send($command) {
	global $socket;
	$code = fputs($socket, $command."\n\r");
	return $code;
}

function say($message) {
	global $accessArray;
	
	foreach ($accessArray as $person) {
		cmd_send("PRIVMSG " . substr($person['hostmask'], 0, strpos($person['hostmask'], "!")) . " :" . $message);
	}
}

function format($seconds) {
	$preHours = $seconds - ($seconds % 3600);
	$preMinutes = $seconds - ($seconds % 60);
	
	$hours = $preHours / 3600;
	$minutes = $preMinutes / 60;
	$second = $seconds - ($preHours + $preMinutes);
	
	return "[" . $hours . ":" . $minutes . ":" . $second . "]";
}

function searchAccess($hostmask, $accessArray) {
	$return = FALSE;
	
	foreach (array_keys($accessArray) as $userPos) {
		if ($accessArray[$userPos]['hostmask'] == $hostmask) {
			$return = TRUE;
		}
	}
	
	if ($return !== FALSE) {
		return $userPos;
	} else {
		return false;
	}
}

function talk($where, $message) {
	$array = explode("\n", $message);
	
	foreach ($array as $msg) {
		cmd_send("PRIVMSG " . $where . " :" . $msg);
	}
}
?>
