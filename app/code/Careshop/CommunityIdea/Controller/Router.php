<?php

namespace Careshop\CommunityIdea\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Url;
use Careshop\CommunityIdea\Helper\Data;

class Router implements RouterInterface
{

    /**
     * @var ActionFactory
     */
    public $actionFactory;

    /**
     * @var Data
     */
    public $helper;

    /**
     * @param ActionFactory $actionFactory
     * @param Data $helper
     */
    public function __construct(
        ActionFactory $actionFactory,
        Data $helper
    ) {
        $this->actionFactory = $actionFactory;
        $this->helper = $helper;
    }

    /**
     * @param RequestInterface $request
     *
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {

        $identifier = trim($request->getPathInfo(), '/');
        $urlSuffix = $this->helper->getUrlSuffix();

        if ($length = strlen($urlSuffix)) {
            if (substr($identifier, -$length) === $urlSuffix) {
                $identifier = substr($identifier, 0, strlen($identifier) - $length);
            } 
        }

        $routePath = explode('/', $identifier);
        $routeSize = count($routePath);
        if (!$routeSize || ($routeSize > 3) || (array_shift($routePath) !== $this->helper->getRoute())) {
            return null;
        }
     
        $request->setModuleName('community')
            ->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier . $urlSuffix);
        $controller = array_shift($routePath);
        
        if (!$controller) {
            $request->setControllerName('idea')
                ->setActionName('index')
                ->setPathInfo('/community/idea/index');

            return $this->actionFactory->create(Forward::class);
        }
       
        $action = array_shift($routePath) ?: 'index';
        switch ($controller) {
            case 'idea':
                if (!in_array($action, ['index'])) {
                    $idea = $this->helper->getObjectByParam($action, 'url_key');
                    $request->setParam('id', $idea->getId());
                    $action = 'view';
                }
                break;
            case 'author':
                    if (!in_array($action, ['index'])) {
                     
                        $author = $this->helper->getObjectByParam($action, 'url_key','author');
                
                        $request->setParam('id', $author->getId());
                        $idea = $this->helper->getIdeaByAuthorId($author->getId());
                        $request->setParam('idea_id',$idea->getId());
                        $action = 'information';
                    }
                    break;   
            case 'product':
                if (!in_array($action, ['index'])) {
                   
                    $action = 'developer';
                }
                break;   
            default:
           
                $idea = $this->helper->getObjectByParam($controller, 'url_key');
                $request->setParam('id', $idea->getId());
                $controller = 'idea';
                $action = 'view';
        }
        $request->setControllerName($controller)
            ->setActionName($action)
            ->setPathInfo('/community/' . $controller . '/' . $action);

        return $this->actionFactory->create(Forward::class);
    }


}
