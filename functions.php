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
	global $startTime;
	global $nick;
	
	$array = explode("\n", $message);
	
	$recording = "";
	$time = format(time() - $startTime);
	
	foreach ($array as $msg) {
		cmd_send("PRIVMSG " . $where . " :" . $msg);
		
		$recording .= $time . "<" . $nick . "> " . $msg . "\n";
	}
	
	// Added for recording.
	global $channel;
	global $record;
	
	if ($record == TRUE && $where == $channel) {
		global $lectureHandle;
		
		fwrite($lectureHandle, $recording);
	}
}

function smartResponse($message) {
	$nick = $message->getNick();
	$command = strtolower($message->getCommand());
	$parameters = $message->getParameters();
	
	$text = "";
	
	switch ($command) {
		case "join":
			$record = TRUE;
			$text = "* " . $nick . " has joined " . trim($parameters[0]) . ".";
			break;
			
		case "kick":
			$where = $parameters[0];
			$who = $parameters[1];
			
			unset($parameters[0]);
			unset($parameters[1]);
			
			$reason = trim(implode(" ", $parameters));
			
			$record = TRUE;
			$text = "* " . $who . " has been kicked from " . $where . " by " . $nick . " (" . $reason . ")";
			break;
		
		case "nick":
			$record = TRUE;
			$text = "* " . $message->getNick() . " is now known as " . trim($parameters[0]) . ".";
			break;
			
		case "notice":
			$noticeWhat = trim($parameters[1]);
			
			if (!empty($nick)) {
				$record = FALSE;
				$text = "-" . $nick . "- " . $noticeWhat;
			} else {
				$record = FALSE;
				$text = $noticeWhat;
			}
			break;
			
		case "mode":
			$where = $parameters[0];
			$modes = $parameters[1];
			
			unset($parameters[0]);
			unset($parameters[1]);
			
			$operator = substr($modes, 0, 1);
			$modeArray = str_split(substr($modes, 1));
			
			$preText = "";
			
			foreach ($parameters as $person) {
				$currMode = current($modeArray);
				
				$preText .= "* " . $message->getNick() . " sets mode " . $operator . $currMode . " on " . $person . "\n";
				
				next($modeArray);
			}
			
			$record = TRUE;
			$text = trim($preText);
			break;
			
		case "part":
			$channel = $parameters[0];
			unset($parameters[0]);
			$reason = trim(implode(" ", $parameters));
			
			$record = TRUE;
			$text = "* " . $nick . " has left " . $channel . " (" . $reason . ")";
			break;
			
		case "privmsg":
			global $channel;
			
			$where = $parameters[0];
			unset($parameters[0]);
			$msg = trim(implode(" ", $parameters));
			
			if ($where == $channel) {
				$record = TRUE;
				$text = "<" . $nick . "> " . $msg;
			} else {
				$record = FALSE;
				$text = ">" . $nick . "< " . $msg;
			}
			break;
			
		case "quit":
			$reason = trim($parameters[0]);
			
			$record = TRUE;
			$text = "* " . $nick . " has quit (" . $reason . ")";
			break;
	}
	
	$array = array();
	
	if ($text != "") {
		$array['text'] = $text . "\n";
		$array['record'] = $record;
	} else {
		$array['text'] = "";
		$array['record'] = FALSE;
	}
	
	return $array;
}
?>
