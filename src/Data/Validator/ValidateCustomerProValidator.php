<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Data\Validator;

use DrSoftFr\Module\ValidateCustomerPro\Exception\CmsPage\NonexistentCmsPageIdException;
use DrSoftFr\Module\ValidateCustomerPro\Exception\Group\NonexistentGroupIdException;
use Exception;
use DrSoftFr\Module\ValidateCustomerPro\Exception\ValidateCustomerPro\ValidateCustomerProConstraintException;
use DrSoftFr\PrestaShopModuleHelper\Data\Validator\AbstractValidator;
use DrSoftFr\PrestaShopModuleHelper\Data\Validator\ValidatorInterface;

final class ValidateCustomerProValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * @var array
     */
    private array $cmsIds;

    /**
     * @var array
     */
    private array $groupIds;

    public function __construct(
        array $cmsIds,
        array $groupIds
    )
    {
        $this->cmsIds = $cmsIds;
        $this->groupIds = $groupIds;
    }


    /**
     * Validates all the data fields.
     *
     * @param array $data The data array to validate.
     *
     * @return bool Returns true if all the fields pass the validation.
     *
     * @throws ValidateCustomerProConstraintException|NonexistentCmsPageIdException|NonexistentGroupIdException If any of the data fields fail validation.
     */
    public function validate(array $data): bool
    {
        $this
            ->validateActive($data)
            ->validateAdminSendEmailOnActionCustomerAccountAddHook($data)
            ->validateAdminActionCustomerAccountAddEmail($data)
            ->validateCmsNotifyId($data)
            ->validateCmsNotActivatedId($data)
            ->validateCustomerGroupId($data)
            ->validateEnableAutoCustomerGroupSelection($data)
            ->validateEnableEmailApproval($data)
            ->validateEnableEmailPendingApproval($data)
            ->validateEnableManualValidationAccount($data)
            ->validateRequireCompanyField($data)
            ->validateRequireSiretField($data)
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
     * @return ValidateCustomerProValidator
     *
     * @throws ValidateCustomerProConstraintException If the active field is not set.
     * @throws ValidateCustomerProConstraintException If the active field is not a boolean value.
     * @throws Exception
     */
    private function validateActive(array $configuration): ValidateCustomerProValidator
    {
        $this->isSet($configuration, 'active', new ValidateCustomerProConstraintException);
        $this->isBool($configuration, 'active', new ValidateCustomerProConstraintException);

        return $this;
    }

    /**
     * Validates the admin_action_customer_account_add_email field in the configuration array if admin_send_email_on_action_customer_account_add_hook is true.
     * Ensures that the field is set, not empty, and is a string value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return ValidateCustomerProValidator
     *
     * @throws ValidateCustomerProConstraintException If admin_send_email_on_action_customer_account_add_hook is true but admin_action_customer_account_add_email field is not set.
     * @throws ValidateCustomerProConstraintException If admin_send_email_on_action_customer_account_add_hook is true but admin_action_customer_account_add_email field is empty.
     * @throws ValidateCustomerProConstraintException If admin_send_email_on_action_customer_account_add_hook is true but admin_action_customer_account_add_email field is not a string.
     * @throws Exception
     */
    private function validateAdminActionCustomerAccountAddEmail(array $configuration): ValidateCustomerProValidator
    {
        if (!$configuration['admin_send_email_on_action_customer_account_add_hook']) {
            return $this;
        }

        $this->isSet($configuration, 'admin_action_customer_account_add_email', new ValidateCustomerProConstraintException);
        $this->isEmpty($configuration, 'admin_action_customer_account_add_email', new ValidateCustomerProConstraintException);
        $this->isString($configuration, 'admin_action_customer_account_add_email', new ValidateCustomerProConstraintException);

        return $this;
    }

    /**
     * Validates the admin_send_email_on_action_customer_account_add_hook field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return ValidateCustomerProValidator
     *
     * @throws ValidateCustomerProConstraintException If the admin_send_email_on_action_customer_account_add_hook field is not set.
     * @throws ValidateCustomerProConstraintException If the admin_send_email_on_action_customer_account_add_hook field is not a boolean value.
     * @throws Exception
     */
    private function validateAdminSendEmailOnActionCustomerAccountAddHook(array $configuration): ValidateCustomerProValidator
    {
        $this->isSet($configuration, 'admin_send_email_on_action_customer_account_add_hook', new ValidateCustomerProConstraintException);
        $this->isBool($configuration, 'admin_send_email_on_action_customer_account_add_hook', new ValidateCustomerProConstraintException);

        return $this;
    }

    /**
     * Validates the cms_notify_id in the given data array.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return ValidateCustomerProValidator
     *
     * @throws NonexistentCmsPageIdException If the 'cms_notify_id' field is not a valid ID.
     */
    private function validateCmsNotifyId(array $configuration): ValidateCustomerProValidator
    {
        if (!isset($configuration['cms_notify_id'])) {
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
     * @return ValidateCustomerProValidator
     *
     * @throws NonexistentCmsPageIdException If the 'cms_not_activated_id' field is not a valid ID.
     */
    private function validateCmsNotActivatedId(array $configuration): ValidateCustomerProValidator
    {
        if (!isset($configuration['cms_not_activated_id'])) {
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
     * @return ValidateCustomerProValidator
     *
     * @throws NonexistentGroupIdException If the 'customer_group_id' field is not a valid ID.
     */
    private function validateCustomerGroupId(array $configuration): ValidateCustomerProValidator
    {
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
                    'Group width "%d" does not exist.',
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
     * @return ValidateCustomerProValidator
     *
     * @throws ValidateCustomerProConstraintException If the enable_auto_customer_group_selection field is not set.
     * @throws ValidateCustomerProConstraintException If the enable_auto_customer_group_selection field is not a boolean value.
     * @throws Exception
     */
    private function validateEnableAutoCustomerGroupSelection(array $configuration): ValidateCustomerProValidator
    {
        $this->isSet($configuration, 'enable_auto_customer_group_selection', new ValidateCustomerProConstraintException);
        $this->isBool($configuration, 'enable_auto_customer_group_selection', new ValidateCustomerProConstraintException);

        return $this;
    }

    /**
     * Validates the enable_email_approval field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return ValidateCustomerProValidator
     *
     * @throws ValidateCustomerProConstraintException If the enable_email_approval field is not set.
     * @throws ValidateCustomerProConstraintException If the enable_email_approval field is not a boolean value.
     * @throws Exception
     */
    private function validateEnableEmailApproval(array $configuration): ValidateCustomerProValidator
    {
        $this->isSet($configuration, 'enable_email_approval', new ValidateCustomerProConstraintException);
        $this->isBool($configuration, 'enable_email_approval', new ValidateCustomerProConstraintException);

        return $this;
    }

    /**
     * Validates the enable_email_pending_approval field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return ValidateCustomerProValidator
     *
     * @throws ValidateCustomerProConstraintException If the enable_email_pending_approval field is not set.
     * @throws ValidateCustomerProConstraintException If the enable_email_pending_approval field is not a boolean value.
     * @throws Exception
     */
    private function validateEnableEmailPendingApproval(array $configuration): ValidateCustomerProValidator
    {
        $this->isSet($configuration, 'enable_email_pending_approval', new ValidateCustomerProConstraintException);
        $this->isBool($configuration, 'enable_email_pending_approval', new ValidateCustomerProConstraintException);

        return $this;
    }

    /**
     * Validates the enable_manual_validation_account field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return ValidateCustomerProValidator
     *
     * @throws ValidateCustomerProConstraintException If the enable_manual_validation_account field is not set.
     * @throws ValidateCustomerProConstraintException If the enable_manual_validation_account field is not a boolean value.
     * @throws Exception
     */
    private function validateEnableManualValidationAccount(array $configuration): ValidateCustomerProValidator
    {
        $this->isSet($configuration, 'enable_manual_validation_account', new ValidateCustomerProConstraintException);
        $this->isBool($configuration, 'enable_manual_validation_account', new ValidateCustomerProConstraintException);

        return $this;
    }

    /**
     * Validates the require_company_field field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return ValidateCustomerProValidator
     *
     * @throws ValidateCustomerProConstraintException If the require_company_field field is not set.
     * @throws ValidateCustomerProConstraintException If the require_company_field field is not a boolean value.
     * @throws Exception
     */
    private function validateRequireCompanyField(array $configuration): ValidateCustomerProValidator
    {
        $this->isSet($configuration, 'require_company_field', new ValidateCustomerProConstraintException);
        $this->isBool($configuration, 'require_company_field', new ValidateCustomerProConstraintException);

        return $this;
    }

    /**
     * Validates the require_siret_field field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return ValidateCustomerProValidator
     *
     * @throws ValidateCustomerProConstraintException If the require_siret_field field is not set.
     * @throws ValidateCustomerProConstraintException If the require_siret_field field is not a boolean value.
     * @throws Exception
     */
    private function validateRequireSiretField(array $configuration): ValidateCustomerProValidator
    {
        $this->isSet($configuration, 'require_siret_field', new ValidateCustomerProConstraintException);
        $this->isBool($configuration, 'require_siret_field', new ValidateCustomerProConstraintException);

        return $this;
    }

    /**
     * Validates the enable_unauthenticated_customer_alert field in the configuration array.
     * Ensures that the field is set and is a boolean value.
     *
     * @param array $configuration The configuration array to validate.
     *
     * @return ValidateCustomerProValidator
     *
     * @throws ValidateCustomerProConstraintException If the enable_unauthenticated_customer_alert field is not set.
     * @throws ValidateCustomerProConstraintException If the enable_unauthenticated_customer_alert field is not a boolean value.
     * @throws Exception
     */
    private function validateEnableUnauthenticatedCustomerAlert(array $configuration): ValidateCustomerProValidator
    {
        $this->isSet($configuration, 'enable_unauthenticated_customer_alert', new ValidateCustomerProConstraintException);
        $this->isBool($configuration, 'enable_unauthenticated_customer_alert', new ValidateCustomerProConstraintException);

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
     * @throws ValidateCustomerProConstraintException If the enable_unapproved_customer_alert field is not set.
     * @throws ValidateCustomerProConstraintException If the enable_unapproved_customer_alert field is not a boolean value.
     * @throws Exception
     */
    private function validateEnableUnapprovedCustomerAlert(array $configuration): void
    {
        $this->isSet($configuration, 'enable_unapproved_customer_alert', new ValidateCustomerProConstraintException);
        $this->isBool($configuration, 'enable_unapproved_customer_alert', new ValidateCustomerProConstraintException);
    }
}
