<?php

namespace JS\DatatablesBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class AjaxParams {

    /**
     * @var int
     */
    private $draw;

    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $length;

    /**
     * @var SearchParams
     */
    private $search;

    /**
     * @var \ArrayObject
     */
    private $columns;

    /**
     * @var \ArrayObject
     */
    private $order;

    /**
     * @return int
     */
    public function getDraw() {
        return $this->draw;
    }

    /**
     * @return int
     */
    public function getStart() {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getLength() {
        return $this->length;
    }

    /**
     * @return SearchParams
     */
    public function getSearch() {
        return $this->search;
    }

    /**
     * @return \ArrayObject
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * @return \ArrayObject
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * @param int $draw
     * @return AjaxParams
     */
    public function setDraw($draw) {
        $this->draw = $draw;
        return $this;
    }

    /**
     * @param int $start
     * @return AjaxParams
     */
    public function setStart($start) {
        $this->start = $start;
        return $this;
    }

    /**
     * @param int $length
     * @return AjaxParams
     */
    public function setLength($length) {
        $this->length = $length;
        return $this;
    }

    /**
     * @param SearchParams $search
     * @return AjaxParams
     */
    public function setSearch(SearchParams $search) {
        $this->search = $search;
        return $this;
    }

    /**
     * @param \ArrayObject $columns
     * @return AjaxParams
     */
    public function setColumns(\ArrayObject $columns) {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @param \ArrayObject $order
     * @return AjaxParams
     */
    public function setOrder(\ArrayObject $order = null) {
        $this->order = $order;
        return $this;
    }

    /**
     * @param array $data
     * @return AjaxParams
     */
    public function hydrate(array $data) {
        $columns = new \ArrayObject();
        if (isset($data['columns']))
        {
            foreach ($data['columns'] as $column)
            {
                $columns->append((new ColumnParams())->hydrate($column));
            }
        }

        $order = new \ArrayObject();
        if (isset($data['order']))
        {
            foreach ($data['order'] as $dtOrder)
            {
                $order->append((new OrderParams())->hydrate($dtOrder));
            }
        }

        return $this->setDraw(isset($data['draw']) ? (int) $data['draw'] : null)
                        ->setStart(isset($data['start']) ? (int) $data['start'] : null)
                        ->setLength(isset($data['length']) ? (int) $data['length'] : null)
                        ->setSearch((new SearchParams())->hydrate(isset($data['search']) ? $data['search'] : []))
                        ->setColumns($columns)
                        ->setOrder($order);
    }

    static public function loadValidatorMetadata(ClassMetadata $metadata) {
        $metadata->addPropertyConstraint('draw', new Assert\NotBlank());
        $metadata->addPropertyConstraint('draw', new Assert\Type(['type' => 'integer']));

        $metadata->addPropertyConstraint('start', new Assert\NotBlank());
        $metadata->addPropertyConstraint('start', new Assert\Type(['type' => 'integer']));

        $metadata->addPropertyConstraint('length', new Assert\NotBlank());
        $metadata->addPropertyConstraint('length', new Assert\Type(['type' => 'integer']));

        $metadata->addPropertyConstraint('search', new Assert\Valid());

        $metadata->addPropertyConstraint('columns', new Assert\Count(['min' => 1]));
        $metadata->addPropertyConstraint('columns', new Assert\Valid());

        $metadata->addPropertyConstraint('order', new Assert\Valid());
    }

}
