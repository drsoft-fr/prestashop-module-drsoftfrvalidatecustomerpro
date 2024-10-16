<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Adapter\QueryHandler\Group;

use Doctrine\DBAL\Driver\Connection;
use DrSoftFr\Module\ValidateCustomerPro\Query\Group\GetIdGroupsQuery;
use PDO;

/**
 * Handles the query GetIdGroupsQuery
 */
final class GetIdGroupsQueryHandler
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tablePrefix;

    /**
     * @param Connection $connection
     * @param string $tablePrefix
     */
    public function __construct(
        Connection $connection,
        string     $tablePrefix
    )
    {
        $this->connection = $connection;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * Handle GetIdGroupsQuery
     *
     * @param GetIdGroupsQuery $query
     *
     * @return int[]
     */
    public function handle(GetIdGroupsQuery $query): array
    {
        return $this->getData(
            $query->getIdShop()
        );
    }

    /**
     * Get IDs from the group table.
     *
     * @param int|null $shopId The shop ID. Defaults to null.
     *
     * @return int[] The fetched data.
     */
    private function getData(?int $shopId): array
    {
        $join = '';

        if (null !== $shopId) {
            $join = ' INNER JOIN {table_prefix}group_shop gs ON (gs.id_group = g.id_group AND gs.id_shop = :shop_id)';
        }

        $query = str_replace(
            '{table_prefix}',
            $this->tablePrefix,
            'SELECT
            g.id_group
            FROM `{table_prefix}group` g' . $join . ';'
        );

        $stmt = $this->connection->prepare($query);

        if (null !== $shopId) {
            $stmt->bindValue('shop_id', $shopId);
        }

        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $a = [];

        foreach ($rows as $row) {
            $a[] = (int)$row;
        }

        return $a;
    }
}
