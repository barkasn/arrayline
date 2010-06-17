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

class dbSystemLog {
	public static function logMessage($message) {
		global $pdo;
		$stmt = $pdo->prepare('INSERT INTO system_log(created, message) VALUES (:created, :message);');
		$stmt->bindValue(':created', date("Y-m-d H:i:s"));
		$stmt->bindValue(':message', $message);
		$stmt->execute();
		
		$id = $pdo->lastInsertId();
		return new dbSystemLogEntry($id);
	}

	public static function getAllMessages() {
		global $pdo;

		$stmt = $pdo->prepare('SELECT id FROM system_log');
		$stmt->execute();
	
		$dbMessages = array();
		while ($row = $stmt->fetch()) {
			$dbMessages[] = new dbSystemLogEntry($row['id']);	
		}
	
		return $dbMessages;
	}
}
