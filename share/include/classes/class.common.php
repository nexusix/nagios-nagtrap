<?php
###########################################################################
#
# class.common.php -  NagTrap class with functions for the frontend
#
# Copyright (c) 2006 - 2007 Michael Luebben (nagtrap@nagtrap.org)
# Last Modified: 16.12.2007
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
* This Class with functions for the frontend-class 
*/
class common extends frontend {
  
	/**
 	* Check the Request (OK, WARNING, ......)
	*
	* @param string $request
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function checkRequest($request) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::readRequest('.$request.')');
	   if(!isset($request) or $request == "") {
	      $retRequest = 'All';
	   } else {
	      $retRequest = $request;
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method common::readRequest(): '.$retRequest);
	   return($retRequest);  
	}

	/**
	* Check if the Option selected
	*
	* @param string $optionValue
	* @param string $type
	* @param string $sel
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function selected($optionValue,$type,$sel) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::selected('.$optionValue.','.$type.','.$sel.')');
	   $state = "";
	   if($optionValue == $type) {
	      $state = $sel;
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method common::selected(): '.$state);
	   return($state);
	}

	/**
	* Read Trap-Information from database
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function readTrapInfo() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::readTrapInfo()');
	   global $table;
	   $DATABASE = new database($configINI);
	   $DATABASE->connect();
	   $trapInfo = $DATABASE->infoTrap($table['name']);
	   if(!isset($trapInfo['first'])) {
	      $trapInfo['first'] = $trapInfo['last'];  
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method common::readTrapInfo(): Array(...)');
	   return($trapInfo);
	}
	
	/**
	* Check if use unknown-Traps in the Database
	*
	* @param boolean $useUnknownTraps
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function checkIfEnableUnknownTraps($useUnknownTraps) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::checkIfEnableUnknownTraps('.$useUnknownTraps.')');
	   global $languageXML;
	   unset($option);
	   if($useUnknownTraps == "1") {
	      $option='                        <OPTION VALUE="UNKNOWN" '.common::selected("UNKNOWN",$_REQUEST['trapSelect'],"selected").' >'.$languageXML['LANG']['HEADER']['OPTBOX']['SELECTTRAPVALUE']['TRAPUNKNOWN'].'</OPTION>';
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method common::checkIfEnableUnknownTraps(): '.$option);
	   return($option);   
	}
	
	/**
	* Print error-lines
	*
	* @param string $lines
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function printErrorLines($errorLines,$systemError) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::printErrorLines('.$errorLines.','.$systemError.')');
	   $this->site[] = '   <DIV CLASS="errorDescription">';
	   foreach($errorLines as $lines) {
	      $this->site[] = '      '.$lines.'<BR>';  
	   }
	   if($systemError) {
	      $this->site[] = '      Error: <I>'.$systemError.'</I>';
	   }
	   $this->site[] = '   </DIV>';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method common::printErrorLines()');
	}
	
	/**
	* Delete not used fields in the frontend, when unknown-traps was selected
	*
	* @params string $action 
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function showTrapFields($action,$trap,$rowColor,$styleLine) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::showTrapFields('.$action.','.$trap.','.$rowColor.','.$styleLine.')');
	   if($_REQUEST['trapSelect'] != "UNKNOWN") {
	      if($action == "field") {
	         global $languageXML;
	         $this->site[] = '      <TH CLASS="status" WIDTH="10%">'.$languageXML['LANG']['MAIN']['TRAPTABLEHEADER']['CATEGORY'].'</TH>';
	         $this->site[] = '      <TH CLASS="status" WIDTH="7%">'.$languageXML['LANG']['MAIN']['TRAPTABLEHEADER']['SEVERITY'].'</TH>';
	      } elseif($action == "entry") {
	         $this->site[] = '      <TD WIDTH="10%" CLASS="'.$rowColor.'"><P '.$styleLine.'>'.$trap['category'].'</P></TD>';
	         $this->site[] = '      <TD WIDTH="7%" CLASS="'.status.$trap['severity'].'" ALIGN="center"><P '.$styleLine.'>'.$trap['severity'].'</P></TD>';
	      } elseif($action == "searchField") {
                 common::printSearchfield("Category","category","10%");
                 common::printSearchfield("Severity","severity","7%");
	      }
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method common::showTrapFields()');
	}

	/**
	* Create-Link (Icon) in the frontend to delete, mark or archive one trap
	*
	* @params string $menuIcon 
	* @params string $trapID
	* @params string $severity
	* @params string $hostname  
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function showTrapMenuIcons($menuIcon,$trapID,$severity,$hostname) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::showTrapMenuIcons('.$menuIcon.','.$trapID.','.$severity.','.$hostname.')');
	   global $configINI,$languageXML;
	   if($menuIcon == "mark") {
	      if($_REQUEST['trapSelect'] == "" or $_REQUEST['trapSelect'] == "all") {
	         $this->site[] = '         <A HREF="./index.php?action=mark&trapID='.$trapID.'&severity='.$severity.'&hostname='.$hostname.'"><IMG SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/mark.png" BORDER="0" TITLE="'.$languageXML['LANG']['MAIN']['TRAPTABLEENTRY']['OPTIONREAD'].'"></A>';
	      }
	   } elseif($menuIcon == "archive") {
	      if($_REQUEST['trapSelect'] == "" or $_REQUEST['trapSelect'] == "all") {
	         $this->site[] = '         <A HREF="./index.php?action=archive&trapID='.$trapID.'&severity='.$everity.'&hostname='.$hostname.'"><IMG SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/archive.png" BORDER="0" TITLE="'.$languageXML['LANG']['MAIN']['TRAPTABLEENTRY']['OPTIONARCHIVE'].'"></A>';
	      }
	      $this->site[] = '      </TD>';
	   } elseif($menuIcon == "delete") { 
	      $this->site[] = '         <A HREF="./index.php?action=delete&trapSelect='.$_REQUEST['trapSelect'].'&trapID='.$trapID.'&severity='.$severity.'&hostname='.$hostname.'"><IMG SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/delete.png" BORDER="0" TITLE="'.$languageXML['LANG']['MAIN']['TRAPTABLEENTRY']['OPTIONDELETE'].'"></A>';
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method common::showTrapMenuIcons()');
	}
	
	/**
	* Create-Link (Icon) in the frontend to delete, mark or archive more as one trap
	*
	* @params string $menuIcon 
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function showTrapMenuIconFooter($menuIcon) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::showTrapMenuIcons('.$menuIcon.','.$trapID.','.$severity.','.$hostname.')');
	   global $configINI, $languageXML;
	   if($menuIcon == "mark") {
	      if($_REQUEST['trapSelect'] == "" or $_REQUEST['trapSelect'] == "all") {
	         $this->site[] = '         <INPUT TYPE="image" SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/mark.png" NAME="markTraps[0]" TITLE="'.$languageXML['LANG']['MAIN']['TRAPTABLEENTRY']['OPTIONREAD'].'">';
	      }     
	   } elseif($menuIcon == "archive") {
	      if($_REQUEST['trapSelect'] == "" or $_REQUEST['trapSelect'] == "all") {
	         $this->site[] = '         <INPUT TYPE="image" SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/archive.png" NAME="archiveTraps[0]" TITLE="'.$languageXML['LANG']['MAIN']['TRAPTABLEENTRY']['OPTIONARCHIVE'].'">';
	      }   
	   } elseif($menuIcon == "delete") {
	      $this->site[] = '         <INPUT TYPE="image" SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/delete.png" NAME="deleteTraps[0]" TITLE="'.$languageXML['LANG']['MAIN']['TRAPTABLEENTRY']['OPTIONDELETE'].'">';
	   } 
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method common::showTrapMenuIcons()');
	}   
	
	/**
	* Read Traps from Database and create Buttons for pages with limited trap entrys
	*
	* @author Joerg Linge
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function readTraps() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::readTraps()');
	   global $configINI, $hostname, $FRONTEND;
	   $step = $configINI['global']['step'];
	   if(!$_GET['site']){
	      $site = 0;
	      $from = 0;
	      $to = $step;
	      $limit = "0,$step";
	   } else {
	      $site = $_GET['site'];
	      $from = ($site*$step);
	      $to = (($site*$step)+$step);
	      $limit = ($site*$step).",".$step;
	   }
      
	   $DATABASE = new database($configINI);
	   $DATABASE->connect();

	   // Read traps from database
	   $traps = $DATABASE->readTraps($limit);
	   
	   $count = sizeof($traps);
	   
	   if(!isset($_REQUEST['type'])) {
	      $type = "all";  
	   } else {
	      $type = $_REQUEST['type'];  
	   }
	   $this->site[] = '<TABLE BORDER="0" WIDTH="100%">';
	   $this->site[] = '   <TR>'; 
	   if($count == $configINI['global']['step']) {
	      if($site != 0) {
	         $this->site[] = '      <TD WIDTH="45%" ALIGN="right">';
		 $this->site[] = '         <A HREF="index.php?site='.($site-1).'&trapSelect='.$_REQUEST['trapSelect'].'&severity='.$_REQUEST['severity'].'&category='.rawurlencode($_REQUEST['category']).'&hostname='.$_REQUEST['hostname'].'&searchTrapoid='.$_REQUEST['searchTrapoid'].'&searchHostname='.$_REQUEST['searchHostname'].'&searchCategory='.$_REQUEST['searchCategory'].'&searchSeverity='.$_REQUEST['searchSeverity'].'&searchMessage='.$_REQUEST['searchMessage'].'"><IMG SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/previous.png" BORDER="0"></A>';
		 $this->site[] = '      </TD>';
	      } else {
	         $this->site[] = '      <TD WIDTH="45%"></TD>';  
	      }
	      $this->site[] = '      <TD ALIGN="center">';
	      $this->site[] = '         <B>'.$from.'-'.$to.'</B>';
	      $this->site[] = '      </TD>';
	      $this->site[] = '      <TD WIDTH="45%" ALIGN="left">';
	      $this->site[] = '         <A HREF="index.php?site='.($site+1).'&trapSelect='.$_REQUEST['trapSelect'].'&severity='.$_REQUEST['severity'].'&category='.rawurlencode($_REQUEST['category']).'&hostname='.$_REQUEST['hostname'].'&searchTrapoid='.$_REQUEST['searchTrapoid'].'&searchHostname='.$_REQUEST['searchHostname'].'&searchCategory='.$_REQUEST['searchCategory'].'&searchSeverity='.$_REQUEST['searchSeverity'].'&searchMessage='.$_REQUEST['searchMessage'].'"><IMG SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/next.png" BORDER="0"></A>';
	      $this->site[] = '      </TD>';
	   } else {
	      if($site != 0) {
	         $this->site[] = '      <TD WIDTH="45%" ALIGN="right">';
	         $this->site[] = '            <A HREF="index.php?site='.($site-1).'&trapSelect='.$_REQUEST['trapSelect'].'&severity='.$_REQUEST['severity'].'&category='.rawurlencode($_REQUEST['category']).'&hostname='.$_REQUEST['hostname'].'&searchTrapoid='.$_REQUEST['searchTrapoid'].'&searchHostname='.$_REQUEST['searchHostname'].'&searchCategory='.$_REQUEST['searchCategory'].'&searchSeverity='.$_REQUEST['searchSeverity'].'&searchMessage='.$_REQUEST['searchMessage'].'"><IMG SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/previous.png" BORDER="0"></A>';
	         $this->site[] = '      </TD>';
	         $this->site[] = '      <TD ALIGN="center">';
	         $this->site[] = '         <B>'.$from.'-'.($from+$count).'</B>';
	         $this->site[] = '      </TD>';
	      }
	      if($site != 0) {
	         $this->site[] = '      <TD WIDTH="45%"></TD>';  
	      }
	   }
	   $this->site[] = '   </TR>';
	   $this->site[] = '</TABLE>';
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method common::readTraps(): Array(...)');
	   return($traps);
	}
    
	/**
	* Check a page with read traps form database
	*
	* @param string $traps
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*/
	function createTrapPage($traps) {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::createTrapPage('.$traps.')');
	   global $configINI;
	   // Check if a trap mark as read
	   if(isset($traps)){
	      foreach($traps as $trap) {
	         if($trap['trapread'] == "1"){
	            $styleLine = "style='text-decoration: line-through;'";   
	         } else {
	            $styleLine = "";
	         }

	         // Set first row color
	         if(!isset($rowColor)) {
	            $rowColor = "statusOdd";  
	         }

	         // Save the Trap-Message and delete " from Trap-Output
	         $trap['orgFormatline'] = str_replace('"',"",$trap['formatline']);
	         $arrIllegalCharJavabox = explode(",",$configINI['global']['illegalCharJavabox']);
	         foreach ($arrIllegalCharJavabox as $illegalChar) {
	            $trap['orgFormatline'] = str_replace($illegalChar,"",$trap['orgFormatline']);
	         }
 
	         // Cut Trap-Message if that set in the Configurationfile
	         if($configINI['global']['cutTrapMessage'] != "") {
	            if(strlen($trap['formatline']) > $configINI['global']['cutTrapMessage']) {
	               $trap['formatline'] = substr($trap['formatline'],0,$configINI['global']['cutTrapMessage']).'.....';
	            } 
	         }

	         // Print trap
	         $this->showTrap($trap,$rowColor,$styleLine);

	         // Change color from row
	         if($rowIndex == "0") {
	            $rowColor = "statusOdd";
	            $rowIndex = "1";
	         } else {
	            $rowColor = "statusEven";
	            $rowIndex = "0";
	         }
	      }
	   }   
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method common::createTrapPage()');
	}
	
