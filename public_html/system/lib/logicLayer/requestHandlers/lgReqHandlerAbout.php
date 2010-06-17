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

class lgReqHandlerAbout implements iRequestHandler {
	public function __construct() {

	}

	public function processRequest(lgRequest $lgRequest) {
		$page = new lgCmsPage();
		$page->setTitle('About Page');
		$page->appendContent('<h2>About text</h2>');
		$page->appendContent('<h3>Licence</h3>');
		$copyrightInfo=<<<EOF
<pre>
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
    along with this program.  If not, see http://www.gnu.org/licenses/.
</pre>
EOF;
		$page->appendContent('<p>'.$copyrightInfo.'</p>');
		$page->appendContent('<h3>Icons</h3>');
		
$iconsText=<<<EOF
	<p>Icons used in this project are from the 'Silk' collection by Mark James and 
	are licenced under <a rel="license" href="http://creativecommons.org/licenses/by/2.5/">Creative Commons Attribution 2.5 License</a>.
	 For more information regarding the icons please visit: <a href="http://www.famfamfam.com/lab/icons/silk/">famfamfam.com</a>
 
EOF;
		$page->appendContent($iconsText);
		$page->render();
	}

	public function getRequiredPermissions() {
		return array();
	}
}

