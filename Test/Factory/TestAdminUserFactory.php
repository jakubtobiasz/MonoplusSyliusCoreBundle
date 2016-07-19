<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Test\Factory;

use Sylius\Component\Core\Test\Factory\TestUserFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class TestAdminUserFactory implements TestUserFactoryInterface
{
    const DEFAULT_EMAIL = 'admin@example.com';
    const DEFAULT_FIRST_NAME = 'John';
    const DEFAULT_LAST_NAME = 'Doe';
    const DEFAULT_ROLE = 'ROLE_ADMINISTRATION_ACCESS';
    const DEFAULT_PASSWORD = 'password123';

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var FactoryInterface
     */
    private $adminUserFactory;

    /**
     * @param FactoryInterface $customerFactory
     * @param FactoryInterface $adminUserFactory
     */
    public function __construct(FactoryInterface $customerFactory, FactoryInterface $adminUserFactory)
    {
        $this->customerFactory = $customerFactory;
        $this->adminUserFactory = $adminUserFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create($email, $password, $firstName = self::DEFAULT_FIRST_NAME, $lastName = self::DEFAULT_LAST_NAME, $role = self::DEFAULT_ROLE)
    {
        $customer = $this->customerFactory->createNew();

        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);

        $user = $this->adminUserFactory->createNew();

        $user->setCustomer($customer);
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->enable();
        $user->addRole($role);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function createDefault()
    {
        return $this->create(
            self::DEFAULT_EMAIL,
            self::DEFAULT_PASSWORD,
            self::DEFAULT_FIRST_NAME,
            self::DEFAULT_LAST_NAME,
            self::DEFAULT_ROLE
        );
    }
}
