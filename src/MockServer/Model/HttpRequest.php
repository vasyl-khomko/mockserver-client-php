<?php
/**
 * Created by PhpStorm.
 * User: khomk
 * Date: 2/24/2019
 * Time: 2:59 PM
 */

namespace MockServer\Model;


class HttpRequest extends Not implements HttpObject
{
    /** @var NottableString */
    private $method /*= string("")*/
    ;
    /** @var NottableString */
    private $path /*= string("")*/
    ;
    /** @var Parameters */
    private $queryStringParameters /*= new Parameters()*/
    ;
    /** @var Body */
    private $body = null;
    /** @var Headers */
    private $headers /*= new Headers()*/
    ;
    /** @var Cookies */
    private $cookies /*= new Cookies()*/
    ;
    /** @var bool */
    private $keepAlive = null;
    /** @var bool */
    private $secure = null;

    public static function request(string $path = null)
    {
        $request = new HttpRequest();

        if (!is_null($path)) {
            $request->withPath(path);
        }

        return $request;
    }

    public function isKeepAlive(): bool
    {
        return $this->keepAlive;
    }

    /**
     * Match on whether the request was made using an HTTP persistent connection, also called HTTP keep-alive, or HTTP connection reuse
     *
     * @param $isKeepAlive true if the request was made with an HTTP persistent connection
     * @return HttpRequest
     */
    public function withKeepAlive(bool $isKeepAlive)
    {
        $this->keepAlive = $isKeepAlive;
        return $this;
    }

    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * Match on whether the request was made over SSL (i.e. HTTPS)
     *
     * @param $isSsl true if the request was made with SSL
     * @return HttpRequest
     */
    public function withSecure(bool $isSsl)
    {
        $this->secure = $isSsl;
        return $this;
    }

    /**
     * The HTTP method to match on such as "GET" or "POST"
     *
     * The HTTP method all method except a specific value using the "not" operator,
     * for example this allows operations such as not("GET")
     *
     * @param $method the HTTP method such as "GET" or "POST"
     * the HTTP method to not match on not("GET") or not("POST")
     * @return HttpRequest
     */
    public function withMethod(String $method): HttpRequest {
        if (is_string($method)) {
            $this->method = NottableString::string($method);
        } else {
            $this->method = $method;
        }
        return $this;
    }

    /**
     * @param String|null $defaultValue
     * @return NottableString|string
     */
    public function getMethod(String $defaultValue = null) {
        if (is_null($defaultValue)) {
            return $this->method;
        }
        if (empty($this->method->getValue())) {
            return $defaultValue;
        } else {
            return $this->method->getValue();
        }
    }

    /**
     * @param string|NottableString $path
     * @return $this
     */
    public function withPath($path)
    {
        if (is_string($path)) {
            $this->path = NottableString::string($path);
        } else {
            $this->path = $path;
        }

        return $this;
    }

    /**
     * @return NottableString
     */
    public function getPath(): NottableString
    {
        return $this->path;
    }

    /**
     * @param String $method
     * @param String ...$paths
     * @return bool
     */
    public function matches(String $method, String ...$paths): bool {
        $matches = false;
        foreach ($paths as $path) {
            $matches = $this->method->getValue()->equals($method) && $this->path->getValue()->equals($path);
            if ($matches) {
                break;
            }
        }
        return $matches;
    }

    public function getQueryStringParameters() {
        return $this->queryStringParameters;
    }

    public function withQueryStringParameters(Parameters $parameters) {
        $this->queryStringParameters = $parameters;
        return $this;
    }

    public function withQueryStringParameter(Parameter $parameter) {
        $this->queryStringParameters->withEntry($parameter);
        return $this;
    }

    public function getQueryStringParameterList() {
        return $this->queryStringParameters->getEntries();
    }

    /**
     * @param string|NottableString $name
     * @param string|NottableString $value
     * @return bool
     */
    public function hasQueryStringParameter($name, $value): bool {
        return $this->queryStringParameters->containsEntry($name, $value);
    }

