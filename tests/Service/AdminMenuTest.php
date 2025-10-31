<?php

namespace Tourze\JsonRPCLogBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\JsonRPCLogBundle\Entity\RequestLog;
use Tourze\JsonRPCLogBundle\Service\AdminMenu;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private LinkGeneratorInterface&MockObject $linkGenerator;

    private AdminMenu $adminMenu;

    protected function onSetUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    public function testInvokeCreatesSystemMonitoringMenu(): void
    {
        $item = $this->createMock(ItemInterface::class);
        $systemMenu = $this->createMock(ItemInterface::class);

        // 测试当系统监控菜单不存在时创建它
        $item->expects($this->exactly(2))
            ->method('getChild')
            ->with('系统监控')
            ->willReturnOnConsecutiveCalls(null, $systemMenu)
        ;

        $item->expects($this->once())
            ->method('addChild')
            ->with('系统监控')
            ->willReturn($systemMenu)
        ;

        // 设置链接生成器的预期行为
        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->with(RequestLog::class)
            ->willReturn('/admin/json-rpc-log')
        ;

        // 配置系统菜单的行为
        $jsonRpcMenuItem = $this->createMock(ItemInterface::class);

        $systemMenu->expects($this->once())
            ->method('addChild')
            ->with('JsonRPC日志')
            ->willReturn($jsonRpcMenuItem)
        ;

        $jsonRpcMenuItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/json-rpc-log')
            ->willReturn($jsonRpcMenuItem)
        ;

        $jsonRpcMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-exchange-alt')
            ->willReturn($jsonRpcMenuItem)
        ;

        $this->adminMenu->__invoke($item);
    }

    public function testInvokeUsesExistingSystemMonitoringMenu(): void
    {
        $item = $this->createMock(ItemInterface::class);
        $systemMenu = $this->createMock(ItemInterface::class);

        // 测试当系统监控菜单已存在时使用它
        $item->expects($this->exactly(2))
            ->method('getChild')
            ->with('系统监控')
            ->willReturn($systemMenu)
        ;

        $item->expects($this->never())
            ->method('addChild')
        ;

        // 设置链接生成器的预期行为
        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->with(RequestLog::class)
            ->willReturn('/admin/json-rpc-log')
        ;

        // 配置系统菜单的行为
        $jsonRpcMenuItem = $this->createMock(ItemInterface::class);

        $systemMenu->expects($this->once())
            ->method('addChild')
            ->with('JsonRPC日志')
            ->willReturn($jsonRpcMenuItem)
        ;

        $jsonRpcMenuItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/json-rpc-log')
            ->willReturn($jsonRpcMenuItem)
        ;

        $jsonRpcMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-exchange-alt')
            ->willReturn($jsonRpcMenuItem)
        ;

        $this->adminMenu->__invoke($item);
    }
}
