<?php

namespace Auth\Model;

use Zend\Db\TableGateway\TableGateway;

class UsersTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select(array('deleted' => '0'));
        return $resultSet;
    }

    public function getUsers($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getUsersByUsername($un) {
        $un = (string) $un;
        $rowset = $this->tableGateway->select(array('username' => $un));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find use $un");
        }
        return $row;
    }

    public function saveUsers(Users $user) {

        $data = array(
            'username' => $user->username,
            'password' => md5($user->password),
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'role_level' => $user->role_level,
        );

        $id = (int) $user->id;
        if ($id == 0) {

            // Check if email already exists?
            $rowset = $this->tableGateway->select(array('username' => $user->username, 'deleted' => '0'));
            if (!empty($rowset->current())) {
                return $saveResult = array(
                    'error' => true,
                    'message' => 'Email [' . $user->username . '] already exists'
                );
            } else {
                $this->tableGateway->insert($data);
            }
        } else {

            if ($this->getUsers($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Users id does not exist');
            }
        }
    }

    public function deleteUsers($id) {
        #$this->tableGateway->delete(array('id' => (int) $id));
        $data = array(
            'deleted' => '1',
            'activity_date' => date("Y-m-d H:i:s")
        );
        $this->tableGateway->update($data, array('id' => $id));
    }

}
