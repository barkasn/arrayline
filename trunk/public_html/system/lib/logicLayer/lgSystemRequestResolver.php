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
class lgSystemRequestResolver {
	private static $instance;
	private $systemRequestStrings;


	private $systemRequests;

	private function __construct() {


		$this->systemRequests = array(
			'default' => 'lgReqHandlerDefault',
			// Dataset related request
			'viewdatasets' => 'lgReqHandlerDatasets',
			'viewdataset' => 'lgReqHandlerDatasets',
			'processdataset' => 'lgReqHandlerDatasets',
			'deletedataset' => 'lgReqHandlerDatasets',
			'createdataset' => 'lgReqHandlerDatasets',
			'renamedataset' => 'lgReqHandlerDatasets',
			//User related requests
			'viewusers' => 'lgReqHandlerUsers',
			'viewuser' => 'lgReqHandlerUsers',
			'edituser' => 'lgReqHandlerUsers',
			'createuser' => 'lgReqHandlerUsers',
			'deleteuser' => 'lgReqHandlerUsers',
			'viewroles' => 'lgReqHandlerUsers',
			'viewpriviledges' => 'lgReqHandlerUsers',
			'editpermission' => 'lgReqHandlerUsers',
			// About Page
			'about' => 'lgReqHandlerAbout',
		);
	}

	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new lgSystemRequestResolver();
		}
		return self::$instance;
	}

	public function isSystemRequest(lgRequest $lgRequest) {
		 return array_key_exists($lgRequest->getRequestString(), $this->systemRequests);
	}
	
	public function getHandler(lgRequest $lgRequest) {
		if ($this->isSystemRequest($lgRequest)) {
			$moduleClassName = $this->systemRequests[$lgRequest->getRequestString()];
			return new $moduleClassName;
		} else {
			die('Attempt to process an external request as a system request');
		}
	}

}
