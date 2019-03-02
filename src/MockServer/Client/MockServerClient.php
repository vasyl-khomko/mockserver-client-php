<?php

namespace MockServer\Client;


use Exception;
use GuzzleHttp\Client;
use RuntimeException;

class MockServerClient
{
    protected /*MockServerLogger*/ $mockServerLogger /*= new MockServerLogger(this.getClass())*/;
    private /*EventLoopGroup*/ $eventLoopGroup /*= new NioEventLoopGroup()*/;
    private /*Semaphore*/ $availableWebSocketCallbackRegistrations /*= new Semaphore(1)*/;
    private /*String*/ $host;
    private /*String*/ $contextPath;
    private /*Class<MockServerClient>*/ $clientClass;
    protected /*Future<Integer>*/ $portFuture;
    private /*Boolean*/ $secure;
    private /*Integer*/ $port;
    /**
     * @var Client
     */
    private /*NettyHttpClient*/ $httpClient /*= new NettyHttpClient(eventLoopGroup, null)*/;
    private /*HttpRequestSerializer*/ $httpRequestSerializer /*= new HttpRequestSerializer(mockServerLogger)*/;
    private /*PortBindingSerializer*/ $portBindingSerializer /*= new PortBindingSerializer(mockServerLogger)*/;
    private /*ExpectationSerializer*/ $expectationSerializer /*= new ExpectationSerializer(mockServerLogger)*/;
    private /*VerificationSerializer*/ $verificationSerializer /*= new VerificationSerializer(mockServerLogger)*/;
    private /*VerificationSequenceSerializer*/ $verificationSequenceSerializer /*= new VerificationSequenceSerializer(mockServerLogger)*/;

    public function __construct(string $host, int $port, string $contextPath)
    {
    }

    public function getEventLoopGroup()
    {
        return $this->eventLoopGroup;
    }

    public function isSecure()
    {
        return $this->secure != null ? $this->secure : false;
    }

    public function withSecure(bool $secure)
    {
        $this->secure = $secure;
    }

    private function port(): int
    {
        if ($this->port == null) {
            try {
                $this->port = $this->portFuture->get();
            } catch (Exception $ex) {
                throw new RuntimeException($ex);
            }
        }
        return $this->port;
    }

    public function remoteAddress(): InetSocketAddress
    {
        return new InetSocketAddress($this->host, $this->port());
    }

    public function contextPath(): string
    {
        return $this->contextPath;
    }

    private function calculatePath(string $path): string {
        // TODO rewrite
        $cleanedPath = "/mockserver/" . $path;
        if (empty($this->contextPath)) {
            $cleanedPath =
                (!strpos($this->contextPath, "/") === 0 ? "/" : "") .
                $this->contextPath .
                (!strpos($this->contextPath, "/") === strlen($this->contextPath) ? "/" : "") .
                ($cleanedPath.startsWith("/") ? $cleanedPath.substring(1) : $cleanedPath);
        }
        return (!$cleanedPath.startsWith("/") ? "/" : "") . $cleanedPath;
    }

    private function sendRequest(HttpRequest $request): HttpResponse {
        try {
            if ($this->secure != null) {
                $request->withSecure($this->secure);
            }

            $response = $this->httpClient->sendRequest(
                    $request->withHeader(HOST::toString(), $this->host + ":" + $this->port()),
                    ConfigurationProperties::maxSocketTimeout(),
                    TimeUnit::MILLISECONDS
                );

                    if (response != null) {
                        if (response.getStatusCode() != null &&
                            response.getStatusCode() == BAD_REQUEST.code()) {
                            throw new IllegalArgumentException(response.getBodyAsString());
                        }
                        String serverVersion = response.getFirstHeader("version");
                        String clientVersion = Version.getVersion();
                        if (!Strings.isNullOrEmpty(serverVersion) &&
                            !Strings.isNullOrEmpty(clientVersion) &&
                            !clientVersion.equals(serverVersion)) {
                            throw new ClientException("Client version \"" + clientVersion + "\" does not match server version \"" + serverVersion + "\"");
                        }
                    }

                    return response;
                } catch (RuntimeException rex) {
            if (!Strings.isNullOrEmpty(rex.getMessage()) && (rex.getMessage().contains("executor not accepting a task") || rex.getMessage().contains("loop shut down"))) {
                throw new IllegalStateException(this.getClass().getSimpleName() + " has already been closed, please create new " + this.getClass().getSimpleName() + " instance");
            } else {
                throw rex;
            }
        }
    }

    /**
     * Returns whether server MockServer is running, by polling the MockServer a configurable amount of times
     */
    public function isRunning(int $attempts = 10, int $timeout = 500, TimeUnit $timeUnit = TimeUnit::MILLISECONDS): bool
    {
        try {
            $httpResponse = $this->sendRequest($this->request()->withMethod("PUT")->withPath($this->calculatePath("status")));
            if ($httpResponse->getStatusCode() == HttpStatusCode::OK_200()::code()) {
                return true;
            } else if ($attempts == 0) {
                return false;
            } else {
                try {
                    $timeUnit->sleep($timeout);
                } catch (InterruptedException $e) {
                    // ignore interrupted exception
                }
                return $this->isRunning($attempts - 1, $timeout, $timeUnit);
            }
        } catch (SocketConnectionException $sce) {
            return false;
        }
    }

    /**
     * Bind new ports to listen on
     */
    public function bind(...$ports): array {
        $boundPorts = $this->sendRequest(
            $this->request()
                ->withMethod("PUT")
                ->withPath($this->calculatePath("bind"))
                ->withBody($this->portBindingSerializer->serialize($this->portBinding($ports)), StandardCharsets::UTF_8)
        )->getBodyAsString();

        return $this->portBindingSerializer->deserialize(boundPorts)->getPorts();
    }

    /**
     * Stop MockServer gracefully (only support for Netty version, not supported for WAR version)
     */
    public function stop(bool $ignoreFailure): MockServerClient
    {
        MockServerEventBus::getInstance()->publish(EventType::STOP);
        try {
            $this->sendRequest($this->request()->withMethod("PUT")->withPath($this->calculatePath("stop")));
        if ($this->isRunning()) {
            for ($i = 0; $this->isRunning() && $i < 50; $i++) {
                TimeUnit.MILLISECONDS.sleep(5);
            }
        }
        } catch (RejectedExecutionException $ree) {
            $this->mockServerLogger->trace("Request rejected because closing down but logging at trace level for information just in case due to some other actual error " . $ree);
        } catch (Exception $e) {
        if (!$this->ignoreFailure) {
            $this->mockServerLogger->warn("Failed to send stop request to MockServer " . $e->getMessage());
        }
    }
        if (!eventLoopGroup.isShuttingDown()) {
            eventLoopGroup.shutdownGracefully();
        }
        return clientClass.cast(this);
    }

}