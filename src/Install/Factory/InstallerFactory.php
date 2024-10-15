<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Install\Factory;

use DrSoftFr\Module\ValidateCustomerPro\Install\Installer;

/**
 * The InstallerFactory class is responsible for creating instances of the Installer class.
 */
final class InstallerFactory
{
    public static function create(): Installer
    {
        return new Installer();
    }
}
