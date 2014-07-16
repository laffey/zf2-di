<?php
/**
 * Example of a controller using the di
 */

namespace Ei\module\Example;


class MainController {

    /**
     * Load a list of available dashaboards to the user
     *      if a dashboard is already selected, load that dashboard
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        /** @var $dashBoard Ei\Application\Reporter\Dashboard */
        $dashBoard = null;
        $dashBoardId = (int)$this->_params->fromQuery('dashboard', 0);
        $dashboardListRepository = $this->_di->get('dashboard_list_repository');

        if ($dashBoardId > 0) {
            $dashBoard = $this->_di->get('dashboard_factory')->load($dashBoardId);
        }
        
        // ...
        //  bla bla bla
    }
    
} 