<?php

namespace Erp\PropertyBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\PropertySettings;
use Erp\UserBundle\Entity\User;

class PropertyFixture extends Fixture {

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $objectManager) {
        /** @var User $tenant */
        $tenant = $this->getReference('peterparker@test.com');
        /** @var User $manager */
        $manager = $this->getReference('tonystark@test.com');

        /** @var PropertySettings $propertySettings */
        $propertySettings = new PropertySettings();
        $propertySettings->setAllowAutoDraft(false);
        $propertySettings->setAllowCreditCardPayments(true);
        $propertySettings->setAllowPartialPayments(false);
        $propertySettings->setDayUntilDue('3');
        $propertySettings->setPaymentAmount('999');
        $objectManager->persist($propertySettings);
        $objectManager->flush();

        $object = new Property();
        $object->setSettings($propertySettings);
        $object->setTenantUser($tenant)
                ->setName('Test Property')
                ->setUser($manager)
                ->setStatus(Property::STATUS_DRAFT);

        $tenant->setTenantProperty($object);
        $manager->addProperty($object);

        $objectManager->persist($tenant);
        $objectManager->persist($object);
        $objectManager->flush();
    }

}
