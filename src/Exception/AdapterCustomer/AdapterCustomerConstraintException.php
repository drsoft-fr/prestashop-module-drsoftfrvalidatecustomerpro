<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Exception\AdapterCustomer;

/**
 * Is thrown when AdapterCustomer constraints are violated
 */
class AdapterCustomerConstraintException extends AdapterCustomerException
{
    /**
     * When ID field is invalid.
     */
    public const INVALID_ID = 10;

    /**
     * When id_customer field is invalid.
     */
    public const INVALID_ID_CUSTOMER = 20;

    /**
     * When active field is invalid.
     */
    public const INVALID_ACTIVE = 30;

    /**
     * When date_add field is invalid.
     */
    public const INVALID_DATE_ADD = 40;

    /**
     * When date_upd field is invalid.
     */
    public const INVALID_DATE_UPD = 50;
}
