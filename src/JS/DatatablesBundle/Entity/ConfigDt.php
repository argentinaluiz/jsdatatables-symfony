<?php

namespace JS\DatatablesBundle\Entity;

class ConfigDt {

    /**
     * @var array doctrine or nativequery
     */
    private $typeQuery;

    /**
     * @var array
     */
    private $primaryKey;

    /**
     * @var string
     */
    private $aliasDefault;

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $classDt;

    /**
     * @var \ArrayObject
     */
    private $columns;

    /**
     * @return string
     */
    public function getTypeQuery() {
        return $this->typeQuery;
    }

    /**
     * @param string $typeQuery
     * @return ConfigDt
     */
    public function setTypeQuery($typeQuery) {
        $this->typeQuery = $typeQuery;
        return $this;
    }

    /**
     * @return array
     */
    public function getPrimaryKey() {
        return $this->primaryKey;
    }

    /**
     * @param array $primaryKey
     * @return ConfigDt
     */
    public function setPrimaryKey(array $primaryKey) {
        $this->primaryKey = $primaryKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getAliasDefault() {
        return $this->aliasDefault;
    }

    /**
     * @return string
     */
    public function getClassName() {
        return $this->className;
    }

    /**
     * @param string $aliasDefault
     * @return ConfigDt
     */
    public function setAliasDefault($aliasDefault) {
        $this->aliasDefault = $aliasDefault;
        return $this;
    }

    /**
     * @param string $className
     * @return ConfigDt
     */
    public function setClassName($className) {
        $this->className = $className;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassDt() {
        return $this->classDt;
    }

    /**
     * @param string $classDt
     * @return ConfigDt
     */
    public function setClassDt($classDt) {
        $this->classDt = $classDt;
        return $this;
    }

    /**
     * @return \ArrayObject
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * @param \ArrayObject $columns
     * @return ConfigDt
     */
    public function setColumns(\ArrayObject $columns) {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @param string $data
     * @return mixed ColumnDt or false
     */
    public function getColumn($data) {
        foreach ($this->getColumns() as $column) {
            if ($column->getData() == $data) {
                return $column;
            }
        }
        return false;
    }

    /**
     * @param array $data
     * @return ConfigDt
     */
    public function hydrate(array $data) {
        $columns = new \ArrayObject;
        foreach ($data['columns'] as $column) {
            $columns->append((new ColumnDt())->hydrate($column));
        }

        return $this->setTypeQuery(isset($data['type_query']) ? $data['type_query'] : 'doctrine')
                        ->setPrimaryKey($data['primary_key'])
                        ->setColumns($columns)
                        ->setAliasDefault($data['alias_default'])
                        ->setClassName($data['class_name'])
                        ->setClassDt(isset($data['class_dt']) ? $data['class_dt'] : null);
    }

}
