<?php

class dbScript {

	private $id;
	private $filename;
	private $executionCommand;
	private $canBeCalledDirectly;
	private $dirty;	

	public function __construct($id) {
		global $pdo;
		$stmt = $pdo->prepare('SELECT filename, execution_command, can_be_called_directly FROM scripts WHERE id = :id;');
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		
		if ($row = $stmt->fetch()) {
			$this->id = $id;
			$this->filename = $row['filename'];
			$this->executionCommand = $row['execution_command'];
			$this->canBeCalledDirectly = $row['can_be_called_directly'];
			$this->dirty = false;
		}
	}
	
	public function getId() {
		return $this->id;
	}

	public function getFilename() {
		return $this->filename;
	}

	public function setFilename($value) {
		$this->filename = $value;
		$this->dirty = true;
	}
	
	public function getExecutionCommand() {
		return $this->executionCommand;
	}	

	public function setExecutionCommand($value) {
		$this->executionCommand = $value;
		$this->dirty = true;
	}

	public function getCanBeCalledDirectly() {
		return $this->canBeCalledDirectly;
	}

	public function setCanBeCalledDirectly() {
		$this->canBeCalledDirectly = $value;
		$this->dirty = true;
	}	

	public function save() {
		if ($this->dirty) {
			$stmt = $pdo->prepare('UPDATE scripts SET filename = :filename, execution_command = :execution_command, can_be_called_directly = :can_be_called_directly');
			$stmt->bindValue(':filename', $thids->filename);
			$stmt->bindValue(':execution_command', $this->executionCommand);
			$stmt->bindValue(':can_be_called_directly', $this->canBeCalledDirectly);
			$stmt->execution();

			$this->dirty = false;	

		}	
	}
}
