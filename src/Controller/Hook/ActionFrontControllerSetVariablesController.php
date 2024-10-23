<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Controller\Hook;

use Customer;
use DrSoftFr\Module\ValidateCustomerPro\Config;
use DrSoftFr\PrestaShopModuleHelper\Controller\Hook\AbstractHookController;
use DrSoftFr\PrestaShopModuleHelper\Controller\Hook\HookControllerInterface;
use Exception;
use Throwable;

/**
 * Class ActionFrontControllerSetVariablesController
 */
final class ActionFrontControllerSetVariablesController extends AbstractHookController implements HookControllerInterface
{
    /**
     * @var array $settings
     */
    private $settings;

    /**
     * Checks if the data meets the required criteria.
     *
     * @return bool Returns true if the data is valid, false otherwise.
     *
     * @throws Exception
     */
    private function checkData(): bool
    {
        if (
            !empty($this->getContext()->customer)
        ) {
            return false;
        }

        if (!($this->getContext()->customer instanceof Customer)) {
            return false;
        }

        if (empty($this->getContext()->customer->id_default_group)) {
            return false;
        }

        if ((int)$this->getContext()->customer->id_default_group !== (int)$this->settings['customer_group_id']) {
            return false;
        }

        if (
            true === $this->settings['require_company_field'] &&
            empty($this->getContext()->customer->company)
        ) {
            return false;
        }

        if (
            true === $this->settings['require_siret_field'] &&
            empty($this->getContext()->customer->siret)
        ) {
            return false;
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
     * Runs the process and retrieves an array of values.
     *
     * @return array An array of values with key 'is_validate_customer_pro' indicating if customer is pro.
     */
    public function run(): array
    {
        $values = [
            'is_validate_customer_pro' => false
        ];

        try {
            $this->settings = $this->module->get(Config::SETTING_PROVIDER_SERVICE);

            if (false === $this->settings['active']) {
                return $values;
            }

            $values['is_validate_customer_pro'] = $this->checkData();
        } catch (Throwable $t) {
            $this->handleException($t);
        }

        return $values;
    }
}
