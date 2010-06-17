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

class lgSystem {
	private static $instance;
	private $specialRequestStrings;

	private function __construct () {
		$this->initialize();		
	}

	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new lgSystem();
		}
		return self::$instance;
	}

	public function initialize() {
		// These are the really special requests like login and
		// logout that are handled by the basic system classes
		// and are processed before usere authentication
		$this->specialRequestStrings = array('login','logout');
	}
	
	public function processRequest($lgRequest) {
		$authenticator = lgAuthenticator::getInstance();

		if ($this->isSpecialRequest($lgRequest)) {
			$this->handleSpecialRequest($lgRequest);
		} else {
			$user = lgUserHelper::getUserFromEnviroment();
			$requestHandler = $this->getRequestHandler($lgRequest);

			if (!$user) {
				$authenticator->displayLoginPage();
			} else if (!$requestHandler) {
				$this->showHandlerNotFound();
			} else {
				if ($user->checkPermissions($requestHandler->getRequiredPermissions())) {
					$requestHandler->processRequest($lgRequest);
				} else {
					$authenticator->displayInsufficientPermissions();
				}
			}
		}
	}

	private function getRequestHandler(lgRequest $lgRequest) {
		$requestHandler = NULL;
		$systemRequestResolver = lgSystemRequestResolver::getInstance();
		if ($systemRequestResolver->isSystemRequest($lgRequest)) {
			$requestHandler = $systemRequestResolver->getHandler($lgRequest);
		}
		return $requestHandler;
	}

	private function showHandlerNotFound() {
		$page = new lgPage();
		$page->setTitle('Fatal Error');
		$page->setContent('A Fatal Error Occured. The Required Module was not found');
		$page->render();
	}
	

	private function isSpecialRequest($lgRequest) {
		return in_array($lgRequest->getRequestString(),$this->specialRequestStrings);
	}

	private function handleSpecialRequest($lgRequest) {
		//TODO: Perhaps in the future the case can be removed and some
		// more elaborate array based can be used, something that would remove
		// the need for a separate array to keep the special request names
		// which is rather inelegant
		switch ($lgRequest->getRequestString()) {
			case 'login':
				$authenticator = lgAuthenticator::getInstance();
				$authenticator->processRequest($lgRequest);
				break;
			case 'logout':
				$authenticator = lgAuthenticator::getInstance();
				$authenticator->processRequest($lgRequest);	
				break;
			case 'default':
				$defaultPageHandler = new lgDefaultPageHandler();
				$defaultPageHandler->handleRequest($lgRequest);
				exit;
			default:
				die ('Fatal Error. Unknown Special Request Encountered');
		}
	}


}
