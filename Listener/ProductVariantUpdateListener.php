<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\Processor\ProductVariantCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Event\ProductVariantUpdated;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ProductVariantUpdateListener
{
    private ProductVariantRepositoryInterface $productVariantRepository;

    private ProductVariantCatalogPromotionsProcessorInterface $productVariantCatalogPromotionsProcessor;

    private EntityManagerInterface $entityManager;

    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantCatalogPromotionsProcessorInterface $productVariantCatalogPromotionsProcessor,
        EntityManagerInterface $entityManager
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->productVariantCatalogPromotionsProcessor = $productVariantCatalogPromotionsProcessor;
        $this->entityManager = $entityManager;
    }

    public function __invoke(ProductVariantUpdated $event): void
    {
        /** @var ProductVariantInterface|null $variant */
        $variant = $this->productVariantRepository->findOneBy(['code' => $event->code]);
        if ($variant === null) {
            return;
        }

        $this->productVariantCatalogPromotionsProcessor->process($variant);

        $this->entityManager->flush();
    }
}
