<?php

namespace JS\DatatablesBundle\Entity;

class ColumnDt extends AbstractColumn {

    /**
     * @var ServerDt
     */
    private $server;

    /**
     * @return ServerDt
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * @param ServerDt $server
     * @return ColumnDt
     */
    public function setServer(ServerDt $server) {
        $this->server = $server;
        return $this;
    }

    /**
     * @param array $data
     * @return ColumnDt
     */
    public function hydrate(array $data) {
        parent::hydrate($data);
        return $this->setServer((new ServerDt())->hydrate($data['server']));
    }

}
