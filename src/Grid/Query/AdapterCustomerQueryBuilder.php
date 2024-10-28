<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use DrSoftFr\PrestaShopModuleHelper\Traits\QueryBuilderFiltersPreparerTrait;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class AdapterCustomerQueryBuilder builds search & count queries for AdapterCustomer grid.
 */
final class AdapterCustomerQueryBuilder extends AbstractDoctrineQueryBuilder
{
    use QueryBuilderFiltersPreparerTrait;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     */
    public function __construct(
        Connection                                $connection,
        string                                    $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    )
    {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria);

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getQueryBuilder($searchCriteria)
            ->select('COUNT(DISTINCT dfrvcpac.`id`)');
    }

    /**
     * Get generic query builder.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     *
     * @throws Exception
     */
    private function getQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->select(['dfrvcpac.*'])
            ->from($this->dbPrefix . 'drsoft_fr_validate_customer_pro_adapter_customer', 'dfrvcpac');

        $this->applyFilters($qb, $searchCriteria->getFilters());

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $filters
     *
     * @return void
     *
     * @throws Exception
     */
    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        $allowedFilters = [
            'id' => [
                'alias' => 'dfrvcpac',
                'operator' => '=',
                'type' => 'INT'
            ],
            'active' => [
                'alias' => 'dfrvcpac',
                'operator' => '=',
                'type' => 'INT'
            ],
            'id_customer' => [
                'alias' => 'dfrvcpac',
                'operator' => '=',
                'type' => 'INT'
            ],
            'date_add' => [
                'alias' => 'dfrvcpac',
                'operator' => null,
                'type' => 'DATE'
            ],
            'date_upd' => [
                'alias' => 'dfrvcpac',
                'operator' => null,
                'type' => 'DATE'
            ],
        ];

        $this->handle($qb, $filters, $allowedFilters);
    }
}
