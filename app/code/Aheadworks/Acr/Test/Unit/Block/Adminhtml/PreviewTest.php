<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Acr\Test\Unit\Block\Adminhtml;

use Aheadworks\Acr\Block\Adminhtml\Preview;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\Config;
use Aheadworks\Acr\Api\Data\PreviewInterface;
use Aheadworks\Acr\Api\Data\PreviewInterfaceFactory;
use Magento\Framework\Registry;

/**
 * Class PreviewTest
 * Test for \Aheadworks\Acr\Block\Adminhtml\Preview
 *
 * @package Aheadworks\Acr\Test\Unit\Block\Adminhtml
 */
class PreviewTest extends TestCase
{
    /**
     * @var Preview
     */
    private $block;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $coreRegistryMock;

    /**
     * @var PreviewInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $previewFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->configMock = $this->createPartialMock(Config::class, ['getSenderName', 'getSenderEmail']);
        $this->coreRegistryMock = $this->createPartialMock(Registry::class, ['registry']);
        $this->previewFactoryMock = $this->createPartialMock(PreviewInterfaceFactory::class, ['create']);

        $this->block = $objectManager->getObject(
            Preview::class,
            [
                'config' => $this->configMock,
                'coreRegistry' => $this->coreRegistryMock,
                'previewFactory' => $this->previewFactoryMock
            ]
        );
    }

    /**
     * Test getSenderName method
     */
    public function testGetSenderName()
    {
        $senderName = 'name';
        $storeId = 1;
        $previewMock = $this->initialPreview();

        $previewMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->configMock->expects($this->once())
            ->method('getSenderName')
            ->with($storeId)
            ->willReturn($senderName);

        $this->assertEquals($senderName, $this->block->getSenderName());
    }

    /**
     * Test getSenderEmail method
     */
    public function testGetSenderEmail()
    {
        $senderEmail = 'email@example.com';
        $storeId = 1;
        $previewMock = $this->initialPreview();

        $previewMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->configMock->expects($this->once())
            ->method('getSenderEmail')
            ->with($storeId)
            ->willReturn($senderEmail);

        $this->assertEquals($senderEmail, $this->block->getSenderEmail());
    }

    /**
     * Test getRecipientName method
     */
    public function testGetRecipientName()
    {
        $recipientName = 'name';
        $previewMock = $this->initialPreview();

        $previewMock->expects($this->once())
            ->method('getRecipientName')
            ->willReturn($recipientName);

        $this->assertEquals($recipientName, $this->block->getRecipientName());
    }

    /**
     * Test getRecipientEmail method
     */
    public function testGetRecipientEmail()
    {
        $recipientEmail = 'email@example.com';
        $previewMock = $this->initialPreview();

        $previewMock->expects($this->once())
            ->method('getRecipientEmail')
            ->willReturn($recipientEmail);

        $this->assertEquals($recipientEmail, $this->block->getRecipientEmail());
    }

    /**
     * Test getMessageContent method
     */
    public function testGetMessageContent()
    {
        $messageContent = 'message';
        $previewMock = $this->initialPreview();

        $previewMock->expects($this->once())
            ->method('getContent')
            ->willReturn($messageContent);

        $this->assertEquals($messageContent, $this->block->getMessageContent());
    }

    /**
     * Test getMessageSubject method
     */
    public function testGetMessageSubject()
    {
        $messageSubject = 'subject';
        $previewMock = $this->initialPreview();

        $previewMock->expects($this->once())
            ->method('getSubject')
            ->willReturn($messageSubject);

        $this->assertEquals($messageSubject, $this->block->getMessageSubject());
    }

    /**
     * Initial preview mock object
     *
     * @return PreviewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function initialPreview()
    {
        $previewMock = $this->getMockForAbstractClass(PreviewInterface::class);

        $this->coreRegistryMock->expects($this->exactly(2))
            ->method('registry')
            ->with('aw_acr_preview')
            ->willReturn($previewMock);

        return $previewMock;
    }
}
