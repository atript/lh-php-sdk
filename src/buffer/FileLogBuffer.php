<?php
namespace LogHero\Client;
require_once __DIR__ . '/LogBuffer.php';


class FileLogBuffer implements LogBufferInterface {
    private $fileLocation;
    private $lastDumpTimestampFileLocation;
    private $maxBufferFileSizeInBytes;
    private $maxDumpTimeIntervalSeconds;

    public function __construct($bufferFileName, $maxBufferFileSizeInBytes=15000, $maxDumpTimeIntervalSeconds=5*60) {
        $this->fileLocation = $bufferFileName;
        $this->maxDumpTimeIntervalSeconds = $maxDumpTimeIntervalSeconds;
        $this->lastDumpTimestampFileLocation = str_replace('.txt', '', $bufferFileName) . '.last-dump.timestamp';
        $this->maxBufferFileSizeInBytes = $maxBufferFileSizeInBytes;
    }

    public function push($logEvent) {
        file_put_contents($this->fileLocation, serialize($logEvent)."\n", FILE_APPEND | LOCK_EX);
        chmod($this->fileLocation, 0666);
    }

    public function needsDumping() {
        if ($this->sizeInBytes() >= $this->maxBufferFileSizeInBytes) {
            return true;
        }
        if (file_exists($this->lastDumpTimestampFileLocation)) {
            $lastDumpTimestamp = filemtime ($this->lastDumpTimestampFileLocation);
            $currentTimestamp = microtime(true);
            $nextDumpTimestamp = $lastDumpTimestamp + $this->maxDumpTimeIntervalSeconds;
            return $currentTimestamp >= $nextDumpTimestamp;
        }
        return false;
    }

    public function dump() {
        $logEvents = array();
        if (!file_exists($this->fileLocation)) {
            return $logEvents;
        }
        $fp = fopen($this->fileLocation, 'r+');
        if (flock($fp, LOCK_EX)) {
            file_put_contents ($this->lastDumpTimestampFileLocation, 'loghero.io');
            chmod($this->lastDumpTimestampFileLocation, 0666);
            while (($logEventLine = fgets($fp)) !== false) {
                $logEvents[] = unserialize($logEventLine);
            }
            ftruncate($fp, 0);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return $logEvents;
    }

    private function sizeInBytes() {
        if(!file_exists($this->fileLocation)) {
            return 0;
        }
        return filesize($this->fileLocation);
    }
}
