<?php

namespace JS\DatatablesBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use JS\DatatablesBundle\Entity\AjaxParams;
use JS\DatatablesBundle\Entity\ConfigDt;
use JS\DatatablesBundle\Doctrine\ORM\Query\Expr\Select;
use JS\DatatablesBundle\Doctrine\ORM\Query\Expr\OrderBy;

class JSDataTables {

    protected $dtArrayConfig = [];

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \JS\DataTablesBundle\Entity\ConfigDt
     */
    protected $dtConfig;

    /**
     * @var \JS\DataTablesBundle\Entity\AjaxParams
     */
    protected $ajaxData;

    /**
     * @var QueryBuilder or string
     */
    protected $query;

    /**
     * @var \JS\DataTablesBundle\Service\ListClause
     */
    protected $listClause;
    protected $validator;
    protected $params;

    private function prepareParams() {
        $this->getAjaxData()->hydrate($this->params);
        if (count($this->getValidator()->validate($this->getAjaxData())) == 0)
        {
            $this->getDtConfig()->hydrate($this->dtArrayConfig);
            $this->query = $this->getEntityManager()->createQueryBuilder();
            $this->createClauseWhere();
        } else
        {
            throw new \InvalidArgumentException("Não foi possível identificar os parametros da consulta");
        }
    }

    protected function generateQuery($query) {
        $queryPrepared = $query;
        if ($this->getDtConfig()->getTypeQuery() != 'doctrine')
        {
            $queryPrepared = $this->getEntityManager()->getConnection()->prepare($query->getDQL() . ' LIMIT :offset,:limit');
            $parameters = $query->getParameters()->toArray();
            foreach ($parameters as $param)
            {
                $queryPrepared->bindValue($param->getName(), $param->getValue(), $param->getType());
            }
            $queryPrepared->bindValue('offset', (int) $this->getAjaxData()->getStart(), \PDO::PARAM_INT);
            $queryPrepared->bindValue('limit', (int) $this->getAjaxData()->getLength(), \PDO::PARAM_INT);
        }
        return $queryPrepared;
    }

    public function getPaginator() {
        $this->prepareParams();
        return [
            "draw" => intval($this->getAjaxData()->getDraw()),
            "recordsTotal" => $this->getCountTotalData(),
            "recordsFiltered" => $this->getCountTotalFiltered(),
            "data" => $this->getData()
        ];
    }

    protected function select() {
        $this->query->addSelect(Select::getSelect($this->getDtConfig(), $this->getAjaxData()));
    }

    protected function from($query) {
        if ($this->getDtConfig()->getTypeQuery() == 'doctrine')
        {
            $query->from($this->getDtConfig()->getClassName(), $this->getDtConfig()->getAliasDefault());
        } else
        {
            $table = $this->getEntityManager()->getClassMetadata($this->getDtConfig()->getClassName())->getTableName();
            $query->from($table, $this->getDtConfig()->getAliasDefault());
        }
    }

    protected function join($query) {

    }

    protected function createClauseWhere() {
        $this->listClause->createList($this->getDtConfig(), $this->getAjaxData());
    }

    protected function where($query) {
        return $this->listClause->generateWhere($query);
    }

    protected function order() {
        $order = (new OrderBy())->generateOrderBy($this->getDtConfig(), $this->getAjaxData());
        if (count($order->getParts()) > 0)
        {
            $this->query->addOrderBy($order);
        }
    }

    protected function limit() {
        $this->query
                ->setFirstResult($this->getAjaxData()->getStart())
                ->setMaxResults($this->getAjaxData()->getLength());
    }

    protected function execute($query) {
        if ($query instanceof QueryBuilder)
        {
            return $query->getQuery()->getArrayResult();
        } else
        {
            $query->execute();
            return $query->fetchAll();
        }
    }

    public function getData() {
        $this->select();
        $this->from($this->query);
        $this->join($this->query);
        $this->where($this->query);
        $this->order();
        $this->limit();
        return $this->execute($this->generateQuery($this->query));
    }

    protected function getCountTotalData() {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->addSelect($query->expr()->count($this->getDtConfig()->getPrimaryKey()['alias'] .
                        '.' . $this->getDtConfig()->getPrimaryKey()['name']));
        $this->from($query);
        $this->join($query);
        if ($this->getDtConfig()->getTypeQuery() != 'doctrine')
        {
            $query = $this->getEntityManager()->getConnection()->prepare($query->getDQL());
        }
        $countTotal = $this->execute($query);
        return intval($countTotal[0][key($countTotal[0])]);
    }

    protected function getCountTotalFiltered() {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->addSelect($query->expr()->count($this->getDtConfig()->getPrimaryKey()['alias'] .
                        '.' . $this->getDtConfig()->getPrimaryKey()['name']));
        $this->from($query);
        $this->join($query);
        $this->where($query);
        if ($this->getDtConfig()->getTypeQuery() != 'doctrine')
        {
            $statement = $this->getEntityManager()->getConnection()->prepare($query->getDQL());
            $parameters = $query->getParameters()->toArray();
            foreach ($parameters as $param)
            {
                $statement->bindValue($param->getName(), $param->getValue(), $param->getType());
            }
            $query = $statement;
        }
        $countFiltered = $this->execute($query);
        return intval($countFiltered[0][key($countFiltered[0])]);
    }

    public function getDtConfig() {
        if (!$this->dtConfig)
        {
            $this->dtConfig = new ConfigDt ();
        }
        return $this->dtConfig;
    }

    public function getDtArrayConfig() {
        return $this->dtArrayConfig;
    }

    public function setDtArrayConfig($dtArrayConfig) {
        $this->dtArrayConfig = $dtArrayConfig;
        return $this;
    }

    public function getAjaxData() {
        if (!$this->ajaxData)
        {
            $this->ajaxData = new AjaxParams();
        }
        return $this->ajaxData;
    }

    public function getListClause() {
        return $this->listClause;
    }

    public function setListClause($listClause) {
        $this->listClause = $listClause;
        return $this;
    }

    public function setValidator($validator) {
        $this->validator = $validator;
        return $this;
    }

    public function getValidator() {
        return $this->validator;
    }

    public function getEntityManager() {
        return $this->entityManager;
    }

    public function setEntityManager(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
        return $this;
    }

    public function getParams() {
        return $this->params;
    }

    public function setParams($params) {
        $this->params = $params;
        return $this;
    }

}
