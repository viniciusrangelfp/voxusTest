<?php

namespace Voxus\Core\ORM;
use Voxus\Core\connection\DB;

abstract class Model {
    protected string $tableName;
    protected $db;

    public function __construct()
    {
        $this->id = 0;
        $this->db = DB::conn();
    }

    public function save(){
        $class = new \ReflectionClass($this);

        if ($this->id > 0) {
            $query = $this->updateQuery($class);
        }else{
            $query = $this->insertQuery($class);
        }

        try {
            $result = $this->db->exec($query);
            if($this->id < 1){
                $this->id = $this->db->lastInsertId();
            }
        }catch(\Exception $e){
            throw new \Exception($e);
        }

        return $result;
    }

    public function find(array $options = [],$extra=''){
        $result = [];
        $query = '';

        $where = ['clause'=>'','conditions'=>[]];

        if(!empty($options)){
            foreach($options as $key=>$value){
                $where['conditions'][] = '`'.$key.'` = "'.$value.'"';
            }
            $where['clause'] = " WHERE ".implode(' AND ',$where['conditions']);
        }

        $query = 'SELECT * FROM '.$this->tableName;
        if(!empty($where['clause'])){
            $query .= $where['clause'];
        }
        if(!empty($extra)){
            $query .= ' '.$extra;
        }

        $raw = $this->db->query($query);

        /*if ($this->db->errorCode()) {
            throw new \Exception($this->db->errorInfo()[2]);
        }*/

        foreach ($raw as $rawRow) {
            $result[] = $this->injectFields($rawRow);
        }

        return $result;
    }

    public function findById($id){
        $result = $this->find(['id'=>$id]);
        if(isset($result[0])){
            return $result[0];
        }
        return [];
    }

    public function injectFields(array $object){
        $class = new \ReflectionClass($this);

        $entity = $class->newInstance();


        foreach($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property){
            if (isset($object[$property->getName()])) {
                $property->setValue($entity,$object[$property->getName()]);
            }
        }

        return $entity;
    }

    private function insertQuery($class){
        $fields = [];
        $values = [];

        foreach($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property){
            $propertyName = $property->getName();
            if($propertyName == 'id'){
                continue;
            }

            $fields[] =$propertyName;
            $values[] = $this->solveField($this->{$propertyName});
        }

        $query = 'INSERT INTO '.$this->tableName;
        $query .= ' ('.implode(',',$fields).' )';
        $query .= ' VALUES ('.implode(',',$values).' )';
        return $query;
    }

    private function updateQuery($class){
        $props = [];

        foreach($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property){
            $propertyName = $property->getName();
            $props[] = '`'.$propertyName.'` = "'.$this->{$propertyName}.'"';
        }

        $setClause = implode(',',$props);

       return 'UPDATE `'.$this->tableName.'` SET '.$setClause.' WHERE id = '.$this->id;
    }

    private function solveField($fieldValue){
        $fieldType = gettype($fieldValue);
        switch($fieldType){
            CASE 'string':
                return "'".$fieldValue."'";
            break;
            default:
                return $fieldValue;
        }
    }
}
