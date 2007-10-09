<?
echo "Unauthorized access attempt has been logged. Cheers.";
exit();
# Syntax:
# <endpoint|*> <telnet> <hostname|-> <port|-> <username|-> <line password> <enable password>
# FIXME: <endpoint|*> <rsh> <username>

# S-T-A-R-T
switch1 telnet - - - password2 enablepassword
switch2 telnet - - - password enablepassword
# S-T-O-P

?>
