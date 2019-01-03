<?php
namespace OCA\SecSignID\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\Mapper;
use OCA\SecSignID\Db\User;

class IDMapper extends Mapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'secsignid', '\OCA\SecSignID\Db\User');
    }

    public function find($userId) {
        $sql = 'SELECT * FROM *PREFIX*secsignid WHERE user_id = ?';
        return $this->findEntity($sql, [$userId]);
    }

    public function findByName($secsignid) {
        $sql = 'SELECT * FROM *PREFIX*secsignid WHERE secsignid = ?';
        return $this->findEntity($sql, [$secsignid]);
    }

    public function findEnabled() {
        $sql = 'SELECT * FROM *PREFIX*secsignid WHERE enabled = true';
        return $this->findEntities($sql);
    }

    public function findDisabled() {
        $sql = 'SELECT * FROM *PREFIX*secsignid WHERE enabled = false';
        return $this->findEntities($sql);
    }

    public function findAll() {
        $sql = 'SELECT * FROM *PREFIX*users';
        return $this->findEntities($sql);
    }

    /*public function findAll($userId) {
        $sql = 'SELECT * FROM *PREFIX*secsignid WHERE user_id = ?';
        return $this->findEntities($sql, [$userId]);
    }*/

}