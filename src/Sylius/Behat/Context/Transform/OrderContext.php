<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\User\Repository\CustomerRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OrderContext implements Context
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Transform :order
     */
    public function getOrderByNumber($orderNumber)
    {
        $orderNumber = $this->getOrderNumber($orderNumber);
        $order = $this->orderRepository->findOneBy(['number' => $orderNumber]);

        Assert::notNull($order, sprintf('Cannot find order with number %s', $orderNumber));

        return $order;
    }

    /**
     * @Transform /^this order made by "([^"]+)"$/
     */
    public function getOrderByCustomer($email)
    {
        $customer = $this->customerRepository->findOneBy(['email' => $email]);
        Assert::notNull($customer, sprintf('Cannot find customer with email %s.', $email));

        $order = $this->orderRepository->findByCustomer($customer);
        $order = end($order);
        Assert::isInstanceOf($order, OrderInterface::class, sprintf('Cannot find order for %s.', $customer->getFullName()));

        return $order;
    }

    /**
     * @Transform :orderNumber
     * @Transform /^an order "([^"]+)"$/
     */
    public function getOrderNumber($orderNumber)
    {
        $orderNumber = str_replace('#', '', $orderNumber);

        return $orderNumber;
    }
}