    public function getFirstQueryStringParameter(String $name) {
        return $this->queryStringParameters->getFirstValue($name);
    }

    public function withBody($body, $charset = null) {
        if (is_string($body)) {
            $this->body = new StringBody($body, $charset);
        } else {
            $this->body = $body;
        }

        return $this;
    }

    //@JsonIgnore
    public function getBodyAsRawBytes() {
        // TODO return this.body != null ? this.body.getRawBytes() : new byte[0];
    }

    //@JsonIgnore
    public function getBodyAsString(): string {
        if ($this->body != null) {
            return $this->body->toString();
        } else {
            return null;
        }
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function withHeaders(Headers $headers) {
        $this->headers = $headers;
        return $this;
    }

    public function withHeader(Header $header) {
        $this->headers->withEntry($header);
        return $this;
    }

    public function replaceHeader(Header $header) {
        $this->headers->replaceEntry($header);
        return $this;
    }

    public function getHeaderList() {
    return $this->headers->getEntries();
    }

    public function getHeader(String $name) {
        return $this->headers->getValues($name);
    }

    public function getFirstHeader(String $name) {
        return $this->headers->getFirstValue($name);
    }

    public function containsHeader(String $name) {
        return $this->headers->containsEntry($name);
    }

    public function removeHeader(String $name) {
        $this->headers->remove(name);
        return $this;
    }

    public function getCookies() {
        return $this->cookies;
    }

    public function withCookies(Cookies $cookies) {
        $this->cookies = $cookies;
        return $this;
    }

    public function withCookie(Cookie $cookie) {
        $this->cookies->withEntry($cookie);
        return $this;
    }

    public function getCookieList() {
        return $this->cookies->getEntries();
    }

    public function socketAddressFromHostHeader() {
        if (!Strings::isNullOrEmpty($this->getFirstHeader(HOST::toString()))) {
            $isSsl = $this->isSecure() != null && $this->isSecure();
            $hostHeaderParts = $this->getFirstHeader(HOST::toString())::split(":");
            return new InetSocketAddress($hostHeaderParts[0], $hostHeaderParts->length > 1 ? Integer::parseInt($hostHeaderParts[1]) : $isSsl ? 443 : 80);
        } else {
            throw new IllegalArgumentException("Host header must be provided to determine remote socket address, the request does not include the \"Host\" header:" . NEW_LINE . $this);
        }
    }

    public function clone() {
        return not($this->request(), $this->not)
            ->withMethod($this->method)
            ->withPath($this->path)
            ->withQueryStringParameters($this->getQueryStringParameters()->clone())
            ->withBody($this->body)
            ->withHeaders($this->getHeaders()->clone())
            ->withCookies($this->getCookies()->clone())
            ->withKeepAlive($this->keepAlive)
            ->withSecure($this->secure);
    }

    public function update(HttpRequest $replaceRequest) {
        if (!Strings::isNullOrEmpty($replaceRequest->getMethod()->getValue())) {
            $this->withMethod($replaceRequest->getMethod());
        }
        if (!Strings::isNullOrEmpty($replaceRequest->getPath()->getValue())) {
            $this->withPath($replaceRequest->getPath());
        }
        foreach ($replaceRequest->getHeaderList() as $header) {
            $this->getHeaders()->replaceEntry($header);
        }
        foreach ($replaceRequest->getCookieList() as $cookie) {
            $this->withCookie($cookie);
        }
        foreach ($replaceRequest->getQueryStringParameterList() as $parameter) {
            $this->getQueryStringParameters()->replaceEntry($parameter);
        }
        if ($replaceRequest->getBody() != null) {
            $this->withBody($replaceRequest->getBody());
        }
        if ($replaceRequest->isSecure() != null) {
            $this->withSecure($replaceRequest->isSecure());
        }
        if ($replaceRequest->isKeepAlive() != null) {
            $this->withKeepAlive($replaceRequest->isKeepAlive());
        }
        return $this;
    }

}