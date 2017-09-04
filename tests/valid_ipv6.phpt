--TEST--
valid() and VALIDATE_IP v6
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php
$ipv6_test = array(
    "::127.0.0.1"                   => true,
    "FF01::101:127.0.1"             => false,
    "FF01:0:0:0:101:127.0.1.1"      => false,
    "FF01:0:0:0:237:101:127.0.1.1"  => true,
    "FF01::101"                     => true,
    "A1080::8:800:200C:417A"        => false,
    "1080::8:Z00:200C:417A"         => false,
    "FF01::101::1"                  => false,
    "1080::8:800:200C:417A"         => true,
    "1080:0:0:0:8:800:200C:417A"    => true,
    "2001:ec8:1:1:1:1:1:1"          => true,
    "ffff::FFFF:129.144.52.38"      => true,
    "::ffff:1.2.3.4"                => true,
    "0:0:0:0:0:FFFF:129.144.52.38"  => true,
    "0:0:0:0:0:0:13.1.68.3"         => true,
    "0:0:0:0:0:0:0:13.1.68.3"       => false,
    "::13.1.68.3"                   => true,
    "::FFFF:129.144.52.38"          => true,

    "1:2:3:4:5:6::129.144.52.38"    => false,
    "::1:2:3:4:5:6:129.144.52.38"   => false,
    "1:2:3::4:5:6:129.144.52.38"    => false,

    "1:2:3:4::5:6:7:8"              => false,
    "::1:2:3:4:5:6:7"               => true,
    "::1:2:3:4:5:6:7:8"             => false,
    "1:2:3:4:5:6:7::"               => true,
    "1:2:3:4:5:6:7:8::"             => false,
    "1:2:3:4:5:6:7::8"              => false,

    "1:2:3:4:5:6:7:8g"              => false,
    "1:2:3:4:5:6:7:g"               => false,
    "1:2:3:4:5g:6:7:8"              => false,

    'a:b:c:d:e::1.2.3.4'            => true,
    '::0:a:b:c:d:e:f'               => true,
    '0:a:b:c:d:e:f::'               => true,
    ':::1.2.3.4'                    => false,
    '8:::1.2.3.4'                   => false,
    '::01.02.03.04'                 => false,
    '::1.00.3.4'                    => false,
    '0:0:0:255.255.255.255'         => false,
    '0:0:0::255.255.255.255'        => true,
);
foreach ($ipv6_test as $ip => $exp) {
	$out = false;
	try {
		$out = valid($ip, [VALIDATE_IP, VALIDATE_IP_IPV6, ['min'=>1, 'max'=>30]], $status);
	} catch (Exception $e) {
		var_dump($e->getMessage());
	}
	var_dump($out); //valid() returns FALSE, but $out became TURE because exception prevents return value assignment to $out.
    $out = ($out === $ip);
    if ($exp !== $out) {
        echo "$ip failed (expected ", $exp?"true":"false", ", got ",
            $out?"true":"false", ")\n";
    }
}

echo "Ok\n";
?>
--EXPECT--
string(11) "::127.0.0.1"
string(106) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: 'FF01::101:127.0.1')"
bool(false)
string(113) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: 'FF01:0:0:0:101:127.0.1.1')"
bool(false)
string(28) "FF01:0:0:0:237:101:127.0.1.1"
string(9) "FF01::101"
string(111) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: 'A1080::8:800:200C:417A')"
bool(false)
string(110) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '1080::8:Z00:200C:417A')"
bool(false)
string(101) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: 'FF01::101::1')"
bool(false)
string(21) "1080::8:800:200C:417A"
string(26) "1080:0:0:0:8:800:200C:417A"
string(20) "2001:ec8:1:1:1:1:1:1"
string(24) "ffff::FFFF:129.144.52.38"
string(14) "::ffff:1.2.3.4"
string(28) "0:0:0:0:0:FFFF:129.144.52.38"
string(21) "0:0:0:0:0:0:13.1.68.3"
string(112) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '0:0:0:0:0:0:0:13.1.68.3')"
bool(false)
string(11) "::13.1.68.3"
string(20) "::FFFF:129.144.52.38"
string(115) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '1:2:3:4:5:6::129.144.52.38')"
bool(false)
string(116) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '::1:2:3:4:5:6:129.144.52.38')"
bool(false)
string(115) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '1:2:3::4:5:6:129.144.52.38')"
bool(false)
string(105) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '1:2:3:4::5:6:7:8')"
bool(false)
string(15) "::1:2:3:4:5:6:7"
string(106) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '::1:2:3:4:5:6:7:8')"
bool(false)
string(15) "1:2:3:4:5:6:7::"
string(106) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '1:2:3:4:5:6:7:8::')"
bool(false)
string(105) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '1:2:3:4:5:6:7::8')"
bool(false)
string(105) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '1:2:3:4:5:6:7:8g')"
bool(false)
string(104) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '1:2:3:4:5:6:7:g')"
bool(false)
string(105) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '1:2:3:4:5g:6:7:8')"
bool(false)
string(18) "a:b:c:d:e::1.2.3.4"
string(15) "::0:a:b:c:d:e:f"
string(15) "0:a:b:c:d:e:f::"
string(99) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: ':::1.2.3.4')"
bool(false)
string(100) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '8:::1.2.3.4')"
bool(false)
string(102) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '::01.02.03.04')"
bool(false)
string(99) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '::1.00.3.4')"
bool(false)
string(110) "IP address validataion: Invalid IPv6 address (Key: '', Validator: IP, Flags: 2 Value: '0:0:0:255.255.255.255')"
bool(false)
string(22) "0:0:0::255.255.255.255"
Ok
