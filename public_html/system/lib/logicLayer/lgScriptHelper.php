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

class lgScriptHelper {
	public static function getAllScripts() {
	 	$dbScripts = dbScriptHelper::getAllScripts();
		$lgScripts = self::getLogicalFromDatabaseScripts();

		return $lgScripts;
	}

	public static function getScriptByInternalName($internalName) {
		$lgScript;

		$dbScript = dbScriptHelper::getScriptByInternalName($internalName);
		if ($dbScript) {
			$lgScript = self::getLogicalFromDatabaseScript($dbScript);
		}

		return $lgScript;
	}

	public static function createScript($internalName, $filename, $executionCommand, $canBeCalledDirectly) {
		$dbScript = dbScriptHelper::createScript($internalName, $filename, $executionCommand, $canBeCalledDirectly);
		return self::getLogicalScriptFromDatabaseScript($dbScript);
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

	
