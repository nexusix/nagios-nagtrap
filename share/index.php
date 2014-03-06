<?php
###########################################################################
#
# index.php -  NagTrap start page
#
# Copyright (c) 2006 - 2011 Michael Luebben (nagtrap@nagtrap.org)
# Last Modified: 06.02.2011
#
# License:
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License version 2 as
# published by the Free Software Foundation.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
###########################################################################
error_reporting(E_ALL ^ E_NOTICE);

require("./include/defines/global.php");

require("./include/functions/functions.debug.php");

require("./include/classes/class.main.php");
require("./include/classes/class.frontend.php");
require("./include/classes/class.common.php");
require("./include/classes/class.mysqlDB.php");


$MAIN = new main();

// Read config.ini.php
$configINI = $MAIN->readConfig(CONST_MAINCFG);

if ($_GET['doJob'] == '') {
	// Set variables in configuration
	$configINI['database']['tableSnmpttArchive'] = "snmptt_archive";
	
	// Read error.xml for error-messages
	$errorXML = $MAIN->readXML("./include/xml/language/".$configINI['global']['language']."/error.xml");
	
	// Read language 
	$languageXML = $MAIN->readXML("./include/xml/language/".$configINI['global']['language']."/main.xml");
	
	// Set table
	$table = $MAIN->setTable($tableName,$_REQUEST['severity'],$_REQUEST['trapSelect']);
	
	$FRONTEND = new frontend($configINI);
	
	$FRONTEND->openSite();
	
	$FRONTEND->constructorHeader();
	
	if ($MAIN->checkUser() == "0") {
	   $FRONTEND->printError("AUTHENTIFICATION",NULL);
	} else {
	   $DATABASE = new database($configINI);
	   $DATABASE->connect();
	
	   // If set action, then mark, delete or archive a trap in the database
	   if($_GET['action'] == "mark" or $_GET['action'] == "delete" or $_GET['action'] == "archive") {
	      $DATABASE->handleTrap($_GET['action'],$_GET['trapID'],$table['name']); 
	   }
	
	   // Mark more as one trap 
	   if($_POST["markTraps"] AND $_POST["trapIDs"]){
	      foreach($_POST["trapIDs"] as $trapID){
	         $DATABASE->handleTrap("mark",$trapID,$table['name']); 
	      }
	   }
	
	   // Delete more as one trap 
	   if($_POST["deleteTraps"] AND $_POST["trapIDs"]){
	      foreach($_POST["trapIDs"] as $trapID){
	         $DATABASE->handleTrap("delete",$trapID,$table['name']);
	      }
	   }
	
	   // Delete more as one trap 
	   if($_POST["archiveTraps"] AND $_POST["trapIDs"]){
	      foreach($_POST["trapIDs"] as $trapID){
	         $DATABASE->handleTrap("archive",$trapID,$table['name']);
	      }
	   }
	
	   $FRONTEND->constructorMain();
	   $FRONTEND->constructorFooter();
	}
	$FRONTEND->closeSite();
	$FRONTEND->printSite();
} elseif ($_GET['doJob'] == 'autoArchive') {
	$DATABASE = new database($configINI);
	$DATABASE->connect();
	if ($_GET['authID'] == $configINI['jobs']['authID']) {
		if (DEBUG&&DEBUGLEVEL&1) debug('Start search: Old Records');
			
		$result = $DATABASE->searchOldRecords($configINI['database']['tableSnmptt'],time()-($configINI['global']['daysToArchive']*86400));
	
		// Delete more as one trap
		foreach($result as $key => $trapID){
			$DATABASE->handleTrap("archive",$trapID,$configINI['database']['tableSnmptt']);
		}
		
		// Report job
		$DATABASE->reportJob('archive','0',count($result), $_SERVER['REQUEST_TIME']);
		
		if (DEBUG&&DEBUGLEVEL&1) debug('END search');
	} else {
		$DATABASE->reportJob('archive','2',0, $_SERVER['REQUEST_TIME'],'Wrong auth ID!');
		if (DEBUG&&DEBUGLEVEL&1) debug('END search: FALSE - Wrong auth ID!');
	}		
} elseif ($_GET['doJob'] == 'autoDelete') {
	$DATABASE = new database($configINI);
	$DATABASE->connect();
	if ($_GET['authID'] == $configINI['jobs']['authID']) {
		if (DEBUG&&DEBUGLEVEL&1) debug('Start search: Old Records');
			
		$result = $DATABASE->searchOldRecords($configINI['database']['tableSnmpttArchive'],time()-($configINI['jobs']['daysToDelete']*86400));
	
		// Delete more as one trap
		foreach($result as $key => $trapID){
			$DATABASE->handleTrap("delete",$trapID,$configINI['database']['tableSnmpttArchive']);
		}
		
		// Report job
		$DATABASE->reportJob('delete','0',count($result), $_SERVER['REQUEST_TIME']);
		
		if (DEBUG&&DEBUGLEVEL&1) debug('END search');
	} else {
		$DATABASE->reportJob('delete','2',0, $_SERVER['REQUEST_TIME'],'Wrong auth ID!');
		if (DEBUG&&DEBUGLEVEL&1) debug('END search: FALSE - Wrong auth ID!');
	}
}
?>
