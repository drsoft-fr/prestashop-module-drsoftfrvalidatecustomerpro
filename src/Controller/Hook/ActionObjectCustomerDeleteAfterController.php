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
use Throwable;

final class ActionObjectCustomerDeleteAfterController extends AbstractHookController implements HookControllerInterface
{
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
     * Handles the deletion of a customer by removing the AdapterCustomer entity from the database.
     *
     * @param Customer $customer The customer entity to be deleted.
     *
     * @return bool Returns true if the AdapterCustomer is successfully deleted, false otherwise.
     *
     * @throws Exception
     */
    private function handleDeletionCustomer(Customer $customer): bool
    {
        /** @var EntityManagerInterface $em */
        $em = $this->module->get('doctrine.orm.entity_manager');

        /** @var AdapterCustomerRepository $repository */
        $repository = $this->module->get('drsoft_fr.module.validate_customer_pro.repository.adapter_customer_repository');

        /** @var AdapterCustomer $obj */
        $obj = $repository->findOneBy([
            'idCustomer' => (int)$customer->id
        ]);

        if (null === $obj) {
            return true;
        }

        $em->remove($obj);
        $em->flush();

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
     * Runs the process to handle object deletion.
     *
     * @return bool Returns true if the object deletion process is successful, false otherwise.
     */
    public function run(): bool
    {
        try {
            if (false === $this->checkObject()) {
                return true;
            }

            return $this->handleDeletionCustomer($this->props['object']);
        } catch (Throwable $t) {
            $this->handleException($t);

            return false;
        }
    }
}
