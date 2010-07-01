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
class lgUserHelper {
	public static function getUserFromEnviroment() {
		if(isset($_SESSION['isloggedin'])  && $_SESSION['isloggedin'] == true) {
			$userid = $_SESSION['userid'];

			return new lgUser($userid);
		} else {
			return NULL;
		}
	}

	public static function getUserByUsername($username) {
		$dbUser = dbUserHelper::getUserByUsername($username);
		if ($dbUser) {
			return new lgUser($dbUser->getId());
		} else {
			return NULL;
		}
	}

	public static function createUser($username, $password) {
		$created = date('Y-m-d H:i');
		$dbUser = dbUserHelper::createUser($username,sha1($password), $created);	
		if (!$dbUser) {
			throw new Exceptions('A Fatal error occured while attempting to create a new user');
		} else {
			return new lgUser($dbUser->getId());
		}	
		return NULL;
	}

	public static function getAllUsers($getDeleted = false) {
		if ($getDeleted) {
			$dbUsers = dbUserHelper::getAllUsers();
		} else {
			$dbUsers = dbUserHelper::getAllActiveUsers();
		}

		$lgUserObjects = array();
		if (!empty($dbUsers)) {
			foreach ($dbUsers as $usr) {
				$lgUserObjects[] = new lgUser($usr->getId());
			}
			return $lgUserObjects;
		} else {
			return NULL;
		}
	}


}
