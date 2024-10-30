<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Query\Customer;

use DrSoftFr\Module\ValidateCustomerPro\Exception\Customer\CustomerConstraintException;

/**
 * Get all id_customer's
 */
final class GetIdCustomersQuery
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
     * @throws CustomerConstraintException
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
     * @throws CustomerConstraintException
     */
    private function assertIsNullOrPositiveInt($value)
    {
        if (null === $value || (is_int($value) && 0 < $value)) {
            return;
        }

        throw new CustomerConstraintException(
            sprintf(
                'Invalid id "%s" provided.',
                var_export(
                    $value, true
                )
            ),
            CustomerConstraintException::INVALID_NULL_OR_NON_NEGATIVE_INT
        );
    }
}
