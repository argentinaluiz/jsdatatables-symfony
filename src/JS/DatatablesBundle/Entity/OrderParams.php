<?php

namespace JS\DatatablesBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class OrderParams {

    /**
     * @var int
     */
    private $column;

    /**
     * @var string
     */
    private $dir;

    /**
     * @return int
     */
    public function getColumn() {
        return $this->column;
    }

    /**
     * @return string
     */
    public function getDir() {
        return $this->dir;
    }

    /**
     * @param int $column
     * @return OrderParams
     */
    public function setColumn($column) {
        $this->column = $column;
        return $this;
    }

    /**
     * @param string $dir
     * @return OrderParams
     */
    public function setDir($dir) {
        $this->dir = $dir;
        return $this;
    }

    /**
     * @param array $data
     * @return OrderParams
     */
    public function hydrate(array $data) {
        return $this->setColumn(isset($data['column']) ? (int) $data['column'] : null)
                        ->setDir(isset($data['dir']) ? $data['dir'] : null);
    }

    static public function loadValidatorMetadata(ClassMetadata $metadata) {
        $metadata->addPropertyConstraint('column', new Assert\NotBlank());
        $metadata->addPropertyConstraint('column', new Assert\Type(['type' => 'integer']));
        $metadata->addPropertyConstraint('dir', new Assert\NotBlank());
        $metadata->addPropertyConstraint('dir', new Assert\Choice(['choices' => ['asc', 'desc']]));
    }

}
