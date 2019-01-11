<?php
namespace OCA\SecSignID\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\Mapper;
use OCA\SecSignID\Db\User;

class UserMapper extends Mapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'secsignid', '\OCA\SecSignID\Db\User');
    }

    public function findAll() {
        $sql = 'SELECT * FROM *PREFIX*users ';
        return $this->findEntities($sql);
    }

}