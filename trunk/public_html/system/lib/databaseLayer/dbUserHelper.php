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
class dbUserHelper {
	public static function getUserByUserName($username) {
		global $pdo;
		$stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username;');
		$stmt->bindValue(':username', $username);
		$stmt->execute();
		
		if ($row = $stmt->fetch()) {
			$id = $row['id'];
			return new dbUser($id);
		} else {
			return NULL;
		}
	}

	public static function createUser($username, $passwordSha1, $created) {
		global $pdo;

		$stmt = $pdo->prepare('INSERT INTO users(username, passwordsha1, created) VALUES (:username, :passwordsha1, :created);');
		$stmt->bindValue(':username', $username);
		$stmt->bindValue(':passwordsha1', $passwordSha1);
		$stmt->bindValue(':created', $created);
	
		$stmt->execute();

		$id = $pdo->lastInsertid();
		return new dbUser($id);
	}

	public static function getAllUsers() {
		global $pdo;
	
		$stmt = $pdo->prepare('SELECT id FROM users;');
		$stmt->execute();

		$dbUserObjects = array();
		while($row = $stmt->fetch()) {
			$dbUserObjects[] = new dbUser($row['id']);
		}
		return $dbUserObjects;
	}

			
}
