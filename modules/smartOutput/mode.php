//<?php
return function($data) {
	if ($data['command'] == "mode") {
		// Start of variable declaration to cover from the transition.
		$message = $data['message'];
		
		$nick = $message->getNick();
		$command = strtolower($message->getCommand());
		$parameters = $message->getParameters();
		
		$text = "";
		// End.
		
		if (isset($parameters[1])) {
			$where = $parameters[0];
			$modes = $parameters[1];
		} else {
			global $nickArray;
			global $socketKey;
			
			$nick = $nickArray[$socketKey];
			
			$where = $nick;
			$modes = $parameters[0];
			
		}
		
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
		
		$text = trim($preText);

		// Start of functions copyandpasta to cover from the transition.
		if ($text != "") {
			echo time() . " " . $text . "\n";
		}
		// End.
	}
}
?>
