<?php

namespace Auth\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class UsersTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $select = new Select();
        $select->from(array('U' => 'users'));
        $select->columns(array('id', 'username', 'firstname', 'lastname', 'rolelevel'));
        $select->join(array('UR' => 'userroles'), 'UR.UserRoleIdx = U.rolelevel', array('rolename'));
        $select->where('deleted = 0');
        $select->order('rolelevel ASC, U.lastname ASC');
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getUsers($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Error: No record found.");
        }
        return $row;
    }

    public function getUsersByUsername($un) {
        $un = (string) $un;

        $select = new Select();
        $select->from(array('U' => 'users'));
        $select->columns(array('id', 'username', 'firstname', 'lastname', 'rolelevel'));
        $select->join(array('UR' => 'userroles'), 'UR.UserRoleIdx = U.rolelevel', array('rolename'));
        $select->where(array('deleted' => 0, 'username' => $un));

        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();

        if (!$row) {
            throw new \Exception("Could not find user?");
        }
        return $row;
    }

    public function saveUsers(Users $user) {

        $data = array(
            'username' => filter_var($user->username, 'FILTER_SANITIZE_EMAIL'),
            'password' => md5($user->password),
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'rolelevel' => $user->rolelevel,
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
