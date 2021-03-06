#!/usr/bin/perl
#
# check_snmptraps.pl - icinga/)nagios plugin
#
#
# Copyright (C) 2012 Michael Luebben
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
#
# ==================== Database connect information ====================
my $dbHost = "nagios-srv.omon.kv.aval";
my $dbName = "snmptt";
my $dbUser = "snmptt";
my $dbPass = "snmptt";
my $dbTable = "snmptt";
my $dbTableUnknown = "snmptt_unknown";

# ==================== Debugging ====================
my $enableDebug = "0";
my $debugLogFile = "/usr/local/nagtrap/var/log/check_snmptraps.log";

# ==================== Variables ====================
my ($opt_V, $opt_h, $opt_C, $opt_H, $opt_O, $opt_t, $opt_r, $opt_S, $opt_m, $opt_w, $opt_c, $opt_u);
my $PROGNAME = "check_snmptraps.pl";
my $version = "1.3.2";
my $queryCategory = "";
my $queryTrapOid = "";
my $queryTrapRead = "";
my $dbQuery;
my @resultWarning;
my @resultCritical;
my @resultUnknown;
my $lastWarningTrapMessage;
my $lastCriticalTrapMessage;
my $lastUnknownTrapMessage;
my $countWarning = "0";
my $countCritical = "0";
my $countUnknown = "0";
my $outputWarning;
my $outputCritical;
my $outputUnknown;
my $exitCode;
my $HiResTime;
my $startTime;
my $endTime;
my $executeTime;
my $querySearchString;

# ==================== Load Perl modules ====================
use strict;
use Getopt::Long;
use vars qw($PROGNAME);
use lib "/usr/local/nagios/libexec"; # Pfad zur util.pm !!
use utils qw($TIMEOUT %ERRORS &print_revision &support);
use Time::HiRes qw(time);
use DBI();

# ==================== Functions ====================
# Print Usage
sub print_usage (){
   printf "\nUsage:\n";
   printf "   -H (--hostname)\n";
   printf "	Hostname to query - (required)\n";
   printf "   -S (--search)\n";
   printf "     search a expression in the message content\n";
   printf "   -C (--category)\n";
   printf "	Category from SNMP-Trap (optional)\n";
   printf "   -O (--oid)\n";
   printf "  	OID from SNMP-Trap (optional)\n";
   printf "   -w (--warning)\n";
   printf "	Check for warning traps in the database\n";
   printf "   -c (--critical)\n";
   printf "	Check for critical traps in the database\n";
   printf "      (Default is set the option -w and -c)\n";
   printf "   -u (--unknown)\n";
   printf " Use the option -u without -w and/or -c!";
   printf "	Check for unknown traps in the database\n";
   printf "   -r (--read)\n";
   printf "	Read marked traps from database too.\n";
   printf "   -m (--message)\n";
   printf "	Print Message from last read trap from database.\n";
   printf "	This option can only used with -w or -c!\n";
   printf "   -t (--timeout)\n";
   printf "     seconds before the plugin times out (default=$TIMEOUT)\n";
   printf "   -V (--version)\n";
   printf "     Plugin version\n";
   printf "   -h (--help)\n";
   printf "     Print this help \n";
}

# Print help
sub print_help () {
   printf "Copyright (c) 2011 Michael Luebben\n\n";
   printf "check_snmptraps.pl plugin for Nagios monitors snmptraps \n";
   printf "in the database for a target host\n";
   print_usage();
   printf "\n";
   print_revision($PROGNAME, '$Revision: 1.3.1 $');
}

# ==================== Debugging ====================
# SYNTAX: debugLog(<Option>,<Message>);
# Options:
#   0 = Make a start entry into the debugLog debugLog
#   1 = Write message to debugLog debugLog
#   2 = Make a stop entry into the debugLog debugLog
sub debug {
   if ($enableDebug == 1) {
      my $option = $_[0];
      my $value = $_[1];
      $HiResTime = sprintf("%.5f", time());
      open(debugLog, ">>$debugLogFile");
         if ($option == 0) {
            $startTime = $HiResTime;
            print debugLog $HiResTime." ---------------===== Start debugging =====---------------\n";
            print debugLog $HiResTime." Plugin-Version: ".$version."\n";
         } elsif ($option == 1) {
            chomp($value);
            if($value) {
               print debugLog $HiResTime." ".$value."\n";
            }
         } elsif ($option == 2) {
            $endTime = sprintf("%.5f", time());
            $executeTime = $endTime - $value;
            $executeTime = sprintf("%.2f",$executeTime);
            print debugLog $HiResTime." Plugin executen time ".$executeTime."s\n";
            print debugLog $HiResTime." ---------------===== Stop debugging =====----------------\n\n";
         }
      close(debugLog);
   }
}

