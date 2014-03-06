<?PHP
/**
 * Here are defined some constants for use in NagTrap
 *
 * @author Michael Luebben <nagtrap@nagtrap.org>
 * @author Lars Michelsen <lars@vertical-visions.de>
 */

define('DEBUG',FALSE);
/**
 * For wanted debug output summarize these possible options:
 * 1: function beginning and ending
 * 2: progres informations in the functions
 * 4: render time
 */
define('DEBUGLEVEL', 1);
define('DEBUGFILE', '/usr/local/nagtrap/var/log/nagtrap-debug.log');

define('CONST_VERSION', '1.5.0');
define('CONST_MAINCFG', '/usr/local/nagtrap/etc/config.ini.php');
?>
