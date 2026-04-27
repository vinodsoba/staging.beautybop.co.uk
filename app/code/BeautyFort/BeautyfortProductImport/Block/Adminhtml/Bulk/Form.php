<?php
namespace BeautyFort\BeautyfortProductImport\Block\Adminhtml\Bulk;

use Magento\Backend\Block\Template;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class Form extends Template
{
    protected $categoryCollectionFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function getCategories()
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToFilter('is_active', 1);

        return $collection;
    }
}