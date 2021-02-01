#!/bin/sh -e

THISDIR=$(dirname "$0")
"$THISDIR"/express_pre.sh
"$THISDIR"/express_phpunit.sh
"$THISDIR"/express_post.sh
