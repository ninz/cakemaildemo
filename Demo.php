#!/usr/bin/php

<?php

include 'Request.php';

$reqstring = <<<'REQSTRING'
POST /lists/3/items HTTP/1.1
Host: todolist.com
Content-Type: text/json
Accept: text/json
Api-Key: 123abc
Content-Length: 24

{"content" : "buy milk"}
REQSTRING;

try {
    $req = HttpRequestHandler::handleRequest($reqstring);

    print "Method: " . $req->getMethod() . "\n";
    print "URL: " . $req->getUrl() . "\n";
    print "HTTP version: " . $req->getHttpVersion() . "\n\n";
    print "Full array of headers:\n\n";
    print_r($req->getHeaders());
    print "\n\n";
    print "Test of getHeader method: \n";
    print "[Good header] -> Api-Key: " . $req->getHeader("Api-Key") . "\n";
    print "[Bad header] -> apikey: " . $req->getHeader("apikey") . "\n";
    print "\n\n";
    print "Message body: \n\n";
    print $req->getMessageBody() . "\n\n";
} catch (RequestException $e) {
    print "Caught exception: " . $e->getMessage();
}

?>