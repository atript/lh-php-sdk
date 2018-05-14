<?php
namespace LogHero\Client;

class InvalidLogEventException extends \Exception {

}


class LogEvent {
    protected $landingPagePath;
    protected $method;
    protected $statusCode;
    protected $timestamp;
    protected $pageLoadTimeMilliSec;
    protected $userAgent;
    protected $ipAddress;
    protected $hostname;
    protected $referer = null;

    function setHostname($hostname) {
        $this->hostname = $hostname;
        return $this;
    }

    function setLandingPagePath($landingPagePath) {
        $this->landingPagePath = $landingPagePath;
        return $this;
    }

    function setMethod($method) {
        $this->method = $method;
        return $this;
    }

    function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
        return $this;
    }

    function setUserAgent($userAgent) {
        $this->userAgent = $userAgent;
        return $this;
    }

    function setReferer($referer) {
        $this->referer = $referer;
        return $this;
    }

    function setIpAddress($ipAddress) {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
        return $this;
    }

    function getTimestamp() {
        return $this->timestamp;
    }

    function setPageLoadTimeMilliSec($pageLoadTimeMilliSec) {
        $this->pageLoadTimeMilliSec = $pageLoadTimeMilliSec;
        return $this;
    }

    private static function buildCidFromIPAndUserAgent($ipAddress, $userAgent) {
        return hash('md5', $ipAddress . $userAgent);
    }

    public function columns() {
        return array(
            'cid',
            'hostname',
            'landingPage',
            'method',
            'statusCode',
            'timestamp',
            'pageLoadTime',
            'ip',
            'ua',
            'referer'
        );
    }

    public function row() {
        $this->verify();
        return array(
            LogEvent::buildCidFromIPAndUserAgent($this->ipAddress, $this->userAgent),
            $this->hostname,
            $this->landingPagePath,
            $this->method,
            $this->statusCode,
            $this->timestamp->format(\DateTime::ATOM),
            $this->pageLoadTimeMilliSec,
            hash('md5', $this->ipAddress),
            $this->userAgent,
            $this->getExternalReferer()
        );
    }

    private function verify() {
        $this->ensureSet($this->landingPagePath, 'Landing page path');
        $this->ensureSet($this->userAgent, 'User agent');
        $this->ensureSet($this->ipAddress, 'Ip address');
        $this->ensureSet($this->hostname, 'Hostname');
        $this->ensureSet($this->timestamp, 'Timestamp');
    }

    private static function ensureSet($property, $propertyName) {
        if (!$property) {
            throw new InvalidLogEventException('Log event is incomplete: ' . $propertyName . ' is null');
        }
    }

    private function getExternalReferer() {
        if ($this->referer == 'direct') {
            return null;
        }
        return $this->referer;
    }

}
