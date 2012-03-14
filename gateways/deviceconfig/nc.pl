#!/usr/bin/perl -w

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

use strict;
use Getopt::Long qw(:config pass_through);;
use Fcntl;
use IPC::Open2;
my $NC_EXEC = 'nc';

my ($marker, $marker_halt);
my $wait_for;
my $help;
GetOptions ("stopwhen=s" => \$marker,
			"haltwhen=s" => \$marker_halt,
			"putwhen=s" => \$wait_for,
			"help|h" => \$help);
if ($help) {
	print <<END;
Wrapper around nc for non-interactive sessions.
It can close session when it sees the regexp-matching string in session output.
It can wait for command prompt to push the next command to remote side.
It DOES NOT directly link its standard input with nc`s standard input.

Additional options availivle:
--help, -h           This help message
--stopwhen=<regexp>  Close nc when this string is seen in the output
--putwhen=<regexp>   Put next line only if last output line complies to regexp

Original nc options:
END
	exec($NC_EXEC);
	exit;
}

my $hostname = 'unknown';
if (@ARGV >= 2) {
	$hostname = $ARGV[$#ARGV - 1];
}
my ($break_re, $prompt_re, $halt_re);
if ($marker) {
	$break_re = qr/($marker)/m;
}
if ($marker_halt) {
	$halt_re = qr/($marker_halt)/m;
}
if ($wait_for) {
	$prompt_re = qr/$wait_for/;
}
$| = 1;

my @cmds = <STDIN>;

my $log = '';
local (*Reader, *Writer);
my $pid = open2(\*Reader, \*Writer, $NC_EXEC, @ARGV) or die "cant start $NC_EXEC: $!\n";
my $stdin_ended = 0;

my $total_read = 0;
my $vec = '';
while () {
	vec($vec, fileno(Reader), 1) = 1;
	select($vec, undef, undef, undef);

	my $buff;
	my $nb = sysread(Reader, $buff, 64 * 1024);
	$total_read += $nb;
	print $buff if $nb; # echo to STDOUT
	if (! $nb) {
		if (@cmds and $total_read) {
			print STDERR "$hostname: connection interrupted by remote side.\n";
		}
		last; # exit if nc closed connection
	}

	$log .= $buff;
	my $halt_matched = $halt_re && $log =~ $halt_re;
	if ($halt_matched or $break_re && $log =~ $break_re) {
		if ($halt_matched) {
			chomp $log;
			$log =~ s/.*\n//;
			$log =~ s/[^ -~]//g;
			print STDERR "$hostname: Matched line '$log', connection interrupted.\n";
			exit 1;
		}
		else {
			exit 0;
		}
	}
	$log =~ s/.*\n//s; # keep only last line in log

	if ($prompt_re and $log =~ $prompt_re) { # if interative mode and pending commands
		if (@cmds) {
			print Writer shift @cmds; # push one command
			$log = '';
		}
		else { # if interactive mode and no more commands, exit
			last;
		}
	}
	elsif (! $prompt_re and @cmds) { # if non-interactive mode and pending commands, push them all
		print Writer @cmds;
		undef @cmds;
	}
}
close Reader;
close Writer;
waitpid( $pid, 0 );
exit $? >> 8;
