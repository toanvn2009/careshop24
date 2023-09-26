<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab\Renderer;

use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Multiselect;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Careshop\CommunityIdea\Model\ResourceModel\Category\Collection;
use Careshop\CommunityIdea\Model\ResourceModel\Category\CollectionFactory as CommunityCategoryCollectionFactory;


class Category extends Multiselect
{
    /**
     * @var CommunityCategoryCollectionFactory
     */
    public $collectionFactory;

    /**
     * @var AuthorizationInterface
     */
    public $authorization;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var categoriesTree
     */
    protected $categoriesTree;
   
    /**
     * Category constructor.
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param CommunityCategoryCollectionFactory $collectionFactory
     * @param AuthorizationInterface $authorization
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        CommunityCategoryCollectionFactory $collectionFactory,
        AuthorizationInterface $authorization,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->authorization = $authorization;
        $this->_urlBuilder = $urlBuilder;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @inheritdoc
     */
    public function getElementHtml()
    {
        $html = '<div class="admin__field-control admin__control-grouped">';
        $html .= '<div id="community-category-select" class="admin__field"
                    data-bind="scope:\'communityCategory\'" data-index="index">';
        $html .= '<!-- ko foreach: elems() -->';
        $html .= '<input name="idea[categories_ids]" data-bind="value: value" style="display: none"/>';
        $html .= '<!-- ko template: elementTmpl --><!-- /ko -->';
        $html .= '<!-- /ko -->';
        $html .= '</div>';

        $html .= '<div class="admin__field admin__field-group-additional admin__field-small"
                  data-bind="scope:\'create_category_button\'">';
        $html .= '<div class="admin__field-control">';
        $html .= '<!-- ko template: elementTmpl --><!-- /ko -->';
        $html .= '</div></div></div>';

        $html .= '<!-- ko scope: \'create_category_modal\' -->
        <!-- ko template: getTemplate() --><!-- /ko --><!-- /ko -->';

        $html .= $this->getAfterElementHtml();

        return $html;
    }

    /**
     * Get no display
     *
     * @return bool
     */
    public function getNoDisplay()
    {
        $isNotAllowed = !$this->authorization->isAllowed('Careshop_CommunityIdea::category');

        return $this->getData('no_display') || $isNotAllowed;
    }

    /**
     * @return Array
     */
    public function getCategoriesArray()
    { 
        if ($this->categoriesTree === null) {
            /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            $collection = $this->collectionFactory->create();
            $arrayElements = array();
            $element = array();
            foreach ($collection as $category) {
             
                $elemnt[$category->getId()] = array(
                    'parent_id' => $category->getParentId(),
                    'value' => $category->getId(),
                    'is_active' => $category->getData('enabled'),
                    'label' =>  $category->getName(),
                );
                $arrayElements = $elemnt;
            }
            $this->categoriesTree = $arrayElements;
        }

        return $this->categoriesTree ;
    }
    /**
     * @return mixed
     */
    public function getCategoriesTree()
    {   
        $categories = $this->getCategoriesArray(); 
      
        $tree = $this->buildTree($categories);
        return $tree; 
    }

    function buildTree(&$array) {
        $tree = array();
    
        // Create an associative array with each key being the ID of the item
        foreach($array as $k => &$v) {
          $tree[$v['value']] = &$v;
        }
    
        // Loop over the array and add each child to their parent
        foreach($tree as $k => &$v) {
            if(!$v['parent_id'] || $v['parent_id'] ==0) {
              continue;
            }
            $tree[$v['parent_id']]['optgroup'][] = &$v;
        }
    
        // Loop over the array again and remove any items that don't have a parent of 0;
        foreach($tree as $k => &$v) {
          if(!$v['parent_id']) {
            continue;
          }
          unset($tree[$k]);
        }
    
        return $tree;
    }
	
    /**
     * Get values for select
     *
     * @return array
     */
    public function getValues()
    {
        $values = $this->getValue();
        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        if (!count($values)) {
            return [];
        }

        /* @var $collection Collection */
        $collection = $this->collectionFactory->create()
            ->addIdFilter($values);

        $options = [];
        foreach ($collection as $category) {
            $options[] = $category->getId();
        }

        return $options;
    }

    /**
     * Attach Community Category suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = '<script type="text/x-magento-init">
            {
                "*": {
                    "Magento_Ui/js/core/app": {
                        "components": {
                            "communityCategory": {
                                "component": "uiComponent",
                                "children": {
                                    "community_select_category": {
                                        "component": "Careshop_CommunityIdea/js/components/new-category",
                                        "config": {
                                            "filterOptions": true,
                                            "disableLabel": true,
                                            "chipsEnabled": true,
                                            "levelsVisibility": "1",
                                            "elementTmpl": "ui/grid/filters/elements/ui-select",
                                            "options": ' . json_encode($this->getCategoriesTree()) . ',
                                            "value": ' . json_encode($this->getValues()) . ',
                                            "listens": {
                                                "index=create_category:responseData": "setParsed",
                                                "newOption": "toggleOptionSelected"
                                            },
                                            "config": {
                                                "dataScope": "community_select_category",
                                                "sortOrder": 10
                                            }
                                        }
                                    }
                                }
                            },
                            "create_category_button": {
                                "title": "' . __('New Category') . '",
                                "formElement": "container",
                                "additionalClasses": "admin__field-small",
                                "componentType": "container",
                                "component": "Magento_Ui/js/form/components/button",
                                "template": "ui/form/components/button/container",
                                "actions": [
                                    {
                                        "targetName": "create_category_modal",
                                        "actionName": "toggleModal"
                                    },
                                    {
                                        "targetName": "create_category_modal.create_category",
                                        "actionName": "render"
                                    },
                                    {
                                        "targetName": "create_category_modal.create_category",
                                        "actionName": "resetForm"
                                    }
                                ],
                                "additionalForGroup": true,
                                "provider": false,
                                "source": "product_details",
                                "displayArea": "insideGroup"
                            },
                            "create_category_modal": {
                                "config": {
                                    "isTemplate": false,
                                    "componentType": "container",
                                    "component": "Magento_Ui/js/modal/modal-component",
                                    "options": {
                                        "title": "' . __('New Category') . '",
                                        "type": "slide"
                                    },
                                    "imports": {
                                        "state": "!index=create_category:responseStatus"
                                    }
                                },
                                "children": {
                                    "create_category": {
                                        "label": "",
                                        "componentType": "container",
                                        "component": "Magento_Ui/js/form/components/insert-form",
                                        "dataScope": "",
                                        "update_url": "' . $this->_urlBuilder->getUrl('mui/index/render') . '",
                                        "render_url": "' .
                                $this->_urlBuilder->getUrl(
                                    'mui/index/render_handle',
                                    ['handle' => 'community_category_create', 'buttons' => 1]
                                ) . '",
                                        "autoRender": false,
                                        "ns": "community_new_category_form",
                                        "externalProvider": "community_new_category_form.new_category_form_data_source",
                                        "toolbarContainer": "${ $.parentName }",
                                        "formSubmitType": "ajax"
                                    }
                                }
                            }
                        }
                    }
                }
            }
        </script>';

        return $html;
    }
}
