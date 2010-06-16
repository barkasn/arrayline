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

class lgAuthenticator implements iRequestHandler {
	private static $instance;
		
	private function _construct() {

	}

	public static function getInstance() {
		self::$instance = new lgAuthenticator();
		return self::$instance;
	}

	public function processRequest(lgRequest $lgRequest) {
		switch($lgRequest->getRequestString()) {
			case 'login':
				$this->processLoginRequest($lgRequest);
				break;
			case 'logout':
				$this->processLogoutRequest($lgRequest);
				break;
			default:
				die('Unknown request');
		}
	}

	public function getRequiredPermissions() {
		return array();
	}

	public function displayInsufficientPermissions() {
		$page = new lgPage();
		$page->setTitle('Insufficient permissions');
		$content = <<<EOT
<h2>Insufficient permissions</h2>
<p>You do not have sufficient permissions to access this page.<p>
<p>Click <a href="index.php">here</a> to return to the home page.</p>
EOT;
		$page->setContent($content);
		$page->render();
	}

	public function displayLoginPage() {
		$page = new lgPage();
		$page->linkCss('login.css');
		$page->setTitle('Login Page');
		$page->setContent($this->getLoginForm());
		$page->render();
		exit;
	}

	private function processLogoutRequest($lgRequest) {
		$_SESSION = array();

		$postLogoutPage = new lgPage();
		$postLogoutPage->setTitle('Logout Sucessful');
		$postLogoutPage->setContent('Logout Sucessful');
		$postLogoutPage->render();
	}

	private function processLoginRequest($lgRequest) {
		$args = $lgRequest->getPostArray();

		$username = $args['username'];
		$password = $args['password'];
	
		if ($username == '' || $password == '') {
			$this->showLoginFailed('Username and Password required');
			exit;
		} else {
			$lgUser = lgUserHelper::getUserByUserName($username);
			if ($lgUser == NULL) {
				$this->showLoginFailed('Invalid username or password');
				exit;
			} else if ($lgUser->checkPassword($password)) {
				$this->setUserLoggedIn($lgUser->getId());
				$this->showLoginSuccess();
			} else {
				$this->showLoginFailed('Invalid username or password');
			}
		}
	}

	private function setUserLoggedIn($id) {
		$this->cleanupSession();	
		$_SESSION['isloggedin'] = true;
		$_SESSION['userid'] = $id;
	}

	private function cleanupSession() {
		if (isset($_SESSION) && !empty($_SESSION)) {
			foreach (array_keys($_SESSSION) as $key) {
				unset ($_SESSION[$key]);
			}
		}
	}

	private function showLoginSuccess() {
		$page = new lgPage();
		$page->setTitle('Login Sucessful');
		$page->setContent('Login Sucessful. You will be redirected to the main page in 3 seconds.');
		$page->setRedirect('index.php', 3);
		$page->render();
	}

	private function showLoginFailed($message = '') {
		$page = new lgPage();
		$page->setTitle('Login Failed');
		$page->setContent('Login Failed. '.$message);
		$page->render();
	}

	private function getLoginForm() {
		$ret =<<<EOT
<div class="page login-page">
<h2>ArrayLine Login</h2>
<form method="post" action='index.php'>
	<label for="username">Username:</label><input type="text" name="username" id="username"><br />
	<label for="password">Password:</label><input type="password" name="password" id="password"><br />
	<input type="hidden" name="requeststring" value="login">
	<input type="submit" id="login" value="Login"</input>
</form>
</div>
EOT;
		return $ret;
	}
}
