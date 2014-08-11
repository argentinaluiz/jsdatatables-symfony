<?php

namespace JS\DatatablesBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class SearchParams {

    /**
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $regex;

    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isRegex() {
        return $this->regex;
    }

    /**
     * @param string $value
     * @return SearchParams
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * @param bool $regex
     * @return SearchParams
     */
    public function setRegex($regex) {
        $this->regex = $regex;
        return $this;
    }

    /**
     * @param array $data
     * @return SearchParams
     */
    public function hydrate(array $data) {
        return $this->setRegex(isset($data['regex']) ? filter_var($data['regex'], FILTER_VALIDATE_BOOLEAN) : null)
                        ->setValue(isset($data['value']) ? $data['value'] : null);
    }

    static public function loadValidatorMetadata(ClassMetadata $metadata) {
        $metadata->addPropertyConstraint('value', new Assert\NotNull());
        $metadata->addPropertyConstraint('regex', new Assert\NotNull());
    }

}
