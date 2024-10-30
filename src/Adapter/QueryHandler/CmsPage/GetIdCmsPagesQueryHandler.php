<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Adapter\QueryHandler\CmsPage;

use Doctrine\DBAL\Driver\Connection;
use DrSoftFr\Module\ValidateCustomerPro\Query\CmsPage\GetIdCmsPagesQuery;
use PDO;

/**
 * Handles the query GetIdCmsPagesQuery
 */
final class GetIdCmsPagesQueryHandler
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
     * Handle GetIdCmsPagesQuery
     *
     * @param GetIdCmsPagesQuery $query
     *
     * @return int[]
     */
    public function handle(GetIdCmsPagesQuery $query): array
    {
        return $this->getData(
            $query->getIdShop()
        );
    }

    /**
     * Get IDs from the cms table.
     *
     * @param int|null $shopId The shop ID. Defaults to null.
     *
     * @return int[] The fetched data.
     */
    private function getData(?int $shopId): array
    {
        $join = '';

        if (null !== $shopId) {
            $join = ' INNER JOIN {table_prefix}cms_shop cs ON (cs.id_cms = c.id_cms AND cs.id_shop = :shop_id)';
        }

        $query = str_replace(
            '{table_prefix}',
            $this->tablePrefix,
            'SELECT
            c.id_cms
            FROM {table_prefix}cms c' . $join . ';'
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
