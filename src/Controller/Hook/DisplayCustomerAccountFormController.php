<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Controller\Hook;

use Carrier;
use Cart;
use Configuration;
use Country;
use Customer;
use CustomerAddressForm;
use CustomerAddressFormatter;
use CustomerAddressPersister;
use DrSoftFr\Module\ValidateCustomerPro\Config;
use DrSoftFr\PrestaShopModuleHelper\Controller\Hook\AbstractHookController;
use DrSoftFr\PrestaShopModuleHelper\Controller\Hook\HookControllerInterface;
use Exception;
use Throwable;
use Tools;

final class DisplayCustomerAccountFormController extends AbstractHookController implements HookControllerInterface
{
    private const FORM_FIELDS_DATA_PROVIDER_SERVICE = 'drsoft_fr.module.validate_customer_pro.data.form_fields_data_provider';

    /**
     * @var null|CustomerAddressForm $customerFormAddress
     */
    private $customerFormAddress = null;

    /**
     * @var null|CustomerAddressFormatter $customerFormFormatter
     */
    private $customerFormFormatter = null;

    /**
     * @var array
     */
    private $formFields;

    /**
     * @var array $settings
     */
    private $settings;

    /**
     * Sets the 'required' attribute to true for the form field with the name 'alias'.
     *
     * @param array $templateFormFields Reference to the array containing template form fields.
     *
     * @return void
     */
    private function setAliasFormFieldAsRequired(array &$templateFormFields): void
    {
        foreach ($templateFormFields as &$field) {
            if ($field['name'] !== 'alias') {
                continue;
            }

            $field['required'] = true;

            break;
        }
    }

    /**
     * Gets the field data for a specific form field ID.
     *
     * @param string $id The ID of the form field to retrieve data for.
     *
     * @return array The data of the form field with the specified ID, or an empty array if not found.
     */
    private function getFormFieldData(string $id): array
    {
        $field = array_values(array_filter($this->formFields, function ($field) use ($id) {
            return $field['name'] === $id;
        }));

        if (
            empty($field) ||
            empty($field[0])
        ) {
            return [];
        }

        return $field[0];
    }

    /**
     * Inserts additional form fields into the template form fields array.
     *
     * @param array $additionalFormFields An array of additional form fields to be inserted.
     * @param array $requiredFormFields An array of required form fields.
     * @param array $templateFormFields The template form fields array where additional fields will be inserted.
     *
     * @return void
     */
    private function insertAdditionalFormField(
        array $additionalFormFields,
        array $requiredFormFields,
        array &$templateFormFields
    ): void
    {
        if (empty($additionalFormFields)) {
            return;
        }

        foreach ($additionalFormFields as $additionalFormField) {
            $formFieldData = $this->getFormFieldData($additionalFormField);

            if (
                empty($formFieldData) ||
                empty($formFieldData['field'])
            ) {
                continue;
            }

            if (in_array($formFieldData['field'], $templateFormFields)) {
                continue;
            }

            $name = $formFieldData['field'];
            $label = empty($formFieldData['label']) ? Tools::ucfirst($name) : $formFieldData['label'];

            $formFieldProps = [
                'name' => $name,
                'required' => in_array($additionalFormField, $requiredFormFields),
                'autocomplete' => false,
                'errors' => [],
                'maxLength' => 255,
                'type' => 'text',
                'value' => null,
                'label' => $label,
            ];
            $templateFormFields[$name] = $formFieldProps;
        }
    }

    /**
     * Populate the form fields array with values.
     *
     * @param array $templateFormFields Reference to the template form fields array.
     * @param array $values The values to populate the form fields with.
     *
     * @return void
     */
    private function populateFormFields(array &$templateFormFields, array $values): void
    {
        if (empty($values)) {
            return;
        }

        foreach ($values as $key => $value) {
            if (empty($templateFormFields[$key])) {
                continue;
            }

            $templateFormFields[$key]['value'] = $value;
        }
    }

