<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Controller\Hook;

use Customer;
use Doctrine\ORM\EntityManagerInterface;
use DrSoftFr\Module\ValidateCustomerPro\Config;
use DrSoftFr\Module\ValidateCustomerPro\Entity\AdapterCustomer;
use DrSoftFr\Module\ValidateCustomerPro\Repository\AdapterCustomerRepository;
use DrSoftFr\PrestaShopModuleHelper\Controller\Hook\AbstractHookController;
use DrSoftFr\PrestaShopModuleHelper\Controller\Hook\HookControllerInterface;
use Exception;
use Language;
use Mail;
use PrestaShopDatabaseException;
use PrestaShopException;
use Throwable;
use Validate;

final class ActionObjectUpdateAfterController extends AbstractHookController implements HookControllerInterface
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
     * Checks if the object is valid.
     *
     * @return bool True if the object is valid, false otherwise.
     *
     * @throws Exception
     */
    private function checkObject(): bool
    {
        if (empty($this->props['object'])) {
            return false;
        }

        if (!($this->props['object'] instanceof Customer)) {
            return false;
        }

        return true;
    }

    /**
     * Checks the Siret data for validity.
     *
     * Ensures that the Siret data is not empty and is a valid Siret number.
     *
     * @return bool Returns true if the Siret data is valid, otherwise throws an Exception.
     *
     * @throws Exception
     */
    private function checkSiretData(): bool
    {
        if (empty($this->customer->siret)) {
            return false;
        }

        if (!Validate::isSiret($this->customer->siret)) {
            throw new Exception('Siret is not valid.');
        }

        return true;
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
     * Handles validation logic for a customer and saves the changes to the database.
     *
     * @return bool Returns true if the validation was successful, otherwise false.
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws Exception
     */
    private function handleValidationCustomer(): bool
    {
        /** @var EntityManagerInterface $em */
        $em = $this->module->get('doctrine.orm.entity_manager');

        /** @var AdapterCustomerRepository $repository */
        $repository = $this->module->get('drsoft_fr.module.validate_customer_pro.repository.adapter_customer_repository');

        /** @var AdapterCustomer $obj */
        $obj = $repository->findOneBy([
            'idCustomer' => $this->customer->id
        ]);

        if (null === $obj) {
            if (false === $this->checkSiretData()) {
                return true;
            }

            $obj = new AdapterCustomer();

            $obj->setIdCustomer($this->customer->id);
            $em->persist($obj);
        }

        $oldActivation = $obj->isActive();
        $obj->setActive((bool)$this->customer->active);
        $em->flush();

        if (false === $this->settings['enable_email_approval']) {
            return true;
        }

        if (
            false === $oldActivation &&
            true === $obj->isActive()
        ) {
            $language = new Language($this->langId);
            $shopId = (int)$this->getContext()->shop->id;

            $mailVars = [
                '{new_customer_id}' => $this->customer->id,
                '{new_customer_last_name}' => $this->customer->lastname,
                '{new_customer_first_name}' => $this->customer->firstname,
                '{new_customer_email}' => $this->customer->email,
                '{new_customer_link}' => $this->getContext()->link->getPageLink('my-account'),
            ];


            if (empty($this->customer->email)) {
                throw new Exception('Action customer account add email is not defined');
            }

            $result = Mail::send(
                $this->langId,
                'validate_account',
                $this->getContext()->getTranslator()->trans(
                    'Your account has been activated !',
                    [],
                    'Modules.Drsoftfrvalidatecustomerpro.Email',
                    $language->locale
                ),
                $mailVars,
                $this->customer->email,
                (string)$this->customer->firstname . ' ' . (string)$this->customer->lastname,
                null,
                null,
                null,
                null,
                _PS_MODULE_DIR_ . $this->module->name . '/mails/',
                false,
                $shopId
            );

            if (!$result) {
                throw new Exception('An error occurred while sending the account activation e-mail.');
            }
        }

        return true;
    }


    public function run(): bool
    {
        try {
            if (false === $this->checkObject()) {
                return true;
            }

            $this->settings = $this->module->get(Config::SETTING_PROVIDER_SERVICE);

            if (false === $this->settings['active']) {
                return true;
            }

            $this->langId = (int)$this->getContext()->language->id;
            $this->customer = $this->props['object'];

            return $this->handleValidationCustomer();
        } catch (Throwable $t) {
            $this->handleException($t);

            return false;
        }
    }
}
