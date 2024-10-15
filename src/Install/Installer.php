<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Install;

use Exception;
use Module;

/**
 * Class responsible for modifications needed during installation/uninstallation of the module.
 */
final class Installer
{
    const HOOKS = [];

    public function __construct()
    {
    }

    /**
     * Module's installation entry point.
     *
     * @param Module $module The module to install.
     *
     * @return bool True if the module is successfully installed.
     *
     * @throws Exception If an error occurs during the installation process.
     */
    public function install(Module $module): bool
    {
        if (!$this->registerHooks($module)) {
            throw new Exception('An error occurred when registering hooks for the module.');
        }

        return true;
    }

    /**
     * Module's uninstallation entry point.
     *
     * @return bool True if the module is successfully uninstalled
     *
     * @throws Exception if an error occurs when deleting the module parameters.
     */
    public function uninstall(): bool
    {
        return true;
    }

    /**
     * Register hooks for the module.
     *
     * @param Module $module
     *
     * @return bool
     */
    private function registerHooks(Module $module): bool
    {
        return (bool)$module->registerHook(self::HOOKS);
    }
}
