<?php

declare(strict_types=1);

use DrSoftFr\Module\ValidateCustomerPro\Config;
use DrSoftFr\Module\ValidateCustomerPro\Controller\Admin\AdapterCustomerController;
use DrSoftFr\Module\ValidateCustomerPro\Controller\Admin\IndexController;
use DrSoftFr\Module\ValidateCustomerPro\Controller\Admin\SettingController;
use DrSoftFr\Module\ValidateCustomerPro\Controller\Hook\ActionAuthenticationController;
use DrSoftFr\Module\ValidateCustomerPro\Controller\Hook\ActionCustomerAccountAddController;
use DrSoftFr\Module\ValidateCustomerPro\Controller\Hook\ActionFrontControllerSetVariablesController;
use DrSoftFr\Module\ValidateCustomerPro\Controller\Hook\ActionListMailThemesController;
use DrSoftFr\Module\ValidateCustomerPro\Controller\Hook\ActionObjectCustomerDeleteAfterController;
use DrSoftFr\Module\ValidateCustomerPro\Controller\Hook\ActionObjectUpdateAfterController;
use DrSoftFr\Module\ValidateCustomerPro\Install\Factory\InstallerFactory;
use DrSoftFr\Module\ValidateCustomerPro\Install\Installer;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerChain;

