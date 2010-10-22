//<?php
return function($message) {
	global $nick;

	$who = $message->getNick();
	
	if ($who == $nick) {
		$parameters = $message->getParameters();

		$nick = trim($parameters[0]);
		
		global $initiator;
		global $config;
		global $botInfoMarkUp;
		global $botInfoCode;
		
		$botInfoCode['nick'] = trim($parameters[0]);
		
		$initiator = str_replace($botInfoMarkUp, $botInfoCode, $config['botInfo']['initiator']);
		
	}

}
?>
