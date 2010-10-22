//<?php
return function($data) {
	if ($data['command'] == "privmsg") {
		// Start of variable declaration to cover from the transition.
		$message = $data['message'];
		
		$nick = $message->getNick();
		$command = strtolower($message->getCommand());
		$parameters = $message->getParameters();
		
		$text = "";
		// End.
		
		$where = $parameters[0];
		$preMsg = trim($parameters[1]);
		
		$actionStart = substr($preMsg, 0, strlen("ACTION "));
		$actionEnd = substr($preMsg, -1, 1);
		
		if ($actionStart == "ACTION " && $actionEnd == "") {
			$msg = substr($preMsg, strlen("ACTION "), -1);
			
			$text = "* " . $nick . " " . $msg;
		} else {
			$msg = $preMsg;

			$marker = substr($where, 0, 1);
			
			if ($marker == "#") {
				$text = "<" . $nick . "> " . $msg;
			} else {
				$text = ">" . $nick . "< " . $msg;
			}
		}

		// Start of functions copyandpasta to cover from the transition.
		if ($text != "") {
			echo time() . " " . $text . "\n";
		}
		// End.
	}
}
?>
