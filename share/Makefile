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
DATAROOTDIR=${prefix}/share


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
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${DATAROOTDIR}
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${DATAROOTDIR}/images
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${DATAROOTDIR}/images/dropline
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${DATAROOTDIR}/include
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${DATAROOTDIR}/include/classes
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${DATAROOTDIR}/include/css
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${DATAROOTDIR}/include/defines
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${DATAROOTDIR}/include/functions
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${DATAROOTDIR}/include/js
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${DATAROOTDIR}/include/xml
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${DATAROOTDIR}/include/xml/language
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${DATAROOTDIR}/include/xml/language/DE
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${DATAROOTDIR}/include/xml/language/EN

	for file in *.php; \
		do $(INSTALL) -m 644 $(INSTALL_OPTS) $$file $(DESTDIR)$(DATAROOTDIR); done

	for file in images/dropline/*.png; \
		do $(INSTALL) -m 644 $(INSTALL_OPTS) $$file $(DESTDIR)$(DATAROOTDIR)/images/dropline; done

	for file in include/classes/*.php; \
		do $(INSTALL) -m 644 $(INSTALL_OPTS) $$file $(DESTDIR)$(DATAROOTDIR)/include/classes; done

	for file in include/css/*.css; \
		do $(INSTALL) -m 644 $(INSTALL_OPTS) $$file $(DESTDIR)$(DATAROOTDIR)/include/css; done

	for file in include/defines/*.php; \
		do $(INSTALL) -m 644 $(INSTALL_OPTS) $$file $(DESTDIR)$(DATAROOTDIR)/include/defines; done

	for file in include/functions/*.php; \
		do $(INSTALL) -m 644 $(INSTALL_OPTS) $$file $(DESTDIR)$(DATAROOTDIR)/include/functions; done

	for file in include/js/*.js; \
		do $(INSTALL) -m 644 $(INSTALL_OPTS) $$file $(DESTDIR)$(DATAROOTDIR)/include/js; done

	for file in include/xml/language/DE/*.xml; \
		do $(INSTALL) -m 644 $(INSTALL_OPTS) $$file $(DESTDIR)$(DATAROOTDIR)/include/xml/language/DE; done

	for file in include/xml/language/EN/*.xml; \
		do $(INSTALL) -m 644 $(INSTALL_OPTS) $$file $(DESTDIR)$(DATAROOTDIR)/include/xml/language/EN; done

clean:
	rm -f Makefile
	rm -f include/defines/global.php

distclean: clean
