//<?php
return function($message) {
	global $registered;
	
	$params = $message->getParameters();
	
	if (!empty($params[1])) {
		$text = trim($params[1]);
		
		if ($registered == TRUE && $message->getNick() == "NickServ" && $text == "If you do not change within one minute, I will change your nick.") {
			global $NSpassword;
			say("privmsg", "NickServ", "IDENTIFY " . $NSpassword);
		}
	}
	
	return true;
}
?>
