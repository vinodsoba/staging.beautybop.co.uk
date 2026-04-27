<?php
/**
 * Class Banner
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_Banner
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\Banner\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Banner
 *
 * @category Sparsh
 * @package  Sparsh_Banner
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Banner extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * DateTime
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * Construct
     *
     * @param Context     $context        context
     * @param DateTime    $date           date
     * @param string|null $resourcePrefix resourcePrefix
     */
    public function __construct(
        Context $context,
        DateTime $date,
        $resourcePrefix = null
    ) {
    
        parent::__construct($context, $resourcePrefix);
        $this->date = $date;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sparsh_banner', 'banner_id');
    }

    /**
     * Process post data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object object
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {

        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->date->gmtDate());
        }

        $object->setUpdateTime($this->date->gmtDate());

        return parent::_beforeSave($object);
    }
}
