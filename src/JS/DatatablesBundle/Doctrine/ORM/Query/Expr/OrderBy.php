<?php

namespace JS\DatatablesBundle\Doctrine\ORM\Query\Expr;

use Doctrine\ORM\Query\Expr\OrderBy as DoctrineOrderBy;
use JS\DatatablesBundle\Entity\AjaxParams;
use JS\DataTablesBundle\Entity\ConfigDt;

class OrderBy extends DoctrineOrderBy {

    private static $point = '.';
    private static $asc = 'ASC';
    private static $desc = 'DESC';

    /**
     * @return \Doctrine\ORM\Query\Expr\OrderBy
     */
    public function generateOrderBy(ConfigDt $dtConfig, AjaxParams $ajaxParams) {
        foreach ($ajaxParams->getOrder() as $order) {
            $index = $order->getColumn();
            $column = isset($ajaxParams->getColumns()[$index]) ? $ajaxParams->getColumns()[$index] : null;
            $dtColumn = $dtConfig->getColumn($column->getData());
            if ($column && $column->isOrderable() && $dtColumn) {
                $name = $dtColumn->getServer()->getField()['name'];
                $alias = $dtColumn->getServer()->getField()['alias'];
                $this->add($alias . self::$point . $name, $order->getDir());
            }
        }
        return $this;
    }

    /**
     * @param string $sort
     * @param mixed $order Description
     * @return \Doctrine\ORM\Query\Expr\OrderBy
     */
    public function add($sort, $order = 0) {
        if (is_int($order)) {
            $order = ($order == 0) ? self::$asc : self::$desc;
        } else {
            $order = (strtoupper($order) == self::$asc) ? self::$asc : self::$desc;
        }
        parent::add($sort, $order);
        return $this;
    }

}