	/**
	* Create entry for Category, if selected table not "unknown"
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*
	*/
	function createCategoryEntry() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::createCategoryEntry()');
	   global $table,$languageXML;
	   if($table['name'] != "snmptt_unknown") {
	      $this->site[] = '                     <TR>';
	      $this->site[] = '                        <TD VALIGN="top" ALIGN="left" CLASS="filterName">'.$languageXML['LANG']['HEADER']['FILTER']['CATEGORY'].':</TD>';
	      $this->site[] = '                        <TD VALIGN="top" ALIGN="left" CLASS="filterName">';
	      $this->site[] = '                            '.common::checkRequest(rawurldecode($_REQUEST['category']));
	      $this->site[] = '                        </TD>';
	      $this->site[] = '                     </TR>';
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method common::createCategoryEntry()');
	}

	/**
	* Create filter menu for categories
	*
	* @author Michael Luebben <nagtrap@nagtrap.org>
	*
	*/
	function createCategoryFilter() {
	   if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::createCategoryFilter()');
	   global $table,$languageXML;
	   if($table['name'] != "snmptt_unknown") {
	      $this->site[] = '               <TR>';
	      $this->site[] = '                  <TD ALIGN="left" COLSPAN="2" CLASS="optBoxItem">'.$languageXML['LANG']['HEADER']['OPTBOX']['CATEGORY'].':</TD>';
	      $this->site[] = '               <TR>';
	      $this->site[] = '                  <TD ALIGN="left" COLSPAN="2" class="optBoxItem">';
	      $this->site[] = '                     <SELECT NAME="category">';
	      $this->site[] = '                        <OPTION VALUE="" '.common::selected("",$_REQUEST['category'],"selected").' >'.$languageXML['LANG']['HEADER']['OPTBOX']['OPTION']['VALUEALL'].'</OPTION>';
	      $DATABASE = new database($configINI);
	      $DATABASE->connect();
	      $allCategory = $DATABASE->readCategory($table['name']);
	      if(isset($allCategory)) {
	         foreach($allCategory as $category) {
	            $this->site[] = '                        <OPTION VALUE='.rawurlencode($category).' '.common::selected($category,rawurldecode($_REQUEST['category']),"selected").'>'.$category.'</OPTION>'; 
	         }
	      }
	      $this->site[] = '                     </SELECT>';
	      $this->site[] = '                  </TD>';
	      $this->site[] = '               </TR>';
	   }
	   if (DEBUG&&DEBUGLEVEL&1) debug('End method common::createCategoryFilter()');
	}

