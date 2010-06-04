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

class lgReqHandlerDefault implements iRequestHandler {
	public function __construct() {

	}

	public function processRequest(lgRequest $lgRequest) {
		// Display Default page	
		$page = new lgCmsPage();
		$page->setTitle('Default Page');
		$page->setContent('Welcome to Arrayline<form method="POST"><input type="hidden" name="requeststring" value="logout"><input type="submit" value="logout"/></form>');
		$page->render();
		
	}

	public function getRequiredPermissions() {
		return array();
	}
}

