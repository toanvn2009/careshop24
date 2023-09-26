<?php
    namespace Rokanthemes\Customcustomer\Block\Widget;
 
    class Dob extends \Magento\Customer\Block\Widget\Dob
    {
        /**
         * Returns format which will be applied for DOB in javascript
         *
         * @return string
         */
        public function getDateFormat()
        {
            $escapedDateFormat = 'MM/dd/yyyy';
            return $escapedDateFormat;
        }

    }