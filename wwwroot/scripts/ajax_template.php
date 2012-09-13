<?php
	require_once( "../db.inc.php" );
	require_once( "../facilities.inc.php" );

	$user=new User();
	$user->UserID = $_SERVER["REMOTE_USER"];
	$user->GetUserRights($facDB);
	
	$tmpl = new DeviceTemplate();

	$deviceList=array();
	// if user has read rights then return a search if not return blank
	if ($user->ReadAccess) {
		$searchTerm="";
		if ( isset($_REQUEST["q"] ))
			$searchTerm=$_REQUEST["q"];
			
		//This will ensure that an empty json record set is returned if this is called directly or in some strange manner
		if ( $searchTerm !="" ) {
			$tmpl->TemplateID = intval( $searchTerm );
			$tmpl->GetTemplateByID( $facDB );
		}
	}
	header('Content-Type: application/json');
	echo json_encode($tmpl);  
?>
