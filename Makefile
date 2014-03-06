####################################################################
# Makefile for NagTrap
####################################################################

###############################
# Source code directories
###############################
SRC_SHARE=./share
SRC_CONFIG=./etc
SRC_PLUGIN=./plugin


###############################
# Compiler Settings
###############################
CC=@CC@
CFLAGS=@CFLAGS@ -DPACKAGE_NAME=\"nagtrap\" -DPACKAGE_TARNAME=\"nagtrap\" -DPACKAGE_VERSION=\"1.5.0\" -DPACKAGE_STRING=\"nagtrap\ 1.5.0\" -DPACKAGE_BUGREPORT=\"michael_luebben@web.de\" -DPACKAGE_URL=\"\" -DPACKAGE=\"nagtrap\" -DVERSION=\"1.5.0\" -DDEFAULT_monitoring_USER=\"nagios\" -DDEFAULT_monitoring_GROUP=\"nagios\"
LDFLAGS=@LDFLAGS@ 

prefix=/usr/local/nagtrap
exec_prefix=${prefix}
LOGDIR=${prefix}/var/log
CFGDIR=${prefix}/etc
BINDIR=${exec_prefix}/bin
INCDIR=${prefix}/include
INSTALL=/usr/bin/install -c
INSTALL_OPTS=-o nagios -g nagios
COMMAND_OPTS=-o nagios -g nagios
HTMLDIR=${prefix}/share
LOCALSTATEDIR=${prefix}/var
HTTPD_CONF=/etc/httpd/conf.d

CP=@CP@
PERL=/usr/bin/perl

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
none:
	@echo ""
	@echo "*** Installation **********************************************"
	@echo ""
	@echo " make install"
	@echo "     - Makes a completely installation for nagtrap"
	@echo ""
	@echo ""
	@echo "*** Support Notes *******************************************"
	@echo ""
	@echo "If you have questions about configuring or running Icinga,"
	@echo "please make sure that you:"
	@echo ""
	@echo "     - Look at the sample config files"
	@echo ""
	@echo "before you post a question to one of the mailing lists at"
	@echo "Monitoring Portal:"
	@echo "http://www.monitoring-portal.org"
	@echo ""
	@echo "Also make sure to include pertinent information that could"
	@echo "help others help you.  This might include:"
	@echo ""
	@echo "     - What version of Icinga you are using"
	@echo ""
	@echo "*************************************************************"
	@echo ""
	@echo "Enjoy."
	@echo ""

install:
	$(INSTALL) -m 755 $(INSTALL_OPTS) -d $(DESTDIR)${prefix}
	$(INSTALL) -m 755 $(COMMAND_OPTS) -d $(DESTDIR)$(LOCALSTATEDIR)
	$(INSTALL) -m 755 $(COMMAND_OPTS) -d $(DESTDIR)$(LOGDIR)

	cd $(SRC_SHARE) && $(MAKE) $@
	cd $(SRC_CONFIG) && $(MAKE) $@
	cd $(SRC_PLUGIN) && $(MAKE) $@

	@echo ""
	@echo ""
	@echo ""
	@echo "Installation if NagTrap succeeded."
	@echo ""
	@echo	"Please check the new Apache2 configuration (/etc/httpd/conf.d/nagtrap.conf)."
	@echo ""
	@echo "You can install it simply by invoking 'make install-webconf'."
	@echo ""
	@echo "*************************************************************"
	@echo ""
	@echo "Have fun!"
	@echo ""

install-html:
	cd $(SRC_SHARE) && $(MAKE) install

install-config:
	cd $(SRC_CONFIG) && $(MAKE) install

install_log:
	$(INSTALL) -m 755 $(COMMAND_OPTS) -d $(DESTDIR)$(LOCALSTATEDIR)
	$(INSTALL) -m 755 $(INSTALL_OPTS_WEB) -d $(DESTDIR)$(LOGDIR)

install-done:

install-plugin:
	cd $(SRC_PLUGIN) && $(MAKE) $@

install-webconf:
	$(INSTALL) -m 644 etc/apache2/nagtrap.conf $(DESTDIR)$(HTTPD_CONF);

clean:
	rm -f Makefile
	rm -f config.log config.status subst

	cd $(SRC_SHARE) && $(MAKE) $@
	cd $(SRC_CONFIG) && $(MAKE) $@
	cd $(SRC_PLUGIN) && $(MAKE) $@

distclean: clean
	
	
create-tarball:
	./make-tarball --prefix ${PACKAGE_TARNAME}-${PACKAGE_VERSION}/
