DESTDIR ?=
prefix  ?= /usr/local

docdir    ?= $(prefix)/share/doc/racktables
datadir   ?= $(prefix)/share/racktables
staticdir ?= $(datadir)/static
codedir   ?= $(datadir)/code

INSTALL         := install
INSTALL_DATA    := $(INSTALL) -m 644
INSTALL_DIR     := $(INSTALL) -m 755 -d
INSTALL_PROGRAM := $(INSTALL) -m 755

install-docs: COPYING ChangeLog LICENSE README
	$(INSTALL_DIR) $(DESTDIR)$(docdir)
	$(INSTALL_DATA) $^ $(DESTDIR)$(docdir)

install-helpers: scripts gateways
	$(INSTALL_DIR) $(DESTDIR)$(datadir)
	cp -r $^ $(DESTDIR)$(datadir)
	find $(DESTDIR)$(datadir)/scripts -type d -a -name '.svn' -exec rm -rf \{\} \; -prune
	find $(DESTDIR)$(datadir)/gateways -type d -a -name '.svn' -exec rm -rf \{\} \; -prune

install-static: wwwroot/css wwwroot/js wwwroot/pix
	$(INSTALL_DIR) $(DESTDIR)$(staticdir)
	cp -r $^ $(DESTDIR)$(staticdir)
	find $(DESTDIR)$(staticdir) -type d -a -name '.svn' -exec rm -rf \{\} \; -prune

install-code: wwwroot/inc wwwroot/favicon.ico wwwroot/index.php
	$(INSTALL_DIR) $(DESTDIR)$(codedir)/inc
	$(INSTALL_DATA) wwwroot/favicon.ico wwwroot/index.php $(DESTDIR)$(codedir)
	$(INSTALL_DATA) wwwroot/inc/*.php $(DESTDIR)$(codedir)/inc

install: install-helpers install-static install-code
