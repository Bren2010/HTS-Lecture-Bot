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
	global $channel;
	
	$array = explode("\n", $message);
	
	foreach ($array as $msg) {
		cmd_send("PRIVMSG " . $where . " :" . $msg);
		
		// Added for recording.
		if ($channel == $where) {
			$parser = new ircMsg(":" . $nick . "!" . $nick . "@bren2010.com PRIVMSG " . $where . " :" . $msg . "\n");
			
			$lectorText = smartResponse($parser);
			
			echo $lectorText['text'];
		}
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
		$array['text'] = date("[h:i:s] ", time()) . " " . $text . "\n";
		$array['record'] = date("[h:i:s] ", time()) . " " . $record;
		
		if ($record == FALSE) {
			$array['text'] = "// " . $array['text'];
			$array['record'] = "// " . $array['record'];
		}
	} else {
		$array['text'] = "";
		$array['record'] = FALSE;
	}

	return $array;
}
?>
