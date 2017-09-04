--TEST--
Bug #71745 FILTER_FLAG_NO_RES_RANGE does not cover whole 127.0.0.0/8 range
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php
//https://tools.ietf.org/html/rfc6890#section-2.1

$privateRanges = array();
// 10.0.0.0/8
$privateRanges['10.0.0.0/8'] = array('10.0.0.0', '10.255.255.255');

// 169.254.0.0/16
$privateRanges['168.254.0.0/16'] = array('169.254.0.0', '169.254.255.255');

// 172.16.0.0/12
$privateRanges['172.16.0.0/12'] = array('172.16.0.0', '172.31.0.0');

// 192.168.0.0/16
$privateRanges['192.168.0.0/16'] = array('192.168.0.0', '192.168.255.255');

foreach ($privateRanges as $key => $range) {
	list($min, $max) = $range;
	var_dump('*****'. $key .'*****');
	try {
		var_dump(valid($min, [VALIDATE_IP,
							  VALIDATE_IP_IPV4|VALIDATE_IP_ALLOW_RESERVED,
							  ['min'=>0, 'max'=>16]], $status));
		var_dump(valid($max, [VALIDATE_IP,
							  VALIDATE_IP_IPV4|VALIDATE_IP_ALLOW_RESERVED,
							  ['min'=>0, 'max'=>16]], $status));
	} catch (Exception $e) {
		var_dump($e->getMessage());
	}
}

$reservedRanges = array();

// 0.0.0.0/8
$reservedRanges['0.0.0.0/8'] = array('0.0.0.0', '0.255.255.255');

// 10.0.0.0/8
$reservedRanges['10.0.0.0/8'] = array('10.0.0.0', '10.255.255.255');

// 100.64.0.0/10
$reservedRanges['10.64.0.0/10'] = array('100.64.0.0', '100.127.255.255');

// 127.0.0.0/8
$reservedRanges['127.0.0.0/8'] = array('127.0.0.0', '127.255.255.255');

// 169.254.0.0/16
$reservedRanges['169.254.0.0/16'] = array('169.254.0.0', '169.254.255.255');

// 172.16.0.0/12
$reservedRanges['172.16.0.0/12'] = array('172.16.0.0', '172.31.0.0');

// 192.0.0.0/24
$reservedRanges['192.0.0.0/24'] = array('192.0.0.0', '192.0.0.255');

// 192.0.0.0/29
$reservedRanges['192.0.0.0/29'] = array('192.0.0.0', '192.0.0.7');

// 192.0.2.0/24
$reservedRanges['192.0.2.0/24'] = array('192.0.2.0', '192.0.2.255');

// 198.18.0.0/15
$reservedRanges['198.18.0.0/15'] = array('198.18.0.0', '198.19.255.255');

// 198.51.100.0/24
$reservedRanges['198.51.100.0/24'] = array('198.51.100.0', '198.51.100.255');

// 192.88.99.0/24
$reservedRanges['192.88.99.0/24'] = array('192.88.99.0', '192.88.99.255');

// 192.168.0.0/16
$reservedRanges['192.168.0.0/16'] = array('192.168.0.0', '192.168.255.255');

// 203.0.113.0/24
$reservedRanges['203.0.113.0/24'] = array('203.0.113.0', '203.0.113.255');

// 240.0.0.0/4 + 255.255.255.255/32
$reservedRanges['240.0.0.0/4'] = array('224.0.0.0', '255.255.255.255');

foreach ($reservedRanges as $key => $range) {
	list($min, $max) = $range;
	var_dump('*****'. $key .'*****');
	try {
		var_dump(valid($min, [VALIDATE_IP, VALIDATE_IP_IPV4|VALIDATE_IP_ALLOW_RESERVED,
							  ['min'=>0, 'max'=>100]], $status));
	} catch (Exception $e) {
		var_dump($e->getMessage());
	}
	try {
		var_dump(valid($min, [VALIDATE_IP, VALIDATE_IP_IPV4|VALIDATE_IP_ALLOW_RESERVED,
							  ['min'=>0, 'max'=>100]], $status));
	} catch (Exception $e) {
		var_dump($e->getMessage());
	}
}


?>
--EXPECT--
string(20) "*****10.0.0.0/8*****"
string(105) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 5 Value: '10.0.0.0')"
string(24) "*****168.254.0.0/16*****"
string(11) "169.254.0.0"
string(15) "169.254.255.255"
string(23) "*****172.16.0.0/12*****"
string(107) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 5 Value: '172.16.0.0')"
string(24) "*****192.168.0.0/16*****"
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 5 Value: '192.168.0.0')"
string(19) "*****0.0.0.0/8*****"
string(7) "0.0.0.0"
string(7) "0.0.0.0"
string(20) "*****10.0.0.0/8*****"
string(105) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 5 Value: '10.0.0.0')"
string(105) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 5 Value: '10.0.0.0')"
string(22) "*****10.64.0.0/10*****"
string(10) "100.64.0.0"
string(10) "100.64.0.0"
string(21) "*****127.0.0.0/8*****"
string(9) "127.0.0.0"
string(9) "127.0.0.0"
string(24) "*****169.254.0.0/16*****"
string(11) "169.254.0.0"
string(11) "169.254.0.0"
string(23) "*****172.16.0.0/12*****"
string(107) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 5 Value: '172.16.0.0')"
string(107) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 5 Value: '172.16.0.0')"
string(22) "*****192.0.0.0/24*****"
string(9) "192.0.0.0"
string(9) "192.0.0.0"
string(22) "*****192.0.0.0/29*****"
string(9) "192.0.0.0"
string(9) "192.0.0.0"
string(22) "*****192.0.2.0/24*****"
string(9) "192.0.2.0"
string(9) "192.0.2.0"
string(23) "*****198.18.0.0/15*****"
string(10) "198.18.0.0"
string(10) "198.18.0.0"
string(25) "*****198.51.100.0/24*****"
string(12) "198.51.100.0"
string(12) "198.51.100.0"
string(24) "*****192.88.99.0/24*****"
string(11) "192.88.99.0"
string(11) "192.88.99.0"
string(24) "*****192.168.0.0/16*****"
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 5 Value: '192.168.0.0')"
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 5 Value: '192.168.0.0')"
string(24) "*****203.0.113.0/24*****"
string(11) "203.0.113.0"
string(11) "203.0.113.0"
string(21) "*****240.0.0.0/4*****"
string(9) "224.0.0.0"
string(9) "224.0.0.0"
