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
class dbPermissionHelper {
	public static function getAllPermissions() {
		global $pdo;
		$stmt = $pdo->prepare('SELECT id FROM permissions;');
		$stmt->execute();
		$dbPermissions = array();
		while ($row = $stmt->fetch()) {
			$dbPermissions[] = new dbPermission($row['id']);
		}
		return $dbPermissions;
	}

	public static function createPermission() {
		global $pdo;
		$stmt = $pdo->prepare('INSERT INTO permissions(internal_name, name, description) VALUES(:internal_name, :name, :description);');
		$stmt->bindValue(':internal_name', $internalName);
		$stmt->bindValue(':name', $name);
		$stmt->bindValue(':description', $description);
		$stmt->execute();
		$id = $pdo->lastInstertId();
		return new dbPermission($id);
	}
}
