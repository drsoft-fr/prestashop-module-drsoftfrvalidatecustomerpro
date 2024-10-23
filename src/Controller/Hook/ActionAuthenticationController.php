<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Controller\Hook;

use Customer;
use DrSoftFr\Module\ValidateCustomerPro\Config;
use DrSoftFr\Module\ValidateCustomerPro\Entity\AdapterCustomer;
use DrSoftFr\Module\ValidateCustomerPro\Exception\AdapterCustomer\AdapterCustomerNotFoundException;
use DrSoftFr\Module\ValidateCustomerPro\Repository\AdapterCustomerRepository;
use DrSoftFr\PrestaShopModuleHelper\Controller\Hook\AbstractHookController;
use DrSoftFr\PrestaShopModuleHelper\Controller\Hook\HookControllerInterface;
use Exception;
use Throwable;
use Tools;

final class ActionAuthenticationController extends AbstractHookController implements HookControllerInterface
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var int $langId
     */
    private $langId;

    /**
     * @var array $settings
     */
    private $settings;

    /**
     * Checks if the data is valid.
     *
     * @return bool True if the data is valid, false otherwise.
     *
     * @throws Exception
     */
    private function checkData(): bool
    {
        if (
            empty($this->getContext()->customer)
        ) {
            return false;
        }

        if (!($this->getContext()->customer instanceof Customer)) {
            return false;
        }

        if (0 >= (int)$this->getContext()->customer->id) {
            return false;
        }

        return true;
    }

    /**
     * Handle the customer logout process.
     *
     * Logs out the current customer, handles alerts and redirection based on module settings.
     *
     * @param string $alert The alert message to be displayed upon logout.
     *
     * @return void
     *
     * @throws Exception
     */
    private function handleCustomerLogout(string $alert): void
    {
        $this->customer->logout();

        if (true === $this->settings['enable_unapproved_customer_alert']) {
            $this->getContext()->controller->errors[] = $alert;
        }

        if (!empty($this->settings['cms_not_activated_id'])) {
            Tools::Redirect(
                $this->getContext()
                    ->link
                    ->getCMSLink(
                        (int)$this->settings['cms_not_activated_id'],
                        null,
                        null,
                        $this->langId
                    )
            );
        }
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

        $this->logger->error($errorMessage, [
            'error_code' => $t->getCode(),
            'object_type' => null,
            'object_id' => null,
            'allow_duplicate' => false,
        ]);
    }

    /**
     * Handles the manual validation of a customer account.
     *
     * - If the customer's SIRET is empty or manual validation is disabled, exits early.
     * - Retrieves the customer object using the AdapterCustomerRepository.
     * - Throws an AdapterCustomerNotFoundException if the customer object is not found.
     * - Checks if the customer is active and handles the logout if not active.
     *
     * @return void
     *
     * @throws AdapterCustomerNotFoundException
     * @throws Exception
     */
    private function handleManualValidationAccount(): void
    {
        if (empty($this->customer->siret)) {
            return;
        }

        if (false === $this->settings['enable_manual_validation_account']) {
            return;
        }

        /** @var AdapterCustomerRepository $repository */
        $repository = $this->module->get('drsoft_fr.module.validate_customer_pro.repository.adapter_customer_repository');

        /** @var AdapterCustomer $obj */
        $obj = $repository->findOneBy([
            'idCustomer' => (int)$this->customer->id
        ]);

        if (null === $obj) {
            throw new AdapterCustomerNotFoundException(sprintf(
                    'Manufacturer with id_customer "%d" was not found',
                (int)$this->customer->id
                )
            );
        }

        if (false === $obj->isActive()) {
            $alert = $this->getContext()->getTranslator()->trans(
                'Your customer account has not yet been validated by our teams.',
                [],
                'Modules.Drsoftfrvalidatecustomerpro.Error'
            );

            $this->handleCustomerLogout($alert);
        }
    }

    /**
     * Runs the execution of the method, handling exceptions and logging errors if necessary.
     *
     * @return void
     */
    public function run(): void
    {
        try {
            $this->settings = $this->module->get(Config::SETTING_PROVIDER_SERVICE);

            if (false === $this->settings['active']) {
                return;
            }

            if (false === $this->checkData()) {
                return;
            }

            $this->customer = $this->getContext()->customer;
            $this->langId = (int)$this->getContext()->language->id;

            $this->handleManualValidationAccount();
        } catch (Throwable $t) {
            $this->handleException($t);
        }
    }
}
