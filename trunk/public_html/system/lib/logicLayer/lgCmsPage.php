<?php
// $Id$

/*

    Arrayline, an extensible bioinfomatics data processing platform
    Copyright (C) 2010 Nikolaos Barkas

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.
 
    You should have received a copy of the GNU Affero General Public License
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

		$currentUser = lgUserHelper::getUserFromEnviroment();
		$username = $currentUser->getUsername();

		$navRendered = $this->getRenderedNavigation();
		$this->content =<<<EOT
			<div class="page">
				<div class="header">
					<h2><a href="index.php"><span>Arrayline</span></a></h2>
					<p class="moto">An extensible bioinformatics data platform</p>
					<div class="user-info">Logged in as $username [<a href="index.php?requeststring=logout">logout</a>]</div>
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
						<p class="license"><a href="index.php?requeststring=about">Arrayline is free software licenced under the terms of AGPL</a></p>
					</div>
				</div>
			</div>
EOT;

		parent::render();	
		
	}


	private function getRenderedNavigation() {
		$ret = <<<EOT
		<ul class="main-menu">
			<li>
				<a href="index.php?requeststring=createdataset">
					<span>
						<img src="images/icons/add.png" alt="add icon" />Create Dataset
					</span>
				</a>
			</li>
			<li>
				<a href="index.php?requeststring=viewdatasets">
					<span>
						<img src="images/icons/layout.png" alt="add icon" />View Datasets
					</span>
				</a>
			</li>
			<li>
				<a href="index.php?requeststring=createuser">
					<span>
						<img src="images/icons/add.png" alt="add icon" />Create User
					</span>
				</a>
			</li>
			<li>
				<a href="index.php?requeststring=viewusers">
					<span>
						<img src="images/icons/layout.png" alt="add icon" />View Users
					</span>
				</a>
			</li>
<!--			<li>
				<a href="index.php?requeststring=viewroles">
					<span>				
						<img src="images/icons/layout.png" alt="add icon" />View Roles
					</span>
				</a>
			</li> -->
<!--			<li>
				<a href="index.php?requeststring=viewpriviledges">
					<span>
						<img src="images/icons/layout.png" alt="add icon" />View Priviledges
					</span>
				</a>
			</li> -->
			<li>
				<a href="index.php?requeststring=about">
					<span>
						<img src="images/icons/brick.png" alt="add icon" />About
					</span>
				</a>
			</li>
<!--			<li>
				<a href="index.php?requeststring=logout">
					<span>
						<img src="images/icons/cancel.png" alt="add icon" />Logout
					</span>
				</a>
			</li> -->
		</ul>
EOT;
		return $ret;
	}

	
}
