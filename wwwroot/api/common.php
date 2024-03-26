<?php

function outSuccess($result='', $count=0, $msg='ok') {
    return output(true, $msg, $result, $count);
}

function outFail($msg='fail'){
    return output(false, $msg, NULL, 0);
}

function output($yn, $msg, $result, $count=0) {
    return array(
            'success'    => $yn,
            'message'    => $msg,
            'totalCount' => $count,
            'data'       => $result
       );
}

function verifyIntegerArrary($parameter) {
	if (! empty($parameter)) {
		$arr_ids = explode(",",$parameter);
		$tmp_ids = array();
		foreach ($arr_ids as $id ) {
			if (is_numeric($id)) {
				$tmp_ids[] = $id;
			}
		}           
		
		if (count($tmp_ids) > 0) {
			$parameter = implode(",",$tmp_ids);
		} else {
			return array(False,'parameter invalid');
		}
	}
	return array(True,$parameter);
}


function putLog($content,$level='debug') {
	if (is_array($content))
		$content = json_encode($content);
	$caller = debug_backtrace()[1]['function'];
	$content = time() .",". $level .",". $caller .",".$content."\n";
	file_put_contents('../FiMo_Log/log_'.date("j.n.Y").'.log', $content, FILE_APPEND);
}

/*function verifyArg($argname, $argtype) {
    switch ($argtype)
	{
	case 'string':
		return assertStringArg ($argname);
	case 'string0':
		return assertStringArg ($argname, TRUE);
	case 'natural0':
		if ('' == assertStringArg ($argname, TRUE))
			return '';
		// fall through
		// old style, for backward compatibility
	case 'uint':
	case 'natural':
		return assertNaturalNumArg ($argname);
	case 'unsigned0':
		if ('' == assertStringArg ($argname, TRUE))
			return '';
		// fall through
		// old style, for backward compatibility
	case 'uint0':
	case 'unsigned':
		return assertUnsignedIntArg ($argname);
	case 'decimal0':
		if ('' == assertStringArg ($argname, TRUE))
			return '';
		// fall through
	case 'decimal':
		if (! preg_match ('/^\d+(\.\d+)?$/', assertStringArg ($argname)))
            throw new InvalidArgumentException($argname . "format error");
		return $sic[$argname];
	case 'inet':
		return assertIPArg ($argname);
	case 'inet4':
		return assertIPv4Arg ($argname);
	case 'inet6':
		return assertIPv6Arg ($argname);
	case 'l2address0':
		if ('' == assertStringArg ($argname, TRUE))
			return '';
		// fall through
	case 'l2address':
		assertStringArg ($argname);
		try
		{
			l2addressForDatabase ($sic[$argname]);
		}
		catch (InvalidArgException $iae)
		{
			throw $iae->newIRAE ($argname);
		}
		return $sic[$argname];
	case 'tag':
		if (!validTagName (assertStringArg ($argname)))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Invalid tag name');
		return $sic[$argname];
	case 'pcre':
		return assertPCREArg ($argname);
	case 'json':
		if (NULL === ($ret = json_decode (assertStringArg ($argname), TRUE)))
			throw new InvalidRequestArgException ($argname, '(omitted)', 'Invalid JSON code received from client');
		return $ret;
	case 'array':
		if (! array_key_exists ($argname, $_REQUEST))
			throw new InvalidRequestArgException ($argname, '(missing argument)');
		if (! is_array ($_REQUEST[$argname]))
			throw new InvalidRequestArgException ($argname, '(omitted)', 'argument is not an array');
		return $_REQUEST[$argname];
	case 'array0':
		if (! array_key_exists ($argname, $_REQUEST))
			return array();
		if (! is_array ($_REQUEST[$argname]))
			throw new InvalidRequestArgException ($argname, '(omitted)', 'argument is not an array');
		return $_REQUEST[$argname];
	case 'datetime0':
		if ('' == assertStringArg ($argname, TRUE))
			return '';
		// fall through
	case 'datetime':
		$argvalue = assertStringArg ($argname);
		try
		{
			timestampFromDatetimestr ($argvalue); // discard the result on success
		}
		catch (InvalidArgException $iae)
		{
			throw $iae->newIRAE ($argname);
		}
		return $argvalue;
	case 'dateonly0':
		if ('' == assertStringArg ($argname, TRUE))
			return '';
		// fall through
	case 'dateonly':
		$argvalue = assertStringArg ($argname);
		try
		{
			SQLDateFromDateStr ($argvalue); // discard the result on success
		}
		catch (InvalidArgException $iae)
		{
			throw $iae->newIRAE ($argname);
		}
		return $argvalue;
	case 'enum/attr_type':
		assertStringArg ($argname);
		if (!in_array ($sic[$argname], array ('uint', 'float', 'string', 'dict','date')))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/vlan_type':
		assertStringArg ($argname);
		// "Alien" type is not valid until the logic is fixed to implement it in full.
		if (!in_array ($sic[$argname], array ('ondemand', 'compulsory')))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/wdmstd':
		assertStringArg ($argname);
		global $wdm_packs;
		if (! array_key_exists ($sic[$argname], $wdm_packs))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/ipproto':
		assertStringArg ($argname);
		global $vs_proto;
		if (!array_key_exists ($sic[$argname], $vs_proto))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/natv4proto':
		assertStringArg ($argname);
		global $natv4_proto;
		if (! array_key_exists ($sic[$argname], $natv4_proto))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/alloc_type':
		assertStringArg ($argname);
		if (!in_array ($sic[$argname], array ('regular', 'shared', 'virtual', 'router', 'sharedrouter', 'point2point')))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/dqcode':
		assertStringArg ($argname);
		global $dqtitle;
		if (! array_key_exists ($sic[$argname], $dqtitle))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'enum/yesno':
		if (! in_array ($sic[$argname], array ('yes', 'no')))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	case 'iif':
		assertNaturalNumArg ($argname);
		if (!array_key_exists ($sic[$argname], getPortIIFOptions()))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'Unknown value');
		return $sic[$argname];
	// 'vlan' -- any valid VLAN ID except the default
	// 'vlan1' -- any valid VLAN ID including the default
	case 'vlan':
	case 'vlan1':
		assertNaturalNumArg ($argname);
		if (! isValidVLANID ($sic[$argname]))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'not a valid VLAN ID');
		if ($argtype == 'vlan' && $sic[$argname] == VLAN_DFL_ID)
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'default VLAN not allowed');
		return $sic[$argname];
	case 'uint-vlan':
	case 'uint-vlan1':
		$argvalue = assertStringArg ($argname);
		try
		{
			list ($vdom_id, $vlan_id) = decodeVLANCK ($argvalue);
		}
		catch (InvalidArgException $iae)
		{
			throw $iae->newIRAE ($argname);
		}
		if ($argtype == 'uint-vlan' && $vlan_id == VLAN_DFL_ID)
			throw new InvalidRequestArgException ($argname, $argvalue, 'default VLAN not allowed');
		return $argvalue;
	case 'rackcode/expr':
		if ('' == assertStringArg ($argname, TRUE))
			return array();
		if (! $expr = compileExpression ($sic[$argname]))
			throw new InvalidRequestArgException ($argname, $sic[$argname], 'not a valid RackCode expression');
		return $expr;
	case 'htmlcolor0':
		if ('' == assertStringArg ($argname, TRUE))
			return '';
		// fall through
	case 'htmlcolor':
		$argvalue = assertStringArg ($argname);
		if (! isHTMLColor ($argvalue))
			throw new InvalidRequestArgException ($argname, $argvalue, 'not an HTML color');
		return $argvalue;
	default:
		throw new InvalidArgException ('argtype', $argtype); // comes not from user's input
	}

}*/


?>