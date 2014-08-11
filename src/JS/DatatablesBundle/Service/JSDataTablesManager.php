<?php

namespace JS\DatatablesBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class JSDataTablesManager implements ContainerAwareInterface {

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    public function getDt($name, Request $request) {
        $dataTablesConfig = $this->container->getParameter('js_datatables.config');
        if (array_key_exists($name, $dataTablesConfig))
        {
            $dtConfig = $this->getDtConfig($name);
            if (isset($dtConfig['is_service']))
            {
                return $this->container->get($dtConfig['service_name']);
            } else
            {
                if (isset($dtConfig['class_dt']))
                {
                    $instance = new $dtConfig['class_dt'];
                    return $this->injectDependencies($instance, $this->getDtConfig($name), $request);
                } else
                {
                    return $this->injectDependencies(new JSDataTables(), $this->getDtConfig($name), $request);
                }
            }
        } else
        {
            throw new \InvalidArgumentException(sprintf('Invalid DataTables %s', $name));
        }
    }

    public function injectDependencies(JSDataTables $dataTable, $dtConfig, Request $request) {
        return $dataTable->setDtArrayConfig($dtConfig)
                        ->setEntityManager($this->container->get('doctrine.orm.entity_manager'))
                        ->setListClause($this->container->get('js.datatables.list_clause'))
                        ->setParams($request->query->all())
                        ->setValidator($this->container->get('validator'));
    }

    public function getDtConfig($name) {
        return $this->container->getParameter('js_datatables.config')[$name]['dt_config'];
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

}
