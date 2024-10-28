<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Grid\Definition\Factory;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\IdentifierColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdapterCustomerGridDefinitionFactory is responsible for creating AdapterCustomer definition.
 */
final class AdapterCustomerGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'drsoft_fr_validate_customer_pro_adapter_customer';

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $dbPrefix;

    /**
     * @param HookDispatcherInterface|null $hookDispatcher
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher = null,
        Connection              $connection,
        string                  $dbPrefix
    )
    {
        parent::__construct($hookDispatcher);

        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId(): string
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName(): string
    {
        return $this->trans(
            'Customer',
            [],
            'Modules.Drsoftfrvalidatecustomerpro.Admin'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id',
                ])
            )
            ->add((new IdentifierColumn('id'))
                ->setName($this->trans('ID', [], 'Modules.Drsoftfrvalidatecustomerpro.Admin'))
                ->setOptions([
                    'identifier_field' => 'id',
                ])
            )
            ->add((new IdentifierColumn('id_customer'))
                ->setName($this->trans('Customer ID', [], 'Modules.Drsoftfrvalidatecustomerpro.Admin'))
                ->setOptions([
                    'identifier_field' => 'id_customer',
                ])
            )
            ->add((new ToggleColumn('active'))
                ->setName($this->trans('Is active', [], 'Modules.Drsoftfrvalidatecustomerpro.Admin'))
                ->setOptions([
                    'field' => 'active',
                    'primary_field' => 'id',
                    'route' => 'admin_drsoft_fr_validate_customer_pro_adapter_customer_toggle_active',
                    'route_param_name' => 'id',
                ])
            )
            ->add(
                (new DateTimeColumn('date_add'))
                    ->setName($this->trans('Date', [], 'Admin.Global'))
                    ->setOptions([
                        'format' => 'Y-m-d H:i',
                        'field' => 'date_add',
                    ])
            )
            ->add(
                (new DateTimeColumn('date_upd'))
                    ->setName($this->trans('Date', [], 'Admin.Global'))
                    ->setOptions([
                        'format' => 'Y-m-d H:i',
                        'field' => 'date_add',
                    ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_drsoft_fr_validate_customer_pro_adapter_customer_edit',
                                'route_param_name' => 'id',
                                'route_param_field' => 'id',
                                'clickable_row' => true,
                            ])
                        )
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => Request::METHOD_DELETE,
                                'route' => 'admin_drsoft_fr_validate_customer_pro_adapter_customer_delete',
                                'route_param_name' => 'id',
                                'route_param_field' => 'id',
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                                'modal_options' => new ModalOptions([
                                    'title' => $this->trans('Delete selection', [], 'Admin.Actions'),
                                    'confirm_button_label' => $this->trans('Delete', [], 'Admin.Actions'),
                                    'confirm_button_class' => 'btn-danger',
                                    'close_button_label' => $this->trans('Cancel', [], 'Admin.Actions'),
                                ]),
                            ])
                        )
                ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id', NumberType::class))
                ->setAssociatedColumn('id')
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('ID', [], 'Modules.Drsoftfrvalidatecustomerpro.Admin'),
                    ],
                ])
            )
            ->add(
                (new Filter('id_customer', NumberType::class))
                    ->setAssociatedColumn('id_customer')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search by customer ID', [], 'Modules.Drsoftfrvalidatecustomerpro.Admin'),
                        ],
                    ])
            )
            ->add(
                (new Filter('active', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('active')
            )
            ->add(
                (new Filter('date_add', DateRangeType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('date_add')
            )
            ->add(
                (new Filter('date_upd', DateRangeType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('date_upd')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search_by_filter_id',
                    'reset_route_params' => [
                        'filterId' => self::GRID_ID,
                    ],
                    'redirect_route' => 'admin_drsoft_fr_validate_customer_pro_adapter_customer_index',
                ])
                ->setAssociatedColumn('actions')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions(): BulkActionCollection
    {
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('enable_bulk'))
                ->setName($this->trans('Enable selected', [], 'Modules.Drsoftfrvalidatecustomerpro.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_drsoft_fr_validate_customer_pro_adapter_customer_bulk_enable',
                    'confirm_message' => $this->trans('Enable selected items?', [], 'Modules.Drsoftfrvalidatecustomerpro.Warning'),
                    'modal_options' => new ModalOptions([
                        'title' => $this->trans('Enable selection', [], 'Modules.Drsoftfrvalidatecustomerpro.Actions'),
                        'confirm_button_label' => $this->trans('Enable', [], 'Modules.Drsoftfrvalidatecustomerpro.Actions'),
                        'confirm_button_class' => 'btn-success',
                        'close_button_label' => $this->trans('Cancel', [], 'Admin.Actions'),
                    ]),
                ])
            )
            ->add((new SubmitBulkAction('delete_bulk'))
                ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_drsoft_fr_validate_customer_pro_adapter_customer_bulk_delete',
                    'confirm_message' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                    'modal_options' => new ModalOptions([
                        'title' => $this->trans('Delete selection', [], 'Modules.Drsoftfrvalidatecustomerpro.Actions'),
                        'confirm_button_label' => $this->trans('Delete', [], 'Modules.Drsoftfrvalidatecustomerpro.Actions'),
                        'confirm_button_class' => 'btn-danger',
                        'close_button_label' => $this->trans('Cancel', [], 'Admin.Actions'),
                    ]),
                ])
            )
            ->add((new SubmitBulkAction('disable_bulk'))
                ->setName($this->trans('Disable selected', [], 'Modules.Drsoftfrvalidatecustomerpro.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_drsoft_fr_validate_customer_pro_adapter_customer_bulk_disable',
                    'confirm_message' => $this->trans('Disable selected items?', [], 'Modules.Drsoftfrvalidatecustomerpro.Warning'),
                    'modal_options' => new ModalOptions([
                        'title' => $this->trans('Disable selection', [], 'Modules.Drsoftfrvalidatecustomerpro.Actions'),
                        'confirm_button_label' => $this->trans('Disable', [], 'Modules.Drsoftfrvalidatecustomerpro.Actions'),
                        'confirm_button_class' => 'btn-warning',
                        'close_button_label' => $this->trans('Cancel', [], 'Admin.Actions'),
                    ]),
                ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new LinkGridAction('export'))
                    ->setName($this->trans('Export', [], 'Admin.Actions'))
                    ->setIcon('cloud_download')
                    ->setOptions([
                        'route' => 'admin_drsoft_fr_validate_customer_pro_adapter_customer_export',
                    ])
            )
            ->add((new SimpleGridAction('common_refresh_list'))
                ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                ->setIcon('refresh')
            )
            ->add((new SimpleGridAction('common_show_query'))
                ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                ->setIcon('code')
            )
            ->add((new SimpleGridAction('common_export_sql_manager'))
                ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                ->setIcon('storage')
            );
    }
}
