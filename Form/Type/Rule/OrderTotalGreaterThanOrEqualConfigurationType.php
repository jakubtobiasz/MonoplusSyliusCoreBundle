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

namespace Sylius\Bundle\CoreBundle\Form\Type\Rule;

use Sylius\Bundle\ShippingBundle\Form\EventSubscriber\AddTotalFormSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class OrderTotalGreaterThanOrEqualConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventSubscriber(new AddTotalFormSubscriber());
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_shipping_method_rule_order_total_greater_than_or_equal_configuration';
    }
}
