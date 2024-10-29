<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Install\Factory;

use DrSoftFr\Module\ValidateCustomerPro\Data\Configuration\SettingConfiguration;
use DrSoftFr\Module\ValidateCustomerPro\Install\FixturesInstaller;
use DrSoftFr\Module\ValidateCustomerPro\Install\Installer;
use PrestaShop\PrestaShop\Adapter\Configuration;

/**
 * The InstallerFactory class is responsible for creating instances of the Installer class.
 */
final class InstallerFactory
{
    /**
     * Create a new Installer instance with a FixturesInstaller and SettingConfiguration.
     *
     * @return Installer A new instance of the Installer class.
     */
    public static function create(): Installer
    {
        return new Installer(
            new FixturesInstaller(),
            new SettingConfiguration(
                new Configuration()
            ));
    }
}
