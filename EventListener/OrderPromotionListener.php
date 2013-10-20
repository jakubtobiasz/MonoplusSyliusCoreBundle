<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\Promotion\CouponHandler;
use Sylius\Bundle\PromotionsBundle\Processor\PromotionProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order promotion listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderPromotionListener
{
    /**
     * Order promotion processor.
     *
     * @var PromotionProcessorInterface
     */
    protected $promotionProcessor;

    /**
     * @var CouponHandler
     */
    protected $couponHandler;

    /**
     * Constructor.
     *
     * @param PromotionProcessorInterface $promotionProcessor
     * @param CouponHandler $couponHandler
     */
    public function __construct(PromotionProcessorInterface $promotionProcessor, CouponHandler $couponHandler)
    {
        $this->promotionProcessor = $promotionProcessor;
        $this->couponHandler = $couponHandler;
    }

    /**
     * Get the order from event and run the promotion processor on it.
     *
     * @param GenericEvent $event
     */
    public function processOrderPromotion(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order promotion listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        // remove former promotion adjustments as they are calculated each time the cart changes
        $order->removePromotionAdjustments();
        $this->couponHandler->handle($order);
        $this->promotionProcessor->process($order);
    }
}
