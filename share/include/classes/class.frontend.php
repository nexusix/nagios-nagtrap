<?php
###########################################################################
#
# class.common.php -  NagTrap class with functions to create the frontend
#
# Copyright (c) 2006 - 2007 Michael Luebben (nagtrap@nagtrap.org)
# Last Modified: 13.10.2007
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
* This Class creates the Web-Frontend for the Nagtrap Frontend
*/

class frontend {
	var $site;
	
	/**
	* Constructor
	*
	* @param config $configINI
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/  
	function frontend(&$configINI) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::contructor()');
	   $this->configINI = &$configINI;
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::contructor()');
	}

	// ==================================== Functions to create the page ====================================
	
	/**
	* Open a Web-Site in a Array site[].
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function openSite() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::openSite()');
	   $this->site[] = '<HTML>';
	   $this->site[] = '<HEAD>';
	   $this->site[] = '<META HTTP-EQUIV="Content-Type" CONTENT="text/html; CHARSET=UTF-8"/>';
	   $this->site[] = '<TITLE>NagTrap '.CONST_VERSION.'</TITLE>';
	   $this->site[] = '<SCRIPT TYPE="text/javascript" SRC="./include/js/nagtrap.js"></SCRIPT>';
	   $this->site[] = '<SCRIPT TYPE="text/javascript" SRC="./include/js/overlib.js"></SCRIPT>';
	   $this->site[] = '<SCRIPT TYPE="text/javascript" SRC="./include/js/prototype.js"></SCRIPT>';
	   $this->site[] = '<SCRIPT TYPE="text/javascript" SRC="./include/js/scriptaculous.js"></SCRIPT>';
	   $this->site[] = '<LINK HREF="'.$this->configINI['nagios']['prefix'].'/nagtrap/include/css/nagtrap.css" REL="stylesheet" TYPE="text/css">';
	   $this->site[] = '<LINK HREF="'.$this->configINI['nagios']['prefix'].'/stylesheets/status.css" REL="stylesheet" TYPE="text/css">';
	   $this->site[] = '<LINK HREF="'.$this->configINI['nagios']['prefix'].'/stylesheets/showlog.css" REL="stylesheet" TYPE="text/css">';
	   $this->site[] = '<LINK HREF="'.$this->configINI['nagios']['prefix'].'/stylesheets/common.css" REL="stylesheet" TYPE="text/css">';
	   $this->site[] = '</HEAD>';
	   $this->site[] = '<BODY CLASS="status">';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::openSite()');
	}
	
	/**
	* Closed a Web-Site in the Array site[]
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function closeSite() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::closeSite()');
	   $this->site[] = '</BODY>';
	   $this->site[] = '</HTML>';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::closeSite()');
	}
	
	/**
	* Create a Web-Side from the Array site[].
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function printSite() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::printSite()');
	   foreach ($this->site as $row) {
	      echo $row."\n";
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::printSite()');
	   if (DEBUG&&DEBUGLEVEL&4) debugFinalize();
	}
	
	// ======================= Contructor and functions for the header of the frontend ======================
	
	/**
	* Constructor for the header
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function constructorHeader() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::constructorHeader()');
	   global $table;
	   $this->site[] = '<TABLE BORDER="0" WIDTH="100%" CELLPADDING="0" CELLSPACING="0">';
	   $this->site[] = '   <TR>';
	   $this->site[] = '      <TD ALIGN="left" VALIGN="top" WIDTH="33%">';
	   $this->createInfoBox();
	   $this->site[] = '         <BR>';
	   $this->createFilter();
	   $this->site[] = '      </TD>';
	   $this->site[] = '      <TD ALIGN="center" VALIGN="top" WIDTH="33%">';
	   $this->createNavBox();
	   $this->site[] = '         <BR>';
	   $this->createDBInfo($table);
	   $this->site[] = '      </TD>';
	   $this->site[] = '      <TD ALIGN="right" VALIGN="top" WIDTH="33%">';
	   $this->createOptBox();
	   $this->site[] = '      </TD>';
	   $this->site[] = '   </TR>';
	   $this->site[] = '</TABLE>';
	   common::checkForAttentions();
	   $this->site[] = '<BR><BR>';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::constructorHeader()');
	}
	
	/**
	* Create a Info-Box
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function createInfoBox() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::createInfoBox()');
	   global $languageXML;
	   $this->site[] = '         <TABLE CLASS="infoBox" BORDER="1" CELLPADDING="0" CELLSPACING="0">';
	   $this->site[] = '            <TR>';
	   $this->site[] = '               <TD CLASS="infoBox">';
	   $this->site[] = '                  <DIV CLASS="infoBoxTitle">'.$languageXML['LANG']['HEADER']['INFOBOX']['CURRENTTRAPLOG'].'</DIV>';
	   $trapInfo = common::readTrapInfo();
	   // FIXME: View function.php --> Class common!
	   $this->site[] = '                  '.$languageXML['LANG']['HEADER']['INFOBOX']['LASTUPDATE'].': '.$trapInfo['last'].'<BR>';
	   $this->site[] = '                  Nagios&reg; - <A HREF="http://www.nagios.org" TARGET="_new" CLASS="homepageURL">www.nagios.org</A><BR>';
	   $this->site[] = '                  NagTrap&copy; by Michael L&#252;bben<BR>';
	   $this->site[] = '                  '.$languageXML['LANG']['HEADER']['INFOBOX']['LOGGEDINAS'].' <I>'.$_SERVER['PHP_AUTH_USER'].'</I><BR>';
	   $this->site[] = '               </TD>';
	   $this->site[] = '            </TR>';
	   $this->site[] = '         </TABLE>';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::createInfoBox()');
	}
	
	/**
	* Create a Filter-Box
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function createFilter() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::createFilter()');
	   global $hostname, $languageXML, $configINI;
	   $this->site[] = '         <TABLE BORDER="1" CLASS="filter" CELLSPACING="0" CELLPADDING="0">';
	   $this->site[] = '            <TR>';
           $this->site[] = '               <TD CLASS="filter">';
           $this->site[] = '                  <TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0">';
	   $this->site[] = '                     <TR>';
	   $this->site[] = '                        <TD COLSPAN="2" VALIGN="top" ALIGN="left" CLASS="filterTitle">'.$languageXML['LANG']['HEADER']['FILTER']['DISPLAYFILTERS'].':</TD>';
	   $this->site[] = '                        <TD></TD>';
	   $this->site[] = '                     </TR>';
	   $this->site[] = '                     <TR>';
	   $this->site[] = '                        <TD VALIGN="top" ALIGN="left" CLASS="filterName">'.$languageXML['LANG']['HEADER']['FILTER']['HOST'].':</TD>';
	   $this->site[] = '                        <TD VALIGN="top" ALIGN="left" CLASS="filterName">';
	   $this->site[] = '                            '.common::checkRequest($_REQUEST['hostname']);
	   $this->site[] = '                        </TD>';
	   $this->site[] = '                     </TR>';
	   $this->site[] = '                     <TR>';
	   $this->site[] = '                        <TD VALIGN="top" ALIGN="left" CLASS="filterName">'.$languageXML['LANG']['HEADER']['FILTER']['SEVERITYLEVEL'].':</TD>';
	   $this->site[] = '                        <TD VALIGN="top" ALIGN="left" CLASS="filterName">';
	   $this->site[] = '                            '.common::checkRequest($_REQUEST['severity']);
	   $this->site[] = '                        </TD>';
	   $this->site[] = '                     </TR>';
	   $this->site[] = '                     <TR>';
	   $this->site[] = '                            '.common::createCategoryEntry();
	   $this->site[] = '                     <TR COLSPAN="2">';
	   $this->site[] = '                        <TD COLSPAN="2" VALIGN="top" ALIGN="center" CLASS="filterName"><A HREF="./index.php"><B></I>'.$languageXML['LANG']['HEADER']['FILTER']['RESET'].'</I></B></A></TD>';
	   $this->site[] = '                     </TR>';
	   $this->site[] = '                  </TABLE>';
	   $this->site[] = '               </TD>';
	   $this->site[] = '            </TR>';
	   $this->site[] = '         </TABLE>';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::createFilter()');
	}  
	
	/**
	* Create a Navigation-Box
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function createNavBox() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::createNavBox()');
	   global $languageXML;
	   $this->site[] = '         <TABLE CLASS="navBox" BORDER="0" CELLSPACING="0" CELLPADDING="0">';
	   $this->site[] = '            <TR>';
	   $this->site[] = '               <TD ALIGN="center" VALIGN="center" CLASS="navBoxItem">';
	   $this->site[] = '                  <IMG SRC="'.$this->configINI['nagios']['images'].'empty.gif" BORDER="0" WIDTH="75" HEIGHT="1">';
	   $this->site[] = '               </TD>';
	   $this->site[] = '               <TD WIDTH=15></TD>';     
	   $this->site[] = '               <TD ALIGN="center" CLASS="navBoxDate">';
	   $this->site[] = '                  <DIV CLASS="navBoxTitle">'.$languageXML['LANG']['HEADER']['NAVBOX']['LOGFILENAV']['LINE1'].'<BR>'.$languageXML['LANG']['HEADER']['NAVBOX']['LOGFILENAV']['LINE2'].'</DIV><BR>';
	   $trapInfo = common::readTrapInfo();
	   $this->site[] = '                     '.$trapInfo['first'];
	   $this->site[] = '                  <BR>'.$languageXML['LANG']['HEADER']['NAVBOX']['TO'].'<BR>';
	   $this->site[] = '                     '.$trapInfo['last'];
	   $this->site[] = '               </TD>';
	   $this->site[] = '               <TD WIDTH=15></TD>';
	   $this->site[] = '               <TD>';
	   $this->site[] = '                  <IMG SRC="'.$this->configINI['nagios']['images'].'empty.gif" BORDER="0" WIDTH="75" HEIGHT="1">';
	   $this->site[] = '               </TD>';
	   $this->site[] = '            </TR>';
	   $this->site[] = '         </TABLE>';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::createNavBox()');
	}
	
	/**
	* Create a Database-Information for the Navigation
	*
	* @param string $table
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function createDBInfo($table) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::createDBInfo('.$table.')');
	   global $languageXML;
	   $this->site[] = '         <DIV CLASS="navBoxFile">';
	   $this->site[] = '            '.$languageXML['LANG']['HEADER']['DBINFO']['DATABASE'].': '.$this->configINI['database']['name'].' '.$languageXML['LANG']['HEADER']['DBINFO']['TABLE'].': '.$table['name'];
	   $this->site[] = '         </DIV>';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::createDBInfo()');
	}
	  
	/**
	* Create a Box for Options
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function createOptBox() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::createOptBox()');
	   global $languageXML;
	   $this->site[] = '         <TABLE BORDER="0" CLASS="optBox">';
	   $this->site[] = '            <FORM METHOD="get" ACTION="./index.php">';
	   $this->site[] = '               <TR>';
	   $this->site[] = '                  <TD ALIGN="left" COLSPAN="2" CLASS="optBoxItem">'.$languageXML['LANG']['HEADER']['OPTBOX']['SELECTTRAP'].':</TD>';
	   $this->site[] = '               </TR>';
	   $this->site[] = '               <TR>';
	   $this->site[] = '                  <TD ALIGN="left" COLSPAN="2" class="optBoxItem">';
	   $this->site[] = '                     <SELECT NAME="trapSelect">';
	   $this->site[] = '                        <OPTION VALUE="all" '.common::selected("all",$_REQUEST['trapSelect'],"selected").' >'.$languageXML['LANG']['HEADER']['OPTBOX']['SELECTTRAPVALUE']['TRAPACTUEL'].'</OPTION>';
	   $this->site[] = '                        <OPTION VALUE="ARCHIVED" '.common::selected("ARCHIVED",$_REQUEST['trapSelect'],"selected").' >'.$languageXML['LANG']['HEADER']['OPTBOX']['SELECTTRAPVALUE']['TRAPARCHIVED'].'</OPTION>';
	   $this->site[] = common::checkIfEnableUnknownTraps($this->configINI['global']['useUnknownTraps']);
	   $this->site[] = '                     </SELECT>';
	   $this->site[] = '                  </TD>';
	   $this->site[] = '               </TR>';
	   $this->site[] = '               <TR>';
	   $this->site[] = '                  <TD ALIGN="left" COLSPAN="2" CLASS="optBoxItem">'.$languageXML['LANG']['HEADER']['OPTBOX']['SEVERITYDETAIL'].':</TD>';
	   $this->site[] = '               </TR>';
	   $this->site[] = '               <TR>';
	   $this->site[] = '                  <TD ALIGN="left" COLSPAN="2" class="optBoxItem">';
	   $this->site[] = '                     <SELECT NAME="severity">';
	   $this->site[] = '                        <OPTION VALUE="" '.common::selected("",$_REQUEST['severity'],"selected").' >'.$languageXML['LANG']['HEADER']['OPTBOX']['OPTION']['VALUEALL'].'</OPTION>';
	   $this->site[] = '                        <OPTION VALUE="OK" '.common::selected("OK",$_REQUEST['severity'],"selected").' >Traps ok</OPTION>';
	   $this->site[] = '                        <OPTION VALUE="WARNING" '.common::selected("WARNING",$_REQUEST['severity'],"selected").' >Traps warning</OPTION>';
	   $this->site[] = '                        <OPTION VALUE="CRITICAL" '.common::selected("CRITICAL",$_REQUEST['severity'],"selected").' >Traps critical</OPTION>';
	   $this->site[] = '                     </SELECT>';
	   $this->site[] = '                  </TD>';
	   $this->site[] = '               </TR>';
	   $this->site[] = common::createCategoryFilter();
	   $this->site[] = '               <TR>';
	   $this->site[] = '                  <TD ALIGN="left" CLASS="optBoxItem">'.$languageXML['LANG']['HEADER']['OPTBOX']['OLDERENTRIESFIRST'].':</TD>';
	   $this->site[] = '                  <TD></TD>';
	   $this->site[] = '               </TR>';
	   $this->site[] = '               <TR>';
	   $this->site[] = '                  <TD ALIGN="left" VALIGN="bottom" CLASS="optBoxItem"><INPUT TYPE="checkbox" name="oldestfirst" '.common::selected("on",$_REQUEST['oldestfirst'],"checked").' ></TD>';
	   $this->site[] = '                  <TD ALIGN="right" CLASS="optBoxItem"><INPUT TYPE="submit" VALUE="'.$languageXML['LANG']['HEADER']['OPTBOX']['UPDATEBUTTON'].'"></TD>';
	   $this->site[] = '                  <INPUT TYPE="hidden" NAME="hostname" VALUE="'.$_GET['hostname'].'">';
	   $this->site[] = '               </TR>';
	   $this->site[] = '            </FORM>';
	   $this->site[] = '         </TABLE>';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::createOptBox()');
	}

	/**
	* Create a error-message
	*
	* @param string $error
	* @param string $systemError
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/ 
	function printError($error,$systemError) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::printError('.$error.','.$systemError.')');
	   global $errorXML;
	   $this->site[] = '<HR>';
	   $this->site[] = '   <DIV CLASS="errorMessage">'.$errorXML['ERROR'][$error]['MESSAGE'].'</DIV>';
	   common::printErrorLines($errorXML['ERROR'][$error]['DESCRIPTION'],$systemError);
	   $this->site[] = '</HR>';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::printError()');
	}

	// ======================== Contructor and functions for the main of the frontend =======================
	
	/**
	* Constructor for the main
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function constructorMain() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::contructorMain()');
	   global $languageXML, $traps, $hostname, $configINI;;

	   // Check database connection and read traps from database
	   $traps = common::readTraps();
	   $this->site[] = '<TABLE WIDTH="100%" BORDER="0">';
	   $this->site[] = '   <TR>';
	   $this->site[] = '      <TH CLASS="status" WIDTH="6%"><A HREF="#" onclick="Effect.toggle(\'searchBar\',\'slide\'); return false;">'.$languageXML['LANG']['MAIN']['TRAPTABLEHEADER']['SEARCH'].'</A></TH>';
	   $this->site[] = '      <TH CLASS="status" WIDTH="7%">'.$languageXML['LANG']['MAIN']['TRAPTABLEHEADER']['TRAPTIME'].'</TH>';
	   $this->site[] = '      <TH CLASS="status" WIDTH="17%">'.$languageXML['LANG']['MAIN']['TRAPTABLEHEADER']['TRAPOID'].'</TH>';
	   $this->site[] = '      <TH CLASS="status" WIDTH="17%">'.$languageXML['LANG']['MAIN']['TRAPTABLEHEADER']['HOST'].'</TH>';
	   common::showTrapFields("field",NULL,NULL,NULL);
	   $this->site[] = '      <TH CLASS="status" WIDTH="*">'.$languageXML['LANG']['MAIN']['TRAPTABLEHEADER']['MESSAGE'].'</TH>';
	   $this->site[] = '   </TR>';
	   $this->site[] = '   <FORM NAME="form1" ACTION="index.php?severity='.$_REQUEST['severity'].'&category='.$_REQUEST['category'].'&hostname='.$_REQUEST['hostname'].'" METHOD="POST">';
	   $this->site[] = '</TABLE>';
           $this->site[] = '<DIV ID="searchBar" STYLE="display:none;">';
	   $this->site[] = '   <TABLE WIDTH="100%" BORDER="0">';
	   $this->site[] = '      <TR CLASS="searchField">';
	   $this->site[] = '         <TD WIDTH="6%" ALIGN="center">';
	   $this->site[] = '            <INPUT TYPE="image" SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/search.png" TITLE="'.$languageXML['LANG']['MAIN']['SEARCHBAR']['SEARCHBUTTON'].'" NAME="searchButton">';
	   $this->site[] = '         </TD>';
	   $this->site[] = '         <TD WIDTH="7%">';
	   $this->site[] = '         Time';
	   $this->site[] = '         </TD>';
	   common::printSearchfield("Trapoid","trapoid","17%");
	   common::printSearchfield("Hostname","hostname","17%");
	   common::showTrapFields("searchField",NULL,NULL,NULL);
           $this->site[] = '         <TD CLASS="searchField" WIDTH="*">';
           $this->site[] = '            <INPUT CLASS="searchField" ID="searchMessage" NAME="searchMessage" AUTOCOMPLETE="off" TYPE="text"/>';
           $this->site[] = '         </TD>';
	   $this->site[] = '      </TR>';
	   $this->site[] = '   </TABLE>';
           $this->site[] = '</DIV>';
	   $this->site[] = '<TABLE>';
	   common::createTrapPage($traps);
	   $this->site[] = '</TABLE>';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::contructorMain()');
	}
	
	/**
	* Create a Java Infobox
	*
	* @param string $formatline
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/ 
	function javaInfoBox($formatline) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::javaInfoBox('.$formatline.')');
	   $infoBox = 'onmouseover="return overlib(\'';
	   $infoBox .= $formatline;
	   $infoBox .= '\', CAPTION, \'Trap-Message\', VAUTO);" onmouseout="return nd();" ';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::javaInfoBox(): '.$infoBox);
	   return($infoBox);
	}
	
	/**
	* Show traps
	*
	* @param array	$trap
	* @param string $rowColor
	* @param string $styleLine
	* 
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/ 
	function showTrap($trap,$rowColor,$styleLine) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::javaInfoBox('.$formatline.')');
	   global $configINI, $languageXML, $hostname;
	   $this->site[] = '   <TR>';

	   // Checkbox
	   $this->site[] = '      <TD WIDTH="6%" "CLASS="'.$rowColor.'"><INPUT TYPE="checkbox" NAME="trapIDs[]" VALUE="'.$trap['id'].'" '.$_GET['sel'].'>';

	   // Mark a trap
	   common::showTrapMenuIcons("mark",$trap['id'],$_REQUEST['severity'],$_REQUEST['hostname']);

	   // Delete a trap
	   common::showTrapMenuIcons("delete",$trap['id'],$_REQUEST['severity'],$_REQUEST['hostname']);

	   // Archive a trap
	   common::showTrapMenuIcons("archive",$trap['id'],$_REQUEST['severity'],$_REQUEST['hostname']);
	   $this->site[] = '      <TD WIDTH="7%" CLASS="'.$rowColor.'"><P '.$styleLine.'>'.$trap['traptime'].'</P></TD>';
	   $this->site[] = '      <TD WIDTH="17%" CLASS="'.$rowColor.'"><P '.$styleLine.'>'.$trap['trapoid'].'</P></TD>';

	   // Select host
	   $this->site[] = '      <TD WIDTH="17%" CLASS="'.$rowColor.'"><A HREF="./index.php?trapSelect='.$_REQUEST['trapSelect'].'&severity='.$_REQUEST['severity'].'&category='.rawurlencode($_REQUEST['category']).'&hostname='.$trap['hostname'].'"><P '.$styleLine.'>'.$trap['hostname'].'</P></A></TD>';
	   common::showTrapFields("entry",$trap,$rowColor,$styleLine);
	   $this->site[] = '      <TD WIDTH="*" CLASS="'.$rowColor.'"><P '.$styleLine.' '.$this->javaInfoBox($trap['orgFormatline']).'CLASS="formatline">'.htmlentities($trap['formatline']).'</P></TD>';
	   $this->site[] = '   </TR>';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::javaInfoBox()');
	}
	
	// ======================= Contructor and functions for the footer of the frontend ====================== 
	
	/**
	* Constructor for the main
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function constructorFooter() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method frontend::constructorFooter()');
	   global $configINI, $languageXML, $hostname, $table;
	   $this->site[] = '<TABLE WIDTH="100%" BORDER="0">';
	   $this->site[] = '   <TR>';
	   $this->site[] = '      <TD CLASS="linkBox">';
	   $this->site[] = '         <IMG SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/arrow.png" BORDER="0">';
	   $this->site[] = '         <INPUT TYPE="checkbox" NAME="checkbox" VALUE="checkbox" onClick="checkAll(\'yes\'); return true;">(Mark all)';
	   common::showTrapMenuIconFooter("mark");
	   common::showTrapMenuIconFooter("delete");
	   common::showTrapMenuIconFooter("archive");
	   $this->site[] = '         <INPUT TYPE="hidden" NAME="oldestfirst" VALUE="'.$_REQUEST['oldestfirst'].'">';
	   $this->site[] = '         <INPUT TYPE="hidden" NAME="severity" VALUE="'.$_REQUEST['severity'].'">';
	   $this->site[] = '         <INPUT TYPE="hidden" NAME="category" VALUE="'.$_REQUEST['category'].'">';
	   $this->site[] = '         <INPUT TYPE="hidden" NAME="hostname" VALUE="'.$_REQUEST['hostname'].'">';
	   $this->site[] = '         <INPUT TYPE="hidden" NAME="trapSelect" VALUE="'.$_REQUEST['trapSelect'].'">';
	   $this->site[] = '      </TD>';	   
	   $this->site[] = '   </TR>';
	   $this->site[] = '</TABLE>';
	   $this->site[] = '</FORM>';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method frontend::constructorFooter()');
	}
}
?>
