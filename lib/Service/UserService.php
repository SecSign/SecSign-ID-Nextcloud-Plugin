<?php
namespace OCA\SecSignID\Service;

use \OCP\IConfig;


class UserService {

    private $config;
    private $appName;

    public function __construct(IConfig $config, $appName){
        $this->config = $config;
        $this->appName = $appName;
    }

    public function getUserValue($key, $userId, $default = '') {
        return $this->config->getUserValue($userId, $this->appName, $key, $default);
    }

    public function setUserValue($key, $userId, $value) {
        $this->config->setUserValue($userId, $this->appName, $key, $value);
    }

}