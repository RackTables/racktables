<?php
	require_once( "../db.inc.php" );
	require_once( "../facilities.inc.php" );

	$user=new User();
	$user->UserID = $_SERVER["REMOTE_USER"];
	$user->GetUserRights($facDB);
	
	$pnl = new PowerPanel();

	// if user has read rights then return a search if not return blank
	if ($user->ReadAccess) {
		$searchTerm="";
		if ( isset($_REQUEST["q"] ))
			$searchTerm=$_REQUEST["q"];
			
		//This will ensure that an empty json record set is returned if this is called directly or in some strange manner
		if ( $searchTerm !="" ) {
			$pnl->PanelID = intval( $searchTerm );
			$pnl->GetPanel( $facDB );
		}
	}
	header('Content-Type: application/json');
	echo json_encode($pnl);  
?>