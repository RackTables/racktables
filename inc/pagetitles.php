<?php
/*
*
*  This file is a library of title generation functions for RackTables.
*
*/

function dynamic_title_ipaddress ()
{
	return array ('name' => $_REQUEST['ip'], 'params' => array ('ip' => $_REQUEST['ip']));
}

function dynamic_title_ipv4net ()
{
	global $pageno;
	switch ($pageno)
	{
		case 'ipv4net':
			$range = getIPv4NetworkInfo ($_REQUEST['id']);
			return array ('name' => $range['ip'].'/'.$range['mask'], 'params' => array('id'=>$_REQUEST['id']));
			break;
		case 'ipaddress':
			$range = getIPv4NetworkInfo (getIPv4AddressNetworkId ($_REQUEST['ip']));
			return array
			(
				'name' => $range['ip'] . '/' . $range['mask'],
				'params' => array
				(
					'id' => $range['id'],
					'hl_ipv4_addr' => $_REQUEST['ip']
				)
			);
			break;
		default:
			return NULL;
	}
}

function dynamic_title_row ()
{
	global $pageno;
	$ret = array();
	switch ($pageno)
	{
		case 'rack':
			assertUIntArg ('rack_id', __FUNCTION__);
			$rack = getRackData ($_REQUEST['rack_id']);
			if ($rack == NULL)
			{
				showError ('getRackData() failed', __FUNCTION__);
				return NULL;
			}
			$ret['name'] = $rack['row_name'];
			$ret['params']['row_id'] = $rack['row_id'];
			break;
		case 'row':
			assertUIntArg ('row_id', __FUNCTION__);
			$rowInfo = getRackRowInfo ($_REQUEST['row_id']);
			if ($rowInfo == NULL)
			{
				showError ('getRackRowInfo() failed', __FUNCTION__);
				return NULL;
			}
			$ret['name'] = $rowInfo['name'];
			$ret['params']['row_id'] = $_REQUEST['row_id'];
			break;
		default:
			return NULL;
	}
	return $ret;
}

function dynamic_title_rack ()
{
	$rack = getRackData ($_REQUEST['rack_id']);
	return array ('name' => $rack['name'], 'params' => array ('rack_id' => $_REQUEST['rack_id']));
}

function dynamic_title_object ()
{
	global $pageno;
	$ret = array();
	switch ($pageno)
	{
		case 'object':
			assertUIntArg ('object_id', __FUNCTION__);
			$object = getObjectInfo ($_REQUEST['object_id']);
			if ($object == NULL)
			{
				showError ('getObjectInfo() failed', __FUNCTION__);
				return NULL;
			}
			$ret['name'] = $object['dname'];
			$ret['params']['object_id'] = $_REQUEST['object_id'];
			break;
		default:
			return NULL;
	}
	return $ret;
}

function dynamic_title_vservice ()
{
	global $pageno;
	$ret = array();
	switch ($pageno)
	{
		case 'ipv4vs':
			assertUIntArg ('vs_id', __FUNCTION__);
			$ret['name'] = buildVServiceName (getVServiceInfo ($_REQUEST['vs_id']));
			$ret['params']['vs_id'] = $_REQUEST['vs_id'];
			break;
		default:
			return NULL;
	}
	return $ret;
}

function dynamic_title_rspool ()
{
	global $pageno;
	$ret = array();
	switch ($pageno)
	{
		case 'ipv4rspool':
			assertUIntArg ('pool_id', __FUNCTION__);
			$poolInfo = getRSPoolInfo ($_REQUEST['pool_id']);
			$ret['name'] = empty ($poolInfo['name']) ? 'ANONYMOUS' : $poolInfo['name'];
			$ret['params']['pool_id'] = $_REQUEST['pool_id'];
			break;
		default:
			return NULL;
	}
	return $ret;
}

function dynamic_title_search ()
{
	if (isset ($_REQUEST['q']))
	{
		$ret['name'] = "search results for '${_REQUEST['q']}'";
		$ret['params']['q'] = $_REQUEST['q'];
	}
	else
	{
		$ret['name'] = "search results";
		$ret['params'] = array();
	}
	return $ret;
}

function dynamic_title_user ()
{
	$userinfo = getUserInfo ($_REQUEST['user_id']);
	return array
	(
		'name' => "Local user '" . $userinfo['user_name'] . "'",
		'params' => array ('user_id' => $_REQUEST['user_id'])
	);
}

function dynamic_title_file ()
{
	global $pageno;
	$ret = array();
	switch ($pageno)
	{
		case 'file':
			assertUIntArg ('file_id', __FUNCTION__);
			$file = getFileInfo ($_REQUEST['file_id']);
			if ($file == NULL)
			{
				showError ('getFileInfo() failed', __FUNCTION__);
				return NULL;
			}
			$ret['name'] = htmlspecialchars ($file['name']);
			$ret['params']['file_id'] = $_REQUEST['file_id'];
			break;
		case 'files':
			assertStringArg ('entity_type', __FUNCTION__);
			$ret['name'] = $_REQUEST['entity_type'];
			$ret['params']['entity_type'] = $_REQUEST['entity_type'];
			break;
		default:
			return NULL;
	}
	return $ret;
}

function dynamic_title_chapter ()
{
	$chapters = getChapterList();
	$chapter_no = $_REQUEST['chapter_no'];
	$chapter_name = isset ($chapters[$chapter_no]) ? $chapters[$chapter_no] : 'N/A';
	return array
	(
		'name' => "Chapter '${chapter_name}'",
		'params' => array ('chapter_no' => $chapter_no)
	);
}

?>
