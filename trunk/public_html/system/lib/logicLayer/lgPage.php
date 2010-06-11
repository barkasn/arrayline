<?php
/*

Copyright 2010 Nikolaos Barkas

This is part of Arrayline

Arrayline is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Arrayline is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.


*/
class lgPage {
	protected $title;
	protected $content;
	private $redirect;
	private $redirectTimeout;
	private $redirectUrl;
	private $cssFiles;

	public function __construct() {
		$cssFiles = array();
	}

	public function getTitle() {
		return $this->title;
	}

	public function setTitle($value) {
		$this->title = $value;
	}

	public function getContent() {
		return $this->content;
	}

	public function setContent($value) {
		$this->content = $value;	
	
	}
	
	public function setRedirect($url, $timeout) {
		$this->redirect = true;
		$this->redirectTimeout = $timeout;
		$this->redirectUrl = $url;	
		
	}

	public function linkCss($url) {
		$this->cssFiles[] = $url;
	}

	protected  function getRenderedCssLinks() {
		$ret = '';
		if ($this->cssFiles) {
			foreach ($this->cssFiles as $cssFile) {
				$ret .= '<link rel="stylesheet" type="text/css" href="styles/'.$cssFile.'" />';
			}
		}
		return $ret;
	}


	public function render() {
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Arrayline - <?php echo $this->title; ?></title>
		<?php echo $this->getRenderedCssLinks() ?>
		<?php

		if ($this->redirect) {
			echo '<meta http-equiv="refresh" content="'.$this->redirectTimeOut.';URL='.$this->redirectUrl.'" />';
		}

		?>
	</head>
	<body>
		<?php echo $this->content; ?>
	</body>
</html>

	<?php
	}
}
