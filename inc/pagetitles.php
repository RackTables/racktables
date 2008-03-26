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

function dynamic_title_iprange ()
{
	global $pageno;
	switch ($pageno)
	{
		case 'iprange':
			$range = getIPRange($_REQUEST['id']);
			return array ('name' => $range['ip'].'/'.$range['mask'], 'params' => array('id'=>$_REQUEST['id']));
			break;
		case 'ipaddress':
			$range = getRangeByIp($_REQUEST['ip']);
			return array ('name' => $range['ip'].'/'.$range['mask'], 'params' => array('id'=>$range['id']));
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
			$ret['name'] = $rowInfo['dict_value'];
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
		case 'vservice':
			assertUIntArg ('id', __FUNCTION__);
			$ret['name'] = buildVServiceName (getVServiceInfo ($_REQUEST['id']));
			$ret['params']['id'] = $_REQUEST['id'];
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
		case 'rspool':
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

function dynamic_title_objgroup ()
{
	global $pageno;
	$ret = array();
	switch ($pageno)
	{
		case 'objgroup':
			assertUIntArg ('group_id', __FUNCTION__, TRUE);
			$group_id = $_REQUEST['group_id'];
			$groupInfo = getObjectGroupInfo();
			if ($groupInfo == NULL)
			{
				showError ('getObjectGroupInfo() failed', __FUNCTION__);
				return NULL;
			}
			$ret['name'] = $groupInfo[$group_id]['name'];
			$ret['params']['group_id'] = $group_id;
			break;
		case 'object':
			assertUIntArg ('object_id', __FUNCTION__);
			$objectInfo = getObjectInfo ($_REQUEST['object_id']);
			if ($objectInfo == NULL)
			{
				showError ('getObjectInfo() failed', __FUNCTION__);
				return NULL;
			}
			$ret['name'] = $objectInfo['objtype_name'];
			$ret['params']['group_id'] = $objectInfo['objtype_id'];
			break;
		default:
			return NULL;
	}
	return $ret;
}

?>
