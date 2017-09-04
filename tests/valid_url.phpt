--TEST--
valid() and VALIDATE_URL
--SKIPIF--
<?php if (!extension_loaded("validate")) die("skip"); ?>
--FILE--
<?php

$values = Array(
'http://example.com/index.html',
'http://www.example.com/index.php',
'http://www.example/img/test.png',
'http://www.example/img/dir/',
'http://www.example/img/dir',
'http://www.thelongestdomainnameintheworldandthensomeandthensomemoreandmore.com/',
'http://toolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolong.com',
'http://eauBcFReEmjLcoZwI0RuONNnwU4H9r151juCaqTI5VeIP5jcYIqhx1lh5vV00l2rTs6y7hOp7rYw42QZiq6VIzjcYrRm8gFRMk9U9Wi1grL8Mr5kLVloYLthHgyA94QK3SaXCATklxgo6XvcbXIqAGG7U0KxTr8hJJU1p2ZQ2mXHmp4DhYP8N9SRuEKzaCPcSIcW7uj21jZqBigsLsNAXEzU8SPXZjmVQVtwQATPWeWyGW4GuJhjP4Q8o0.com',
'http://kDTvHt1PPDgX5EiP2MwiXjcoWNOhhTuOVAUWJ3TmpBYCC9QoJV114LMYrV3Zl58.kDTvHt1PPDgX5EiP2MwiXjcoWNOhhTuOVAUWJ3TmpBYCC9QoJV114LMYrV3Zl58.kDTvHt1PPDgX5EiP2MwiXjcoWNOhhTuOVAUWJ3TmpBYCC9QoJV114LMYrV3Zl58.CQ1oT5Uq3jJt6Uhy3VH9u3Gi5YhfZCvZVKgLlaXNFhVKB1zJxvunR7SJa.com.',
'http://kDTvHt1PPDgX5EiP2MwiXjcoWNOhhTuOVAUWJ3TmpBYCC9QoJV114LMYrV3Zl58R.example.com',
'http://[2001:0db8:0000:85a3:0000:0000:ac1f:8001]',
'http://[2001:db8:0:85a3:0:0:ac1f:8001]:123/me.html',
'http://[2001:db8:0:85a3::ac1f:8001]/',
'http://[::1]',
'http://cont-ains.h-yph-en-s.com',
'http://..com',
'http://a.-bc.com',
'http://ab.cd-.com',
'http://-.abc.com',
'http://abc.-.abc.com',
'http://underscore_.example.com',
'http//www.example/wrong/url/',
'http:/www.example',
'file:///tmp/test.c',
'ftp://ftp.example.com/tmp/',
'/tmp/test.c',
'/',
'http://',
'http:/',
'http:',
'http',
'',
-1,
array(),
'mailto:foo@bar.com',
'news:news.php.net',
'file://foo/bar',
"http://\r\n/bar",
"http://example.com:qq",
"http://example.com:-2",
"http://example.com:65536",
"http://example.com:65537",
);
foreach ($values as $value) {
    try {
        var_dump(valid($value, [VALIDATE_URL, VALIDATE_URL_ALLOW_ANY_SCHEME,
								['min'=>10, 'max'=>1000]], $status));
    } catch (Exception $e) {
        var_dump($e->getMessage());
    }
}


