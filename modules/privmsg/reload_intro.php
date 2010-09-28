
return function ($message) {
	global $nick;
	
	$parameters = $message->getParameters();
	
	$where = $parameters[0];
	unset($parameters[0]);
	$msg = trim(implode(" ", $parameters));
	
	if ($where == $nick && $msg == "reload_intro") {
		global $accessArray;
		
		$hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
		
		$search = searchAccess($hostmask, $accessArray);
		
		if ($search !== FALSE) {
			$level = $accessArray[$search]['level'];
			
			if ($level == '2') { // <-- Requires operator class
				$config = parse_ini_file("config.ini", true);
				
				global $intro;
				global $postIntro;
				global $rules;
				global $iniVar;
				global $iniVal;
				
				$intro = str_replace($iniVar, $iniVal, $config['lecture']['intro']);
				$postIntro = str_replace($iniVar, $iniVal, $config['lecture']['postIntro']);
				$rules = str_replace($iniVar, $iniVal, $config['lecture']['rules']);
				
				say("The intro, post intro, and rules have been reloaded from the ini.");
			}
		}
	}
}
?>
