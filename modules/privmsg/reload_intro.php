
return function ($message) {
	global $accessArray;
	global $initiated;
	global $nick;
	
	$parameters = $message->getParameters();
	$where = $parameters[0];
    
    if ($where != $nick) return;
    
    $hostmask = $message->getNick() . "!" . $message->getName() . "@" . $message->getHost();
    $search = searchAccess($hostmask, $accessArray);
    
    if (!$search) return;
    
    if ($level = $accessArray[$search]['level'] != 2) return;
	
    $msg = trim($parameters[1]);
    
	if ($msg != 'reload intro') return;

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
};
?>
