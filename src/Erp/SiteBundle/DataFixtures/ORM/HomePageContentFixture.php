<?php

namespace Erp\SiteBundle\DataFixtures\ORM;

use Erp\SiteBundle\Entity\HomePageContent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class HomePageContentFixture extends Fixture {

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $objectManager) {
        $homePageContent = new HomePageContent();
        $homePageContent->setServiceBody('<div class="row"><div class="aboutus_title">Are you a property manager or landlord looking to streamline your process? Perhaps you&#39;re a tenant who wishes it was easier to pay rent?</div><p>Sound familiar? <a href="/register">That&#39;s why we created Zoobdoo.</a> So thet everything a property manager, landlord, or tenant needs can be found in one place - in a simple, easy-to-navigate platform.</p><p><a href="#features">Check out the features below.</a></p></div>');
        $homePageContent->setFeatureBody('<div class="features-title-block"><span>Zoobdoo Portal Features</span></div>

<div class="row text-capabilities">
<div class="col-md-6">
<h2>Landlord Capabilities:</h2>

<div class="block-service">
<div class="col-md-2">
<div class="manager1-icon icon">&nbsp;</div>
</div>

<div class="col-md-10 text-block-service">
<p><strong>Process</strong> online payments straight to your account</p>
</div>
</div>

<div class="block-service">
<div class="col-md-2">
<div class="manager2-icon icon">&nbsp;</div>
</div>

<div class="col-md-10 text-block-service">
<p><strong>accept</strong> applications electronically &amp; receive application fees</p>
</div>
</div>

<div class="block-service">
<div class="col-md-2">
<div class="manager3-icon icon">&nbsp;</div>
</div>

<div class="col-md-10 text-block-service">
<p><strong>run</strong> credit criminal &amp; eviction history</p>
</div>
</div>
</div>

<div class="col-md-6">
<h2>Tenant Capabilities:</h2>

<div class="block-service">
<div class="col-md-2">
<div class="tenant1-icon icon">&nbsp;</div>
</div>

<div class="col-md-10 text-block-service">
<p><strong>pay rent online</strong> (ahc &amp; credit cards accepted)</p>
</div>
</div>

<div class="block-service">
<div class="col-md-2">
<div class="tenant2-icon icon">&nbsp;</div>
</div>

<div class="col-md-10 text-block-service">
<p><strong>esign</strong> applications &amp; contracts</p>
</div>
</div>

<div class="block-service">
<div class="col-md-2">
<div class="tenant3-icon icon">&nbsp;</div>
</div>

<div class="col-md-10 text-block-service">
<p><strong>automated</strong> maintenance requests</p>
</div>
</div>
</div>
</div>
');
        $objectManager->persist($homePageContent);
        $objectManager->flush();
    }

}
