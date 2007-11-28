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
			return array ('name' => $range['ip'].'/'.$range['mask'], 'params' => array('id'=>$_GET['id']));
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
			assertUIntArg ('rack_id');
			$rack = getRackData ($_REQUEST['rack_id']);
			if ($rack == NULL)
			{
				showError ('getRackData() failed in dynamic_title_row()');
				return NULL;
			}
			$ret['name'] = $rack['row_name'];
			$ret['params']['row_id'] = $rack['row_id'];
			break;
		case 'row':
			assertUIntArg ('row_id');
			$rowInfo = getRackRowInfo ($_REQUEST['row_id']);
			if ($rowInfo == NULL)
			{
				showError ('getRackRowInfo() failed in dynamic_title_row()');
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
	$rack = getRackData ($_GET['rack_id']);
	return array ('name' => $rack['name'], 'params' => array ('rack_id' => $_GET['rack_id']));
}

function dynamic_title_object ()
{
	global $pageno;
	$ret = array();
	switch ($pageno)
	{
		case 'object':
			assertUIntArg ('object_id');
			$object = getObjectInfo ($_REQUEST['object_id']);
			if ($object == NULL)
			{
				showError ('getObjectInfo() failed in dynamic_title_object()');
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
			assertUIntArg ('group_id');
			$groupInfo = getObjectGroupInfo ($_REQUEST['group_id']);
			if ($groupInfo == NULL)
			{
				showError ('getObjectGroupInfo() failed in dynamic_title_objgroup()');
				return NULL;
			}
			$ret['name'] = $groupInfo['name'];
			$ret['params']['group_id'] = $groupInfo['id'];
			break;
		case 'object':
			assertUIntArg ('object_id');
			$objectInfo = getObjectInfo ($_REQUEST['object_id']);
			if ($objectInfo == NULL)
			{
				showError ('getObjectInfo() failed in dynamic_title_objgroup()');
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

function static_title ($pageno)
{
	$ret['params'] = array();
	switch ($pageno)
	{
		case 'index':
			$ret['name'] = 'Main page';
			break;
		case 'ipv4space':
			$ret['name'] = 'IPv4 space';
			break;
		case 'rackspace':
			$ret['name'] = 'Rackspace';
			break;
		case 'objects':
			$ret['name'] = 'Objects';
			break;
		case 'perms':
			$ret['name'] = 'Permissions';
			break;
		case 'config':
			$ret['name'] = 'Configuration';
			break;
		case 'accounts':
			$ret['name'] = 'Accounts';
			break;
		case 'portmap':
			$ret['name'] = 'Port compatibility map';
			break;
		case 'attrs':
			$ret['name'] = 'Attributes';
			break;
		case 'dict':
			$ret['name'] = 'Dictionary';
			break;
		case 'ui':
			$ret['name'] = 'User interface';
			break;
		case 'reports':
			$ret['name'] = 'Reports';
			break;
		case 'help':
			$ret['name'] = 'Help';
			break;
		default:
			$ret['name'] = 'UNKNOWN';
			break;
	}
	return $ret;
}

?>
