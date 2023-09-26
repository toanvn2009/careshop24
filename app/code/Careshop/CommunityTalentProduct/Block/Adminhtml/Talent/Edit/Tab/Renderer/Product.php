<?php

namespace Careshop\CommunityTalentProduct\Block\Adminhtml\Talent\Edit\Tab\Renderer;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Multiselect;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;

class Product extends Multiselect
{
    /**
     * @var CommunityTagCollectionFactory
     */
    public $collectionFactory;

    /**
     * Authorization
     *
     * @var AuthorizationInterface
     */
    public $authorization;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Tag constructor.
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param CommunityTagCollectionFactory $collectionFactory
     * @param AuthorizationInterface $authorization
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        AuthorizationInterface $authorization,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->authorization = $authorization;
        $this->_urlBuilder = $urlBuilder;
        $this->_productloader = $_productloader;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @inheritdoc
     */
    public function getElementHtml()
    {
        $html = '';
        if ($this->getValue()) 
        {
            $product = $this->_productloader->create()->load($this->getValue());
            $html = '<a target="_blank" href="'.$this->_urlBuilder->getUrl('catalog/product/edit/id/'.$this->getValue().'').'">'.$product->getName().'</a>';
        }
        return $html;
    }
}
