####################################################################
# Makefile for NagTrap
####################################################################


###############################
# Compiler Settings
###############################
CC=@CC@
CFLAGS=@CFLAGS@ -DPACKAGE_NAME=\"nagtrap\" -DPACKAGE_TARNAME=\"nagtrap\" -DPACKAGE_VERSION=\"1.5.0\" -DPACKAGE_STRING=\"nagtrap\ 1.5.0\" -DPACKAGE_BUGREPORT=\"michael_luebben@web.de\" -DPACKAGE_URL=\"\" -DPACKAGE=\"nagtrap\" -DVERSION=\"1.5.0\" -DDEFAULT_monitoring_USER=\"nagios\" -DDEFAULT_monitoring_GROUP=\"nagios\"
LDFLAGS=@LDFLAGS@ 

prefix=/usr/local/nagtrap
exec_prefix=${prefix}
INSTALL=/usr/bin/install -c
INSTALL_OPTS=-o nagios -g nagios
SYSCONFDIR=${prefix}/etc
DATAROOTDIR=${prefix}/share
MONITORING_LIBEXECDIR=/usr/local/nagios/libexec

###############################
# Tools
###############################
MKDIR=/bin/mkdir

###############################
# Global
###############################
PACKAGE_TARNAME=nagtrap
PACKAGE_NAME=nagtrap
PACKAGE_VERSION=1.5.0


###############################
# Installation
###############################

install:
	for file in *.pl; \
		do $(INSTALL) -m 755 $(INSTALL_OPTS) $$file $(DESTDIR)$(MONITORING_LIBEXECDIR); done

clean:
	rm -f Makefile
	rm -f check_snmptraps.pl
	rm -f check_jobs.pl

distclean: clean
