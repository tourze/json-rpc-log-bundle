<?php

namespace Tourze\JsonRPCLogBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\JsonRPCLogBundle\Entity\RequestLog;

/**
 * JsonRPC 请求日志管理控制器
 * @extends AbstractCrudController<RequestLog>
 */
#[AdminCrud(routePath: '/json-rpc/log', routeName: 'json_rpc_log')]
final class RequestLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RequestLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('JsonRPC日志')
            ->setEntityLabelInPlural('JsonRPC日志')
            ->setPageTitle('index', 'JsonRPC 请求日志')
            ->setPageTitle('detail', 'JsonRPC 日志详情')
            ->setHelp('index', '记录JsonRPC服务端的重要请求与响应信息，用于监控和故障排查')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'apiName', 'description', 'createdFromIp'])
            ->showEntityActionsInlined()
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('description', '操作记录')
            ->hideOnIndex()
            ->setFormTypeOptions(['required' => false])
        ;

        yield TextField::new('apiName', 'API名称')
            ->setMaxLength(50)
        ;

        yield TextField::new('renderStatus', '状态')
            ->hideOnForm()
            ->addCssClass('text-center')
            ->formatValue(function ($value) {
                if ('异常' === $value) {
                    return '<span class="text-danger">' . $value . '</span>';
                }

                return '<span class="text-success">' . $value . '</span>';
            })
        ;

        yield TextField::new('renderTrackUser', '操作用户')
            ->hideOnForm()
        ;

        yield TextField::new('createdFromIp', '来源IP')
            ->hideOnForm()
        ;

        yield TextField::new('serverIp', '服务端IP')
            ->hideOnDetail()
            ->hideOnForm()
        ;

        yield TextField::new('stopwatchDuration', '执行时长(ms)')
            ->hideOnForm()
            ->formatValue(function ($value) {
                return $value ? number_format(floatval($value), 2) . ' ms' : '';
            })
        ;

        yield TextareaField::new('request', '请求内容')
            ->hideOnIndex()
            ->setFormTypeOptions(['required' => false])
            ->formatValue(function ($value) {
                return $this->formatJson($value);
            })
        ;

        yield TextareaField::new('response', '响应内容')
            ->hideOnIndex()
            ->setFormTypeOptions(['required' => false])
            ->formatValue(function ($value) {
                return $this->formatJson($value);
            })
        ;

        yield TextareaField::new('exception', '异常信息')
            ->hideOnIndex()
            ->setFormTypeOptions(['required' => false])
        ;

        yield TextField::new('stopwatchResult', 'Stopwatch结果')
            ->hideOnIndex()
            ->hideOnForm()
        ;

        yield TextField::new('createdFromUa', '用户代理')
            ->hideOnIndex()
            ->hideOnForm()
            ->setMaxLength(100)
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('apiName', 'API名称'))
            ->add(TextFilter::new('createdFromIp', '来源IP'))
            ->add(TextFilter::new('description', '操作记录'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
        ;
    }

    /**
     * 格式化JSON字符串显示
     */
    private function formatJson(?string $json): string
    {
        if (null === $json || '' === $json) {
            return '';
        }

        $decoded = json_decode($json, true);
        if (JSON_ERROR_NONE === json_last_error()) {
            $formatted = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            return false !== $formatted ? $formatted : $json;
        }

        return $json;
    }
}