# ==================== Get options ====================
Getopt::Long::Configure('bundling');
GetOptions(
   "H=s" => \$opt_H, "hostname"	=> \$opt_H,
   "C=s" => \$opt_C, "category"	=> \$opt_C,
   "O=s" => \$opt_O, "oid"	=> \$opt_O,
   "w"   => \$opt_w, "warning"  => \$opt_w,
   "c" 	 => \$opt_c, "critical" => \$opt_c,
   "u" 	 => \$opt_u, "unknown" 	=> \$opt_u,
   "r"   => \$opt_r, "read"	=> \$opt_r,
   "S=s" => \$opt_S, "search=s"	=> \$opt_S,
   "m"   => \$opt_m, "message"  => \$opt_m,
   "t=i" => \$opt_t, "timeout"  => \$opt_t,
   "V"   => \$opt_V, "version"  => \$opt_V,
   "h"   => \$opt_h, "help"     => \$opt_h);

# ==================== Main ====================
if ($opt_t) {
   $TIMEOUT=$opt_t;
}
 
# Just in case of problems, let's not hang Nagios
$SIG{'ALRM'} = sub {
   print "CRITCAL - Plugin Timed out\n";
   &debug(1,"CRITCAL - Plugin Timed out");
   exit $ERRORS{"CRITICAL"};
};
alarm($TIMEOUT);

&debug(0);

if ($opt_V) {
   printf "$PROGNAME Version $version\n";
   exit $ERRORS{'OK'};
}
 
if ($opt_h) {
   print_help();
   exit $ERRORS{'OK'};
}
 
if (! $opt_H) {
   print "No Hostname specified\n\n";
   print_usage();
   &debug(1,"No Hostname specified");
   &debug(1,"Exit code: UNKNOWN");
   &debug(2,$startTime);
   exit $ERRORS{'UNKNOWN'};
}

# Create query for search string in trap messages
if ($opt_S) {
   $querySearchString = "AND LOCATE('$opt_S',formatline)>0";
} else {
   $querySearchString = "";
}

# Create query's for set options
if ($opt_C) {
   $queryCategory = "AND category='$opt_C'";
}

if ($opt_O) {
   $queryTrapOid = "AND trapoid='$opt_O'";
}

if ($opt_r) {
   $queryTrapRead = "";
} else {
   $queryTrapRead = "AND trapread='0'";
}

if ($opt_w && $opt_c && $opt_u || $opt_w && $opt_u || $opt_c && $opt_u){
	print "Use the option -u without -w and/or -c!";
	print_usage();
   	&debug(1,"Use the option -u without -w and/or -c!");
   	&debug(1,"Exit code: UNKNOWN");
    &debug(2,$startTime);
   	exit $ERRORS{'UNKNOWN'};
}
# Set -w and -c as default
if (!$opt_w && !$opt_c && !$opt_u){
   $opt_w = "1";
   $opt_c = "1";
}

# Connect to the database.
my $dbConnect = DBI->connect("DBI:mysql:database=$dbName;host=$dbHost","$dbUser","$dbPass") ||
   die "ERROR - Can't connect to MySQL-Database: ".$DBI::errstr."\n";
&debug(1,"Connect to DB: TRUE");

# Read warning-Traps from database
if ($opt_w) {
   $dbQuery = $dbConnect->prepare("SELECT formatline FROM $dbTable WHERE hostname='$opt_H' AND severity='WARNING' $queryCategory $queryTrapOid $queryTrapRead $querySearchString");
   &debug(1,"Query: SELECT formatline FROM ".$dbTable." WHERE hostname='".$opt_H."' AND severity='WARNING' ".$queryCategory." ".$queryTrapOid." ".$queryTrapRead." ".$querySearchString);
   $dbQuery->execute();
   while (@resultWarning = $dbQuery->fetchrow_array) {
      $countWarning++;
      $lastWarningTrapMessage = $resultWarning['0'];
   }
   &debug(1,"Count warnings: ".$countWarning);
   &debug(1,"Last trap message: ".$lastWarningTrapMessage);
}

