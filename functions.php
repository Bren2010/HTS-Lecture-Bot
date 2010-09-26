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
	
	$unFhours = $preHours / 3600;
	$unFminutes = $preMinutes / 60;
	$unFseconds = $seconds - ($preHours + $preMinutes);
	
	$hours_len = strlen($unFhours);
	$minutes_len = strlen($unFminutes);
	$seconds_len = strlen($unFseconds);
	
	if ($hours_len != 2) {
		$format_hours = "0" . $unFhours;
	} else {
		$format_hours = $unFhours;
	}
	
	if ($minutes_len != 2) {
		$format_minutes = "0" . $unFminutes;
	} else {
		$format_minutes = $unFminutes;
	}
	
	if ($seconds_len != 2) {
		$format_seconds = "0" . $unFseconds;
	} else {
		$format_seconds = $unFseconds;
	}
	
	return "[" . $format_hours . ":" . $format_minutes . ":" . $format_seconds . "]";
}

function searchAccess($hostmask, $accessArray) {
	$return = FALSE;
	
	foreach (array_keys($accessArray) as $userPos) {
		if ($accessArray[$userPos]['hostmask'] == $hostmask) {
			$return = $userPos;
			break;
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
