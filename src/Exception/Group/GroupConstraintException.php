<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Exception\Group;

/**
 * Is thrown when Group constraints are violated
 */
class GroupConstraintException extends GroupException
{
    /**
     * When invalid null or non negative int value.
     */
    public const INVALID_NULL_OR_NON_NEGATIVE_INT = 10;
}
