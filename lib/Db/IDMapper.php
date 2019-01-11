<?php
namespace OCA\SecSignID\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;
use OCA\SecSignID\Db\ID;

class IDMapper extends QBMapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'secsignid', ID::class);
    }

    public function addUser($id){
        return $this->insert($id);
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
        $sql = 'SELECT * FROM *PREFIX*secsignid WHERE enabled = 1';
        return $this->findEntities($sql);
    }

    public function findDisabled() {
        $sql = 'SELECT * FROM *PREFIX*secsignid WHERE enabled = 0';
        return $this->findEntities($sql);
    }

    public function findID($userId) {
        $sql = 'SELECT * FROM *PREFIX*secsignid WHERE user_id = ?';
        return $this->findEntities($sql, [$userId]);
    }

    public function findAll() {
        $sql = 'SELECT * FROM *PREFIX*secsignid';
        return $this->findEntities($sql);
    }

}