        /**
        * Create box for attentions
        *
        * @param string $attentionMsg
        *
        * @author Michael Luebben <nagtrap@nagtrap.org>
        */
        function printAttention($attentionMsg) {
	   global $configINI;
           if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::printAttention('.$attentionMsg.')');
           $this->site[] = '<TABLE BORDER="0" WIDTH="100%" CELLPADDING="0" CELLSPACING="0">';
           $this->site[] = '   <TR>';
           $this->site[] = '      <TD WIDTH="35%" ALIGN="right"><IMG SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/attention.png" BORDER="0"></TD>';
           $this->site[] = '      <TD ALIGN="center">'.$attentionMsg.'</TD>';
           $this->site[] = '      <TD WIDTH="35%" ALIGN="left"><IMG SRC="'.$configINI['global']['images'].$configINI['global']['iconStyle'].'/attention.png" BORDER="0"></TD>';
           $this->site[] = '   </TR>';
           $this->site[] = '</TABLE>';
           if (DEBUG&&DEBUGLEVEL&1) debug('End  method common::printAttention()');
        }

        /**
        * Check for any attentions
        *
	* 1. Debugging enabled
	* 2. ...
	*
        * @author Michael Luebben <nagtrap@nagtrap.org>
        */
        function checkForAttentions() {
	   global $languageXML;
           if (DEBUG&&DEBUGLEVEL&1) debug('Start method common::checkForAttentions()');
	   if (DEBUG) common::printAttention($languageXML['LANG']['HEADER']['ATTENTION']['ENABLEDDEBUG']);
           if (DEBUG&&DEBUGLEVEL&1) debug('End method common::checkForAttentions()');
	}

        /**
        * Create serachfield 
        *
        * @param string $searchFieldname
        * @param string $searchType
        * @param string $fieldWidth
        * @param string $inputSize
        *
        * @author Michael Luebben <nagtrap@nagtrap.org>
        */
	function  printSearchfield($searchFieldname,$searchType,$fieldWidth) {
           $this->site[] = '         <TD CLASS="searchField" WIDTH="'.$fieldWidth.'">';
           $this->site[] = '            <INPUT CLASS="searchField" ID="search'.$searchFieldname.'" NAME="search'.$searchFieldname.'" AUTOCOMPLETE="off" TYPE="text/">';
           $this->site[] = '            <DIV CLASS="updateSearch" ID="update'.$searchFieldname.'"></DIV>';
           $this->site[] = '            <SCRIPT TYPE="text/javascript" LANGUAGE="javascript" CHARSET="utf-8">';
           $this->site[] = '               new Ajax.Autocompleter(\'search'.$searchFieldname.'\',\'update'.$searchFieldname.'\',\'./search.php?searchType='.$searchType.'\', {minChars: 1 });';
           $this->site[] = '            </SCRIPT>';
           $this->site[] = '         </TD>';
	}
}
?>
