<?php

namespace Rokanthemes\Customaddress\Helper;

use Exception;
use Magento\Customer\Api\AddressRepositoryInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Directory\Model\Country
     */
    protected $_country;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;


	/**
     * Initialize dependencies.
     *
     * @param \Magento\Directory\Model\Country $country
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Directory\Model\Country $country,
        AddressRepositoryInterface $addressRepository
        ) {
        parent::__construct($context);
        $this->_country = $country;
        $this->addressRepository = $addressRepository;
    }

    public function getCountryByCode($country_code){
        $country = $this->_country->load($country_code)->getName();
        return $country;
    }

    /**
     * @param $addressId
     *
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    public function getEmailbyAddressId($addressId)
    {
        try {
            $addressData = $this->addressRepository->getById($addressId);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        };
        if($addressData->getCustomAttribute("email_field"))
            return $addressData->getCustomAttribute("email_field")->getValue();
        else 
            return '';
    }
}