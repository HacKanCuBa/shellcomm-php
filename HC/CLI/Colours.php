<?php

namespace HC\CLI;

require_once 'Colors' . DIRECTORY_SEPARATOR . 'Color.php';

class Colours extends \Colors\Color
{

	protected $themes = [
		'shell' => ['green', 'bg_default'],
		'error' => ['red', 'bg_default'],
		'warning' => ['yellow', 'bg_default'],
		'info' => ['light_blue', 'bg_default'],
	];

	public function __construct() {
		parent::__construct();

		$this->setTheme($this->themes);
	}

	public function colour($string, $theme = 'shell', $style = 'default') {
		return $this->apply($style, $this->$theme($string));
	}

	public function addTheme($name, array $theme) {
		$this->themes[$name] = $theme;
		$this->setTheme($this->themes);
	}
}
