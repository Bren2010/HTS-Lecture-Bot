//<?php
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
    
	if ($msg != 'reload access') return;

	$config = parse_ini_file("config.ini", true);
	
	$accessArray = $config['access'];
	
	say ("The access list has been refreshed!");
};
?>
