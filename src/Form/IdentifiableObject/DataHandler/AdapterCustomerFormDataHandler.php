<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Form\IdentifiableObject\DataHandler;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use DrSoftFr\Module\ValidateCustomerPro\Data\Validator\AdapterCustomerValidator;
use DrSoftFr\Module\ValidateCustomerPro\Entity\AdapterCustomer;
use DrSoftFr\Module\ValidateCustomerPro\Exception\AdapterCustomer\AdapterCustomerConstraintException;
use DrSoftFr\Module\ValidateCustomerPro\Exception\AdapterCustomer\AdapterCustomerNotFoundException;
use DrSoftFr\Module\ValidateCustomerPro\Repository\AdapterCustomerRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;

final class AdapterCustomerFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var AdapterCustomerRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AdapterCustomerValidator
     */
    private $validator;

    /**
     * @param AdapterCustomerRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param AdapterCustomerValidator $validator
     */
    public function __construct(
        AdapterCustomerRepository     $repository,
        EntityManagerInterface $entityManager,
        AdapterCustomerValidator $validator
    )
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function create(array $data)
    {
        $this->validateData($data);

        $obj = new AdapterCustomer();

        $obj->hydrate($data);
        $this->entityManager->persist($obj);
        $this->entityManager->flush();

        return $obj->getId();
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function update($id, array $data): int
    {
        $this->validateData($data);

        /** @var AdapterCustomer $obj */
        $obj = $this->repository->find($id);

        if (null === $obj) {
            throw new AdapterCustomerNotFoundException(
                sprintf(
                    'AdapterCustomer with id "%d" was not found',
                    $id
                )
            );
        }

        $obj->hydrate($data);
        $this->entityManager->flush();

        return $obj->getId();
    }

    /**
     * Validate the given data
     *
     * @param array $data The data to be validated
     *
     * @return void Returns true if the validation passes, AdapterCustomerConstraintException otherwise
     *
     * @throws AdapterCustomerConstraintException
     */
    private function validateData(array $data): void
    {
        $this->validator->validate($data);
    }
}
