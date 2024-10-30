<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Exception\Customer;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;

/**
 * Is thrown when Customer constraints are violated
 */
class CustomerConstraintException extends CustomerException
{
    /**
     * When invalid null or non negative int value.
     */
    public const INVALID_NULL_OR_NON_NEGATIVE_INT = 10;
}
