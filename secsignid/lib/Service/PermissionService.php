<?php
namespace OCA\SecSignID\Service;

use \OCP\IConfig;


class PermissionService {

    private $config;
    private $appName;

    public function __construct(IConfig $config, $appName){
        $this->config = $config;
        $this->appName = $appName;
    }

    public function getAppValue($key) {
        return $this->config->getAppValue($this->appName, $key);
    }

    public function setAppValue($key, $value) {
        $this->config->setAppValue($this->appName, $key, $value);
    }
}