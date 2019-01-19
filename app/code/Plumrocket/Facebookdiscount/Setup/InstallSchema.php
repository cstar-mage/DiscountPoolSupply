<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package Plumrocket_Facebook_Discount
 * @copyright Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license http://wiki.plumrocket.net/wiki/EULA End-user License Agreement
 */

namespace Plumrocket\Facebookdiscount\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Create table 'facebookdiscount_log'
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('facebookdiscount_log'))
            ->addColumn('item_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true,
                ], 'Item Id')
            ->addColumn('customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                    'default' => '0',
                    'nullable' => false
                ], 'Customer Id')
            ->addColumn('visitor_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                    'default' => '0',
                    'nullable' => false
                ], 'Visitor Id')
            ->addColumn('active', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 1, [
                    'default' => '1',
                    'nullable' => false
                ], 'Active')
            ->addColumn('date_created', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, [
                    'default' => '0000-00-00 00:00:00',
                    'nullable' => false
                ], 'Date Created')
            ->addColumn('discount', \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT, null, [
                    'default' => '0',
                    'nullable' => false
                ], 'Discount')
            ->addColumn('action', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 1, [
                    'default' => '0',
                    'nullable' => false
                ], 'Action')
            ->setComment('Facebookdiscount Items Table');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
