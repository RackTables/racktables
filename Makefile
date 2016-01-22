DESTDIR   ?=
prefix    ?= /usr/local
docdir    ?= $(prefix)/share/doc/RackTables
datadir   ?= $(prefix)/share
# Default values reproduce the layout present in tar.gz, which concurs with
# the default values of $racktables_gwdir and $racktables_staticdir. But
# once the latter are patched for a shared deploy, it is enough to execute
# "make install" with appropriate arguments to split the filesystem.
staticdir ?= $(datadir)/RackTables/wwwroot
applibdir ?= $(datadir)/RackTables/wwwroot/inc
indexdir  ?= $(datadir)/RackTables/wwwroot
scriptdir ?= $(datadir)/RackTables

INSTALL         := install
INSTALL_DATA    := $(INSTALL) -m 644
INSTALL_DIR     := $(INSTALL) -m 755 -d
INSTALL_PROGRAM := $(INSTALL) -m 755

install-docs: COPYING ChangeLog LICENSE README.md
	$(INSTALL_DIR) $(DESTDIR)$(docdir)
	$(INSTALL_DATA) $^ $(DESTDIR)$(docdir)

install-helpers: scripts gateways
	$(INSTALL_DIR) $(DESTDIR)$(scriptdir)
	cp -r $^ $(DESTDIR)$(scriptdir)
	find $(DESTDIR)$(scriptdir)/scripts -type d -a -name '.git' -exec rm -rf \{\} \; -prune
	find $(DESTDIR)$(scriptdir)/gateways -type d -a -name '.git' -exec rm -rf \{\} \; -prune

install-static: wwwroot/css wwwroot/js wwwroot/pix
	$(INSTALL_DIR) $(DESTDIR)$(staticdir)
	cp -r $^ $(DESTDIR)$(staticdir)
	find $(DESTDIR)$(staticdir) -type d -a -name '.git' -exec rm -rf \{\} \; -prune

install-applib: wwwroot/inc
	$(INSTALL_DIR) $(DESTDIR)$(applibdir)/inc
	$(INSTALL_DATA) wwwroot/inc/*.php $(DESTDIR)$(applibdir)

install-index: wwwroot/index.php
	$(INSTALL_DIR) $(DESTDIR)$(indexdir)
	$(INSTALL_DATA) wwwroot/index.php $(DESTDIR)$(indexdir)

install: install-helpers install-static install-applib install-index
