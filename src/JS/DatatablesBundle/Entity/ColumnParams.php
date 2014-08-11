<?php

namespace JS\DatatablesBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class ColumnParams extends AbstractColumn {

    /**
     * @var SearchParams
     */
    private $search;

    /**
     * @return SearchParams
     */
    public function getSearch() {
        return $this->search;
    }

    /**
     * @param SearchParams $search
     * @return ColumnParams
     */
    public function setSearch(SearchParams $search) {
        $this->search = $search;
        return $this;
    }

    /**
     * @param array $data
     * @return ColumnParams
     */
    public function hydrate(array $data) {
        parent::hydrate($data);
        return $this->setSearch((new SearchParams())->hydrate(isset($data['search']) ? $data['search'] : []));
    }

    static public function loadValidatorMetadata(ClassMetadata $metadata) {
        $metadata->addPropertyConstraint('search', new Assert\Valid());
    }

}
