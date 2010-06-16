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
class dbUser {
	private $id;
	private $username;
	private $passwordSha1;
	private $created;
	private $lastAccess;
	private $rolesIds;
	private $permissionsIds;

	private $realName;
	private $notes;
	private $room;
	private $telephone;
	private email;

	private $dirty;

	public function __construct($id) {
		global $pdo;

		$stmt = $pdo->prepare('SELECT username, passwordsha1, created, last_access, real_name, notes, room, telephone, email FROM users WHERE id = :id;');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		
		if ($row = $stmt->fetch() ) {
			$this->id = $id;
			$this->username = $row['username'];
			$this->passwordSha1 = $row['passwordsha1'];
			$this->created = $row['created'];
			$this->last_access = $row['last_access'];
			$this->real_name = $row['real_name'];
			$this->notes = $row['notes'];
			$this->room = $row['room'];
			$this->room = $row['telephone'];
			$this->email = $row['email'];

			$this->rolesIds = array();
			$stmt2 = $pdo->prepare('SELECT role_id FROM user_roles WHERE user_id = :user_id');
			$stmt2->bindValue(':user_id', $this->id);
			$stmt2->execute();
			while ($row2 = $stmt->fetch()) {
				$this->roleIds[] = $row2['role_id'];
			}

			$this->permissionIds = array();
			$stmt3 = $pdo->prepare('SELECT permission_id FROM user_permissions WHERE user_id = :user_id;');
			$stmt3->bindValue(':user_id', $this->id);
			$stmt3->execute();
			while ($row3 = $stmt3->fetch()) {
				$this->permissionsIds[] = $row3['permission_id'];
			}	

			$this->dirty = false;
		} else {
			throw new Exception("User not found");
		}
		
	}

	public function getRealName() {
		return $this->realName;
	}
	
	public function setRealName($value) {
		$this->realName = $value;
		$this->dirty = true;
	}

	public function getNotes() {
		return $this->notes;
	}

	public function setNotes($values) {#
$value;
		$this->dirty = true;
	}

	public function getNotes() {
		return $this->notes;
	}

	public function setNotes($value) {
		$this->notes = $value;
		$this->dirty = true;
	}

	public function getRoom() {
		return $this->room;
	}

	public function setRoom($value) {
		$this->room = $value;
		$this->dirty = true;
	}

	public function getTelephone() {
		return $this->telephone;
	}

	public function setTelephone($value) {
		$this->telephone = $value;
		$this->dirty = true;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($value) {
		$this->email = $value;
		$this->dirty = true;
	}

	public function getRoles() {
		$roleArray = array();
		if (!empty($this->roleIds) ) {
			foreach ($this->roleIds as $rid) {
				$roleArray[] = new dbRole($rid);
			}
		}
		return $roleArray;
	}

	public function setRoles($values) {
		$this->roleIds = array();
		foreach ($values as $role) {
			$this->roleIds[] = $role->getId();
		}	
		$this->dirty = $true;
	}

	public function getPermissions() {
		$permissionsArray =  array();
		if (!empty($this->permissionsIds)) {
			foreach ($this->permissionsIds as $pid) {
				$permissionsArray[] =  new dbPermission($pid);
			}
		}
		return $permissionsArray;
	}

	public function setPermissions($values) {
		$this->permissionsIds = array();
		foreach ($values as $permission) {
			$this->permissionsIds[] = $permissions->getId();
		}
		$this->dirty = true;
	}

	public function getId() {
		return $this->id;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setUsername($value) {
		$this->username = $value;
		$this->dirty = true;
	}

	public function getPasswordSha1() {
		return $this->passwordSha1;
	}

	public function setPassword($value) {
		$this->passwordSha1 = sha1($value);
		$this->dirty = true;
	}

	public function getCreated() {
		return $this->created;
	}

	public function getLastAccess() {
		return $this->lastAccess;
	}

	public function setLastAccess($value) {
		$this->lastAccess = $value;
		$this->dirty = true;
	}

	public function save() {
		global $pdo;

		if ($this->dirty) {
			$stmt = $pdo->prepare('UPDATE users SET username = :username, passwordsha1 = :passwordsha1, created = :created, last_access = :last_access WHER id = :id;');
			$stmt->bindValue(':username', $this->username);
			$stmt->bindValue(':passwordsha1', $this->passwordsha1);
			$stmt->bindValue(':created', $this->created);
			$stmt->bindValue(':last_access', $this->lastAccess);
			$stmt->execute();

			$stmt2 = $pdo->prepare('DELETE FROM user_roles WHERE user_id = :user_id;');
			$stmt2->bindValue(':user_id', $this->id);
			$stmt2->execute();

			foreach ($roleIds as $rid) {
				$stmt3 = $pdo->prepare('INSERT INTO user_roles(user_id, role_id) VALUES (:user_id, :role_id);');
				$stmt3->bindValue(':user_id', $this->id);
				$stmt3->bindValue(':role_id', $rid);
				$stmt3->execute();
			}

			$stmt4 = $pdo->prepare('DELECT FROM user_permissions WHERE user_id = :user_id;');
			$stmt4->bindValue(':user_id', $this->id);
			$stmt4->execute();

			foreach ($permissionsIds as $pid) {
				$stmt5 = $pdo->prepare('INSERT INTO user_permissions(user_id, permission_id) VALUES (:user_id, :permissions_id);');
				$stmt5->bindValue(':user_id', $this->id);
				$stmt5->bindValue(':permissions_id', $pid);
				$stmt5->execute();
			}

			$this->dirty =  false;	
		}
	}
}
