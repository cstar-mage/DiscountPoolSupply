<?php

namespace Wyomind\GoogleCustomerReviews\Model\Config\Source;

class Lang extends \Magento\Config\Model\Config\Source\Locale
{

    private $_lang = array("cs", "da", "de", "en_AU", "en_GB", "en_US", "es", "fr", "it", "ja", "nl", "no", "pl", "pt_BR", "ru", "sv", "tr");

    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $toReturn = [];
        foreach ($options as $option) {
            foreach ($this->_lang as $lang) {
                if (strpos($option['value'], $lang) === 0) {
                    $toReturn[] = $option;
                }
            }
        }
        return $toReturn;
    }

}
