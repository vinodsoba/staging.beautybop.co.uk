<?php

namespace BeautyBop\Core\Plugin;

use Magento\Theme\Block\Html\Topmenu as MagentoTopmenu;
use Magento\Framework\Data\Tree\Node;

class Topmenu
{
    /**
     * Clean brand menu labels, sort Brands A-Z,
     * and append a View All Brands link.
     */
    public function beforeGetHtml(
        MagentoTopmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    ) {
        $menu = $subject->getMenu();

        foreach ($menu->getChildren() as $topLevelItem) {

            if (
                strcasecmp(
                    trim((string)$topLevelItem->getName()),
                    'Brands'
                ) !== 0
            ) {
                continue;
            }

            $brandItems = [];

            foreach ($topLevelItem->getChildren() as $brandItem) {

                $name = trim((string)$brandItem->getName());

                $name = preg_replace(
                    '/\s+Aftershave\s*&\s*Perfume$/i',
                    '',
                    $name
                );

                $name = preg_replace(
                    '/\s+Fragrance$/i',
                    '',
                    $name
                );

                if (strcasecmp($name, 'Zadig Voltaire') === 0) {
                    $name = 'Zadig & Voltaire';
                }

                $brandItem->setName(trim($name));

                $brandItems[] = $brandItem;
            }

            /*
             * Sort existing brands A-Z.
             */
            usort($brandItems, function ($a, $b) {
                return strcasecmp(
                    trim((string)$a->getName()),
                    trim((string)$b->getName())
                );
            });

            /*
             * Remove existing children.
             */
            $existingChildren = [];

            foreach ($topLevelItem->getChildren() as $child) {
                $existingChildren[] = $child;
            }

            foreach ($existingChildren as $child) {
                $topLevelItem->removeChild($child);
            }

            /*
             * Add brands back in alphabetical order.
             */
            foreach ($brandItems as $brandItem) {
                $topLevelItem->addChild($brandItem);
            }

            /*
             * Append View All Brands.
             */
            $viewAllNode = new Node(
                [
                    'name'       => __('View All Brands'),
                    'id_field'   => 'view-all-brands',
                    'url'        => $topLevelItem->getUrl(),
                    'is_active'  => false,
                    'has_active' => false,
                    'class'      => 'bb-view-all-brands'
                ],
                'id_field',
                $topLevelItem->getTree(),
                $topLevelItem
            );

            $topLevelItem->addChild($viewAllNode);

            break;
        }

        return [
            $outermostClass,
            $childrenWrapClass,
            $limit
        ];
    }
}