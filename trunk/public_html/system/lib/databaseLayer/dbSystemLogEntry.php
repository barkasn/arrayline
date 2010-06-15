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

class dbSystemLogEntry {
	private $id;
	private $message;
	private $created;
	private $dirty;

	public function __construct($id) {
		global $pdo;

		$stmt = $pdo->prepare('SELECT message, created FROM system_log WHERE id =:id;');
		$stmt->bindValue(':id', $id);
		$stmt->execute();

		if ($row = $stmt->fetch()) {
			$this->message = $row['message'];
			$this->created = $row['created'];
			$this->id = $id;
			$this->dirty = true;
		} else {
			throw new Exception('Record Not Found');
		}
	}

	public function getId() {
		return $this->id;
	}

	public function getMessage() {
		return $this->message();
	}

	public function save() {
		if ($this->dirty) {
			// TODO: Implement
		}
	}
}

