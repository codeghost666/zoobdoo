<?php

namespace Erp\SiteBundle\DataFixtures\ORM;

use Erp\CoreBundle\Entity\Image;
use Erp\SiteBundle\Entity\HomePageSlider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class HomePageSliderFixture extends Fixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $objectManager)
    {
        $image = new Image();
        $image->setName('slide1.jpeg');
        $image->setPath('uploads/images');
        $image->setCreatedDate();
        $image->setUpdatedDate();
        $objectManager->persist($image);
        $objectManager->flush();
        $homePageSlider = new HomePageSlider();
        $homePageSlider->setTitle('<p>Finally! An all-in-one online system for property managers, landlords &amp; tenants.</p>');
        $homePageSlider->setText('<p>Simpler. Time-saving. Better.</p>');
        $homePageSlider->setImage($image);
        $homePageSlider->setCreatedDate();
        $homePageSlider->setUpdatedDate();
        $objectManager->persist($homePageSlider);
        $objectManager->flush();

        $image = new Image();
        $image->setName('slide2.jpeg');
        $image->setPath('uploads/images');
        $image->setCreatedDate();
        $image->setUpdatedDate();
        $objectManager->persist($image);
        $objectManager->flush();
        $homePageSlider = new HomePageSlider();
        $homePageSlider->setTitle('<p>It&rsquo;s not easy to manage multiple properties. You need a system that works. Introducing Zoobdoo.</p>');
        $homePageSlider->setText('<p>A one-stop-shop for all your property manager needs.</p>');
        $homePageSlider->setImage($image);
        $homePageSlider->setCreatedDate();
        $homePageSlider->setUpdatedDate();
        $objectManager->persist($homePageSlider);
        $objectManager->flush();

        $image = new Image();
        $image->setName('slide3.jpeg');
        $image->setPath('uploads/images');
        $image->setCreatedDate();
        $image->setUpdatedDate();
        $objectManager->persist($image);
        $objectManager->flush();
        $homePageSlider = new HomePageSlider();
        $homePageSlider->setTitle('<p>Sick of taking multiple work-order related phone calls a day? Not anymore.</p>');
        $homePageSlider->setText('<p>With Zoobdoo, work-order requests are done online.</p>');
        $homePageSlider->setImage($image);
        $homePageSlider->setCreatedDate();
        $homePageSlider->setUpdatedDate();
        $objectManager->persist($homePageSlider);
        $objectManager->flush();

        $image = new Image();
        $image->setName('slide4.jpeg');
        $image->setPath('uploads/images');
        $image->setCreatedDate();
        $image->setUpdatedDate();
        $objectManager->persist($image);
        $objectManager->flush();
        $homePageSlider = new HomePageSlider();
        $homePageSlider->setTitle('<p>Admit it. You have a checkbook for one reason. And that&rsquo;s to pay your rent. Not anymore.</p>');
        $homePageSlider->setText('<p>Pay your rent online with Zoobdoo. Go ahead - get rid of that checkbook.</p>');
        $homePageSlider->setImage($image);
        $homePageSlider->setCreatedDate();
        $homePageSlider->setUpdatedDate();
        $objectManager->persist($homePageSlider);
        $objectManager->flush();

    }

}