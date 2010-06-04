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
class dbRole {
	private $internalName;
	private $name;
	private $description;
	private $permissionIds;
	private $dirty;

	public __constructor($id) {
		global $dbh;
		$stmt = $dbh->prepare('SELECT internal_name, name, description FROM roles WHERE id = :id;');
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		if ($row = $stmt->fetch()) {
			$this->id = $row['id'];
			$this->name = $row['name'];
			$this->description = $row['description'];
			
			$stmt2 = $dbh->prepare('SELECT permissions_id FROM roles_permissions WHERE role_id = :id;');
			$stmt2 = $dbh->bindValue(':id', $this->id);
			$stmt2->execute();
			$permissions = array();
			while ($row2 = $stmt2->fetch()) {
				$permissionIds[] = $row2['permission_id'];	
			}
	
			$this->dirty = false;
		}
		else 
		{
			throw new Exception('Role not Found');
		}
	}

	public function getName() {
		return $this->name;
	}

	public function setName($value) {
		$this->name = $value;
		$this->dirty = true;
	}

	public function getInternalName() {
		return $this->internalName;
	}

	public function setInternaltName($value) {
		$this->internalName = $value;
		$this->dirty =  true;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($value) {
		$this->description = $value;
		$this->dirty = true;
	}

	public function getPermissions() {
		$permissionArray = array();
		foreach ($this->permissionIds as $pid) {
			$permissionArray[] = new dbPermission($pid);
		}
		return $permissionArray;
	}

	public function setPermissions($values) {
		$this->permissionIds = array();
		foreach ($values as $p) {
		{
			$this->permissionIds[] = $p->getId();
		}
		$this->dirty = true;
	}
		

	public function save() {
		global $dbh;
		if ($this->dirty) {
			$stmt = $dbh->prepare('UPDATE roles SET internal_name  = :internalName, name = : name, description = :description WHERE id = :id;');
			$stmt->bindValue(':internalName', $this->internalName);
			$stmt->bindValue(':name', $this->name);
			$stmt->bindValue(':descrption', $this->description);
			$stmt->bindValue(':id', $this->id);
			$stmt->execute();

			$stmt2 = $dbh->prepare('DELETE FROM roles_permissions WHERE roles_id = :id;');
			$stmt2->bindValue(':id', $this->id);
			$stmt2->execute();

			foreach ($this->permissionsIds as $pid) {
				$stmt3 = $dbh->prepare('INSERT INTO roles_permissions(roles_id, permission_id) VALUES (:role_id, :permission_id);');
				$stmt3->bindValue(':role_id', $this->id);
				$stmt3->bindValue(':permission_id', $pid);
				$stmt3->execute();
			}
			$this->dirty = false;

		}
	}
			



}

	
