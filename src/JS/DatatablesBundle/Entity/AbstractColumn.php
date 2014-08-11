<?php

namespace JS\DatatablesBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractColumn {

    /**
     * @var string
     */
    private $data;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $searchable;

    /**
     * @var bool
     */
    private $orderable;

    /**
     * @return string
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isSearchable() {
        return $this->searchable;
    }

    /**
     * @return bool
     */
    public function isOrderable() {
        return $this->orderable;
    }

    /**
     * @param string $data
     * @return AbstractColumn
     */
    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    /**
     * @param string $name
     * @return AbstractColumn
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @param bool $searchable
     * @return AbstractColumn
     */
    public function setSearchable($searchable) {
        $this->searchable = $searchable;
        return $this;
    }

    /**
     * @param bool $orderable
     * @return AbstractColumn
     */
    public function setOrderable($orderable) {
        $this->orderable = $orderable;
        return $this;
    }

    /**
     * @param array $data
     * @return AbstractColumn
     */
    public function hydrate(array $data) {
        return $this->setData(isset($data['data']) ? $data['data'] : null)
                        ->setName(isset($data['name']) ? $data['name'] : '')
                        ->setOrderable(isset($data['orderable']) ? filter_var($data['orderable'], FILTER_VALIDATE_BOOLEAN) : null)
                        ->setSearchable(isset($data['orderable']) ? filter_var($data['orderable'], FILTER_VALIDATE_BOOLEAN) : null);
    }

    static public function loadValidatorMetadata(ClassMetadata $metadata) {
        $metadata->addPropertyConstraint('data', new Assert\NotNull());
        $metadata->addPropertyConstraint('name', new Assert\NotNull());
        $metadata->addPropertyConstraint('orderable', new Assert\NotNull());
        $metadata->addPropertyConstraint('searchable', new Assert\NotNull());
    }

}
