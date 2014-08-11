<?php

namespace JS\DatatablesBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use JS\DatatablesBundle\Entity\ConfigDt;
use JS\DatatablesBundle\Entity\AjaxParams;
use JS\DatatablesBundle\Entity\ColumnParams;
use JS\DatatablesBundle\Service\FieldFilterManager;

class ListClause {

    /**
     * @var array
     */
    private $clauses = [];

    /**
     * @var \JSDataTables\Service\FieldFilterManager
     */
    private $filterManager;

    public function __construct(FieldFilterManager $filterManager) {
        $this->setFilterManager($filterManager);
    }

    private function getValueFiltered($filters, $value) {
        return $this->getFilterManager()->filter($filters, $value);
    }

    private function createClause($alias, $name, $filters, $value, $searchValue, $separator) {
        if (!$separator) {
            $value = '%' . $this->getValueFiltered($filters, $value) . '%';
            $clause = new Expr\Orx();
            $clause->add(new Expr\Comparison("$alias.$name", 'LIKE', ":$name"));
            $clause->add(new Expr\Andx([
                new Expr\Comparison("$alias.$name", 'IS', "NULL"),
                new Expr\Comparison(":$name", '=', "'%%'")
            ]));
            $this->addClause($name, 'indv', $clause, new Parameter($name, $value));
        }
        $value = '%' . $this->getValueFiltered($filters, $searchValue) . '%';
        $clause = new Expr\Orx();
        $clause->add(new Expr\Comparison("$alias.$name", 'LIKE', ":search_$name"));
        $clause->add(new Expr\Andx([
            new Expr\Comparison("$alias.$name", 'IS', "NULL"),
            new Expr\Comparison(":search_$name", '=', "'%%'")
        ]));
        $this->addClause($name, 'search', $clause, new Parameter("search_$name", $value));
    }

    /**
     * @param \JSDataTables\Entity\ConfigDt $dtConfig
     * @param \JSDataTables\Entity\AjaxParams $ajaxParams
     */
    public function createList(ConfigDt $dtConfig, AjaxParams $ajaxParams) {
        foreach ($ajaxParams->getColumns() as $column) {
            $server = $this->findColumnSearch($dtConfig, $column);
            if ($server) {
                $this->createClause(
                        $server->getField()['alias']
                        , $server->getField()['name']
                        , $server->getFilters()
                        , $column->getSearch()->getValue()
                        , $ajaxParams->getSearch()->getValue()
                        , $server->getSeparator()
                );
            }
        }
    }

    private function findColumnSearch(ConfigDt $dtConfig, ColumnParams $column) {
        $dtColumn = $dtConfig->getColumn($column->getData());
        if ($dtColumn && $column->isSearchable()) {
            return $dtColumn->getServer();
        }
        return false;
    }

    private function createParameters($p, $parameters) {
        if ($p instanceof ArrayCollection) {
            $params = $p->toArray();
            foreach ($params as $param) {
                $parameters->add($param);
            }
        } else {
            $parameters->add($p);
        }
        return $parameters;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $query
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function generateWhere(QueryBuilder $query) {
        $orxSearch = new Expr\Orx();
        $andxIndv = new Expr\Andx();
        $parameters = new ArrayCollection();
        foreach ($this->getClauses() as $key => $value) {
            if (isset($value['indv'])) {
                $andxIndv->add($value['indv']['clause']);
                $this->createParameters($value['indv']['params'], $parameters);
            }
            if (isset($value['search'])) {
                $orxSearch->add($value['search']['clause']);
                $this->createParameters($value['search']['params'], $parameters);
            }
        }
        $andxIndv->add($orxSearch);

        $query->where($andxIndv);
        $p = $parameters->toArray();
        foreach ($p as $param) {
            $query->setParameter($param->getName(), $param->getValue(), $param->getType());
        }

        return $query;
    }

    /**
     * @param string $name
     * @param string $typeFind indv|search
     * @param mixed $clause Expr..|string
     * @param array $params
     * @return ListClause
     */
    public function addClause($name, $typeFind, $clause, $params) {
        $this->clauses[$name][$typeFind] = [
            'clause' => $clause,
            'params' => $params
        ];
        return $this;
    }

    /**
     * @param string $name
     * @return ListClause
     */
    public function removeClause($name) {
        if (isset($this->clauses[$name])) {
            unset($this->clauses[$name]);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getClauses() {
        return $this->clauses;
    }

    /**
     * @return FieldFilterManager
     */
    public function getFilterManager() {
        return $this->filterManager;
    }

    /**
     * @param FieldFilterManager $filterManager
     * @return ListClause
     */
    public function setFilterManager(FieldFilterManager $filterManager) {
        $this->filterManager = $filterManager;
        return $this;
    }

}
