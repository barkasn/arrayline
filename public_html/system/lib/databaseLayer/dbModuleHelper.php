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
class dbModuleHelper {
	static function getAllModules() {

		global $pdo;
		$stmt = $pdo->prepare('SELECT id FROM modules;');
		$stmt->execute();
		$dbModules = array();
		while ($row = $stmt->fetch()) {
			$dbModules = new dbModules($row['id']);
		}
		return $dbModules;
	}

	static function getModuleNameByProcessorName($processorName) {
		global $pdo;

		$stmt = $pdo->prepare('SELECT modules.internal_name as module_name FROM modules, modules_dataset_processors, dataset_processors WHERE modules.id = modules_dataset_processors.module_id AND modules_dataset_processors.dataset_processor_id = dataset_processors.id AND dataset_processors.internal_name = :processor_name;');
		$stmt->bindValue(':processor_name', $processorName);
		$stmt->execute();
		$row = $stmt->fetch();
		$moduleName = $row['module_name'];
		
		return $moduleName;
	}
		

}
