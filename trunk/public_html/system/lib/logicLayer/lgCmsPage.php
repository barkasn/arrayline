<?php
// $Id$

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

class lgCmsPage extends lgPage {

	private $pageHeader;
	private $pageFooter;
	private $pageNavigationPane;

	private $cmsPageContent;

	public function __construct() {
		parent::__construct();
		$this->linkCss('main.css');
	}

	public function setContent($value) {
		$this->cmsPageContent = $value;
	}

	public function appendContent($value) {
		$this->cmsPageContent .= $value;
	}

	public function getContent() {
		return $this->cmsPageContent;
	}


	public function render() {
		$navRendered = $this->getRenderedNavigation();
		$this->content =<<<EOT
			<div class="page">
				<div class="header">
					<h2><span>Arrayline</span></h2>
				</div>
				<div class="navigation">	
					$navRendered

				</div>
				<div class="main">
					$this->cmsPageContent
				</div>
				<div class="footer">
					<div class="footer-internal">
						<p class="website"><a href="http://www.arrayline.org/">www.arrayline.org</a></p>
						<p class="copyright">Arrayline Copyright &copy; 2010 Nikolaos Barkas</p>
					</div>
				</div>
			</div>
EOT;

		parent::render();	
		
	}


	private function getRenderedNavigation() {
		$ret = <<<EOT
			<a href="index.php?requeststring=createdataset">Create Dataset</a><br />
			<a href="index.php?requeststring=viewdatasets">View Datasets</a><br />
			<a href="index.php?requeststring=viewusers">View Users</a><br />
			<a href="index.php?requeststring=createuser">Create User</a><br />
			<a href="index.php?requeststring=viewroles">View Roles</a><br />
			<a href="index.php?requeststring=viewpriviledges">View Priviledges</a><br>
			<a href="index.php?requeststring=logout">Logout</a>
EOT;
		return $ret;
	}

	
}
