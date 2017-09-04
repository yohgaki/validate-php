--TEST--
validate() and VALIDATE_IP
--SKIPIF--
<?php if (!extension_loaded("filter")) die("skip"); ?>
--FILE--
<?php
	$ips = [
		"192.168.0.1",
		"192.168.0.1.1",
		"::1",
		"fe00::0",
		"::123456",
		"::1::b",
		"127.0.0.1",
		"192.168.0.1",
		"192.0.34.166",
		"127.0.0.1",
		"192.168.0.1",
		"192.0.34.166",
		"192.0.0.1",
		"100.64.0.0",
		"100.127.255.255",
		"192.0.34.166",
		"192.0.34.166",
		"256.1237.123.1",
		"255.255.255.255",
		"255.255.255.0",
		"192.0.34.166",
		"256.1237.123.1",
		"",
		-1,
		"::1",
		"....",
		"...",
		"..",
		".",
		"1.1.1.1",
	];

$flags = [
	'none     ' => VALIDATE_FLAG_NONE,
	'v4       ' => VALIDATE_IP_IPV4,
	'v6       ' => VALIDATE_IP_IPV6,
	'v4v6     ' => VALIDATE_IP_IPV4|VALIDATE_IP_IPV6,
	'v4res    ' => VALIDATE_IP_IPV4|VALIDATE_IP_ALLOW_RESERVED,
	'v6res    ' => VALIDATE_IP_IPV6|VALIDATE_IP_ALLOW_RESERVED,
	'v4v6res  ' => VALIDATE_IP_IPV4|VALIDATE_IP_IPV6|VALIDATE_IP_ALLOW_RESERVED,
	'v4pri    ' => VALIDATE_IP_IPV4|VALIDATE_IP_ALLOW_PRIVATE,
	'v6pri    ' => VALIDATE_IP_IPV6|VALIDATE_IP_ALLOW_PRIVATE,
	'v4v6pri  ' => VALIDATE_IP_IPV4|VALIDATE_IP_IPV6|VALIDATE_IP_ALLOW_PRIVATE,
];

$spec = [
	VALIDATE_IP,
	NULL, //flag
	['min'=>0, 'max'=>100]
];

foreach($ips as $ip) {
	echo "******** IP: ".$ip."\n";
	foreach($flags as $name => $flag) {
		echo "** FLAG: ".$name."\n";
		try {
			$spec[1] = $flag;
			var_dump(valid($ip, $spec, $status), $status);
		} catch (Exception $e) {
			var_dump($e->getMessage());
		}
	}
}

