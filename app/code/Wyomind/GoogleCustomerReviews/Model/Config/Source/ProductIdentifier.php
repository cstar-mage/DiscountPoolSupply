<?php

namespace Wyomind\GoogleCustomerReviews\Model\Config\Source;

class ProductIdentifier implements \Magento\Framework\Option\ArrayInterface
{
    
    protected $_attributeRepository = null;
    protected $_objectManager = null;
    
    public function __construct(
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Magento\Framework\ObjectManager\ObjectManager $objectManager
    ) {
        $this->_attributeRepository = $attributeRepository;
        $this->_objectManager = $objectManager;
    }
    
    
    public function toOptionArray()
    {
        $typeCode = \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE;
        $searchCriteria = $this->_objectManager->create('\Magento\Framework\Api\SearchCriteria');
        $attributeList = $this->_attributeRepository->getList($typeCode, $searchCriteria)->getItems();

        $tmp = [];
        $tmp[] = ["label"=>"ID","value"=>"entity_id"];
        foreach ($attributeList as $attribute) {
            if ($attribute->getIsUnique()) {
                $tmp[] = [
                    "value" => $attribute->getAttributeCode(),
                    "label" => $attribute->getDefaultFrontendLabel()
                ];
            }
        }

        return $tmp;
    }
}
