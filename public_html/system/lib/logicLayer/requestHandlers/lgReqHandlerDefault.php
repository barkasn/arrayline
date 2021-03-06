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

class lgReqHandlerDefault implements iRequestHandler {
	public function __construct() {

	}

	public function processRequest(lgRequest $lgRequest) {
		$page = new lgCmsPage();
		$page->setTitle('Home Page');
		$page->appendContent('<p class="notice">Welcome to Arrayline. You can use the menu at the left to create and process you own datasets</p>');
		$page->render();
	}

	public function getRequiredPermissions() {
		return array();
	}
}

