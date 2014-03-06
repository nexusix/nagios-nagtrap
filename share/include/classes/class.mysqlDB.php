<?php
###########################################################################
#
# class.common.php -  NagTrap class with functions to connect to the
#					  MySQL-Database
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

/**
* This Class handles database-connection and - queries
*/
class database {
  
	/**
	* Constructor
	*
	* @param config $configINI
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/  
	function database(&$configINI) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::contructor()');
	   $this->configINI = &$configINI;
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method database::contructor()');
	}
	
	/**
	* Make a connection to the database
	*
	* @param array $configINI
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/  
	function connect() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::connect()');
	   global $configINI, $FRONTEND;
	   $connect = @mysql_connect($configINI['database']['host'], $configINI['database']['user'], $configINI['database']['password']);
	   $dbSelect['code'] = @mysql_select_db($configINI['database']['name'], $connect);

	   // On error, create a array entry with the mysql error
	   if(!$dbSelect['code']) {
	      $FRONTEND->printError("DBCONNECTION",mysql_error());
	      $FRONTEND->closeSite();
	      $FRONTEND->printSite();
	      if (DEBUG&&DEBUGLEVEL&1) debug('End method database::connect(): FALSE -'.mysql_error());
	      exit;
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method database::connect(): TRUE');
	   return($dbSelect);
	}

        /**
        * Cache all Traps from database in a array
        *
        * @param array $table
        * @param array $type
        * @param array $search
        *
        * @author Michael Luebben <nagtrap@nagtrap.org>
        */
        function search($type,$search) {
           if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::search()');
           global $table;

           // Search in the database
           $query = "SELECT DISTINCT ".$type." FROM ".$table['name']." WHERE ".$type." LIKE '%".$search."%'";
           $result = @mysql_query($query);

           // On error, create a array entry with the mysql error
           if(!$result) {
              if (DEBUG&&DEBUGLEVEL&1) debug('End method database::search(): FALSE - '.mysql_error());
              exit;
           }

           while($line = @mysql_fetch_array($result)) {
              $searchResult[] = $line[$type];
           }
           if (DEBUG&&DEBUGLEVEL&1) debug('End method database::search(): Array(...)');
           return($searchResult);
        }
 
