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
 * @package     Plumrocket_Facebook_Discount
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Facebookdiscount\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.1.0', '<')) {

              $table = $installer->getConnection()
                  ->newTable($installer->getTable('facebookdiscount_queue'))
                  ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                          'identity' => true,
                          'nullable' => false,
                          'primary' => true,
                          'unsigned' => true,
                      ], 'Entity Id')
                  ->addColumn('time', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                          'default' => '0',
                          'nullable' => false
                      ], 'Time')
                  ->setComment('Facebookdiscount Queue Table');

              $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}
