//<?php
return function($data) {
	if ($data['command'] == "notice") {
		// Start of variable declaration to cover from the transition.
		$message = $data['message'];
		
		$nick = $message->getNick();
		$command = strtolower($message->getCommand());
		$parameters = $message->getParameters();
		
		$text = "";
		// End.
		
		if (!empty($parameters[1])) {
			$noticeWhat = trim($parameters[1]);
			
			if (!empty($nick)) {
				$text = "-" . $nick . "- " . $noticeWhat;
			} else {
				$text = $noticeWhat;
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