// Flags are not implemented
/*
try {
    var_dump(valid("qwe", [VALIDATE_URL, VALIDATE_URL_SCHEME_REQUIRED,
						   ['min'=>1, 'max'=>100]], $status));
} catch (Exception $e) {
    var_dump($e->getMessage());
}
try {
    var_dump(valid("http://qwe", [VALIDATE_URL, VALIDATE_URL_SCHEME_REQUIRED,
								  ['min'=>1, 'max'=>100]], $status));
} catch (Exception $e) {
    var_dump($e->getMessage());
}
try {
    var_dump(valid("http://", [VALIDATE_URL, VALIDATE_URL_HOST_REQUIRED,
							   ['min'=>1, 'max'=>100]], $status));
} catch (Exception $e) {
    var_dump($e->getMessage());
}
try {
    var_dump(valid("/tmp/test", [VALIDATE_URL, VALIDATE_URL_HOST_REQUIRED,
								 ['min'=>1, 'max'=>100]], $status));
} catch (Exception $e) {
    var_dump($e->getMessage());
}
try {
    var_dump(valid("http://www.example.com", [VALIDATE_URL, VALIDATE_URL_HOST_REQUIRED,
											  ['min'=>1, 'max'=>100]], $status));
} catch (Exception $e) {
    var_dump($e->getMessage());
}
try {
    var_dump(valid("http://www.example.com", [VALIDATE_URL, VALIDATE_URL_PATH_REQUIRED,
											  ['min'=>1, 'max'=>100]], $status));
} catch (Exception $e) {
    var_dump($e->getMessage());
}
try {
    var_dump(valid("http://www.example.com/path/at/the/server/",
				   [VALIDATE_URL, VALIDATE_URL_PATH_REQUIRED,
					['min'=>1, 'max'=>100]], $status));
} catch (Exception $e) {
    var_dump($e->getMessage());
}
try {
    var_dump(valid("http://www.example.com/index.html",
				   [VALIDATE_URL, VALIDATE_URL_QUERY_REQUIRED,
					['min'=>1, 'max'=>100]], $status));
} catch (Exception $e) {
    var_dump($e->getMessage());
}
try {
    var_dump(valid("http://www.example.com/index.php?a=b&c=d",
				   [VALIDATE_URL, VALIDATE_URL_QUERY_REQUIRED,
					['min'=>1, 'max'=>100]], $status));
} catch (Exception $e) {
    var_dump($e->getMessage());
}
*/
echo "Done\n";
?>
--EXPECT--
string(29) "http://example.com/index.html"
string(32) "http://www.example.com/index.php"
string(31) "http://www.example/img/test.png"
string(27) "http://www.example/img/dir/"
string(26) "http://www.example/img/dir"
string(79) "http://www.thelongestdomainnameintheworldandthensomeandthensomemoreandmore.com/"
string(157) "URL validation: Invalid domain (Key: '', Validator: URL, Flags: 4 Value: 'http://toolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolong.com')"
string(337) "URL validation: Invalid domain (Key: '', Validator: URL, Flags: 4 Value: 'http://eauBcFReEmjLcoZwI0RuONNnwU4H9r151juCaqTI5VeIP5jcYIqhx1lh5vV00l2rTs6y7hOp7rYw42QZiq6VIzjcYrRm8gFRMk9U9Wi1grL8Mr5kLVloYLthHgyA94QK3SaXCATklxgo6XvcbXIqAGG7U0KxTr8hJJU1p2ZQ2mXHmp4DhYP8N9SRuEKzaCPcSIcW7uj21jZqBigsLsNAXEzU8SPXZjmVQVtwQATPWeWyGW4GuJhjP4Q8o0.com')"
string(261) "http://kDTvHt1PPDgX5EiP2MwiXjcoWNOhhTuOVAUWJ3TmpBYCC9QoJV114LMYrV3Zl58.kDTvHt1PPDgX5EiP2MwiXjcoWNOhhTuOVAUWJ3TmpBYCC9QoJV114LMYrV3Zl58.kDTvHt1PPDgX5EiP2MwiXjcoWNOhhTuOVAUWJ3TmpBYCC9QoJV114LMYrV3Zl58.CQ1oT5Uq3jJt6Uhy3VH9u3Gi5YhfZCvZVKgLlaXNFhVKB1zJxvunR7SJa.com."
string(159) "URL validation: Invalid domain (Key: '', Validator: URL, Flags: 4 Value: 'http://kDTvHt1PPDgX5EiP2MwiXjcoWNOhhTuOVAUWJ3TmpBYCC9QoJV114LMYrV3Zl58R.example.com')"
string(48) "http://[2001:0db8:0000:85a3:0000:0000:ac1f:8001]"
string(50) "http://[2001:db8:0:85a3:0:0:ac1f:8001]:123/me.html"
string(36) "http://[2001:db8:0:85a3::ac1f:8001]/"
string(12) "http://[::1]"
string(31) "http://cont-ains.h-yph-en-s.com"
string(88) "URL validation: Invalid domain (Key: '', Validator: URL, Flags: 4 Value: 'http://..com')"
string(92) "URL validation: Invalid domain (Key: '', Validator: URL, Flags: 4 Value: 'http://a.-bc.com')"
string(93) "URL validation: Invalid domain (Key: '', Validator: URL, Flags: 4 Value: 'http://ab.cd-.com')"
string(92) "URL validation: Invalid domain (Key: '', Validator: URL, Flags: 4 Value: 'http://-.abc.com')"
string(96) "URL validation: Invalid domain (Key: '', Validator: URL, Flags: 4 Value: 'http://abc.-.abc.com')"
string(106) "URL validation: Invalid domain (Key: '', Validator: URL, Flags: 4 Value: 'http://underscore_.example.com')"
string(101) "URL validation: Invalid URL (Key: '', Validator: URL, Flags: 4 Value: 'http//www.example/wrong/url/')"
string(90) "URL validation: Invalid URL (Key: '', Validator: URL, Flags: 4 Value: 'http:/www.example')"
string(18) "file:///tmp/test.c"
string(26) "ftp://ftp.example.com/tmp/"
string(84) "URL validation: Invalid URL (Key: '', Validator: URL, Flags: 4 Value: '/tmp/test.c')"
string(75) "URL valiation: Too short URL (Key: '', Validator: URL, Flags: 4 Value: '/')"
string(81) "URL valiation: Too short URL (Key: '', Validator: URL, Flags: 4 Value: 'http://')"
string(80) "URL valiation: Too short URL (Key: '', Validator: URL, Flags: 4 Value: 'http:/')"
string(79) "URL valiation: Too short URL (Key: '', Validator: URL, Flags: 4 Value: 'http:')"
string(78) "URL valiation: Too short URL (Key: '', Validator: URL, Flags: 4 Value: 'http')"
string(74) "URL valiation: Too short URL (Key: '', Validator: URL, Flags: 4 Value: '')"
string(76) "URL valiation: Too short URL (Key: '', Validator: URL, Flags: 4 Value: '-1')"
string(124) "Scalar value expected. Array is given. Need VALIDATE_ARRAY as parent spec?  (Key: '', Validator: URL, Flags: 4 Value: 'N/A')"
string(18) "mailto:foo@bar.com"
string(17) "news:news.php.net"
string(14) "file://foo/bar"
string(89) "URL validation: Invalid domain (Key: '', Validator: URL, Flags: 4 Value: 'http://
/bar')"
string(102) "URL validation: Failed to parse URL (Key: '', Validator: URL, Flags: 4 Value: 'http://example.com:qq')"
string(102) "URL validation: Failed to parse URL (Key: '', Validator: URL, Flags: 4 Value: 'http://example.com:-2')"
string(105) "URL validation: Failed to parse URL (Key: '', Validator: URL, Flags: 4 Value: 'http://example.com:65536')"
string(105) "URL validation: Failed to parse URL (Key: '', Validator: URL, Flags: 4 Value: 'http://example.com:65537')"
Done
