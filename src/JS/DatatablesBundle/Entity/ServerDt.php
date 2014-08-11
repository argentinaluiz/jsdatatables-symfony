<?php

namespace JS\DatatablesBundle\Entity;

class ServerDt {

    /**
     * @var array
     */
    private $field;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var string
     */
    private $separator;

    /**
     * @return array
     */
    public function getField() {
        return $this->field;
    }

    /**
     * @return array
     */
    public function getFilters() {
        return $this->filters;
    }

    /**
     * @return string
     */
    public function getSeparator() {
        return $this->separator;
    }

    /**
     * @param array $field
     * @return ServerDt
     */
    public function setField(array $field) {
        $this->field = $field;
        return $this;
    }

    /**
     * @param array $filters
     * @return ServerDt
     */
    public function setFilters(array $filters) {
        $this->filters = $filters;
        return $this;
    }

    /**
     * @param string $separator
     * @return ServerDt
     */
    public function setSeparator($separator) {
        $this->separator = $separator;
        return $this;
    }

    /**
     * @param array $data
     * @return ServerDt
     */
    public function hydrate(array $data) {
        return $this->setField($data['field'])
                        ->setFilters(isset($data['filters']) ? $data['filters'] : [])
                        ->setSeparator(isset($data['separator']) ? $data['separator'] : null);
    }

}
