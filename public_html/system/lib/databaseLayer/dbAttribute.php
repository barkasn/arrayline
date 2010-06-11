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


class dbAttribute {
	private $key;
	private $value;
	private $dirty;

	public function __construct($key) {
		global $pdo;

		$stmt = $pdo->prepare('SELECT value FROM attributes WHERE `key` = :key;');
		$stmt->bindValue(':key',$key);
		$stmt->execute();
		
		if ($row = $stmt->fetch()) {
			$this->key = $key;
			$this->value = $row['value'];
			$dirty = false;
		}

	}

	public function getKey() {
		return $this->key;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
		$this->dirty = true;
	}

	public function save() {
		global $pdo;

		if ($this->dirty) {
			$stmt = $pdo->prepare('UPDATE attributes SET value = :value WHERE `key` = :key;');
			$stmt->bindValue(':key', $this->key);
			$stmt->bindValue(':value', $this->value);
			$stmt->execute();
			$this->dirty = false;
		}
	}

}
