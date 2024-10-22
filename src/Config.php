<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro;

use Throwable;

/**
 * Class Config
 *
 * This class contains constants and a method to generate error messages.
 */
final class Config
{
    const ERROR_MESSAGE_PATTERN = 'drsoftfrvalidatecustomerpro - %s - %d - Throwable #%d - %s.';

    const INSTALLER_SERVICE = 'drsoft_fr.module.validate_customer_pro.install.installer';

    const SETTING_PROVIDER_SERVICE = 'drsoft_fr.module.validate_customer_pro.data.validate_customer_pro_data_provider';

    /**
     * Creates an error message using the given method, line number and throwable object.
     *
     * @param string $method The name of the method where the error occurred.
     * @param int $line The line number where the error occurred.
     * @param Throwable $t The throwable object representing the error.
     *
     * @return string The formatted error message.
     */
    static public function createErrorMessage(string $method, int $line, Throwable $t): string
    {
        return sprintf(self::ERROR_MESSAGE_PATTERN, $method, $line, $t->getCode(), $t->getMessage());
    }
}
