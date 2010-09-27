return function($message) {
	global $nick;
	global $initiated;
	
	$who = $message->getNick();
	
	if ($nick != $who && $initiated == TRUE) {
		global $postIntro;
		global $rules;
		
		talk($message->getNick(), $postIntro . "\n" . $rules);
	}
}
?>
