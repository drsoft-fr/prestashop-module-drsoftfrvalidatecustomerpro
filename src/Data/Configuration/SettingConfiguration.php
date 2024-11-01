<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Data\Configuration;

use DrSoftFr\Module\ValidateCustomerPro\Config;
use DrSoftFr\Module\ValidateCustomerPro\Data\Validator\SettingValidator;
use Exception;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Throwable;

/**
 * Class SettingConfiguration
 *
 * This class handles the configuration ValidateCustomerPro for the application.
 */
final class SettingConfiguration implements DataConfigurationInterface
{
    const CONFIGURATION_KEYS = [
        'active' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_ACTIVE',
        'admin_action_customer_account_add_email' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_ADMIN_ACTION_CUSTOMER_ACCOUNT_ADD_EMAIL',
        'admin_send_email_on_action_customer_account_add_hook' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_ADMIN_EMAIL_ON_ACTION_CUSTOMER_ACCOUNT_ADD',
        'cms_notify_id' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_CMS_NOTIFY_ID',
        'cms_not_activated_id' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_CMS_NOT_ACTIVATED_ID',
        'customer_group_id' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_CUSTOMER_GROUP_ID',
        'enable_auto_customer_group_selection' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_ENABLE_AUTO_CUSTOMER_GROUP_SELECTION',
        'enable_email_approval' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_ENABLE_EMAIL_APPROVAL',
        'enable_email_pending_approval' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_ENABLE_EMAIL_PENDING_APPROVAL',
        'enable_manual_validation_account' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_ENABLE_MANUAL_VALIDATION_ACCOUNT',
        'required_form_fields' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_REQUIRED_FORM_FIELDS',
        'additional_form_fields' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_ADDITIONAL_FORM_FIELDS',
        'enable_unauthenticated_customer_alert' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_ENABLE_UNAUTHENTICATED_CUSTOMER_ALERT',
        'enable_unapproved_customer_alert' => 'DRSOFT_FR_VALIDATE_CUSTOMER_PRO_ENABLE_UNAPPROVED_CUSTOMER_ALERT',
    ];

    const CONFIGURATION_DEFAULT_VALUES = [
        'active' => false,
        'admin_action_customer_account_add_email' => '',
        'admin_send_email_on_action_customer_account_add_hook' => false,
        'cms_notify_id' => 0,
        'cms_not_activated_id' => 0,
        'customer_group_id' => 0,
        'enable_auto_customer_group_selection' => false,
        'enable_email_approval' => false,
        'enable_email_pending_approval' => false,
        'enable_manual_validation_account' => false,
        'required_form_fields' => [],
        'additional_form_fields' => [],
        'enable_unauthenticated_customer_alert' => false,
        'enable_unapproved_customer_alert' => false,
    ];

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var SettingValidator|null
     */
    private $validator;

    /**
     * @param Configuration $configuration
     * @param SettingValidator|null $validator
     */
    public function __construct(
        Configuration    $configuration,
        SettingValidator $validator = null
    )
    {
        $this->configuration = $configuration;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        $configuration = [];

        foreach (self::CONFIGURATION_KEYS as $key => $value) {
            if (in_array(
                $key,
                [
                    'active',
                    'admin_send_email_on_action_customer_account_add_hook',
                    'enable_auto_customer_group_selection',
                    'enable_email_approval',
                    'enable_email_pending_approval',
                    'enable_manual_validation_account',
                    'enable_unauthenticated_customer_alert',
                    'enable_unapproved_customer_alert',
                ],
                true
            )) {
                $configuration[$key] = $this->configuration->getBoolean($value, self::CONFIGURATION_DEFAULT_VALUES[$key]);

                continue;
            }

            if (in_array(
                $key,
                [
                    'cms_notify_id',
                    'cms_not_activated_id',
                    'customer_group_id',
                ],
                true
            )) {
                $configuration[$key] = $this->configuration->getInt($value, self::CONFIGURATION_DEFAULT_VALUES[$key]);

                continue;
            }

            $configuration[$key] = $this->configuration->get($value, self::CONFIGURATION_DEFAULT_VALUES[$key]);
        }

        if (is_string($configuration['required_form_fields'])) {
            $configuration['required_form_fields'] = explode(',', $configuration['required_form_fields']);
        }

        if (is_string($configuration['additional_form_fields'])) {
            $configuration['additional_form_fields'] = explode(',', $configuration['additional_form_fields']);
        }

        return $configuration;
    }

    /**
     * Initialize the configuration.
     *
     * This method initializes the configuration by updating the current configuration
     * with the default values defined in `CONFIGURATION_DEFAULT_VALUES` constant.
     * It updates the configuration using the `updateConfiguration` method.
     *
     * @return void
     *
     * @throws Exception
     */
    public function initConfiguration(): void
    {
        $this->updateConfiguration(self::CONFIGURATION_DEFAULT_VALUES);
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration): array
    {
        $errors = [];

        try {
            $this->validateConfiguration($configuration);

            foreach (self::CONFIGURATION_KEYS as $key => $value) {
                if (in_array(
                    $key,
                    [
                        'required_form_fields',
                        'additional_form_fields'
                    ]
                )) {
                    $this->configuration->set($value, implode(',', $configuration[$key]));

                    continue;
                }

                $this->configuration->set($value, $configuration[$key]);
            }
        } catch (Throwable $t) {
            $errors[] = [
                'key' => Config::createErrorMessage(__METHOD__, __LINE__, $t),
                'domain' => 'Modules.Drsoftfrvalidatecustomerpro.Error',
                'parameters' => [],
            ];
        }

        return $errors;
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function removeConfiguration(): void
    {
        foreach (self::CONFIGURATION_KEYS as $key) {
            $this->configuration->remove($key);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function validateConfiguration(array $configuration): bool
    {
        if (null === $this->validator) {
            return true;
        }

        return $this->validator->validate($configuration);
    }
}
