<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Exception\Customer;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;

/**
 * Throw when the identifier does not correspond to an existing customer.
 */
class NonexistentCustomerIdException extends CustomerException
{
}
