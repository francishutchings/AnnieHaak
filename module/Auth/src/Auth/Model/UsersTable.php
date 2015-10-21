<?php

namespace Auth\Model;

 use Zend\Db\TableGateway\TableGateway;

 class UsersTable
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll()
     {
         $resultSet = $this->tableGateway->select(array('deleted' => '0'));
         return $resultSet;
     }

     public function getUsers($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function saveUsers(Users $user)
     {
         $data = array(
             'username' => $user->username,
             'password' => $user->password,
             'firtsname' => $user->firtsname,
             'lastname' => $user->lastname,
         );

         $id = (int) $user->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getUsers($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Users id does not exist');
             }
         }
     }

     public function deleteUsers($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 }