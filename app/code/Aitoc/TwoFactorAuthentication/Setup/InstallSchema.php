<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\TwoFactorAuthentication\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_twofactorauthentication_user'))
            ->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'User id'
            )
            ->addColumn(
                'original_user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'nullable' => false],
                'Original User Id'
            )
            ->addColumn(
                'user_secret',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'User Secret'
            )
            ->addColumn(
                'time_shift',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => true],
                'Time Shift'
            )
            ->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'TFA Is Active'
            )
            ->addColumn(
                'ip_enabled',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'IP Enabled'
            )
            ->addColumn(
                'ip_list',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'IP List'
            )
            ->addColumn(
                'email_code_enabled',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Email Enabled'
            )
            ->setComment('Two-Factor Authentication User');

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