	/**
	* Read Traps from database
	*
	* @param string $sort
	* @param boolean $limit
	* @param string $hostname
	* @param array $table
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/  
	function readTraps($limit) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::readTraps('.$limit.')');
	   global $hostname, $table, $FRONTEND;

	   // Create WHERE clausel
	   if($_REQUEST['severity'] == "" and $_REQUEST['hostname'] == "" and $_REQUEST['category'] == "" and $_REQUEST['searchTrapoid'] == "" and $_REQUEST['searchHostname'] == "" and $_REQUEST['searchCategory'] == "" and $_REQUEST['searchSeverity'] == "" and $_REQUEST['searchMessage'] == "" or $_REQUEST['severity'] == "UNKNOWN") {
	      $dbQuery = "";
	   } else {
	      if($_REQUEST['searchTrapoid'] != "") {
	         $dbQuerySet[] = "trapoid = '".$_REQUEST['searchTrapoid']."'"; 
	      }
	      if($_REQUEST['searchHostname'] != "") {
	         $dbQuerySet[] = "hostname = '".$_REQUEST['searchHostname']."'"; 
	      } elseif($_REQUEST['hostname'] != "") {
	         $dbQuerySet[] = "hostname = '".$_REQUEST['hostname']."'"; 
	      }
	      if($_REQUEST['searchCategory'] != "") {
	         $dbQuerySet[] = "category = '".rawurldecode($_REQUEST['searchCategory'])."'"; 
	      } elseif($_REQUEST['category'] != "") {
	         $dbQuerySet[] = "category = '".rawurldecode($_REQUEST['category'])."'"; 
	      }
	      if($_REQUEST['searchSeverity'] != "") {
	         $dbQuerySet[] = "severity = '".$_REQUEST['searchSeverity']."'"; 
	      } elseif($_REQUEST['severity'] != "") {
	         $dbQuerySet[] = "severity = '".$_REQUEST['severity']."'"; 
	      }
	      if($_REQUEST['searchMessage'] != "") {
	         $dbQuerySet[] = "formatline LIKE '%".$_REQUEST['searchMessage']."%'"; 
	      }
	      $dbQuery = "WHERE ".implode($dbQuerySet," AND ");
	   }

	   // Set which trap musst reed first from database
	   if ($_REQUEST['oldestfirst'] == "on") {
	      $sort = "ASC";
	   } else {
	      $sort = "DESC";
	   }
 
	   // Read traps from database
	   $query = "SELECT * FROM ".$table['name']." ".$dbQuery." ORDER BY id ".$sort." LIMIT ".$limit;
	   if (DEBUG&&DEBUGLEVEL&2) debug('Method database::readTraps()-> query: '.$query);
	   $result = @mysql_query($query);

	   // On error, create a array entry with the mysql error
	   if(!$result) {
	      $FRONTEND->printError("DBTABLE",mysql_error());
	      $FRONTEND->closeSite();
	      $FRONTEND->printSite(); 
	      if (DEBUG&&DEBUGLEVEL&1) debug('End method database::readTraps(): FALSE - '.mysql_error());
	      exit; 
	   }
   
	   while($line = @mysql_fetch_array($result)) {      
	      $traps[] = $line;
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method database::readTraps(): Array(...)');
	   return($traps);
	}
	
	/**
	* Handle a Traps in the database
	*
	* @param string $handle
	* @param boolean $trapID
	* @param string $tableName
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/  
	function handleTrap($handle,$trapID,$tableName) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::handleTrap('.$handle.','.$trapID.','.$tableName.')');
	   global $FRONTEND;
	   if($handle == "mark") {
	      $query = "UPDATE $tableName SET trapread = 1 WHERE id = $trapID";
	      $result = mysql_query($query);
	   } elseif($handle == "delete") {
	      $query = "DELETE FROM $tableName WHERE id = $trapID";
	      $result = mysql_query($query);
	   } elseif($handle == "archive") {
	      $result = mysql_query("SELECT * FROM $tableName WHERE id = $trapID");
	      $trap = mysql_fetch_array($result);
	      $query = "INSERT INTO snmptt_archive (snmptt_id, eventname, eventid, trapoid, enterprise, community,
	                                            hostname, agentip, category, severity, uptime, traptime,formatline, trapread) 
	                VALUES ('$trap[id]', '$trap[eventname]', '$trap[eventid]', '$trap[trapoid]', '$trap[enterprise]', '$trap[community]',
                                '$trap[hostname]','$trap[agentip]', '$trap[category]', '$trap[severity]', '$trap[uptime]', '$trap[traptime]',
	                        '$trap[formatline]', '$trap[trapread]')";
	      $result = mysql_query($query);
	      $query = "DELETE FROM $tableName WHERE id = $trapID";
	      $result = mysql_query($query);
	   }     
	   if(!$result) {
	      $FRONTEND->printError("DBHANDLETRAP",mysql_error());
	      $FRONTEND->closeSite();
	      $FRONTEND->printSite(); 
	      if (DEBUG&&DEBUGLEVEL&1) debug('End method database::handleTrap(): FALSE - '.mysql_error());
	      exit; 
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method database::handleTrap(): '.$result);
	   return($result);
	}
    
	/**
	* Save job report to database
	*
	* @param string $type			archive or delete
	* @param integer $jobstate		Nagios/Icinga ecit code
	* @param integer $countTraps	Number of archived or deletes traps
	* @param integer $jobTime		Unix timestamp
	* @param integer $message		Information to the job
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/  
	function reportJob($type,$jobState,$countTraps, $jobTime, $jobMessage) {
		if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::reportJob('.$type.','.$jobState.','.$countTraps.','.$jobTime.','.$jobMessage.')');
		if ($type == 'archive') {
			$query = "UPDATE snmptt_jobs SET jobstate = $jobState, count = $countTraps, jobtime = $jobTime, message = '$jobMessage' WHERE type='archive'";
			$result = mysql_query($query);
		} elseif ($type == 'delete') {
			$query = "UPDATE snmptt_jobs SET jobstate = $jobState, count = $countTraps, jobtime = $jobTime, message = '$jobMessage' WHERE type='delete'";
			$result = mysql_query($query);
		}
		if(!$result) {
			if (DEBUG&&DEBUGLEVEL&1) debug('End method database::reportJob(): FALSE - '.mysql_error());
	    	exit;
	    }
	    if (DEBUG&&DEBUGLEVEL&1) debug('End method database::reportJob(): '.$result);
	}
	
	/**
	* Read Trap-Infromation from the database
	*
	* @param string $tableName
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/  
	function infoTrap($tableName) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::infoTrap('.$tableName.')');
	   global $FRONTEND;
	   $query = "SELECT id,traptime FROM $tableName ORDER BY id";
	   $result = mysql_query($query);
	   if(!$result) {
	      $FRONTEND->printError("DBREADTRAP",mysql_error());
	      $FRONTEND->closeSite();
	      $FRONTEND->printSite(); 
	      if (DEBUG&&DEBUGLEVEL&1) debug('End method database::infoTrap(): FALSE - '.mysql_error());
	      exit; 
	   }
	   while($line = mysql_fetch_array($result)) {
	      $trapTime[] = $line['traptime']; 
	   }
	   if($trapTime[0] != "") {
	      $trap[last] = array_pop($trapTime);
	      $trap[first] = array_pop(array_reverse($trapTime));
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method database::infoTrap(): Array(...)');
	   return($trap);
	}

	/**
	* Read category from database
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*
	*/
	function readCategory($tableName) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method database::readCategory('.$tableName.')');
	   global $FRONTEND;
	   $query = "SELECT DISTINCT category FROM $tableName";
	   $result = mysql_query($query);
	   if(!$result) {
	      $FRONTEND->printError("DBREADCATEGORY",mysql_error());
	      $FRONTEND->closeSite();
	      $FRONTEND->printSite(); 
	      if (DEBUG&&DEBUGLEVEL&1) debug('End method database::readCategory(): FALSE - '.mysql_error());
	      exit; 
	   }
	   while ($line = mysql_fetch_array($result)) {
	      $category[] = $line['category'];
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method database::readCategory(): Array(...)');
	   return($category);
	}

	/**
	* Cache all Traps from database in a array
	* they are older than timestamp
	*
	* @param string $table
	* @param string $timestamp
	*
	* @author Adrian Kaegi <adrian.kaegi@medianet.ch>
	*/
	function searchOldRecords($table, $timestamp) {
		if (DEBUG&&DEBUGLEVEL&1) debug('Start method Old Records database::searchOldRecords('.$table.','.$timestamp.')');

		// Search in the database
		$query = 'SELECT id FROM '.$table.' WHERE UNIX_TIMESTAMP(STR_TO_DATE(traptime,\'%a %b %d %H:%i:%S %Y\')) < '.$timestamp.' LIMIT 100000'; 
		$result = mysql_query($query);

		// On error, create a array entry with the mysql error
		if(!$result) {
			if (DEBUG&&DEBUGLEVEL&1) debug('End method Old Records database::searchOldRecords(): FALSE - '.mysql_error());
				exit;
		}

		while($line = @mysql_fetch_array($result)) {
			$searchResult[] = $line[0];
		}
		if (DEBUG&&DEBUGLEVEL&1) debug('End method Old Records database::searchOldRecords(): Array(...)');
		return($searchResult);
	}
}
