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
use PrestaShopException;
use Throwable;
use Validate;

final class ActionCustomerAccountAddController extends AbstractHookController implements HookControllerInterface
{
    /**
     * @var Customer
     */
    private $customer;

    private $customerFields = [
        [
            'property' => 'id',
            'prefix' => '#',
            'suffix' => '',
        ],
        [
            'property' => 'firstname',
            'prefix' => ' ',
            'suffix' => '',
        ],
        [
            'property' => 'lastname',
            'prefix' => ' ',
            'suffix' => '',
        ],
        [
            'property' => 'email',
            'prefix' => '<',
            'suffix' => '>',
        ],
    ];

    /**
     * @var bool $customerIsUpdated Flag indicating if customer information has been updated
     */
    private $customerIsUpdated = false;

    /**
     * @var int $langId
     */
    private $langId;

    /**
     * @var array $settings
     */
    private $settings;

    /**
     * Adds customer information to the given customer info string.
     *
     * @param Customer $customer The customer object.
     * @param string $property The name of the property to extract from the customer object.
     * @param string $customerInfo The customer info string to append the property value to.
     * @param string $prefix The prefix to prepend to the property value before appending it to the customer info string. Default is an empty string.
     * @param string $suffix The suffix to append to the property value before appending it to the customer info string. Default is an empty string.
     *
     * @return string    The updated customer info string.
     */
    private function addCustomerInfo(
        Customer $customer,
        string   $property,
        string   $customerInfo,
        string   $prefix = '',
        string   $suffix = ''
    ): string
    {
        if (empty($customer->$property)) {
            return $customerInfo;
        }

        $customerInfo .= $prefix . $customer->$property . $suffix;

        return $customerInfo;
    }

    /**
     * Checks data validity before processing.
     *
     * @return void
     *
     * @throws Exception
     */
    private function checkData(): void
    {
        if (empty($this->props['newCustomer'])) {
            throw new Exception('New customer is empty.');
        }

        if (!($this->props['newCustomer'] instanceof Customer)) {
            throw new Exception('New customer is not an instance of Customer');
        }
    }

    /**
     * Checks the Siret data for validity.
     *
     * Ensures that the Siret data is not empty and is a valid Siret number.
     *
     * @return bool Returns true if the Siret data is valid.
     */
    private function checkSiretData(): bool
    {
        if (empty($this->customer->siret)) {
            return false;
        }

        if (!Validate::isSiret($this->customer->siret)) {
            return false;
        }

        return true;
    }

    /**
     * Retrieves the customer data as a formatted string.
     *
     * @return string The customer data as a string.
     */
    private function getCustomerData(): string
    {
        $customerInfo = '';

        foreach ($this->customerFields as $field) {
            $customerInfo = $this
                ->addCustomerInfo(
                    $this->customer,
                    $field['property'],
                    $customerInfo,
                    $field['prefix'],
                    $field['suffix']
                );
        }

        return $customerInfo;
    }

    /**
     * Handles sending an admin email for new customer account registration.
     *
     * @param int $langId The language ID for the email.
     * @param Language $language The language object for translation.
     * @param array $mailVars An array of variables to be used in the email template.
     * @param int $shopId The ID of the shop for which the email is sent.
     *
     * @return ActionCustomerAccountAddController
     *
     * @throws Exception
     */
    private function handleAdminEmail(int $langId, Language $language, array $mailVars, int $shopId): ActionCustomerAccountAddController
    {
        if (false === $this->settings['admin_send_email_on_action_customer_account_add_hook']) {
            return $this;
        }

        if (empty($this->settings['admin_action_customer_account_add_email'])) {
            throw new Exception('Admin action customer account add email is not defined');
        }

        $result = Mail::send(
            $langId,
            'admin_pending_account',
            $this->getContext()->getTranslator()->trans(
                'New customer account to validate',
                [],
                'Modules.Drsoftfrvalidatecustomerpro.Email',
                $language->locale
            ),
            $mailVars,
            $this->settings['admin_action_customer_account_add_email'],
            null,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . $this->module->name . '/mails/',
            false,
            $shopId
        );

        if (!$result) {
            throw new Exception('Failure to send e-mail to the admin when registering a new customer.');
        }

        return $this;
    }

    /**
     * Handle the alert feature for the customer account.
     *
     * This method checks if manual validation of the account is enabled in the module settings.
     * If manual validation is enabled, it adds a success message to the controller with a confirmation message for the user.
     * If manual validation is not enabled, it simply returns the current instance of the class.
     *
     * @return ActionCustomerAccountAddController Returns the current instance of the class for method chaining.
     *
     * @throws Exception
     */
    private function handleAlert(): ActionCustomerAccountAddController
    {
        if (false === $this->settings['enable_manual_validation_account']) {
            return $this;
        }

        if (false === $this->settings['enable_unapproved_customer_alert']) {
            return $this;
        }

        $this->getContext()->controller->success[] = $this->getContext()->getTranslator()->trans(
            'Registration successfully. Your account need to be activated. You will receive a confirmation soon.',
            [],
            'Modules.Drsoftfrvalidatecustomerpro.Success'
        );

        return $this;
    }

