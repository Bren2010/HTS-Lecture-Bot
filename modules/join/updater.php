return function($message) {
	global $nick;
	global $initiated;
	
	$who = $message->getNick();
	
	if ($nick != $who && $initiated == TRUE) {
		global $postIntro;
		global $rules;
		
		cmd_send("NOTICE " . $who . " :" . $postIntro);
		cmd_send("NOTICE " . $who . " :" . $rules);
	}
}
?>
