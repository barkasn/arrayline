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
class dbDatasetProcessorHelper {
	public static function getDatasetCreationProcessors() {
		global $pdo;
		$stmt = $pdo->prepare('SELECT id FROM dataset_processors WHERE has_no_accept_states = :has_no_accept_states');
		$stmt->bindValue(':has_no_accept_states', '1');
		$stmt->execute();
		$dbProcessors = array();
		while($row = $stmt->fetch()) {
			$dbProcessors[] = new dbDatasetProcessor($row['id']);
		}
		return $dbProcessors;
	}

	public static function getProcessorsByAcceptState(dbDatasetState $dbDatasetState) {
		global $pdo;
		$stmt = $pdo->prepare('SELECT dataset_processor_id FROM dataset_processors_accept_states WHERE dataset_state_id = :dataset_state_id;');
		$stmt->bindValue(':dataset_state_id', $dbDatasetState->getId());
		$stmt->execute();
		
		$dbDatasetProcessors = array();
		while ($row = $stmt->fetch()) {
			$dbDatasetProcessors[] = new dbDatasetProcessor($row['dataset_processor_id']);
		}

		return $dbDatasetProcessors;
	}

	public static function getProcessorsByProduceState(dbDatasetState $dbDatasetState) {
		global $pdo;
		$stmt = $pdo->prepare('SELECT dataset_processor_id FROM dataset_processors_produce_states WHERE dataset_state_id = :dataset_state_id;');
		$stmt->bindValue(':dataset_state_id', $dbDatasetState->getId());
		$stmt->execute();

		$dbDatasetProcessors = array();
		while ($row = $stmt->fetch()) {
			$dbDatasetProcessors[] = new dbDatasetProcessors($row['dataset_processor_id']);
		}
		return $dbDatasetProcessors;
	}
}
	
