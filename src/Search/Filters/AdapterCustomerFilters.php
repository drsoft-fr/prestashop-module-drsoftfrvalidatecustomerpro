<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Search\Filters;

use DrSoftFr\Module\ValidateCustomerPro\Grid\Definition\Factory\AdapterCustomerGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class AdapterCustomerFilters is responsible for providing default filters for AdapterCustomer grid.
 */
final class AdapterCustomerFilters extends Filters
{
    protected $filterId = AdapterCustomerGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
