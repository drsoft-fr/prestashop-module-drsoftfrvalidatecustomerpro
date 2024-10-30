<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Query\CmsPage;

use DrSoftFr\Module\ValidateCustomerPro\Exception\CmsPage\CmsPageConstraintException;

/**
 * Get all id_cms's
 */
final class GetIdCmsPagesQuery
{
    /**
     * @var int|null
     */
    private $idShop;

    /**
     * Class constructor.
     *
     * @param int|null $idShop The ID of the shop (optional).
     *
     * @throws CmsPageConstraintException
     */
    public function __construct(
        int $idShop = null
    )
    {
        $this->assertIsNullOrPositiveInt($idShop);
        $this->idShop = $idShop;
    }

    /**
     * @return int|null
     */
    public function getIdShop(): ?int
    {
        return $this->idShop;
    }

    /**
     * @param mixed $value
     *
     * @throws CmsPageConstraintException
     */
    private function assertIsNullOrPositiveInt($value)
    {
        if (null === $value || (is_int($value) && 0 < $value)) {
            return;
        }

        throw new CmsPageConstraintException(
            sprintf(
                'Invalid id "%s" provided.',
                var_export(
                    $value, true
                )
            ),
            CmsPageConstraintException::INVALID_NULL_OR_NON_NEGATIVE_INT
        );
    }
}
