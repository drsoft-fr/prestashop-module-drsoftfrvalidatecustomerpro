<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Install;

use DrSoftFr\Module\ValidateCustomerPro\Data\Configuration\SettingConfiguration;
use DrSoftFr\PrestaShopModuleHelper\Traits\ExecuteSqlFromFileTrait;
use Exception;
use Module;
use Throwable;

/**
 * Class responsible for modifications needed during installation/uninstallation of the module.
 */
final class Installer
{
    use executeSqlFromFileTrait;

    const HOOKS = [
        'actionAuthentication',
        'actionCustomerAccountAdd',
        'actionFrontControllerSetVariables',
        'actionListMailThemes',
        'actionObjectCustomerDeleteAfter',
        'actionObjectUpdateAfter',
        'displayCustomerAccountForm',
    ];

    /**
     * @var FixturesInstaller
     */
    private $fixturesInstaller;

    /**
     * @var SettingConfiguration
     */
    private $settingConfiguration;

    public function __construct(
        FixturesInstaller    $fixturesInstaller,
        SettingConfiguration $settingConfiguration
    )
    {
        $this->fixturesInstaller = $fixturesInstaller;
        $this->settingConfiguration = $settingConfiguration;
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

        if (!$this->executeSqlFromFile($module->getLocalPath() . 'src/Install/Resources/sql/install.sql')) {
            throw new Exception('An error has occurred while executing the installation SQL resources.');
        }

        $fixturesConfiguration = $this->fixturesInstaller->install();
        $configuration = array_merge($this->settingConfiguration::CONFIGURATION_DEFAULT_VALUES, $fixturesConfiguration);

        $this->settingConfiguration->updateConfiguration($configuration);

        return true;
    }

    /**
     * Module's uninstallation entry point.
     *
     * @param Module $module The module to uninstall.
     *
     * @return bool True if the module is successfully uninstalled
     *
     * @throws Exception if an error occurs when deleting the module parameters.
     */
    public function uninstall(Module $module): bool
    {
        if (!$this->executeSqlFromFile($module->getLocalPath() . 'src/Install/Resources/sql/uninstall.sql')) {
            throw new Exception('Unable to uninstall sql resources from module.');
        }

        try {
            $this->settingConfiguration->removeConfiguration();
        } catch (Throwable $t) {
            throw new Exception('An error occurred when deleting the module parameters.');
        }

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

    /**
     * @return FixturesInstaller
     */
    public function getFixturesInstaller(): FixturesInstaller
    {
        return $this->fixturesInstaller;
    }

    /**
     * @return SettingConfiguration
     */
    public function getSettingConfiguration(): SettingConfiguration
    {
        return $this->settingConfiguration;
    }
}
