<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Data\Validator;

use DrSoftFr\Module\ValidateCustomerPro\Data\Provider\FormFieldsProvider;
use DrSoftFr\Module\ValidateCustomerPro\Exception\CmsPage\NonexistentCmsPageIdException;
use DrSoftFr\Module\ValidateCustomerPro\Exception\Group\NonexistentGroupIdException;
use Exception;
use DrSoftFr\Module\ValidateCustomerPro\Exception\Setting\SettingConstraintException;
use DrSoftFr\PrestaShopModuleHelper\Data\Validator\AbstractValidator;
use DrSoftFr\PrestaShopModuleHelper\Data\Validator\ValidatorInterface;

final class SettingValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * @var array
     */
    private $cmsIds;

    /**
     * @var FormFieldsProvider
     */
    private $formFieldsProvider;

    /**
     * @var array
     */
    private $groupIds;

    public function __construct(
        array              $cmsIds,
        FormFieldsProvider $formFieldsProvider,
        array              $groupIds
    )
    {
        $this->cmsIds = $cmsIds;
        $this->formFieldsProvider = $formFieldsProvider;
        $this->groupIds = $groupIds;
    }


    /**
     * Validates all the data fields.
     *
     * @param array $data The data array to validate.
     *
     * @return bool Returns true if all the fields pass the validation.
     *
     * @throws SettingConstraintException|NonexistentCmsPageIdException|NonexistentGroupIdException If any of the data fields fail validation.
     */
    public function validate(array $data): bool
    {
        $this
            ->validateActive($data)
            ->validateAdminSendEmailOnActionCustomerAccountAddHook($data)
            ->validateAdminActionCustomerAccountAddEmail($data)
            ->validateCmsNotifyId($data)
            ->validateCmsNotActivatedId($data)
            ->validateEnableAutoCustomerGroupSelection($data)
            ->validateCustomerGroupId($data)
            ->validateEnableEmailApproval($data)
            ->validateEnableEmailPendingApproval($data)
            ->validateEnableManualValidationAccount($data)
            ->validateAdditionalFormFields($data)
            ->validateRequiredFormFields($data)
            ->validateConsistencyFormFields($data)
            ->validateEnableUnauthenticatedCustomerAlert($data)
            ->validateEnableUnapprovedCustomerAlert($data);

        return true;
    }

    /**
     * Validates the active field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator
     *
     * @throws SettingConstraintException If the active field is not set.
     * @throws SettingConstraintException If the active field is not a boolean value.
     * @throws Exception
     */
    private function validateActive(array $configuration): SettingValidator
    {
        $this->isSet($configuration, 'active', new SettingConstraintException);
        $this->isBool($configuration, 'active', new SettingConstraintException);

        return $this;
    }

    /**
     * Validates the admin_action_customer_account_add_email field in the configuration array if admin_send_email_on_action_customer_account_add_hook is true.
     * Ensures that the field is set, not empty, and is a string value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator
     *
     * @throws SettingConstraintException If admin_send_email_on_action_customer_account_add_hook is true but admin_action_customer_account_add_email field is not set.
     * @throws SettingConstraintException If admin_send_email_on_action_customer_account_add_hook is true but admin_action_customer_account_add_email field is empty.
     * @throws SettingConstraintException If admin_send_email_on_action_customer_account_add_hook is true but admin_action_customer_account_add_email field is not a string.
     * @throws Exception
     */
    private function validateAdminActionCustomerAccountAddEmail(array $configuration): SettingValidator
    {
        if (!$configuration['admin_send_email_on_action_customer_account_add_hook']) {
            return $this;
        }

        $this->isSet($configuration, 'admin_action_customer_account_add_email', new SettingConstraintException);
        $this->isEmpty($configuration, 'admin_action_customer_account_add_email', new SettingConstraintException);
        $this->isString($configuration, 'admin_action_customer_account_add_email', new SettingConstraintException);

        return $this;
    }

    /**
     * Validates the admin_send_email_on_action_customer_account_add_hook field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator
     *
     * @throws SettingConstraintException If the admin_send_email_on_action_customer_account_add_hook field is not set.
     * @throws SettingConstraintException If the admin_send_email_on_action_customer_account_add_hook field is not a boolean value.
     * @throws Exception
     */
    private function validateAdminSendEmailOnActionCustomerAccountAddHook(array $configuration): SettingValidator
    {
        $this->isSet($configuration, 'admin_send_email_on_action_customer_account_add_hook', new SettingConstraintException);
        $this->isBool($configuration, 'admin_send_email_on_action_customer_account_add_hook', new SettingConstraintException);

        return $this;
    }

    /**
     * Validates the cms_notify_id in the given data array.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator
     *
     * @throws NonexistentCmsPageIdException If the 'cms_notify_id' field is not a valid ID.
     */
    private function validateCmsNotifyId(array $configuration): SettingValidator
    {
        if (empty($configuration['cms_notify_id'])) {
            return $this;
        }

        $configuration['cms_notify_id'] = (int)$configuration['cms_notify_id'];

        if (
            0 >= $configuration['cms_notify_id'] ||
            !in_array(
                $configuration['cms_notify_id'],
                $this->cmsIds,
                true
            )
        ) {
            throw new NonexistentCmsPageIdException(
                sprintf(
                    'CMS width "%d" does not exist.',
                    $configuration['cms_notify_id']
                )
            );
        }

        return $this;
    }

    /**
     * Validates the cms_not_activated_id in the given data array.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator
     *
     * @throws NonexistentCmsPageIdException If the 'cms_not_activated_id' field is not a valid ID.
     */
    private function validateCmsNotActivatedId(array $configuration): SettingValidator
    {
        if (empty($configuration['cms_not_activated_id'])) {
            return $this;
        }

        $configuration['cms_not_activated_id'] = (int)$configuration['cms_not_activated_id'];

        if (
            0 >= $configuration['cms_not_activated_id'] ||
            !in_array(
                $configuration['cms_not_activated_id'],
                $this->cmsIds,
                true
            )
        ) {
            throw new NonexistentCmsPageIdException(
                sprintf(
                    'CMS width "%d" does not exist.',
                    $configuration['cms_not_activated_id']
                )
            );
        }

        return $this;
    }

    /**
     * Validates the customer_group_id in the given data array.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator
     *
     * @throws NonexistentGroupIdException If the 'customer_group_id' field is not a valid ID.
     */
    private function validateCustomerGroupId(array $configuration): SettingValidator
    {
        if (!$configuration['enable_auto_customer_group_selection']) {
            return $this;
        }

        if (!isset($configuration['customer_group_id'])) {
            return $this;
        }

        $configuration['customer_group_id'] = (int)$configuration['customer_group_id'];

        if (
            0 >= $configuration['customer_group_id'] ||
            !in_array(
                $configuration['customer_group_id'],
                $this->groupIds,
                true
            )
        ) {
            throw new NonexistentGroupIdException(
                sprintf(
                    'Group with "%d" does not exist.',
                    $configuration['customer_group_id']
                )
            );
        }

        return $this;
    }

    /**
     * Validates the enable_auto_customer_group_selection field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator
     *
     * @throws SettingConstraintException If the enable_auto_customer_group_selection field is not set.
     * @throws SettingConstraintException If the enable_auto_customer_group_selection field is not a boolean value.
     * @throws Exception
     */
    private function validateEnableAutoCustomerGroupSelection(array $configuration): SettingValidator
    {
        $this->isSet($configuration, 'enable_auto_customer_group_selection', new SettingConstraintException);
        $this->isBool($configuration, 'enable_auto_customer_group_selection', new SettingConstraintException);

        return $this;
    }

    /**
     * Validates the enable_email_approval field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator
     *
     * @throws SettingConstraintException If the enable_email_approval field is not set.
     * @throws SettingConstraintException If the enable_email_approval field is not a boolean value.
     * @throws Exception
     */
    private function validateEnableEmailApproval(array $configuration): SettingValidator
    {
        $this->isSet($configuration, 'enable_email_approval', new SettingConstraintException);
        $this->isBool($configuration, 'enable_email_approval', new SettingConstraintException);

        return $this;
    }

    /**
     * Validates the enable_email_pending_approval field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator
     *
     * @throws SettingConstraintException If the enable_email_pending_approval field is not set.
     * @throws SettingConstraintException If the enable_email_pending_approval field is not a boolean value.
     * @throws Exception
     */
    private function validateEnableEmailPendingApproval(array $configuration): SettingValidator
    {
        $this->isSet($configuration, 'enable_email_pending_approval', new SettingConstraintException);
        $this->isBool($configuration, 'enable_email_pending_approval', new SettingConstraintException);

        return $this;
    }

    /**
     * Validates the enable_manual_validation_account field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator
     *
     * @throws SettingConstraintException If the enable_manual_validation_account field is not set.
     * @throws SettingConstraintException If the enable_manual_validation_account field is not a boolean value.
     * @throws Exception
     */
    private function validateEnableManualValidationAccount(array $configuration): SettingValidator
    {
        $this->isSet($configuration, 'enable_manual_validation_account', new SettingConstraintException);
        $this->isBool($configuration, 'enable_manual_validation_account', new SettingConstraintException);

        return $this;
    }

    /**
     * Validates the additional_form_fields field in the configuration array.
     *  Ensures that the field is set and is an array.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator The instance of the SettingValidator.
     *
     * @throws SettingConstraintException If the additional_form_fields field is not set.
     * @throws SettingConstraintException If the additional_form_fields field is not an array.
     * @throws Exception
     */
    private function validateAdditionalFormFields(array $configuration): SettingValidator
    {
        $this->isSet($configuration, 'additional_form_fields', new SettingConstraintException);
        $this->isArray($configuration, 'additional_form_fields', new SettingConstraintException);

        $names = $this->formFieldsProvider->getNames();

        foreach ($configuration['additional_form_fields'] as $field) {
            if (!in_array(
                $field,
                $names,
                true
            )) {
                throw new SettingConstraintException(
                    'invalid additional_form_fields field',
                    SettingConstraintException::INVALID_ADDITIONAL_FORM_FIELDS
                );
            }
        }
        unset($field);
        unset($names);

        return $this;
    }

    /**
     * Validates the required_form_fields field in the configuration array.
     *  Ensures that the field is set and is an array.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator The instance of the SettingValidator.
     *
     * @throws SettingConstraintException If the required_form_fields field is not set.
     * @throws SettingConstraintException If the required_form_fields field is not an array.
     * @throws Exception
     */
    private function validateRequiredFormFields(array $configuration): SettingValidator
    {
        $this->isSet($configuration, 'required_form_fields', new SettingConstraintException);
        $this->isArray($configuration, 'required_form_fields', new SettingConstraintException);

        $names = $this->formFieldsProvider->getNames();

        foreach ($configuration['required_form_fields'] as $field) {
            if (!in_array(
                $field,
                $names,
                true
            )) {
                throw new SettingConstraintException(
                    'invalid required_form_fields field',
                    SettingConstraintException::INVALID_REQUIRED_FORM_FIELDS
                );
            }
        }
        unset($field);
        unset($names);

        return $this;
    }

    /**
     * Validates the consistency between additional_form_fields and required_form_fields in the configuration array.
     *
     * Removes 'customer__company' and 'customer__siret' from the required_form_fields.
     * Checks if each required_form field is present in additional_form_fields.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator
     *
     * @throws SettingConstraintException If a required_form field is not present in additional_form_fields.
     */
    private function validateConsistencyFormFields(array $configuration): SettingValidator
    {
        $additionalFormNames = $configuration['additional_form_fields'];
        $requiredFormNames = $configuration['required_form_fields'];

        unset($requiredFormNames[array_search('customer__company', $requiredFormNames)]);
        unset($requiredFormNames[array_search('customer__siret', $requiredFormNames)]);

        foreach ($requiredFormNames as $name) {
            if (!in_array(
                $name,
                $additionalFormNames,
                true
            )) {
                throw new SettingConstraintException(
                    'required_form_fields (' . $name . ') field must be in additional_form_fields',
                    SettingConstraintException::INVALID_CONSISTENCY_FORM_FIELDS
                );
            }
        }

        return $this;
    }

    /**
     * Validates the enable_unauthenticated_customer_alert field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return SettingValidator
     *
     * @throws SettingConstraintException If the enable_unauthenticated_customer_alert field is not set.
     * @throws SettingConstraintException If the enable_unauthenticated_customer_alert field is not a boolean value.
     * @throws Exception
     */
    private function validateEnableUnauthenticatedCustomerAlert(array $configuration): SettingValidator
    {
        $this->isSet($configuration, 'enable_unauthenticated_customer_alert', new SettingConstraintException);
        $this->isBool($configuration, 'enable_unauthenticated_customer_alert', new SettingConstraintException);

        return $this;
    }

    /**
     * Validates the enable_unapproved_customer_alert field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return void
     *
     * @throws SettingConstraintException If the enable_unapproved_customer_alert field is not set.
     * @throws SettingConstraintException If the enable_unapproved_customer_alert field is not a boolean value.
     * @throws Exception
     */
    private function validateEnableUnapprovedCustomerAlert(array $configuration): void
    {
        $this->isSet($configuration, 'enable_unapproved_customer_alert', new SettingConstraintException);
        $this->isBool($configuration, 'enable_unapproved_customer_alert', new SettingConstraintException);
    }
}
