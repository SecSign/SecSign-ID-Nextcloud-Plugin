<?php
namespace OCA\SecSignID\Db;

use OCP\IDbConnection;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\AppFramework\Db\QBMapper;
use OCA\SecSignID\Db\User;

class UserMapper extends QBMapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'secsignid', User::class);
    }

    public function findAll() {
        $qb = $this->db->getQueryBuilder();
        $qb ->select('*')
            ->from('users');
        return $this->findEntities($qb);
        //$sql = 'SELECT * FROM *PREFIX*users';
        //return $this->findEntities($sql);
    }

}