if (!defined('_PS_VERSION_') || !defined('_CAN_LOAD_FILES_')) {
    exit;
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

// TODO continuer l'ajout de fonctionnalité afin de renseigner l'adresse lors de la creation du compte

/**
 * Class drsoftfrvalidatecustomerpro
 */
class drsoftfrvalidatecustomerpro extends Module
{
    /**
     * @var string $authorEmail Author email
     */
    public $authorEmail;

    /**
     * @var string $moduleGithubRepositoryUrl Module GitHub repository URL
     */
    public $moduleGithubRepositoryUrl;

    /**
     * @var string $moduleGithubIssuesUrl Module GitHub issues URL
     */
    public $moduleGithubIssuesUrl;

    /**
     * @var bool $isPsVersion8 Indicates whether the version of PrestaShop is 8 or not
     */
    public $isPsVersion8;

    public function __construct()
    {
        $this->author = 'drSoft.fr';
        $this->bootstrap = true;
        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Drsoftfrvalidatecustomerpro.Admin');
        $this->dependencies = [];
        $this->description = $this->trans('Validate customer at registration and assign groups automatically.', [], 'Modules.Drsoftfrvalidatecustomerpro.Admin');
        $this->displayName = $this->trans('drSoft.fr Validate customer Pro', [], 'Modules.Drsoftfrvalidatecustomerpro.Admin');
        $this->name = 'drsoftfrvalidatecustomerpro';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.8',
            'max' => _PS_VERSION_
        ];
        $this->tab = 'administration';
        $this->tabs = [
            [
                'class_name' => IndexController::TAB_CLASS_NAME,
                'name' => 'Validate Customer Pro',
                'parent_class_name' => 'AdminParentCustomer',
                'route_name' => 'admin_drsoft_fr_validate_customer_pro_index',
                'visible' => true,
            ],
            [
                'class_name' => AdapterCustomerController::TAB_CLASS_NAME,
                'name' => 'Customer',
                'parent_class_name' => IndexController::TAB_CLASS_NAME,
                'visible' => false,
            ],
            [
                'class_name' => SettingController::TAB_CLASS_NAME,
                'name' => 'Setting',
                'parent_class_name' => IndexController::TAB_CLASS_NAME,
                'visible' => false,
            ],
        ];
        $this->version = '0.0.1';
        $this->authorEmail = 'contact@drsoft.fr';
        $this->moduleGithubRepositoryUrl = 'https://github.com/drsoft-fr/prestashop-module-drsoftfrvalidatecustomerpro';
        $this->moduleGithubIssuesUrl = 'https://github.com/drsoft-fr/prestashop-module-drsoftfrvalidatecustomerpro/issues';
        $this->isPsVersion8 = (bool)version_compare(_PS_VERSION_, '8.0', '>=');

        parent::__construct();
    }

    /**
     * Disables the module.
     *
     * @param bool $force_all Whether to disable all instances of the module, even if they are currently enabled.
     *
     * @return bool Whether the module was disabled successfully.
     */
    public function disable($force_all = false)
    {
        if (!parent::disable($force_all)) {
            $this->handleException(
                new Exception(
                    $this->trans(
                        'An error has occurred when deactivating the module.',
                        [],
                        'Modules.Drsoftfrvalidatecustomerpro.Error'
                    )
                )
            );

            return false;
        }

        try {
            $this->getCacheClearerChain()->clear();
        } catch (Throwable $t) {
            $this->handleException($t);
        }

        return true;
    }

    /**
     * Enables the module by clearing the cache and calling the parent's enable method.
     *
     * @param bool $force_all Whether to force the enabling of all modules.
     *
     * @return bool True on successful enable, false otherwise.
     */
    public function enable($force_all = false)
    {
        if (!parent::enable($force_all)) {
            $this->handleException(
                new Exception(
                    $this->trans(
                        'An error has occurred when activating the module.',
                        [],
                        'Modules.Drsoftfrvalidatecustomerpro.Error'
                    )
                )
            );

            return false;
        }

        try {
            $this->getCacheClearerChain()->clear();
        } catch (Throwable $t) {
            $this->handleException($t);
        }

        return true;
    }

    /**
     * Get the CacheClearerChain.
     *
     * @return CacheClearerChain
     *
     * @throws Exception
     */
    private function getCacheClearerChain(): CacheClearerChain
    {
        $cacheClearerChain = $this->get('prestashop.core.cache.clearer.cache_clearer_chain');

        if (!($cacheClearerChain instanceof CacheClearerChain)) {
            throw new Exception('The cacheClearerChain object must implement CacheClearerChain.');
        }

        return $cacheClearerChain;
    }

    /**
     * Redirects the admin user to the ValidateCustomerPro controller in the admin panel.
     *
     * @return void
     */
    public function getContent(): void
    {
        Tools::redirectAdmin(
            $this->context->link->getAdminLink(IndexController::TAB_CLASS_NAME)
        );
    }

    /**
     * Handles an exception by logging an error message.
     *
     * @param Throwable $t The exception to handle.
     *
     * @return void
     */
    private function handleException(Throwable $t): void
    {
        $errorMessage = Config::createErrorMessage(__METHOD__, __LINE__, $t);

        PrestaShopLogger::addLog($errorMessage, 3);

        $this->_errors[] = $errorMessage;
    }

    /**
     * @param array $p
     *
     * @return void
     */
    public function hookActionAuthentication(array $p = []): void
    {
        $file = _PS_MODULE_DIR_ . $this->name . '/' . $this->name . '.php';
        $controller = new ActionAuthenticationController($this, $file, $this->_path, $p);

        $controller->run();
    }

    /**
     * @param array $p
     *
     * @return void
     */
    public function hookActionCustomerAccountAdd(array $p = []): void
    {
        $file = _PS_MODULE_DIR_ . $this->name . '/' . $this->name . '.php';
        $controller = new ActionCustomerAccountAddController($this, $file, $this->_path, $p);

        $controller->run();
    }

    /**
     * @param array $p
     *
     * @return array
     */
    public function hookActionFrontControllerSetVariables(array $p = []): array
    {
        $file = _PS_MODULE_DIR_ . $this->name . '/' . $this->name . '.php';
        $controller = new ActionFrontControllerSetVariablesController($this, $file, $this->_path, $p);

        return $controller->run();
    }

    /**
     * This hook allows you to add mail layout.
     *
     * @param array $p
     *
     * @return void
     */
    public function hookActionListMailThemes(array $p = []): void
    {
        $file = _PS_MODULE_DIR_ . $this->name . '/' . $this->name . '.php';
        $controller = new ActionListMailThemesController($this, $file, $this->_path, $p);

        $controller->run();
    }

    /**
     * @param array $p
     *
     * @return bool
     */
    public function hookActionObjectCustomerDeleteAfter(array $p = []): bool
    {
        $file = _PS_MODULE_DIR_ . $this->name . '/' . $this->name . '.php';
        $controller = new ActionObjectCustomerDeleteAfterController($this, $file, $this->_path, $p);

        return $controller->run();
    }

    /**
     * @param array $p
     *
     * @return bool
     */
    public function hookActionObjectUpdateAfter(array $p = []): bool
    {
        $file = _PS_MODULE_DIR_ . $this->name . '/' . $this->name . '.php';
        $controller = new ActionObjectUpdateAfterController($this, $file, $this->_path, $p);

        return $controller->run();
    }

    /**
     * Installs the module
     *
     * @return bool Returns true if the installation is successful, false otherwise.
     */
    public function install(): bool
    {
        if (!parent::install()) {
            $this->handleException(
                new Exception(
                    $this->trans(
                        'There was an error during the installation.',
                        [],
                        'Modules.Drsoftfrvalidatecustomerpro.Error'
                    )
                )
            );

            return false;
        }

        try {
            $installer = InstallerFactory::create();

            $installer->install($this);
        } catch (Throwable $t) {
            $this->handleException($t);

            return false;
        }

        try {
            $this->getCacheClearerChain()->clear();
        } catch (Throwable $t) {
            $this->handleException($t);
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    /**
     * Uninstalls the module
     *
     * @return bool Returns true if uninstallation was successful, false otherwise
     */
    public function uninstall(): bool
    {
        try {
            /** @var Installer $installer */
            $installer = $this->get(Config::INSTALLER_SERVICE);

            $installer->uninstall($this);
        } catch (Throwable $t) {
            $this->handleException($t);

            return false;
        }

        if (!parent::uninstall()) {
            $this->handleException(
                new Exception(
                    $this->trans(
                        'There was an error during the uninstallation.',
                        [],
                        'Modules.Drsoftfrvalidatecustomerpro.Error'
                    )
                )
            );

            return false;
        }

        try {
            $this->getCacheClearerChain()->clear();
        } catch (Throwable $t) {
            $this->handleException($t);
        }

        return true;
    }
}