# Read critical-Traps from database
if ($opt_c) {
   $dbQuery = $dbConnect->prepare("SELECT formatline FROM $dbTable WHERE hostname='$opt_H' AND severity='CRITICAL' $queryCategory $queryTrapOid $queryTrapRead $querySearchString");
   &debug(1,"Query: SELECT formatline FROM ".$dbTable." WHERE hostname='".$opt_H."' AND severity='CRITICAL' ".$queryCategory." ".$queryTrapOid." ".$queryTrapRead." ".$querySearchString);
   $dbQuery->execute();
   while (@resultCritical = $dbQuery->fetchrow_array) {
      $countCritical++;
      $lastCriticalTrapMessage = $resultCritical['0'];
   }
   &debug(1,"Count critical: ".$countCritical);
   &debug(1,"Last trap message: ".$lastCriticalTrapMessage);
}

# Read Unknown-Traps from database
if ($opt_u) {
   $dbQuery = $dbConnect->prepare("SELECT formatline FROM $dbTableUnknown WHERE hostname='$opt_H' $queryTrapOid $querySearchString");
   &debug(1,"Query: SELECT formatline FROM ".$dbTableUnknown." WHERE hostname='".$opt_H."' ".$queryTrapOid." ".$querySearchString);
   $dbQuery->execute();
   while (@resultUnknown = $dbQuery->fetchrow_array) {
      $countUnknown++;
      $lastUnknownTrapMessage = $resultUnknown['0'];
   }
   &debug(1,"Count unknown: ".$countUnknown);
   &debug(1,"Last trap message: ".$lastUnknownTrapMessage);
}


# Close connection to database
$dbConnect->disconnect;
&debug(1,"Disconnect from DB: TRUE");

# Create Output
if ($opt_w && $opt_c) {
   $exitCode = $ERRORS{'OK'};   
   if ($countWarning == "0") {
      $outputWarning = "No warning Traps";
   } elsif ($countWarning == "1") {
      $outputWarning = "$countWarning warning Trap";
      $exitCode = $ERRORS{'WARNING'};  
   } else {
      $outputWarning = "$countWarning warning Traps";
      $exitCode = $ERRORS{'WARNING'};
   }

   if ($countCritical == "0") {
      $outputCritical = "and no critical traps in the database";
   } elsif ($countCritical == "1") {
      $outputCritical = "and $countCritical critical Trap in the database";
      $exitCode = $ERRORS{'CRITICAL'};
   } else {
      $outputCritical = "and $countCritical critical Traps in the database";
      $exitCode = $ERRORS{'CRITICAL'};
   }

   if($exitCode == $ERRORS{'OK'}) {
      printf "OK - $outputWarning $outputCritical|'warning trap'=$countWarning;;;; 'critical trap'=$countCritical;;;;\n";
      &debug(1,"Output: OK - ".$outputWarning." ".$outputCritical."|'warning trap'=".$countWarning.";;;; 'critical trap'=".$countCritical.";;;;");
   }
   if($exitCode == $ERRORS{'WARNING'}) {
      printf "WARNING - $outputWarning $outputCritical|'warning trap'=$countWarning;;;; 'critical trap'=$countCritical;;;;\n";
      &debug(1,"Output: WARNING - ".$outputWarning." ".$outputCritical."|'warning trap'=".$countWarning.";;;; 'critical trap'=".$countCritical.";;;;");
   }
   if($exitCode == $ERRORS{'CRITICAL'}) {
      printf "CRITICAL - $outputWarning $outputCritical|'warning trap'=$countWarning;;;; 'critical trap'=$countCritical;;;;\n";
      &debug(1,"Output: CRITICAL - ".$outputWarning." ".$outputCritical."|'warning trap'=".$countWarning.";;;; 'critical trap'=".$countCritical.";;;;");
   }
   &debug(1,"Exit code: ".$exitCode);
   &debug(2,$startTime);
   exit $exitCode;
}

