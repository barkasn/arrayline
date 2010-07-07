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
			case 'viewuser':
				$this->processViewUser($lgRequest);
				break;
			default:
				throw new Exception('Invalid Request');
				break;
		}
	}

	private function processViewuser($lgRequest) {
		// TODO: implement
		echo ('View User not implemented');
	}

	private function processEditUserRequest(lgRequest $lgRequest) {
		// TODO: Implement
		$page = new lgCmsPage();
		$page->setTitle('Edit User');
		$page->appendContent('<h2>Edit User</h2>');
		$page->appendContent('<p>Edit user not implemented</p>');
		$page->render();
	
	}

	private function processViewPriviledges(lgRequest $lgRequest) {
		$page = new lgCmsPage();
		$page->setTitle('View Priviledges');
		$lgPermissions = lgPermissionHelper::getAllPermissions();
		$page->appendContent('<h2>View Priviledges</h2>');

		if (empty($lgPermissions)) {
			$page->appendContent('No Permissions found');
		} else {
			foreach($lgPermissions as $p) {
				$page->appendContent('<div class="permission-row">'.
					$p->getName().' <a href="index.php?requeststring=editpermission&permissionid='.
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
		$page->setTitle('View Roles');
		$lgRoles = lgRoleHelper::getAllRoles();
		$page->appendContent('<h2>View Roles</h2>');
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
		$page->appendContent('<h2>View Users</h2>');
		if (empty($lgUsers)) {
			$page->appendContent('No users found'); 
		} else {
			$i = 1;
			$page->appendContent('<div class="user-listing">');
			foreach ($lgUsers as $lgUser) {
				$page->appendContent($this->getUserEntryRendered($lgUser,($i++%2?'odd':'even')));
			}
			$page->appendContent('</div>');
		}
		$page->render();
	}

	private function getUserEntryRendered($lgUser, $class) {
		$username = $lgUser->getUsername();
		$id = $lgUser->getId();

		$return=<<<EOF
	<div class="user-entry $class">
		<div class="user-title">
			$username
		</div>
		<div class="user-actions">
			<a href="index.php?requeststring=edituser&userid=$id">edit</a> | 
			<a href="index.php?requeststring=deleteuser&userid=$id">delete</a>
		</div>
	</div>
EOF;
		return $return;
	}

	private function doDeleteUser(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();
		$userId = empty($postArray['userid'])?'':$postArray['userid'];
		$lgUser = new lgUser($userId);
		$lgUser->delete();

		$page = new lgCmsPage();
		$page->setTitle('User Deleted');
		$page->appendContent('<h2>User deleted</h2>');
		$page->appendContent('<p>User deleted succesfully</p>');

		$page->render();
	}

	private function processDeleteUserRequest(lgRequest $lgRequest) {
		$postArray = $lgRequest->getPostArray();
		$userId = empty($postArray['userid'])?'':$postArray['userid'];
		$currentUser = lgUserHelper::getUserFromEnviroment();
			
		if ($userId == $currentUser->getId()) {
			$this->showDeleteCurrentUserError($lgRequest);
		} else {
			if (empty($postArray['confirm'])) {
				$this->showDeleteConfirmation($lgRequest);
			} else {
				$this->doDeleteUser($lgRequest);
			}
		}
	}


	private function showDeleteCurrentUserError(lgRequest $lgRequest) {
		$page = new lgCmsPage();
		$page->setTitle('Delete User Error');
		$page->appendContent('<h2>Delete User Error</h2>');
		$page->appendContent('<p>You cannot delete the current user.</p>');
		$page->render();

	}

	private function showDeleteConfirmation(lgRequest $lgRequest) {
                $postArray = $lgRequest->getPostArray();
                $userId = empty($postArray['userid'])?'':$postArray['userid'];

                $lgUser = new lguser($userId);

                $page = new lgCmsPage();
                $page->setTitle('Delete user confirmation');
                $page->appendContent('<h2>Delete user confirmation</h2>');

		$page->appendContent('<p>Are you sure you want to delete user '.$lgUser->getUsername().'?</p>');
		$form = new lgHtmlForm();
                $hiddenVals = array (
                        'requeststring' => 'deleteuser',
			'userid' => $lgUser->getId(),
                        'confirm' => '1',
                );
                $form->addFields(lgHtmlFormHelper::getHiddenFieldsFromArray($hiddenVals));
		$form->addField(new lgHtmlSubmitButton('Delete User','Delete User'));
		$page->appendContent($form->getRenderedHtml());
		$page->render();
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
		} else if ($password != $password2) {
			$page->setTitle('Create User - The two passwords do not match');	
			$page->appendContent('The two passwords do not match');
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
