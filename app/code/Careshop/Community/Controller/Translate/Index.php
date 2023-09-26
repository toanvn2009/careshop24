<?php

namespace Careshop\Community\Controller\Translate;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Careshop\CommunityDevelopProduct\Model\DevelopFactory;

class Index extends Action
{
    protected $_pageFactory;
    protected $storeManager;
    private $json;
    private $resultJsonFactory;

    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
        )
	{
		$this->_pageFactory = $pageFactory;
        $this->storeManager = $storeManager;
        $this->json = $json;
        $this->_resource = $resource;
        $this->resultJsonFactory = $resultJsonFactory;
		return parent::__construct($context);
	}

    /**
     * @return ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {   
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $connection = $this->_resource->getConnection();
        $tableName = $this->_resource->getTableName('community_languages_translate');
        $lang_name_from_default = 'English';
        $lang_code_from_default = 'en'; 
        $sql = "SELECT * FROM $tableName WHERE code='".$lang."'";
        $result = $connection->fetchRow($sql);
        if ($result && isset($result['name']) && isset($result['code'])) {
            $lang_name_to_default = $result['name'] ? $result['name'] : $lang_name_from_default;
            $lang_code_to_default = $result['code'] ? $result['code'] : $lang_code_from_default;
        }
        $html = '';
        $html .= '<div class="translate-form">';
            $html .= $this->getLanguagesTranslateFrom($lang_name_to_default, $lang_code_to_default, 'to');
        $html .= '</div>';
        $data = array(
            'html' => $html,
            'from' => 'en',
            'to' => 'vi'
        );
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($data);
    }  

    public function getLanguagesTranslateFrom($lang_name_default, $lang_code_default, $step)
    {
        $connection = $this->_resource->getConnection();
        $tableName = $this->_resource->getTableName('community_languages_translate');
        $sql = "SELECT * FROM $tableName";
        $languages = $connection->fetchAll($sql);
        $text = __('Translate to');
        $html = '';
        $html .= '<div class="languages_translate languages_translate_'.$step.'">';
            $html .= '<div class="languages-'.$step.' languages__button">';
                $html .= $text;
                $html .= ' <span class="languages-value">'.$lang_name_default.'</span>';
            $html .= '</div>';
            $html .= '<ul class="languages_list hidden">';
                $html .= '<li class="languages_item"><a href="#" class="detecting-language" data-value="'.$lang_code_default.'" data-text="'.$lang_name_default.'">'.__('Detect Language').'</a></li>';
                foreach ($languages as $language) {
                    $html .= '<li class="languages_item"><a href="#" data-value="'.$language['code'].'">'.$language['name'].'</a></li>';
                }
            $html .= '</ul>';
            $html .= '<input type="hidden" name="language_code" class="language_code" value="'.$lang_code_default.'">';
        $html .= '</div>';
        return $html;
    }
}
