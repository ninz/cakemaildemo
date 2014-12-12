<?php

/**
 * A class that defines an HTTP request that follows RFC 2616 
 * See: http://www.w3.org/Protocols/rfc2616/rfc2616-sec5.html
 *
 * @package Request
 */
class Request
{

    private $method = '';
    private $url = '';
    private $http_version = '';
    private $headers = [];
    private $message_body = '';

    /**
     * Constructor
     */
    function __construct($request_method, $request_url, $request_http_version, 
        $request_headers, $request_message_body) {

        $this->method = $request_method;
        $this->url = $request_url;
        $this->http_version = $request_http_version;
        $this->headers = $request_headers;
        $this->message_body = $request_message_body;
    }

    /**
     * Gets request method
     *
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Gets request URL
     *
     * @return string
     */    
    public function getUrl() {
        return $this->url;
    }

    /**
     * Gets HTTP version of request
     *
     * @return string
     */
    public function getHttpVersion() {
        return $this->http_version;
    }

    /**
     * Gets the full array of headers
     *
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Gets a single header
     *
     * @param string $header Name of the header
     * @return string Value of the header
     */
    public function getHeader($header) {
        if (array_key_exists($header, $this->headers)) {
            return $this->headers[$header];
        } else {
            return null;
        }
    }

    /**
     * Gets message body of the request
     *
     * @return string
     */
    public function getMessageBody() {
        return $this->message_body;
    }

}

/**
 * A custom RequestException class for the Request class
 *
 * @package Request
 */
class RequestException extends Exception {

}

/**
 * A static class that takes care of handling HTTP requests
 *
 * @package Request
 */
class HttpRequestHandler {

     /**
     * Creates a Request object from an HTTP request string
     * Does some simple validation and throws an exception if finds invalid text
     *
     * note: the first line should actually be 
     * $request_headers = preg_split("/\r\n/", $request_string);
     * according to the RFC but it doesn't work because our
     * sample strings have unix newlines (\n)
     * 
     * @param string $request_string The raw HTTP request string
     * @return Request A new Request object
     */
    function handleRequest($request_string) {

        $request_headers = explode(PHP_EOL, $request_string);
        $request_line = array_shift($request_headers);
        $request_line_array = preg_split("/\s+/", $request_line);

        if (count($request_line_array) != 3) {
            throw new RequestException("Invalid request line: $request_line \n");
        }

        $request_method = $request_line_array[0];
        $request_url = $request_line_array[1];
        $request_http_version = $request_line_array[2];

        $headers = [];
        $request_message_body = '';

        while (count($request_headers) > 0) {
            $header = array_shift($request_headers);
            // in RFC, blank line precedes body, and newline has been stripped
            // so we should be left with an empty string before the body
            if ($header === "") {
                // check if body after
                if (count($request_headers) > 0) {
                    $request_message_body = implode("\n", $request_headers);
                }
                break;
            }

            $header_array = explode(":", $header, 2);
            if (count($header_array) != 2) {
                throw new RequestException("Invalid header: $header \n");
            }
            $header_name = trim($header_array[0]);
            $header_value = trim($header_array[1]);

            $headers[$header_name] = $header_value;
        }

        return new Request($request_method, $request_url, $request_http_version, 
            $headers, $request_message_body);
    }
}


?>