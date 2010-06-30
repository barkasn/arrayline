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

class lgUser {
	private $id;
	private $dbUser;

	public function __construct($id) {
		$this->id = $id;
		if (!$this->dbUser = new dbUser($id)) {
			throw new Exception('Invalid user id');
		}
	}

	public function __destruct() {
		$this->dbUser->save();
	}
		
	public function getId() {
		return $this->id;

	}

	public function getUsername() {
		return $this->dbUser->getUsername();
	}

	public function setUsername($value) {
		$this->dbUser->setUsername($value);
	}

	public function checkPassword($password) {
		$providedPasswordSha1 = sha1($password);
		return ($this->dbUser->getPasswordSha1() == $providedPasswordSha1);
	}

	public function checkPermissions($permissions = array()) {
		$permissionMissing = false;
		$allUserPermissions = $this->getAllPermissions();

		if (!empty($permissions)) {
			foreach ($permissions as $p) {
				if (!in_array($p, $allUserPermissions)) {
					$permissionMissing = true;
					break;
				}
			}
		}

		return (!$permissionMissing);
	}

	public function getAllPermissions() {
		$rolePermissions = $this->getRolePermissions();
		$userPermissions = $this->getUserPermissions();
		$allPermissions = array_unique(array_merge($rolePermissions, $userPermissions));

		return $allPermissions;
	}

	public function getRolePermissions() {
		$permissionStrings = array();

		$dbRoles = $this->dbUser->getRoles();
		if ($dbRoles) {
			foreach($dbRoles as $role) {
				$dbRolePermissions = $dbRole->getPermissions();
				if ($dbRolePermissions) {
					foreach($dbPermission as $permission) {
						$permissionStrings[] = $permission->getInternalString();
					}	
				}
			}
		}
		return $permissionStrings;
	}

	public function getUserPermissions() {
		$permissionStrings = array();	


		$dbUserPermissions = $this->dbUser->getPermissions();
		if (!empty($dbUserPermissions)) {
			foreach($dbUserPermissions as $permission) {
				$permissionStrings[] = $permission->getInternalName();
			}	
		}

		return $permissionStrings;
					
	}

	public function getRealName() {
		return $this->dbUser->getRealName();
	}

	public function setRealName($value) {
	 	$this->dbUser->setRealName($value);
	}

	public function getNotes() {
		return $this->dbUser->getNotes();
	}

	public function setNotes($values) {
		$this->dbUser->setNotes($values);
	}

	public function getRoom() {
		return $this->dbUser->getRoom();
	}

	public function setRoom($value) {
		$this->dbUser->setRoom($value);
	}

	public function getTelephone() {
		return $this->dbUser->getTelephone();
	}

	public function setTelephone($value) {
		$this->dbUser->setTelephone($value);
	}

	public function getEmail() {
		return $this->dbUser->getEmail();
	}

	public function setEmail($value) {
		$this->dbUser->setEmail($value);
	}
}


