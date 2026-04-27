<?php
/**
 * Class Collection
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_Banner
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\Banner\Model\ResourceModel\Banner;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_Banner
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Collection extends AbstractCollection
{
    /**
     * Banner Id
     *
     * @var string
     */
    protected $idFieldName = 'banner_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Sparsh\Banner\Model\Banner::class,
            \Sparsh\Banner\Model\ResourceModel\Banner::class
        );
    }
}
