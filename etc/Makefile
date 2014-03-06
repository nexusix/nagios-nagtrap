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
HTTPD_CONF=/etc/httpd/conf.d


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
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)$(SYSCONFDIR)

	for file in *.php-dist; \
		do $(INSTALL) -m 644 $(INSTALL_OPTS) $$file $(DESTDIR)$(SYSCONFDIR); done

	$(INSTALL) -m 644 apache2/nagtrap.conf $(DESTDIR)$(HTTPD_CONF);
clean:
	rm -f Makefile
	rm -f apache2/nagtrap.conf

distclean: clean
