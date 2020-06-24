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

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use SM\Factory\FactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductReviewExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var ReviewFactoryInterface */
    private $productReviewFactory;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(
        ReviewFactoryInterface $productReviewFactory,
        ProductRepositoryInterface $productRepository,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $stateMachineFactory
    ) {
        $this->productReviewFactory = $productReviewFactory;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->stateMachineFactory = $stateMachineFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): ReviewInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ReviewInterface $productReview */
        $productReview = $this->productReviewFactory->createForSubjectWithReviewer(
            $options['product'],
            $options['author']
        );
        $productReview->setTitle($options['title']);
        $productReview->setComment($options['comment']);
        $productReview->setRating($options['rating']);
        $options['product']->addReview($productReview);

        $this->applyReviewTransition($productReview, $options['status'] ?: $this->getRandomStatus());

        return $productReview;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('title', function (Options $options): string {
                /** @var string $words */
                $words = $this->faker->words(3, true);

                return $words;
            })
            ->setDefault('rating', function (Options $options): int {
                return $this->faker->numberBetween(1, 5);
            })
            ->setDefault('comment', function (Options $options): string {
                /** @var string $sentences */
                $sentences = $this->faker->sentences(3, true);

                return $sentences;
            })
            ->setDefault('author', LazyOption::randomOne($this->customerRepository))
            ->setNormalizer('author', LazyOption::findOneBy($this->customerRepository, 'email'))
            ->setDefault('product', LazyOption::randomOne($this->productRepository))
            ->setNormalizer('product', LazyOption::findOneBy($this->productRepository, 'code'))
            ->setDefault('status', null)
        ;
    }

    private function getRandomStatus(): string
    {
        $statuses = [ReviewInterface::STATUS_NEW, ReviewInterface::STATUS_ACCEPTED, ReviewInterface::STATUS_REJECTED];

        return $statuses[random_int(0, 2)];
    }

    private function applyReviewTransition(ReviewInterface $productReview, string $targetState): void
    {
        /** @var StateMachineInterface $stateMachine */
        $stateMachine = $this->stateMachineFactory->get($productReview, 'sylius_product_review');
        $transition = $stateMachine->getTransitionToState($targetState);

        if (null !== $transition) {
            $stateMachine->apply($transition);
        }
    }
}
