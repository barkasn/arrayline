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

/* 
 * A place for non-security critical system-wide parameters
 */

// BUILT-WIDE CONSTANTS
define('DEBUG', true);

// OTHER PARAMETERS

// PHP Session Name
define('SESSION_NAME','arrayline');

// The location were the system is installed, i.e. were the
// index.php is. Use absolute path for security reasons 
$basepath = '/home/nikolas/public_html/arrayline/';

// The full path of the database configuration file
// Make sure this is not in a public directory
$dbconfig = '/home/nikolas/dbconfig.php';

// The directory were the classes are stores
// relative to $basepath
$classroot = 'system/lib/';

// The directory were the modules are stored
// relative to $basepath
$moduleroot = 'modules/';

// The datastore directory
// relative to the $basepath
$datastoreroot = 'datastore/';

// The job direcotry, where pending jobs are stores
// relative to the $basepath
$jobroot = 'jobs/';

// The minimum length of a username
$minUsernameLength = 6;

// The maximum length of a username
$maxUsernameLength = 25;

// The minimun password lenght
$minPasswordLength = 8;
$maxPasswordLength = 50;

