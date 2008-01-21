#!/bin/sh

authorized()
{
	local endp=$1 user=$2 action=$3 arg1=$4 arg2=$5 skip=yes cval
	[ -z "$endp" -o -z "$user" -o -z "$action" ] && return 1

	# Now we strip PHP wrapping(s) and process auth rules only.
	# Accept more than one ruleset on the floor.
	while read line; do
		if [ "$skip" = "yes" -a "$line" = "# S-T-A-R-T" ]; then
			skip=no
			continue
		fi
		if [ "$skip" = "no" -a "$line" = "# S-T-O-P" ]; then
			skip=yes
			continue
		fi
		[ "$skip" = "yes" ] && continue
		# Allow comments.
		[ -z "${line###*}" ] && continue

		# Parse the line and try to make a decision earliest possible.
		# Username and endpoint must match values/regexps, action
		# must exactly match. Action arguments are tested agains values
		# or regexps, but only for 'change' action.
		# If the current rule doesn't match, advance to the next one.
		# We will fail authorization by default anyway.

		# Test action.
		cval=`echo "$line" | cut -s -d' ' -f3`
		[ "$action" = "$cval" ] || continue

		# Test username.
		cval=`echo "$line" | cut -s -d' ' -f2 | cut -s -d'@' -f1`
		[ -z "${user##$cval}" ] || continue

		# Test endpoint.
		cval=`echo "$line" | cut -s -d' ' -f2 | cut -s -d'@' -f2`
		[ -z "${endp##$cval}" ] || continue

		if [ "$action" = "change" ]; then
			[ -z "$arg1" -o -z "$arg2" ] && return 1
			cval=`echo "$line" | cut -s -d' ' -f4`
			[ -z "${arg1##$cval}" ] || continue
			cval=`echo "$line" | cut -s -d' ' -f5`
			[ -z "${arg2##$cval}" ] || continue
		fi

		# All criterias match. Pick the permission and bail out.
		cval=`echo "$line" | cut -s -d' ' -f1`
		if [ "$cval" = "allow" ]; then
			return 0
		else
			return 1
		fi
	done < "$MYDIR/userauth.php"
	return 1
}
