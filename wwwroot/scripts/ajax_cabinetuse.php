<?php
	require_once( "../db.inc.php" );
	require_once( "../facilities.inc.php" );

	$user=new User();
	$user->UserID=$_SERVER["REMOTE_USER"];
	$user->GetUserRights($facDB);

	$cabinetuse=array();
	// if user has read rights then return a search if not return blank
	if($user->ReadAccess && isset($_REQUEST['cabinet'])){
		$cab=new Cabinet();
		$dev=new Device();
		
		if ( isset( $_REQUEST["deviceid"] ) )
			$dev->DeviceID = $_REQUEST["deviceid"];

		$cab->CabinetID=$dev->Cabinet=intval($_REQUEST['cabinet']);
		$devList=$dev->ViewDevicesByCabinet($facDB);
		$cab->GetCabinet($facDB);

		// Build array of each position used
		foreach($devList as $key => $device) {
			// Only count space occupied by devices other than the current one
			if ( $dev->DeviceID != $device->DeviceID ) {
				if($device->Height > 0){
					$i=$device->Height;
					while($i>0){
						$i--;
						$cabinetuse[$device->Position+$i]=true;
					}
				}
			}
		}
		$i=$cab->CabinetHeight;
		// Fill in unused rack positions for true/false checks
		while($i>0){
			if(!isset($cabinetuse[$i])){
				$cabinetuse[$i]=false;
			}
			$i--;
		}
		// Reverse sort by rack position
		krsort($cabinetuse);
	}
	header('Content-Type: application/json');
	echo json_encode($cabinetuse);
?>