    /**
     * Handles sending a customer email and throws exceptions if necessary.
     *
     * @param int $langId The language ID for email content.
     * @param Language $language The language object for the email template.
     * @param array $mailVars Array of mail variables.
     * @param int $shopId The shop ID for sending the email.
     *
     * @return ActionCustomerAccountAddController
     *
     * @throws Exception
     */
    private function handleCustomerEmail(int $langId, Language $language, array $mailVars, int $shopId): ActionCustomerAccountAddController
    {
        if (false === $this->settings['enable_email_pending_approval']) {
            return $this;
        }

        if (empty($this->customer->email)) {
            throw new Exception('Action customer account add email is not defined');
        }

        $result = Mail::send(
            $langId,
            'pending_account',
            $this->getContext()->getTranslator()->trans(
                'Customer account pending validation',
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
            throw new Exception('Failure to send e-mail when registering a new customer.');
        }

        return $this;
    }

    /**
     * Handles setting the default group for a customer and saves the customer group.
     *
     * @return ActionCustomerAccountAddController
     *
     * @throws Exception|PrestaShopException When the customer group id is not set or when unable to save the customer group.
     */
    private function handleCustomerGroup(): ActionCustomerAccountAddController
    {
        if (false === $this->settings['enable_auto_customer_group_selection']) {
            return $this;
        }

        if (empty($this->settings['customer_group_id'])) {
            throw new Exception('Customer group id must be set.');
        }

        $groupId = (int)$this->settings['customer_group_id'];
        $this->customer->id_default_group = $groupId;
        $this->customerIsUpdated = true;

        $this->customer->addGroups([$groupId]);

        return $this;
    }

    /**
     * Handles saving the Customer group if it has been updated.
     *
     * Checks if the customer group has been updated. If not updated, no action is taken.
     * If the customer group has been updated, attempts to save the Customer group.
     * Throws an exception if unable to save the Customer group.
     *
     * @return ActionCustomerAccountAddController
     *
     * @throws PrestaShopException
     * @throws Exception
     */
    private function handleCustomerSave(): ActionCustomerAccountAddController
    {
        if (false === $this->customerIsUpdated) {
            return $this;
        }

        if (!$this->customer->save()) {
            throw new Exception('Unable to save Customer group.');
        }

        return $this;
    }

    /**
     * Handles sending emails based on certain conditions.
     *
     * @return ActionCustomerAccountAddController
     *
     * @throws Exception
     */
    private function handleEmail(): ActionCustomerAccountAddController
    {
        // TODO il faut tester le fonctionnement des mails sur un vrai serveur.
        if (false === $this->settings['enable_manual_validation_account']) {
            return $this;
        }

        if (
            false === $this->settings['admin_send_email_on_action_customer_account_add_hook'] &&
            false === $this->settings['enable_email_pending_approval']
        ) {
            return $this;
        }

        $language = new Language($this->langId);
        $shopId = (int)$this->getContext()->shop->id;

        $mailVars = [
            '{new_customer_data}' => $this->getCustomerData(),
            '{new_customer_id}' => (int)$this->customer->id,
            '{new_customer_last_name}' => $this->customer->lastname,
            '{new_customer_first_name}' => $this->customer->firstname,
            '{new_customer_email}' => $this->customer->email,
        ];

        $this
            ->handleAdminEmail($this->langId, $language, $mailVars, $shopId)
            ->handleCustomerEmail($this->langId, $language, $mailVars, $shopId);

        return $this;
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
     * Handle the execution of the redirection process.
     *
     * This method checks if manual validation account is enabled in the settings.
     * If manual validation is not enabled or if the CMS notify ID is empty, the method stops execution and returns early.
     * Otherwise, it redirects the user to the CMS link based on the provided CMS notify ID.
     *
     * @return void
     *
     * @throws Exception
     */
    private function handleRedirection(): void
    {
        if (false === $this->settings['enable_manual_validation_account']) {
            return;
        }

        if (empty($this->settings['cms_notify_id'])) {
            return;
        }

        $this
            ->getContext()
            ->controller
            ->redirectWithNotifications(
                $this
                    ->getContext()
                    ->link
                    ->getCMSLink(
                        (int)$this->settings['cms_notify_id'],
                        null,
                        null,
                        $this->langId
                    )
            );
    }

    /**
     * Handle the validation of a customer account.
     *
     * This method performs various operations to validate a customer account,
     * including setting the customer as inactive, logging out the customer, updating customer status, and
     * creating or updating an AdapterCustomer entity in the database with the inactive status.
     * If the new customer account is not created successfully, it throws an Exception.
     *
     * @return ActionCustomerAccountAddController
     *
     * @throws Exception
     */
    private function handleValidationAccount(): ActionCustomerAccountAddController
    {
        if (false === $this->settings['enable_manual_validation_account']) {
            return $this;
        }

        $this->customer->active = 0;

        $this->customer->logout();

        $this->customerIsUpdated = true;

        /** @var EntityManagerInterface $em */
        $em = $this->module->get('doctrine.orm.entity_manager');

        /** @var AdapterCustomerRepository $repository */
        $repository = $this->module->get('drsoft_fr.module.validate_customer_pro.repository.adapter_customer_repository');

        /** @var AdapterCustomer $obj */
        $obj = $repository->findOneBy([
            'idCustomer' => (int)$this->customer->id
        ]);

        if (null === $obj) {
            $obj = new AdapterCustomer();

            $obj->setIdCustomer((int)$this->customer->id);
            $em->persist($obj);
        }

        $obj->setActive(false);
        $em->flush();

        if (empty($obj->getId())) {
            throw new Exception('New customer account is not created.');
        }

        return $this;
    }

    /**
     * Handle the execution of the run method.
     *
     * This method executes various actions based on the module settings and customer data.
     * It checks settings, data, customer group, validation account, emails, alerts, and redirects as needed.
     * If an exception is encountered during execution, proper exception handling is applied.
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

            $this->checkData();

            $this->langId = (int)$this->getContext()->language->id;
            $this->customer = $this->props['newCustomer'];

            if (false === $this->checkSiretData()) {
                return;
            }

            $this
                ->handleCustomerGroup()
                ->handleValidationAccount()
                ->handleCustomerSave()
                ->handleEmail()
                ->handleAlert()
                ->handleRedirection();
        } catch (Throwable $t) {
            $this->handleException($t);
        }
    }
}
