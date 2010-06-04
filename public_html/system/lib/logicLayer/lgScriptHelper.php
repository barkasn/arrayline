<?php 

class lgScriptHelper {

	public static function getAllScripts() {
		global $pdo;
		$stmt = $pdo->prepare('SELECT id FROM scripts;');
		$stmt->execute();
		$dbScripts = array();
		
		while ($row = $stmt->fetch()) {
			$dbScripts[] = new dbScript($row['id']);
		}

		return $dbScripts;
	}

	public static function getScriptByInternalName($internalName) {
		global $pdo;
		$stmt = $pdo->prepare('SELECT id FROM scripts WHERE internal_name = :internal_name;');
		$stmt->bindValue(':internal_name', $internalName);
		$stmt->execute();

		$dbScript;
		if ($row = $stmt->fetch()) {
			$dbScript = new dbScript($row['id']);
		}
		return $dbScript;
	}

	public static function createScript($name) {
		global $pdo;
		$stmt = $pdo->prepare('INSERT INTO scripts(internal_name, filename, execution_command, can_be_called_directly) VALUES (:internal_name, :filename, :execution_command, :can_be_called_directly);');
		$stmt->bindValue(':internal_name', $internalName);
		$stmt->bindValue(':filename', $filename);
		$stmt->bindValue(':execution_command', $executionsCommand);
		$stmt->bindValue(':can_be_called_directly', $canBeCalledDirectly);
		$stmt->execute();

		$id = $pdo->lastInsertId();
		return new dbScript($id);
	}


}

	