    /**
     * Handles the addition of fields to a form.
     *
     * @param array $additionalFormFields An array of additional form fields to be added.
     * @param array $requiredFormFields An array of required form fields.
     * @param array $templateFormFields An array of template form fields.
     * @param array $values An array of values to populate the form fields with.
     *
     * @return string The rendered address form template.
     *
     * @throws Exception
     */
    private function handleAdditionField(
        array $additionalFormFields,
        array $requiredFormFields,
        array $templateFormFields,
        array $values
    ): string
    {
        $this->setAliasFormFieldAsRequired($templateFormFields);
        $this->insertAdditionalFormField($additionalFormFields, $requiredFormFields, $templateFormFields);
        $this->populateFormFields($templateFormFields, $values);

        $this->getContext()->smarty->assign('formFields', $templateFormFields);

        return $this
            ->module
            ->display(
                $this->file,
                'address-form.tpl'
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

        $this->logger->error($errorMessage, [
            'error_code' => $t->getCode(),
            'object_type' => null,
            'object_id' => null,
            'allow_duplicate' => false,
        ]);
    }

    /**
     * Creates an override for the customer address persister.
     *
     * Initializes a new customer instance in the current context.
     * Creates a new Cart instance and assigns the currency and guest ID from the context cookie.
     * Returns a new CustomerAddressPersister instance with the initialized customer, cart, and cookie key.
     *
     * @return CustomerAddressPersister The overridden customer address persister with initialized entities.
     */
    private function makeAddressPersisterOverride(): CustomerAddressPersister
    {
        $this->getContext()->customer = new Customer();
        $cart = new Cart;
        $cart->id_currency = $this->getContext()->cookie->id_currency;
        $cart->id_guest = $this->getContext()->cookie->id_guest;

        return new CustomerAddressPersister(
            $this->getContext()->customer,
            $cart,
            _COOKIE_KEY_
        );
    }

    /**
     * Runs the application logic.
     *
     * @return string The result of running the application logic.
     */
    public function run(): string
    {
        $result = '';

        try {
            $this->settings = $this->module->get(Config::SETTING_PROVIDER_SERVICE);

            if (false === $this->settings['active']) {
                return $result;
            }

            $this->formFields = $this->module->get(self::FORM_FIELDS_DATA_PROVIDER_SERVICE);

            if (0 < (int)$this->getContext()->cookie->id_customer) {
                return '';
            }

            $additionalFormFields = $this->settings['additional_form_fields'];
            $requiredFormFields = $this->settings['required_form_fields'];

            if (empty($additionalFormFields)) {
                return '';
            }

            $this->customerFormFormatter = new CustomerAddressFormatter(
                $this->getContext()->country,
                $this->getContext()->getTranslator(),
                $this->getAvailableCountries()
            );

            $this->customerFormAddress = new CustomerAddressForm(
                $this->getContext()->smarty,
                $this->getContext()->language,
                $this->getContext()->getTranslator(),
                $this->makeAddressPersisterOverride(),
                $this->customerFormFormatter
            );

            $templateForm = $this->customerFormAddress->getTemplateVariables();
            $values = Tools::getAllValues();

            if (!is_array($values)) {
                $values = [];
            }

            $result = $this->handleAdditionField(
                $additionalFormFields,
                $requiredFormFields,
                $templateForm['formFields'],
                $values
            );
        } catch (Throwable $t) {
            $this->handleException($t);
        }

        return $result;
    }

    /**
     * Gets the available countries based on configuration settings.
     *
     * @return array List of available countries depending on configuration settings.
     *
     * @throws Exception
     */
    private function getAvailableCountries(): array
    {
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            return Carrier::getDeliveredCountries(
                $this->getContext()->language->id,
                true,
                true
            );
        } else {
            return Country::getCountries(
                $this->getContext()->language->id,
                true
            );
        }
    }
}
