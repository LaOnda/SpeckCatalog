<?php

namespace SpeckCatalogManager\Controller;

use Zend\Mvc\Controller\ActionController,
    SpeckCatalogManager\Service\FormService,
    SpeckCatalogManager\Entity,
    \Exception;

class IndexController extends ActionController
{
    protected $productService;
    protected $optionService;

    protected $userService;
    protected $sessionService;
    protected $view = array();
    
    public function __construct()
    {
        if(!isset($_GET['constructor'])){
            $_GET['constructor'] = null;
        }
        if(!isset($_GET['entityId'])){
            $_GET['entityId'] = null;
        }
        $this->events()->attach('dispatch', array($this, 'preDispatch'), 100);
    }

    public function preDispatch($e)
    {
        $user = $this->userService->getAuthService()->getIdentity();
        if(!$user){
            return $this->redirect()->toRoute('zfcuser');
        }
    }

    public function sortableSortAction(){
        echo "<script>console.log('sorted');</script>";
    }

    public function indexAction()
    {
        return $this->view;
    }
    
    public function updateRecordAction()
    {
        $modelService = $_GET['className'].'Service';
        $return = $this->$modelService->updateModelFromArray($_POST);
        
        var_dump($return);
        die();
    }

    public function productAction()
    {
        if(isset($_GET['new'])){
            $this->view['product'] = $this->productService->newProduct($_GET['new']);
        }else{
            $this->view['product'] = $this->productService->getProductById($_GET['id']);
        }
        return $this->view;
    }

    private function prepPaginator($name, $items=null, $perPage=10, $pageNum=1)
    {
        if(is_array($items)){ 
           $this->view["{$name}Paginator"] = \Zend\Paginator\Paginator::factory($items)
               ->setItemCountPerPage($perPage)->setCurrentPageNumber($pageNum); 
        }
    }

    public function setUserService($userService)
    {
        $this->userService = $userService;
    }

    public function setSessionService($catalogManagerService)
    {                                              
        $this->sessionService = $catalogManagerService;
    }

    public function entitySearchAction()
    {
        $this->view['nolayout'] = true;
        $modelService = $_GET['className'].'Service';
        $this->view['results'] = $this->$modelService->getModelsBySearchData($_GET['value']);
        return $this->view;
    }   

    protected function livePartialAction()
    {
        $this->view['nolayout'] = true;
        $className = $_GET['className'];
        $modelService = $className.'Service';
        $modelById = 'get'.ucfirst($className).'ById';
        $this->view['partial'] = $className;
        if(isset($GET['parent_id'])){
            $this->$modelService->linkParent($_GET['entityId'], $_GET['parent_id']);
        }  
        $this->view[$className] = $this->$modelService->$modelById($_GET['entityId']);
        return $this->view;
    }
    public function entityOptionsAjaxAction()
    {
        $this->view['nolayout'] = true;
        $this->view['options'] = array('save',);
        return $this->view;
    }

    public function getProductService()
    {
        return $this->productService;
    }

    public function setProductService($productService)
    {
        $this->productService = $productService;
        return $this;
    }
 
    public function getOptionService()
    {
        return $this->optionService;
    }

    public function setOptionService($optionService)
    {
        $this->optionService = $optionService;
        return $this;
    }
}                                   
