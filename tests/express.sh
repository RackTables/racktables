#!/bin/sh

THISDIR=`dirname $0`
BASEDIR=`readlink -f "$THISDIR/.."`
: ${PHPUNIT_BIN:=phpunit}

"$THISDIR"/express_pre.sh && \
"$THISDIR"/express_phpunit.sh && \
"$THISDIR"/express_post.sh && \
exit 0

exit 1
