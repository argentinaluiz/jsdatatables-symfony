<?php

namespace JS\DatatablesBundle\Service;

use Zend\Filter\FilterChain;

class FieldFilterManager {

    /**
     * @var \Zend\Filter\FilterChain
     */
    private $filterChain;

    public function __construct(FilterChain $filterChain) {
        $this->setFilterChain($filterChain);
    }

    /**
     * @return \Zend\Filter\FilterChain
     */
    public function getFilterChain() {
        return $this->filterChain;
    }

    /**
     * @param \Zend\Filter\FilterChain $filterChain
     * @return FieldFilterManager
     */
    public function setFilterChain(FilterChain $filterChain) {
        $this->filterChain = $filterChain;
        return $this;
    }

    /**
     * @param array $filters
     * @param string $value
     * @return mixed
     */
    public function filter(array $filters, $value) {
        foreach ($filters as $v) {
            $this->getFilterChain()->attachByName($v['name'], isset($v['options']) ? $v['options'] : []);
        }
        return $this->getFilterChain()->filter($value);
    }

}
