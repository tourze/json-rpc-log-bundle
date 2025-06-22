<?php

namespace Tourze\JsonRPCLogBundle\Service;

use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\JsonRPCLogBundle\Entity\RequestLog;

/**
 * JsonRPC 日志菜单服务
 */
class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private readonly LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if ($item->getChild('系统监控') === null) {
            $item->addChild('系统监控');
        }

        $systemMenu = $item->getChild('系统监控');

        // JsonRPC 日志菜单
        $systemMenu->addChild('JsonRPC日志')
            ->setUri($this->linkGenerator->getCurdListPage(RequestLog::class))
            ->setAttribute('icon', 'fas fa-exchange-alt');
    }
}
