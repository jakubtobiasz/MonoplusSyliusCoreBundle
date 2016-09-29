<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Twig;

use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Templating\Helper\ProductVariantsPricesHelper;
use Sylius\Bundle\CoreBundle\Twig\ProductVariantsPricesExtension;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 *
 * @mixin ProductVariantsPricesExtension
 */
final class ProductVariantsPricesExtensionSpec extends ObjectBehavior
{
    function let(ProductVariantsPricesHelper $productVariantsPricesHelper)
    {
        $this->beConstructedWith($productVariantsPricesHelper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariantsPricesExtension::class);
    }

    function it_is_twig_extension()
    {
        $this->shouldHaveType(\Twig_Extension::class);
    }

    function it_has_functions()
    {
        $this->getFunctions()->shouldHaveFunction(
            new \Twig_SimpleFunction('sylius_product_variant_prices', [$this, 'getVariantsPrices'])
        );
    }

    function it_uses_helper_to_get_variants_prices(
        ProductInterface $product,
        ProductVariantsPricesHelper $productVariantsPricesHelper
    ) {
        $productVariantsPricesHelper->getPrices($product)->willReturn([['color' => 'purple', 'value' => 12345]]);

        $this->getVariantsPrices($product)->shouldReturn([['color' => 'purple', 'value' => 12345]]);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_product_variant_prices');
    }

    public function getMatchers()
    {
        return [
            'haveFunction' => function ($subject, $key) {

                if (!is_array($subject)) {
                    throw new FailureException('Subject of "hasFunction" matcher must be an array');
                }

                if (!$key instanceof \Twig_SimpleFunction) {
                    throw new FailureException('Key of "hasFunction" matcher must be \Twig_SimpleFunction object');
                }

                /** @var \Twig_SimpleFunction $subjectElement */
                foreach ($subject as $subjectElement) {
                    if ($subjectElement->getName() === $key->getName() && $subjectElement->getCallable()[1] === $key->getCallable()[1]) {
                        return true;
                    }
                }

                return false;
            },
        ];
    }
}