echo "Done\n";
?>
--EXPECT--
******** IP: 192.168.0.1
** FLAG: none     
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 0 Value: '192.168.0.1')"
** FLAG: v4       
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 1 Value: '192.168.0.1')"
** FLAG: v6       
string(108) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '192.168.0.1')"
** FLAG: v4v6     
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 3 Value: '192.168.0.1')"
** FLAG: v4res    
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 5 Value: '192.168.0.1')"
** FLAG: v6res    
string(108) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '192.168.0.1')"
** FLAG: v4v6res  
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 7 Value: '192.168.0.1')"
** FLAG: v4pri    
string(11) "192.168.0.1"
bool(true)
** FLAG: v6pri    
string(109) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '192.168.0.1')"
** FLAG: v4v6pri  
string(11) "192.168.0.1"
bool(true)
******** IP: 192.168.0.1.1
** FLAG: none     
string(101) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 0 Value: '192.168.0.1.1')"
** FLAG: v4       
string(101) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 1 Value: '192.168.0.1.1')"
** FLAG: v6       
string(110) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '192.168.0.1.1')"
** FLAG: v4v6     
string(101) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 3 Value: '192.168.0.1.1')"
** FLAG: v4res    
string(101) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 5 Value: '192.168.0.1.1')"
** FLAG: v6res    
string(110) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '192.168.0.1.1')"
** FLAG: v4v6res  
string(101) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 7 Value: '192.168.0.1.1')"
** FLAG: v4pri    
string(101) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 9 Value: '192.168.0.1.1')"
** FLAG: v6pri    
string(111) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '192.168.0.1.1')"
** FLAG: v4v6pri  
string(102) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 11 Value: '192.168.0.1.1')"
******** IP: ::1
** FLAG: none     
string(102) "IP address validataion: IPv6 address is reserved range (Key: '', Validator: IP, Flags: 0 Value: '::1')"
** FLAG: v4       
string(100) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 1 Value: '::1')"
** FLAG: v6       
string(102) "IP address validataion: IPv6 address is reserved range (Key: '', Validator: IP, Flags: 2 Value: '::1')"
** FLAG: v4v6     
string(102) "IP address validataion: IPv6 address is reserved range (Key: '', Validator: IP, Flags: 3 Value: '::1')"
** FLAG: v4res    
string(100) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 5 Value: '::1')"
** FLAG: v6res    
string(3) "::1"
bool(true)
** FLAG: v4v6res  
string(3) "::1"
bool(true)
** FLAG: v4pri    
string(100) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 9 Value: '::1')"
** FLAG: v6pri    
string(103) "IP address validataion: IPv6 address is reserved range (Key: '', Validator: IP, Flags: 10 Value: '::1')"
** FLAG: v4v6pri  
string(103) "IP address validataion: IPv6 address is reserved range (Key: '', Validator: IP, Flags: 11 Value: '::1')"
******** IP: fe00::0
** FLAG: none     
string(7) "fe00::0"
bool(true)
** FLAG: v4       
string(104) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 1 Value: 'fe00::0')"
** FLAG: v6       
string(7) "fe00::0"
bool(true)
** FLAG: v4v6     
string(7) "fe00::0"
bool(true)
** FLAG: v4res    
string(104) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 5 Value: 'fe00::0')"
** FLAG: v6res    
string(7) "fe00::0"
bool(true)
** FLAG: v4v6res  
string(7) "fe00::0"
bool(true)
** FLAG: v4pri    
string(104) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 9 Value: 'fe00::0')"
** FLAG: v6pri    
string(7) "fe00::0"
bool(true)
** FLAG: v4v6pri  
string(7) "fe00::0"
bool(true)
******** IP: ::123456
** FLAG: none     
string(97) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 0 Value: '::123456')"
** FLAG: v4       
string(105) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 1 Value: '::123456')"
** FLAG: v6       
string(97) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '::123456')"
** FLAG: v4v6     
string(97) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 3 Value: '::123456')"
** FLAG: v4res    
string(105) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 5 Value: '::123456')"
** FLAG: v6res    
string(97) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 6 Value: '::123456')"
** FLAG: v4v6res  
string(97) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 7 Value: '::123456')"
** FLAG: v4pri    
string(105) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 9 Value: '::123456')"
** FLAG: v6pri    
string(98) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 10 Value: '::123456')"
** FLAG: v4v6pri  
string(98) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 11 Value: '::123456')"
******** IP: ::1::b
** FLAG: none     
string(95) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 0 Value: '::1::b')"
** FLAG: v4       
string(103) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 1 Value: '::1::b')"
** FLAG: v6       
string(95) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '::1::b')"
** FLAG: v4v6     
string(95) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 3 Value: '::1::b')"
** FLAG: v4res    
string(103) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 5 Value: '::1::b')"
** FLAG: v6res    
string(95) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 6 Value: '::1::b')"
** FLAG: v4v6res  
string(95) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 7 Value: '::1::b')"
** FLAG: v4pri    
string(103) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 9 Value: '::1::b')"
** FLAG: v6pri    
string(96) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 10 Value: '::1::b')"
** FLAG: v4v6pri  
string(96) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 11 Value: '::1::b')"
******** IP: 127.0.0.1
** FLAG: none     
string(108) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 0 Value: '127.0.0.1')"
** FLAG: v4       
string(108) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 1 Value: '127.0.0.1')"
** FLAG: v6       
string(106) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '127.0.0.1')"
** FLAG: v4v6     
string(108) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 3 Value: '127.0.0.1')"
** FLAG: v4res    
string(9) "127.0.0.1"
bool(true)
** FLAG: v6res    
string(106) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '127.0.0.1')"
** FLAG: v4v6res  
string(9) "127.0.0.1"
bool(true)
** FLAG: v4pri    
string(108) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 9 Value: '127.0.0.1')"
** FLAG: v6pri    
string(107) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '127.0.0.1')"
** FLAG: v4v6pri  
string(109) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 11 Value: '127.0.0.1')"
******** IP: 192.168.0.1
** FLAG: none     
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 0 Value: '192.168.0.1')"
** FLAG: v4       
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 1 Value: '192.168.0.1')"
** FLAG: v6       
string(108) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '192.168.0.1')"
** FLAG: v4v6     
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 3 Value: '192.168.0.1')"
** FLAG: v4res    
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 5 Value: '192.168.0.1')"
** FLAG: v6res    
string(108) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '192.168.0.1')"
** FLAG: v4v6res  
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 7 Value: '192.168.0.1')"
** FLAG: v4pri    
string(11) "192.168.0.1"
bool(true)
** FLAG: v6pri    
string(109) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '192.168.0.1')"
** FLAG: v4v6pri  
string(11) "192.168.0.1"
bool(true)
******** IP: 192.0.34.166
** FLAG: none     
string(12) "192.0.34.166"
bool(true)
** FLAG: v4       
string(12) "192.0.34.166"
bool(true)
** FLAG: v6       
string(109) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '192.0.34.166')"
** FLAG: v4v6     
string(12) "192.0.34.166"
bool(true)
** FLAG: v4res    
string(12) "192.0.34.166"
bool(true)
** FLAG: v6res    
string(109) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '192.0.34.166')"
** FLAG: v4v6res  
string(12) "192.0.34.166"
bool(true)
** FLAG: v4pri    
string(12) "192.0.34.166"
bool(true)
** FLAG: v6pri    
string(110) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '192.0.34.166')"
** FLAG: v4v6pri  
string(12) "192.0.34.166"
bool(true)
******** IP: 127.0.0.1
** FLAG: none     
string(108) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 0 Value: '127.0.0.1')"
** FLAG: v4       
string(108) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 1 Value: '127.0.0.1')"
** FLAG: v6       
string(106) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '127.0.0.1')"
** FLAG: v4v6     
string(108) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 3 Value: '127.0.0.1')"
** FLAG: v4res    
string(9) "127.0.0.1"
bool(true)
** FLAG: v6res    
string(106) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '127.0.0.1')"
** FLAG: v4v6res  
string(9) "127.0.0.1"
bool(true)
** FLAG: v4pri    
string(108) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 9 Value: '127.0.0.1')"
** FLAG: v6pri    
string(107) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '127.0.0.1')"
** FLAG: v4v6pri  
string(109) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 11 Value: '127.0.0.1')"
******** IP: 192.168.0.1
** FLAG: none     
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 0 Value: '192.168.0.1')"
** FLAG: v4       
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 1 Value: '192.168.0.1')"
** FLAG: v6       
string(108) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '192.168.0.1')"
** FLAG: v4v6     
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 3 Value: '192.168.0.1')"
** FLAG: v4res    
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 5 Value: '192.168.0.1')"
** FLAG: v6res    
string(108) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '192.168.0.1')"
** FLAG: v4v6res  
string(108) "IP address validation: IPv4 address is local address (Key: '', Validator: IP, Flags: 7 Value: '192.168.0.1')"
** FLAG: v4pri    
string(11) "192.168.0.1"
bool(true)
** FLAG: v6pri    
string(109) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '192.168.0.1')"
** FLAG: v4v6pri  
string(11) "192.168.0.1"
bool(true)
******** IP: 192.0.34.166
** FLAG: none     
string(12) "192.0.34.166"
bool(true)
** FLAG: v4       
string(12) "192.0.34.166"
bool(true)
** FLAG: v6       
string(109) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '192.0.34.166')"
** FLAG: v4v6     
string(12) "192.0.34.166"
bool(true)
** FLAG: v4res    
string(12) "192.0.34.166"
bool(true)
** FLAG: v6res    
string(109) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '192.0.34.166')"
** FLAG: v4v6res  
string(12) "192.0.34.166"
bool(true)
** FLAG: v4pri    
string(12) "192.0.34.166"
bool(true)
** FLAG: v6pri    
string(110) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '192.0.34.166')"
** FLAG: v4v6pri  
string(12) "192.0.34.166"
bool(true)
******** IP: 192.0.0.1
** FLAG: none     
string(9) "192.0.0.1"
bool(true)
** FLAG: v4       
string(9) "192.0.0.1"
bool(true)
** FLAG: v6       
string(106) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '192.0.0.1')"
** FLAG: v4v6     
string(9) "192.0.0.1"
bool(true)
** FLAG: v4res    
string(9) "192.0.0.1"
bool(true)
** FLAG: v6res    
string(106) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '192.0.0.1')"
** FLAG: v4v6res  
string(9) "192.0.0.1"
bool(true)
** FLAG: v4pri    
string(9) "192.0.0.1"
bool(true)
** FLAG: v6pri    
string(107) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '192.0.0.1')"
** FLAG: v4v6pri  
string(9) "192.0.0.1"
bool(true)
******** IP: 100.64.0.0
** FLAG: none     
string(10) "100.64.0.0"
bool(true)
** FLAG: v4       
string(10) "100.64.0.0"
bool(true)
** FLAG: v6       
string(107) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '100.64.0.0')"
** FLAG: v4v6     
string(10) "100.64.0.0"
bool(true)
** FLAG: v4res    
string(10) "100.64.0.0"
bool(true)
** FLAG: v6res    
string(107) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '100.64.0.0')"
** FLAG: v4v6res  
string(10) "100.64.0.0"
bool(true)
** FLAG: v4pri    
string(10) "100.64.0.0"
bool(true)
** FLAG: v6pri    
string(108) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '100.64.0.0')"
** FLAG: v4v6pri  
string(10) "100.64.0.0"
bool(true)
******** IP: 100.127.255.255
** FLAG: none     
string(15) "100.127.255.255"
bool(true)
** FLAG: v4       
string(15) "100.127.255.255"
bool(true)
** FLAG: v6       
string(112) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '100.127.255.255')"
** FLAG: v4v6     
string(15) "100.127.255.255"
bool(true)
** FLAG: v4res    
string(15) "100.127.255.255"
bool(true)
** FLAG: v6res    
string(112) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '100.127.255.255')"
** FLAG: v4v6res  
string(15) "100.127.255.255"
bool(true)
** FLAG: v4pri    
string(15) "100.127.255.255"
bool(true)
** FLAG: v6pri    
string(113) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '100.127.255.255')"
** FLAG: v4v6pri  
string(15) "100.127.255.255"
bool(true)
******** IP: 192.0.34.166
** FLAG: none     
string(12) "192.0.34.166"
bool(true)
** FLAG: v4       
string(12) "192.0.34.166"
bool(true)
** FLAG: v6       
string(109) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '192.0.34.166')"
** FLAG: v4v6     
string(12) "192.0.34.166"
bool(true)
** FLAG: v4res    
string(12) "192.0.34.166"
bool(true)
** FLAG: v6res    
string(109) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '192.0.34.166')"
** FLAG: v4v6res  
string(12) "192.0.34.166"
bool(true)
** FLAG: v4pri    
string(12) "192.0.34.166"
bool(true)
** FLAG: v6pri    
string(110) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '192.0.34.166')"
** FLAG: v4v6pri  
string(12) "192.0.34.166"
bool(true)
******** IP: 192.0.34.166
** FLAG: none     
string(12) "192.0.34.166"
bool(true)
** FLAG: v4       
string(12) "192.0.34.166"
bool(true)
** FLAG: v6       
string(109) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '192.0.34.166')"
** FLAG: v4v6     
string(12) "192.0.34.166"
bool(true)
** FLAG: v4res    
string(12) "192.0.34.166"
bool(true)
** FLAG: v6res    
string(109) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '192.0.34.166')"
** FLAG: v4v6res  
string(12) "192.0.34.166"
bool(true)
** FLAG: v4pri    
string(12) "192.0.34.166"
bool(true)
** FLAG: v6pri    
string(110) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '192.0.34.166')"
** FLAG: v4v6pri  
string(12) "192.0.34.166"
bool(true)
******** IP: 256.1237.123.1
** FLAG: none     
string(102) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 0 Value: '256.1237.123.1')"
** FLAG: v4       
string(102) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 1 Value: '256.1237.123.1')"
** FLAG: v6       
string(111) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '256.1237.123.1')"
** FLAG: v4v6     
string(102) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 3 Value: '256.1237.123.1')"
** FLAG: v4res    
string(102) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 5 Value: '256.1237.123.1')"
** FLAG: v6res    
string(111) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '256.1237.123.1')"
** FLAG: v4v6res  
string(102) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 7 Value: '256.1237.123.1')"
** FLAG: v4pri    
string(102) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 9 Value: '256.1237.123.1')"
** FLAG: v6pri    
string(112) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '256.1237.123.1')"
** FLAG: v4v6pri  
string(103) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 11 Value: '256.1237.123.1')"
******** IP: 255.255.255.255
** FLAG: none     
string(114) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 0 Value: '255.255.255.255')"
** FLAG: v4       
string(114) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 1 Value: '255.255.255.255')"
** FLAG: v6       
string(112) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '255.255.255.255')"
** FLAG: v4v6     
string(114) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 3 Value: '255.255.255.255')"
** FLAG: v4res    
string(15) "255.255.255.255"
bool(true)
** FLAG: v6res    
string(112) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '255.255.255.255')"
** FLAG: v4v6res  
string(15) "255.255.255.255"
bool(true)
** FLAG: v4pri    
string(114) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 9 Value: '255.255.255.255')"
** FLAG: v6pri    
string(113) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '255.255.255.255')"
** FLAG: v4v6pri  
string(115) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 11 Value: '255.255.255.255')"
******** IP: 255.255.255.0
** FLAG: none     
string(112) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 0 Value: '255.255.255.0')"
** FLAG: v4       
string(112) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 1 Value: '255.255.255.0')"
** FLAG: v6       
string(110) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '255.255.255.0')"
** FLAG: v4v6     
string(112) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 3 Value: '255.255.255.0')"
** FLAG: v4res    
string(13) "255.255.255.0"
bool(true)
** FLAG: v6res    
string(110) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '255.255.255.0')"
** FLAG: v4v6res  
string(13) "255.255.255.0"
bool(true)
** FLAG: v4pri    
string(112) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 9 Value: '255.255.255.0')"
** FLAG: v6pri    
string(111) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '255.255.255.0')"
** FLAG: v4v6pri  
string(113) "IP address validataion: IPv4 address is reverved range (Key: '', Validator: IP, Flags: 11 Value: '255.255.255.0')"
******** IP: 192.0.34.166
** FLAG: none     
string(12) "192.0.34.166"
bool(true)
** FLAG: v4       
string(12) "192.0.34.166"
bool(true)
** FLAG: v6       
string(109) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '192.0.34.166')"
** FLAG: v4v6     
string(12) "192.0.34.166"
bool(true)
** FLAG: v4res    
string(12) "192.0.34.166"
bool(true)
** FLAG: v6res    
string(109) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '192.0.34.166')"
** FLAG: v4v6res  
string(12) "192.0.34.166"
bool(true)
** FLAG: v4pri    
string(12) "192.0.34.166"
bool(true)
** FLAG: v6pri    
string(110) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '192.0.34.166')"
** FLAG: v4v6pri  
string(12) "192.0.34.166"
bool(true)
******** IP: 256.1237.123.1
** FLAG: none     
string(102) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 0 Value: '256.1237.123.1')"
** FLAG: v4       
string(102) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 1 Value: '256.1237.123.1')"
** FLAG: v6       
string(111) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '256.1237.123.1')"
** FLAG: v4v6     
string(102) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 3 Value: '256.1237.123.1')"
** FLAG: v4res    
string(102) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 5 Value: '256.1237.123.1')"
** FLAG: v6res    
string(111) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '256.1237.123.1')"
** FLAG: v4v6res  
string(102) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 7 Value: '256.1237.123.1')"
** FLAG: v4pri    
string(102) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 9 Value: '256.1237.123.1')"
** FLAG: v6pri    
string(112) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '256.1237.123.1')"
** FLAG: v4v6pri  
string(103) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 11 Value: '256.1237.123.1')"
******** IP: 
** FLAG: none     
string(92) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 0 Value: '')"
** FLAG: v4       
string(92) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 1 Value: '')"
** FLAG: v6       
string(92) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 2 Value: '')"
** FLAG: v4v6     
string(92) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 3 Value: '')"
** FLAG: v4res    
string(92) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 5 Value: '')"
** FLAG: v6res    
string(92) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 6 Value: '')"
** FLAG: v4v6res  
string(92) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 7 Value: '')"
** FLAG: v4pri    
string(92) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 9 Value: '')"
** FLAG: v6pri    
string(93) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 10 Value: '')"
** FLAG: v4v6pri  
string(93) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 11 Value: '')"
******** IP: -1
** FLAG: none     
string(94) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 0 Value: '-1')"
** FLAG: v4       
string(94) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 1 Value: '-1')"
** FLAG: v6       
string(94) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 2 Value: '-1')"
** FLAG: v4v6     
string(94) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 3 Value: '-1')"
** FLAG: v4res    
string(94) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 5 Value: '-1')"
** FLAG: v6res    
string(94) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 6 Value: '-1')"
** FLAG: v4v6res  
string(94) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 7 Value: '-1')"
** FLAG: v4pri    
string(94) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 9 Value: '-1')"
** FLAG: v6pri    
string(95) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 10 Value: '-1')"
** FLAG: v4v6pri  
string(95) "IP address validatation: invalid address string (Key: '', Validator: IP, Flags: 11 Value: '-1')"
******** IP: ::1
** FLAG: none     
string(102) "IP address validataion: IPv6 address is reserved range (Key: '', Validator: IP, Flags: 0 Value: '::1')"
** FLAG: v4       
string(100) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 1 Value: '::1')"
** FLAG: v6       
string(102) "IP address validataion: IPv6 address is reserved range (Key: '', Validator: IP, Flags: 2 Value: '::1')"
** FLAG: v4v6     
string(102) "IP address validataion: IPv6 address is reserved range (Key: '', Validator: IP, Flags: 3 Value: '::1')"
** FLAG: v4res    
string(100) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 5 Value: '::1')"
** FLAG: v6res    
string(3) "::1"
bool(true)
** FLAG: v4v6res  
string(3) "::1"
bool(true)
** FLAG: v4pri    
string(100) "IP address validation: IPv4 mode, but format is IPv6 (Key: '', Validator: IP, Flags: 9 Value: '::1')"
** FLAG: v6pri    
string(103) "IP address validataion: IPv6 address is reserved range (Key: '', Validator: IP, Flags: 10 Value: '::1')"
** FLAG: v4v6pri  
string(103) "IP address validataion: IPv6 address is reserved range (Key: '', Validator: IP, Flags: 11 Value: '::1')"
******** IP: ....
** FLAG: none     
string(92) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 0 Value: '....')"
** FLAG: v4       
string(92) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 1 Value: '....')"
** FLAG: v6       
string(101) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '....')"
** FLAG: v4v6     
string(92) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 3 Value: '....')"
** FLAG: v4res    
string(92) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 5 Value: '....')"
** FLAG: v6res    
string(101) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '....')"
** FLAG: v4v6res  
string(92) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 7 Value: '....')"
** FLAG: v4pri    
string(92) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 9 Value: '....')"
** FLAG: v6pri    
string(102) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '....')"
** FLAG: v4v6pri  
string(93) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 11 Value: '....')"
******** IP: ...
** FLAG: none     
string(91) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 0 Value: '...')"
** FLAG: v4       
string(91) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 1 Value: '...')"
** FLAG: v6       
string(100) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '...')"
** FLAG: v4v6     
string(91) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 3 Value: '...')"
** FLAG: v4res    
string(91) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 5 Value: '...')"
** FLAG: v6res    
string(100) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '...')"
** FLAG: v4v6res  
string(91) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 7 Value: '...')"
** FLAG: v4pri    
string(91) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 9 Value: '...')"
** FLAG: v6pri    
string(101) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '...')"
** FLAG: v4v6pri  
string(92) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 11 Value: '...')"
******** IP: ..
** FLAG: none     
string(90) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 0 Value: '..')"
** FLAG: v4       
string(90) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 1 Value: '..')"
** FLAG: v6       
string(99) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '..')"
** FLAG: v4v6     
string(90) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 3 Value: '..')"
** FLAG: v4res    
string(90) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 5 Value: '..')"
** FLAG: v6res    
string(99) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '..')"
** FLAG: v4v6res  
string(90) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 7 Value: '..')"
** FLAG: v4pri    
string(90) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 9 Value: '..')"
** FLAG: v6pri    
string(100) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '..')"
** FLAG: v4v6pri  
string(91) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 11 Value: '..')"
******** IP: .
** FLAG: none     
string(89) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 0 Value: '.')"
** FLAG: v4       
string(89) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 1 Value: '.')"
** FLAG: v6       
string(98) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '.')"
** FLAG: v4v6     
string(89) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 3 Value: '.')"
** FLAG: v4res    
string(89) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 5 Value: '.')"
** FLAG: v6res    
string(98) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '.')"
** FLAG: v4v6res  
string(89) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 7 Value: '.')"
** FLAG: v4pri    
string(89) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 9 Value: '.')"
** FLAG: v6pri    
string(99) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '.')"
** FLAG: v4v6pri  
string(90) "IP address validation: Invalid IPv4 address (Key: '', Validator: IP, Flags: 11 Value: '.')"
******** IP: 1.1.1.1
** FLAG: none     
string(7) "1.1.1.1"
bool(true)
** FLAG: v4       
string(7) "1.1.1.1"
bool(true)
** FLAG: v6       
string(104) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 2 Value: '1.1.1.1')"
** FLAG: v4v6     
string(7) "1.1.1.1"
bool(true)
** FLAG: v4res    
string(7) "1.1.1.1"
bool(true)
** FLAG: v6res    
string(104) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 6 Value: '1.1.1.1')"
** FLAG: v4v6res  
string(7) "1.1.1.1"
bool(true)
** FLAG: v4pri    
string(7) "1.1.1.1"
bool(true)
** FLAG: v6pri    
string(105) "IP address validation: IPv6 mode, but format is IPv4 (Key: '', Validator: IP, Flags: 10 Value: '1.1.1.1')"
** FLAG: v4v6pri  
string(7) "1.1.1.1"
bool(true)
Done
