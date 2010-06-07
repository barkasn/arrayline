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

class lgScriptHelper {
	public static function getAllScripts() {
	 	$dbScripts = dbScriptHelper::getAllScripts();
		$lgScripts = self::getLogicalFromDatabaseScripts();

		return $lgScripts;
	}

	public function getScriptByInternalName($internalName) {
		$dbScript = dbScriptHelper:getScriptByInternalName($internalName);
		return self::getLogicalFromDatabaseScript($dbScript);
	}

	public function createScript($internalName, $filename, $executionCommand, $canBeCalledDirectly) {
		$dbScript = dbScriptHelper::createScript($internalName, $filename, $executionCommand, $canBeCalledDirectly);
		return self::getLogicalScriptFromDatabaseScrip($dbScript);
	}

	private static function getLogicalFromDatabaseScripts($scripts) {
		$lgScripts = array();
		if(!empty($scripts)) {
			foreach ($scripts as $dbScript) {
				$lgScripts[] = self::getLogicalFromDatabaseScript($dbScript);
			}
		}
		return $lgScripts;
	}

	private static function getLogicalFromDatabaseScript(dbScript $dbScript) {
		return new lgScript($dbScript->getId());
	}

}

	