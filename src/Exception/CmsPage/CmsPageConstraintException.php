<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Exception\CmsPage;

/**
 * Is thrown when CmsPage constraints are violated
 */
class CmsPageConstraintException extends CmsPageException
{
    /**
     * When invalid null or non negative int value.
     */
    public const INVALID_NULL_OR_NON_NEGATIVE_INT = 10;
}
