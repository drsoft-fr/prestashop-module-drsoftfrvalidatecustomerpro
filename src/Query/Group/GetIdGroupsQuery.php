<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Query\Group;

use DrSoftFr\Module\ValidateCustomerPro\Exception\Group\GroupConstraintException;

/**
 * Get all id_group's
 */
final class GetIdGroupsQuery
{
    /**
     * @var int|null
     */
    private $idShop;

    /**
     * Class constructor.
     *
     * @param int|null $idShop The ID of the shop (optional).
     *
     * @throws GroupConstraintException
     */
    public function __construct(
        int $idShop = null
    )
    {
        $this->assertIsNullOrPositiveInt($idShop);
        $this->idShop = $idShop;
    }

    /**
     * @return int|null
     */
    public function getIdShop(): ?int
    {
        return $this->idShop;
    }

    /**
     * @param mixed $value
     *
     * @throws GroupConstraintException
     */
    private function assertIsNullOrPositiveInt($value)
    {
        if (null === $value || (is_int($value) && 0 < $value)) {
            return;
        }

        throw new GroupConstraintException(
            sprintf(
                'Invalid id "%s" provided.',
                var_export(
                    $value, true
                )
            ),
            GroupConstraintException::INVALID_NULL_OR_NON_NEGATIVE_INT
        );
    }
}
