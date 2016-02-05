<?php

namespace AnnieHaak\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class Auditing implements InputFilterAwareInterface {

    public $UserName;
    public $Action;
    public $TableName;
    public $OldDataJSON;
    protected $inputFilter;
    protected $adapter;
    protected $sql;

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->sql = <<<SQL
        CALL AuditAction
        (
            :UserName
            ,:Action
            ,:TableName
            ,:OldDataJSON
        )
SQL;
    }

    public function exchangeArray($data) {
        $this->UserName = (!empty($data['UserName'])) ? $data['UserName'] : '';
        $this->Action = (!empty($data['Action'])) ? $data['Action'] : '';
        $this->TableName = (!empty($data['TableName'])) ? $data['TableName'] : '';
        $this->OldDataJSON = (!empty($data['OldDataJSON'])) ? $data['OldDataJSON'] : '';
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function saveAuditAction(Auditing $Audit) {
        $data = array(
            "UserName" => $Audit->UserName,
            "Action" => $Audit->Action,
            "TableName" => $Audit->TableName,
            "OldDataJSON" => $Audit->OldDataJSON
        );
        $DBH = $this->adapter;
        $STH = $DBH->createStatement();
        $STH->prepare($this->sql);
        return $STH->execute($data);
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(array(
                'name' => 'UserName',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 255,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'Action',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 10,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'TableName',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 255,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'OldDataJSON',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8'
                        ),
                    ),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}