if ($opt_w) {
   if ($countWarning == "0") {
      $outputWarning = "No warning Traps in the database";
      $exitCode = $ERRORS{'OK'};   
   } elsif ($countWarning == "1") {
      $outputWarning = "$countWarning warning Trap";
      $exitCode = $ERRORS{'WARNING'};   
   } else {
      $outputWarning = "$countWarning warning Traps";
      $exitCode = $ERRORS{'WARNING'};   
   }
   if ($opt_m) {
      if ($countWarning != "0") {
         $outputWarning .= ": $lastWarningTrapMessage";
         $exitCode = $ERRORS{'WARNING'};   
      } else {
         $exitCode = $ERRORS{'OK'};   
      }
   }

   if($exitCode == $ERRORS{'OK'}) {
      printf "OK - $outputWarning|'warning trap='$countWarning;;;;\n";
      &debug(1,"Output: OK - ".$outputWarning."|'warning trap='".$countWarning.";;;;");
   }
   if($exitCode == $ERRORS{'WARNING'}) {
      printf "WARNING - $outputWarning|'warning trap='$countWarning;;;;\n";
      &debug(1,"Output: WARNING - ".$outputWarning."|'warning trap='".$countWarning.";;;;");
   }
   &debug(1,"Exit code: ".$exitCode);
   &debug(2,$startTime);
   exit $exitCode;
}

if ($opt_c) {
   if ($countCritical == "0") {
      $outputCritical = "No critical Traps in the database";
      $exitCode = $ERRORS{'OK'};
   } elsif ($countCritical == "1") {
      $outputCritical = "$countCritical critical Trap";
      $exitCode = $ERRORS{'CRITICAL'};
   } else {
      $outputCritical = "$countCritical critical Traps";
      $exitCode = $ERRORS{'CRITICAL'};
   }
   if ($opt_m) {
      if ($countCritical != "0") {
         $outputCritical .= ": $lastCriticalTrapMessage";
         $exitCode = $ERRORS{'CRITICAL'};
      } else {
         $exitCode = $ERRORS{'OK'};
      }
   }

   if($exitCode == $ERRORS{'OK'}) {
      printf "OK - $outputCritical|'critical trap='$countCritical;;;;\n";
      &debug(1,"Output: OK - ".$outputCritical."|'critical trap='".$countCritical.";;;;");
   }
   if($exitCode == $ERRORS{'CRITICAL'}) {
      printf "CRITICAL - $outputCritical|'critical trap='$countCritical;;;;\n";
      &debug(1,"Output: CRITICAL - ".$outputCritical."|'critical trap='".$countCritical.";;;;");
   }
   &debug(1,"Exit code: ".$exitCode);
   &debug(2,$startTime);
   exit $exitCode;
}

if ($opt_u) {
   if ($countUnknown == "0") {
      $outputUnknown = "No Unknown Traps in the database";
      $exitCode = $ERRORS{'OK'};
   } elsif ($countUnknown == "1") {
      $outputUnknown = "$countUnknown Unknown Trap";
      $exitCode = $ERRORS{'CRITICAL'};
   } else {
      $outputUnknown = "$countUnknown Unknown Traps";
      $exitCode = $ERRORS{'CRITICAL'};
   }
   if ($opt_m) {
      if ($countUnknown != "0") {
         $outputUnknown .= ": $lastUnknownTrapMessage";
         $exitCode = $ERRORS{'CRITICAL'};
      } else {
         $exitCode = $ERRORS{'OK'};
      }
   }

   if($exitCode == $ERRORS{'OK'}) {
      printf "OK - $outputUnknown|'Unknown trap='$countUnknown;;;;\n";
      &debug(1,"Output: OK - ".$outputUnknown."|'Unknown trap='".$countUnknown.";;;;");
   }
   if($exitCode == $ERRORS{'CRITICAL'}) {
      printf "CRITICAL - $outputUnknown|'Unknown trap='$countUnknown;;;;\n";
      &debug(1,"Output: CRITICAL - ".$outputUnknown."|'Unknown trap='".$countUnknown.";;;;");
   }
   &debug(1,"Exit code: ".$exitCode);
   &debug(2,$startTime);
   exit $exitCode;
}
