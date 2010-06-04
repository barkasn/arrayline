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

// Initialises Database connection
// On error causes immediate termination

require_once($dbconfig);
require_once('extendedPDO.php');


$db_dsn = "mysql:dbname=$db_name;host=$db_host";

$pdo; //declaration
try {
	$pdo = new ExtendedPDO($db_dsn, $db_username, $db_password);
} catch (PDOException $e) {
	die('A Fatal Error has occured. (Error Code: 02)'.$e->getMessage());
	exit; // Just in case
}

// Reset all variables set by db configuration file
$db_dbname = 0;
$db_username = 0;
$db_password = 0;
$db_host = 0;

