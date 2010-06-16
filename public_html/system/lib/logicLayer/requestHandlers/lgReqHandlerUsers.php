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

class lgReqHandlerUsers implements iRequestHandler {
	public function __construct() {

	}

	public function __destruct() {

	}	

	public function getRequiredPermissions() {
		return array('manageusers');
	}

	public function processRequest(lgRequest $lgRequest) {
		switch ($lgRequest->getRequestString()) {
			case 'viewusers':
				$this->processViewUsersRequest($lgRequest);
				break;
			case 'edituser':
				$this->processEditUserRequest($lgRequest);
				break;
			case 'createuser':
				$this->processCreateUserRequest($lgRequest);
				break;
			case 'deleteuser':
				$this->processDeleteUserRequest($lgRequest);
				break;
			case 'viewpriviledges':
				$this->processViewPriviledges($lgRequest);
				break;
			case 'viewroles':
				$this->processViewRoles($lgRequest);
				break;
			case 'editpermission':
				$this->processEditPermission($lgRequest);
				break;
			default:
				die('Invalid Request');
				break;
		}
	}

	private function processViewPriviledges(lgRequest $lgRequest) {
		$page = new lgCmsPage();
		$lgPermissions = lgPermissionHelper::getAllPermissions();

		if (empty($lgPermissions)) {
			$page->appendContent('No Permissions found');
		} else {
			foreach($lgPermissions as $p) {
				$page->appendContent('<div class="permission-row">'.
					$p->getName().'<a href="index.php?requeststring=editpermission&permissionid='.
						$p->getId().'">edit</a></div>');
			}
		}

		$page->render();
	}

	private function processEditPermission(lgRequest $lgRequest) {
		echo 'Edit Permission not implemented';
	}

	private function processViewRoles(lgRequest $lgRequest) {
		$page = new lgCmsPage();
		$lgRoles = lgRoleHelper::getAllRoles();
		if (empty($lgRoles)) {
			$page->appendContent('No roles found');
		} else {
			foreach($lgRoles as $role) {
				$page->appendContent('<div> class="role-row">'.$role->getName().'<a href="index.php?requeststring=editrole&roleid='.$role->getId().'">edit</a></div>');
			}
		}
		$page->render();
	}

	private function processViewUsersRequest(lgRequest $lgRequest) {
		$page = new lgCmsPage();
		$lgUsers  = lgUserHelper::getAllUsers();
		if (empty($lgUsers)) {
			$page->appendContent('No users found'); // Obviously this is here for completeness not for executions. One hopes...
		} else {
			foreach ($lgUsers as $usr) {
				$page->appendContent('<div class="user-row">'.
					$usr->getUsername().
					'<a href="index.php?requeststring=edituser&userid='.$usr->getId().'">edit</a>'.
					'<a href="index.php?requeststring=deleteuser&userid='.$usr->getId().'">delete</a>'.'</div>');
			}
		}
		$page->render();
	}

	private function processEditUserRequest(lgRequest $lgRequest) {
		echo 'Edit User Request Processing Not Implemented';
	}

	private function processDeleteUserRequest(lgRequest $lgRequest) {
		echo 'Delete User Request Processing Not Implemented';
	}

	private function processCreateUserRequest(lgRequest $lgRequest) {
		$postData = $lgRequest->getPostArray();
		if (isset($postData['submitted'])) {
			$this->processCreateUserFromData($postData);
		} else {
			$this->showCreateUserForm();
		}
	}

	private function showCreateUserForm() {
		$page = new lgCmsPage();
		$page->setTitle('Create New User');

		$htmlForm = new lgHtmlForm();

		$usernameField = new lgHtmlTextField('username', 'Username: ');
		$htmlForm->addField($usernameField);

		$passwordField = new lgHtmlPasswordField('password', 'Password: ');
		$htmlForm->addField($passwordField);

		$passwordField2 = new lgHtmlPasswordField('password2', 'Repeat Password: ');
		$htmlForm->addField($passwordField2);

		$realNameField = new lgHtmlTextField('realname', 'Real Name: ');
		$htmlForm->addField($realNameField);

		// TODO: Make this a text area
		$notesField = new lgHtmlTextField('notes', 'Notes: ');
		$htmlForm->addField($notesField);
		
		$roomField = new lgHtmlTextField('room', 'Room: ');
		$htmlForm->addField($roomField);

		$telephoneField = new lgHtmlTextField('telephone', 'Telephone: ');
		$htmlForm->addField($telephoneField);

		$emailField = new lgHtmlTextField('email', 'Email: ');
		$htmlForm->addField($emailField);

		$submitButton = new lgHtmlSubmitButton('submit', 'Create User');
		$htmlForm->addField($submitButton);

		$requestStringField = new lgHtmlHiddenField('requeststring');
		$requestStringField->setValue('createuser');
		$htmlForm->addField($requestStringField);

		$submitConfirmField = new lgHtmlHiddenField('submitted');
		$submitConfirmField->setValue('1');
		$htmlForm->addField($submitConfirmField);	

		$page->appendContent('<h2>Create New User</h2>');
		$page->appendContent($htmlForm->getRenderedHTML());

		$page->render();
	}

	private function processCreateUserFromData(array $postData) {
		// TODO: Turn these into constants
		global $minUsernameLength;
		global $maxUsernameLength;
		global $minPasswordLength;
		global $maxPasswordLength;

		$page = new lgCmsPage();
	
		$username = $postData['username'];
		$password = $postData['password'];
		$password2 = $postData['password2'];	
		$realName = $postData['realname'];
		$notes = $postData['notes'];
		$room = $postData['room'];
		$telephone = $postData['telephone'];
		$email = $postData['email'];

		// TODO: Add check for password

		if (empty($username)) {
			$page->setTitle('Create User - Invalid Username');
			$page->appendContent('You need to enter a username.');
		}  else if (strlen($username) < $minUsernameLength ) { 
			$page->setTitle('Create User - Invalid Username');
			$page->appendContent('The username you have entered is too short. The minimum username length is :'.$minUsernameLength);
		} else if (strlen($username) > $maxUsernameLength ) {
			$page->setTitle('Create user - Invalid Username');
			$page->appendContent('The username you have entered is too long. The maximim usernamer length is: '. $maxUsernameLength);
		} else if (strlen($password) < $minPasswordLength ) {
			$page->setTitle('Create user - Password too short');
			$page->appendContent('The password you have entered it too short. The minimum password length is: '. $minPasswordLength);
		} else if (strlen($password) > $maxPasswordLength ) {
			$page->setTitle('Creater user - Password too long');
			$page->appendContent('The password you have entered is too long. The maximum password length is: '. $maxPasswordLength);
		} else if (lgUserHelper::getUserByUsername($username)) {
			$page->setTitle('Create User - Invalid username');	
			$page->appendContent('A user with this username already exists. Please select a diffent username.');
		} else {
			$lgUser = lgUserHelper::createUser($postData['username'], $postData['password']);

			if ($lgUser) {
				$lgUser->setRealName($realName);
				$lgUser->setNotes($notes);
				$lgUser->setRoom($room);
				$lgUser->setTelephone($telephone);
				$lgUser->setEmail($email);

				$page->setTitle('Create user - sucess');
				$page->setContent('The user was sucessfully created');
			} else {
				// TODO: Show Form with error message
				$page->setTitle('Create user - Error');
				$page->setContent('An error occured while attempting to create the new user.');
			}	
		}
		$page->render();
	}
	
}
