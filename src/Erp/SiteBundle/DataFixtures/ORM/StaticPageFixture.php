<?php

namespace Erp\SiteBundle\DataFixtures\ORM;

use Erp\SiteBundle\Entity\StaticPage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class StaticPageFixture extends Fixture {

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $objectManager) {
        $staticPage = new StaticPage();
        $staticPage->setCode('about');
        $staticPage->setTemplate('index');
        $staticPage->setSlug('about');
        $staticPage->setMetaTitle('About Zoobdoo | zoobdoo.com');
        $staticPage->setMetaDescription('Zoobdoo provides landlords with an easier, more affordable way to manage their properties. Learn more about us here and sign up for our service today!');
        $staticPage->setHeaderTitle('About Us');
        $staticPage->setTitle('About Zoobdoo');
        $staticPage->setContent('<h3>Zoobdoo  is an online property management tool for property managers and tenants, and we love making everything easier. </h3>

<p>It’s what we do. It’s who we are. As a team of experienced professionals from many backgrounds, including realty and property management, we aspire for the online property management lifestyle: a place where property managers can effectively and affordably manage more properties with ease.  </p>

<p>Our goal is to create work that is honest and transparent. Solutions that are simple to understand, quick, and give managers the ability to manage far more properties then they are now. Passion and creativity drives us. We’re willing to go the distance for our property managers. We want them to walk away growing their business while saving more money, and we will do just about anything to get them there. </p>

<p>You can manage your rents, get background and <a href="https://zoobdoo.com/online-tenant-screening">credit checks for managers </a>, create rental contracts along with receiving online digital signatures, <a href="https://zoobdoo.com/pay-rent-online">accept rent payments online</a>, send rental applications and collect fees, and even post <a href="https://zoobdoo.com/post-apartment-for-rent">apartments for rent</a> from the Zoobdoo website onto other public sites. </p>

<p>Let’s us help you grow your business! Drop us an email now at <a href="mailto:info@zoobdoo.com">info@zoobdoo.com</a>, and let’s get started! </p>

<ul><li>• <a href="https://zoobdoo.com/manager-features" style="color:#555;" title="Managers">Manager Features</a></li>
	<li>• <a href="https://zoobdoo.com/tenant-features" style="color:#555;" title="Tenants">Tenant Features</a></li>
	<li>• <a href="https://zoobdoo.com/contact" style="color:#555;" title="Contact Zoobdoo">Contact us</a></li>
</ul>');
        $staticPage->setInSubmenu(1);
        $staticPage->setWithSubmenu(1);
        $staticPage->setCreatedDate();
        $staticPage->setUpdatedDate();
        $objectManager->persist($staticPage);
        $objectManager->flush();




        $staticPage = new StaticPage();
        $staticPage->setCode('faq');
        $staticPage->setTemplate('index');
        $staticPage->setSlug('faq');
        $staticPage->setMetaTitle('Frequently Asked Questions | Zoobdoo');
        $staticPage->setMetaDescription('Have questions about how Zoobdoo works? Click here for answers to our Frequently Asked Questions, or email info@zoobdoo.com for more information.');
        $staticPage->setHeaderTitle('FAQ');
        $staticPage->setTitle('FAQ');
        $staticPage->setContent('<h3>What is Zoobdoo?</h3>

<p>Our service allows property managers to build their business by keeping things simple and affordable. Now property managers have real options. manage your rents, get background and <a href="https://zoobdoo.com/online-tenant-screening">credit checks for property managers</a>, create rental contracts along with receiving online digital signatures, <a href="https://zoobdoo.com/pay-rent-online">accept rent payments online</a>, send rental applications and collect fees, and even post <a href="https://zoobdoo.com/post-apartment-for-rent">apartments for rent</a> from the Zoobdoo website onto other public sites. </p>

<h3> </h3>

<h3>How do the transactions occur?</h3>

<p>The tenant submits payment through Zoobdoo tenant portal, payment processes through the Automatic Clearing House (ACH) or Credit Processor (CC), and payment is deposited directly in property managers account. </p>

<p> </p>

<h3>Price: How much does it cost?</h3>

<ul><li>Property Managers pay nothing! No setup or monthly fees! No contracts or obligations! </li>
	<li>Tenant pays a small $3.00 flat fee per ACH transaction.</li>
	<li>Tenant pays a 2.5% per Credit Card transaction. (Accept Visa, Mastercard and Discover)                   </li>
	<li>Application fee per applicant submission, $1.00. (Perspective Tenant pays during application process)</li>
	<li>Credit, background, and eviction check, $15.</li>
	<li>Posting properties online, FREE.</li>
</ul><p> </p>

<h3>Do I have to sign a contract?</h3>

<p>No, there is no long or short term contract or obligation. You can cancel at anytime.</p>

<p> </p>

<h3>Are there any hidden fees or obligations?</h3>

<p>There are no hidden fees. No contracts or obligations.</p>

<p> </p>

<h3>How do I start accepting tenant payments directly to my account? </h3>

<p>For your security we require the following documents be uploaded using the SendSafely secured and encrypted link.</p>

<ul><li><em><strong>A copy of a voided business bank account check for account to be used.</strong></em></li>
	<li><em><strong>Past 2 months bank statements on associated account to be used.</strong></em></li>
</ul><p>Your merchant account will be operational within 1 business day. </p>

<p> </p>

<h3>How long does it take for a payment to be deposited?</h3>

<p>Funds will typically be deposited into the Payee\'s banking account 3-5 business days after we begin processing the payment. This usually depends on your own bank. </p>

<p> </p>

<h3>What are the services offered?</h3>

<p><strong>Included Free in Monthly Fee:                                                                                                                            </strong></p>

<p>Receive Credit Card and ACH Rent Payments Online                                                                    </p>

<p>Payment History                                                                                                                                                                             </p>

<p>Maintenance/Service Requests Online                                                        </p>

<p>Online Forum                                                                                                </p>

<p>Upload Your Own Rental Applications and Contracts And Charge Fees</p>

<p>Save All Documentation </p>

<p>Upload And Send Receipts</p>

<p>Create Online Rental Application</p>

<p>Create Online Rental Application</p>

<p>Post Property Vacancies Online </p>

<p><strong>Pay as You Go:</strong></p>

<p>Full Credit, Eviction, and Criminal Screening - $15</p>

<p>E-signature per Form - $2.95</p>

<p> </p>

<h3>Can property landlords become members?</h3>

<p>Yes, landlords can become members too for a monthly fee of 11.95 per month.</p>

<p> </p>

<h3>Still have questions?</h3>

<p>Please submit any questions through the <a href="https://zoobdoo.com/contact" style="color:rgb(85,85,85);" title="Contact Zoobdoo">contact us</a> page or info@zoobdoo.com. </p>');
        $staticPage->setInSubmenu(1);
        $staticPage->setWithSubmenu(1);
        $staticPage->setCreatedDate();
        $staticPage->setUpdatedDate();
        $objectManager->persist($staticPage);
        $objectManager->flush();




        $staticPage = new StaticPage();
        $staticPage->setCode('manager-features');
        $staticPage->setTemplate('features');
        $staticPage->setSlug('manager-features');
        $staticPage->setMetaTitle('Manager Features | Zoobdoo');
        $staticPage->setMetaDescription('Discover an easy solution for managing all your properties. Sign up for Zoobdoo to post vacancies and receive payments and maintenance requests online.');
        $staticPage->setHeaderTitle('Manager Features');
        $staticPage->setTitle('Manager Features');
        $staticPage->setContent('<div class="container">
<div class="row">
<div class="col-xs-3 features"><img alt="integrate.png" src="/assets/images/integrate.png" /><p>Integrate your property management website.</p></div>
<div class="col-xs-3 features"><img alt="payment.png" src="/assets/images/payment.png" /><p>Process online payments straight to your account</p></div>
<div class="col-xs-3 features"><img alt="application.png" src="/assets/images/application.png" /><p>Accept applications electronically and receive application fees.</p></div>
<div class="col-xs-3 features"><img alt="thumbs.png" src="/assets/images/thumbs.png" /><p>Run credit, criminal, and eviction history.</p></div>
</div>
<div class="row">
<div class="col-xs-3 features"><img alt="maintenance.png" src="/assets/images/maintenance.png" /><p>Automated maintenance requests.</p></div>
<div class="col-xs-3 features"><img alt="2.png" src="/assets/images/2.png" /><p>E-sign rental applications and contracts.</p></div>
<div class="col-xs-3 features"><img alt="cycle.png" src="/assets/images/cycle.png" /><p>No contracts! Sign-up now with no obligations.</p></div>
<div class="col-xs-3 features"><img alt="secure.png" src="/assets/images/secure.png" /><p>Keep information safe with multiple firewalls, SSL certification, and PCI compliance verification.</p></div>
</div>
<div class="row">
<div class="col-xs-12">
<h3>No setup or monthly fees! No contracts or obligations!</h3>
</div>
</div>
</div>');
        $staticPage->setInSubmenu(1);
        $staticPage->setWithSubmenu(0);
        $staticPage->setCreatedDate();
        $staticPage->setUpdatedDate();
        $objectManager->persist($staticPage);
        $objectManager->flush();





        $staticPage = new StaticPage();
        $staticPage->setCode('tenant-features');
        $staticPage->setTemplate('features');
        $staticPage->setSlug('tenant-features');
        $staticPage->setMetaTitle('Tenant Features | Zoobdoo');
        $staticPage->setMetaDescription('With Zoobdoo, tenants can pay their rent online, submit maintenance requests, search available properties and more. View more great features here!');
        $staticPage->setHeaderTitle('Tenant Features');
        $staticPage->setTitle('Tenant Features');
        $staticPage->setContent('<div class="row">
<div class="col-md-2 col-md-offset-1"><img alt="secure.png" src="/assets/images/payonline.png" /><p>Pay rent online (ACH and Credit Card Accepted)</p>
</div>

<div class="col-md-2"><img alt="secure.png" src="/assets/images/maintenance.png" /><p>Automated maintenance requests</p>
</div>

<div class="col-md-2"><img alt="secure.png" src="/assets/images/application.png" /><p>E-sign applications and contracts</p>
</div>

<div class="col-md-2"><img alt="secure.png" src="/assets/images/secure.png" /><p>Keep information safe with multiple firewalls, SSL certification, and PCI compliance verification.</p>
</div>

<div class="col-md-2"><img alt="secure.png" src="/assets/images/cycle.png" /><p>No monthly fee.</p>
</div>
</div>

<p> </p>

<p> </p>

<h3><a href="https://zoobdoo.com/contact" style="color:rgb(85,85,85);" title="Contact Zoobdoo">Contact us</a> today to learn more about our tenant features.</h3>

<p>Only a small flat fee of $3 charged per ACH transaction.</p>

<p>2.5% charged per credit card transaction.</p>

<p> </p>');
        $staticPage->setInSubmenu(1);
        $staticPage->setWithSubmenu(0);
        $staticPage->setCreatedDate();
        $staticPage->setUpdatedDate();
        $objectManager->persist($staticPage);
        $objectManager->flush();




        $staticPage = new StaticPage();
        $staticPage->setCode('contact');
        $staticPage->setTemplate('index');
        $staticPage->setSlug('contact');
        $staticPage->setMetaTitle('Contact Us | Zoobdoo');
        $staticPage->setMetaDescription('Zoobdoo welcomes your questions, comments and feedback. To contact Zoobdoo, simply submit a contact form here, or email us at info@zoobdoo.com.');
        $staticPage->setHeaderTitle('Contact');
        $staticPage->setTitle('Contact');
        $staticPage->setContent('<p>Address: <br />
3009 Sunrise Bay Ave<br />
Las Vegas, NV 89031</p>

<p>Email: info@zoobdoo.com<br />
Facebook: <a href="https://www.facebook.com/zoobdoomanager/" target="_blank" style="color:#706e7b;">https://www.facebook.com/zoobdoomanager/</a><br />
Twitter: <a href="https://twitter.com/Zoobdoo_" target="_blank" style="color:#706e7b;">https://twitter.com/Zoobdoo_</a><br />
LinkedIn: <a href="https://www.linkedin.com/company/zoobdoo-com" target="_blank" style="color:#706e7b;">https://www.linkedin.com/company/zoobdoo-com</a><br />
Pinterest: <a href="https://www.pinterest.com/zoobdoocom/pins/" target="_blank" style="color:#706e7b;">https://www.pinterest.com/zoobdoocom/pins/</a><br />
</p>');
        $staticPage->setInSubmenu(1);
        $staticPage->setWithSubmenu(1);
        $staticPage->setCreatedDate();
        $staticPage->setUpdatedDate();
        $objectManager->persist($staticPage);
        $objectManager->flush();


        $staticPage = new StaticPage();
        $staticPage->setCode('how-it-works');
        $staticPage->setTemplate('index');
        $staticPage->setSlug('how-it-works');
        $staticPage->setMetaTitle('Zoobdoo: How It Works');
        $staticPage->setMetaDescription('Zoobdoo: How It Works');
        $staticPage->setHeaderTitle('How It Works');
        $staticPage->setTitle('How It Works:');
        $staticPage->setContent('<div class="wrap">
<div class="content-row">
<div>Signup and register your account</div>
</div>

<div class="content-row">
<div class="fa fa-arrow-down arrow"> </div>
</div>

<div class="content-row">
<div>Enter your credit card or bank information along with any tenant(s) information and emails to begin</div>
</div>

<div class="content-row">
<div class="fa fa-arrow-down arrow"> </div>
</div>

<div class="content-row">
<div>Tenant(s) will receive an email link to sign up and register their own personal Zoobdoo tenant account</div>
</div>

<div class="content-row">
<div class="fa fa-arrow-down arrow"> </div>
</div>

<div class="content-row">
<div>Your Managers services are now available</div>
</div>
</div>');
        $staticPage->setInSubmenu(1);
        $staticPage->setWithSubmenu(1);
        $staticPage->setCreatedDate();
        $staticPage->setUpdatedDate();
        $objectManager->persist($staticPage);
        $objectManager->flush();


        $staticPage = new StaticPage();
        $staticPage->setCode('terms-of-service');
        $staticPage->setTemplate('index');
        $staticPage->setSlug('terms-of-service');
        $staticPage->setMetaTitle('Zoobdoo: Terms of Service');
        $staticPage->setMetaDescription('Zoobdoo: Terms of Service');
        $staticPage->setHeaderTitle('Terms of Service');
        $staticPage->setTitle('Terms of Service');
        $staticPage->setContent('<p><em>These Terms of Use were last revised on September 29, 2015.</em></p>

<p><strong>Zoobdoo LLC, a Nevada limited liability company ("Zoobdoo") provides the ZOOBDOO.COM site and related services subject to your compliance with the terms and conditions set forth below. Your use of ZOOBDOO.COM signifies your acknowledgement of and agreement to these Terms of Use. </strong></p>

<p>These Terms of Use apply to all users of the website ZOOBDOO.COM and any other affiliated websites owned or controlled by Zoobdoo, including any and all services and online software available through these sites (collectively "ZOOBDOO.COM"). The term "users" includes both registered members of ZOOBDOO.COM and any other person that accesses ZOOBDOO.COM at any point for any amount of time.</p>

<p> </p>

<p><strong>1. ONLINE VENUE</strong></p>

<p>1.1       <u>Online Venue</u>. ZOOBDOO.COM is an online venue ("Venue") allowing landlords and tenants to interact with each other, including paying and receiving rent and other services. Zoobdoo is not involved in the actual transactions between landlords and tenants or any other users. We do not own or manage, nor can we contract for, any of the properties listed on this site. Zoobdoo is not a landlord, real estate agent, or broker. Rather, Zoobdoo merely provides the venue for users, including landlords and tenants to interact. The actual lease and other transactions are directly between the landlords and tenants who use this site. We are not a party to any rental or other agreement between users. This is true even if you sign a lease using Zoobdoo or use one of Zoobdoo’s forms. While we may facilitate the signing of lease agreements or provide other tools, services or products, we are not a party to any rental or other agreements between users.</p>

<p>1.2       <u>Local Laws and Regulations</u>. You agree that you are responsible for and agree to abide by all laws, rules and regulations applicable to you, including laws, rules, regulations or other requirements relating to taxes, credit cards, data and privacy, taxes, permits or license requirements, zoning ordinances, safety compliance and compliance with all anti-discrimination and fair housing laws. Each state and many local municipalities have unique rules and regulations concerning rental properties, landlords, tenants, and property managers. Nothing on this website constitutes legal advice and Zoobdoo makes no warranty that use of ZOOBDOO.COM or the forms or services offered by ZOOBDOO.COM is permissible under your local laws and regulations. The legal analysis of whether a particular lease provision or landlord practice is permissible in your area cannot be properly represented or accounted for on a web page. You are responsible for following all local laws and regulations, including those related to rental properties in your area. If you have a question about specific laws or regulations in your area, you should contact a local attorney who is authorized to practice in your sate and it familiar with the local laws applicable to you.</p>

<p>1.3       <u>Payments</u>. By providing us with your banking or other payment information, you authorize us to use it and disclose it to our payment gateway providers for the purpose of processing payments. We never take custody of money you transfer, we cannot provide refunds, and we’re not responsible for what recipients do with the payments you make.</p>

<p>1.4       <u>Credit Reports</u>. We are not a credit bureau or credit reporting agency and do not control the contents of credit reports, including reports obtained through ZOOBDOO.COM. If you believe that any information contained in your credit report is inaccurate or incomplete, you should dispute it directly with the credit reporting agency that issued the report.</p>

<p>1.5       <u>No Control Over User Services</u>. Zoobdoo has no control over and does not guarantee: (a) the existence, quality, safety or legality of the services provided among users; (b) the rental properties advertised; (c) the truth or accuracy of users\' content or listings; (d) the ability of landlords to actually rent a property; the ability of tenants to pay rent; or (e) that a landlord or tenant will actually complete a transaction or provide a service. All property listings on ZOOBDOO.COM are the sole responsibility of the user (who may be the owner or a property manager or duly authorized property manager or agent of the owner) and we specifically disclaim any and all liability arising from the alleged accuracy of the listings or any alleged breaches of contract on a user\'s part.</p>

<p>1.6       <u>Legal Compliance</u>. You agree not to use the Venue to offer services which are illegal in Nevada, the United States, or in your local jurisdiction. By using the Venue, you agree to comply with all applicable domestic and international laws, statutes, ordinances and regulations regarding your activity on ZOOBDOO.COM. In addition, you shall be responsible for paying any and all taxes applicable to any services offered through ZOOBDOO.COM.</p>

<p>1.7       <u>Use of the Venue</u>. If you choose to use the Venue, you agree to:</p>

<p>(a) post accurate information about yourself and any properties or services offered by you on the Venue;</p>

<p>(d) list properties and services in appropriate categories;</p>

<p>(e) abide by all applicable laws, third party rights, and Zoobdoo policies;</p>

<p>(f) pay all required fees to ZOOBDOO.COM for use of the service; and</p>

<p>(g) use the Venue at your own risk.</p>

<p>1.8       <u>Placing Listings</u>. If you place a listing on the Venue, you are legally and solely responsible for the content of the listing and your interactions with any landlords or tenants. By placing a listing on the Venue, you warrant that all aspects of the listing comply with Zoobdoo\'s published policies. You further warrant that you are in compliance with Zoobdoo\'s published policies. You also warrant that you may legally post the listing.</p>

<p>1.9       <u>Responding to Listings</u>. If you respond to a listing on the Venue, you are legally responsible for your interactions with any landlords or tenants. You further warrant that you are in compliance with Zoobdoo\'s published policies. You are responsible for reading the full item listing before completing a transaction. When you enter into an agreement with a landlord or tenant you are entering into a legally binding contract directly with the landlord or tenant, not with Zoobdoo.</p>

<p>1.10     <u>Scams</u>. Use common sense when placing or responding to a listing through the Venue. Use the same common sense you would use in the real world when reading an ad. If it is too good to be true, it is a scam. For more information about various online scams and how to avoid them, go to http://OnGuardOnline.gov.</p>

<p>1.11     <u>Cancellation of Listings or Accounts</u>. Zoobdoo reserves the right to terminate any listing or to cancel the account of any Venue user at any time and for any reason.</p>

<p>1.12     <u>Additional Policies</u>. The use of the Venue is subject to published policies posted on the ZOOBDOO.COM website, including policies regarding listing items, selling items, buying items, and the current list of service fees. Your use of the Venue is subject to those policies, which are incorporated into this Agreement by this reference.</p>

<p> </p>

<p><strong>2. WEBSITE ACCESS</strong></p>

<p>2.1 Zoobdoo grants you a limited, revocable, nonexclusive license to access ZOOBDOO.COM for your own personal use.</p>

<p>2.2       In order to access some features of ZOOBDOO.COM, you will have to create an account. You may never use another\'s account without the account holder\'s permission. When creating your account, you must provide accurate and complete information. Should any of your information change after submitting it to ZOOBDOO.COM, you are required to update that information immediately.</p>

<p>2.3       You are solely responsible for the activity that occurs on your account, and you must keep your account password secure. You must notify Zoobdoo immediately of any breach of security or unauthorized use of your account. Although Zoobdoo will not be liable for your losses caused by any unauthorized use of your account, you may be liable for the losses of Zoobdoo or others due to such unauthorized use.</p>

<p>2.4       You agree not to use or launch any automated system, including without limitation, "robots," "spiders," and "offline readers" that access ZOOBDOO.COM. You agree not to collect or harvest any personally identifiable information, including account names, from ZOOBDOO.COM, nor to use the communication systems provided by ZOOBDOO.COM for any commercial solicitation or illegal or improper purposes.</p>

<p>2.5       Notwithstanding the foregoing, Zoobdoo grants the operators of recognized international public search engines, such as Google and Bing permission to use spiders to copy materials from the site for the sole purpose of creating publicly available searchable indices of the materials. Zoobdoo reserves the right to revoke these exceptions either generally or in specific cases.</p>

<p>2.6       <u>Termination of Account</u>. You agree that Zoobdoo, in its sole discretion, has the right (but not the obligation) to delete or deactivate your account, block your email or IP address, or otherwise terminate your access to or use of ZOOBDOO.COM (or any part thereof), immediately and without notice, and remove and discard any content within ZOOBDOO.COM, for any reason, including, without limitation, if Zoobdoo believes that you have acted inconsistently with the letter or spirit of the Terms of Use. Further, you agree that Zoobdoo shall not be liable to you or any third-party for any termination of your access to ZOOBDOO.COM. Further, you agree not to attempt to use ZOOBDOO.COM after said termination.</p>

<p>2.7       <u>Termination of Service</u>. Zoobdoo reserves the right to modify or discontinue, temporarily or permanently, its website, including its interactive features, blogs, forums, comments (or any part thereof) with or without notice at any time. You agree that Zoobdoo shall not be liable to you or to any third party for any modification, suspension, or discontinuance of any service.</p>

<p>2.7       <u>Third Party Links</u>. ZOOBDOO.COM may contain links to third party websites that are not owned or controlled by Zoobdoo. Zoobdoo has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third party websites. In addition, Zoobdoo will not and cannot censor or edit the content of any third-party site. You expressly relieve Zoobdoo from any and all liability arising from your use of any third-party website. Accordingly, we encourage you to be aware when you leave the ZOOBDOO.COM website and to read the terms and conditions and privacy policy of other websites that you visit.</p>

<p>2.8       <u>Consent to Electronic Service</u>. When you use ZOOBDOO.COM or send e-mails to us, you are communicating with us electronically. You consent to receive communications from us electronically. We will communicate with you by e-mail or by posting notices on ZOOBDOO.COM. You agree that all agreements, notices, disclosures and other communications that we provide to you electronically satisfy any legal requirement that such communications be in writing.</p>

<p> </p>

<p><strong>3. THIRD PARTY SUBMISSIONS / INTERACTIVE FEATURES</strong></p>

<p>3.1       The ZOOBDOO.COM website may permit the submission, hosting, sharing, and/or publishing of text, photographs, audio, videos, reviews, or other content, including property information and property listings, by you, other users, and other third parties such as our partners or affiliates ("Third Party Submissions").</p>

<p>3.2       Third Party Submissions also include, but are not limited to, property information, reviews, photograph submissions, profile submissions, and any other interactive area of ZOOBDOO.COM.</p>

<p>3.3       Third Party Submissions may contain the views and opinions of individual content authors. Third Party Submissions are for informational purposes only and do not necessarily reflect the thoughts and opinions of Zoobdoo or any officer, employee, agent, or affiliate thereof.</p>

<p>3.4       By posting, submitting or uploading a Third Party Submission to any area of ZOOBDOO.COM, you automatically grant, and you represent and warrant that you have the right to grant, to Zoobdoo an irrevocable, perpetual, non-exclusive, fully paid, sublicensable, transferable, worldwide license to use, copy, perform, display, and distribute said Third Party Submission and to prepare derivative works of, or incorporate into other works, said Third Party Submission, and to grant and authorize sublicenses (through multiple tiers) of the foregoing. Furthermore, by posting Third Party Submissions to ZOOBDOO.COM, you automatically grant Zoobdoo all rights necessary to prohibit any subsequent aggregation, display, copying, duplication, reproduction, or exploitation of the Third Party Submission by any party for any purpose.</p>

<p>3.5       You acknowledge that the interactive features on ZOOBDOO.COM are not for private communications. You acknowledge that you have no expectation of privacy with regard to any submission to ZOOBDOO.COM. Zoobdoo cannot guarantee the security of any information you disclose through ZOOBDOO.COM. You make such disclosures at your own risk. Zoobdoo has no obligation to retain or provide you copies of Third Party Submissions</p>

<p>3.6       By posting Third Party Submissions to ZOOBDOO.COM or by using any other interactive area of the website, you specifically agree to comply with each of the following:</p>

<p>(a) You will not post or transmit any material that violates or infringes the rights of any other party, including, without limitation, rights of privacy, rights of publicity, copyright, trademark, or other intellectual property rights.</p>

<p>(b) If your employer has rights to intellectual property you create, you have either (i) received permission from your employer to post or make available the material, including but not limited to any software, or (ii) secured from your employer a waiver as to all rights in or to the material.</p>

<p>(c) You have fully complied with any third-party licenses relating to the material you post or transmit and have done all things necessary to successfully pass through to Book By Part any required terms.</p>

<p>(d) You will not post or transmit any material that is false, deceptive, misleading, or deceitful.</p>

<p>(e) You will not post or transmit any material that is abusive, hateful, racist, bigoted, sexist, harassing, threatening, inflammatory, defamatory, vulgar, obscene, sexually-oriented, profane or is otherwise in violation of any applicable law, rule or regulation.</p>

<p>(f) You will not post or transmit any material that deceptively impersonates any person or entity.</p>

<p>(g) Your username or the material you submit is not named in a manner that misleads users into thinking that you are another person or company.</p>

<p>(h) You will post content that constitutes or contains any form of advertising or solicitation or for commercial purposes. Do not post a URL unless it directly relates to a user\'s question.</p>

<p>(i)        You will not post or transmit any software or computer files that contains malware, computer viruses, worms, Trojan horses, rootkits, spyware, adware, and other malicious or unrequested software, computer code, or file.</p>

<p>(j) You will not post or transmit any content that is intended to promote or commit an illegal act.</p>

<p>Failure to comply with the above rules may lead to you being immediately and permanently banned, with notification to law enforcement and/or your internet service provider.</p>

<p>3.7       When posting Third Party Submissions, you agree never to use a third-party agent, service, or intermediary that offers to post Third Party Submissions to ZOOBDOO.COM on your behalf ("Posting Agent"). Posting Agents are not permitted to post Third Party Submissions on behalf of others, to cause Third Party Submissions to be so posted, or otherwise access ZOOBDOO.COM to facilitate posting Third Party Submissions on behalf of others, except with express written permission from Zoobdoo.</p>

<p>3.8       You are and shall remain solely responsible for any and all Third Party Submissions you make through ZOOBDOO.COM. You acknowledge that any information you disclose through ZOOBDOO.COM becomes public information and can be used by people you do not know. Accordingly, you should exercise caution when deciding to disclose any information on ZOOBDOO.COM. Any such disclosures are at your own risk.</p>

<p>3.9       Zoobdoo does not prescreen Third Party Submissions submitted and Zoobdoo has no duty to monitor any interactive area of its website. Although we may monitor or review Third Party Submissions from time to time, we do not actively monitor the Third Party Submissions of the interactive areas, including Event listings, and we are not under any obligation to do so. Each user is solely responsible and liable for the contents of his or her Third Party Submissions, and we are not responsible in any way for the content or opinions expressed therein. We have the right, but not the obligation, to remove, edit or move, at any time, any material posted, in each case as we deem appropriate. Zoobdoo may refuse, to post, deliver, remove, modify or otherwise use or take any action with respect to any submission in any ZOOBDOO.COM forum.</p>

<p>3.10     You acknowledge that by participating in any interactive area on this website, you are granting Zoobdoo the unrestricted right, throughout the world and in perpetuity, to copy, sublicense, adapt, transmit, perform, display or otherwise use, at no cost whatsoever to Zoobdoo, any and all material or content you post or submit, including, without limitation, all intellectual property rights embodied therein.</p>

<p>3.11     You must not describe or assign keywords to your Third Party Submissions in a misleading or unlawful manner, including in a manner intended to trade on the name or reputation of others, and Zoobdoo may change or remove any description or keyword that it considers inappropriate or unlawful in Zoobdoo\'s sole discretion.</p>

<p> </p>

<p><strong>4. LIMITS ON THIRD PARTY SUBMISSIONS - DIGITAL MILLENNIUM COPYRIGHT ACT</strong></p>

<p>4.1       You understand that when using the ZOOBDOO.COM website, you will be exposed to Third Party Submissions from a variety of sources, and that Zoobdoo is not responsible for the accuracy, usefulness, safety, or intellectual property rights of or relating to such Third Party Submissions. You further understand and acknowledge that you may be exposed to Third Party Submissions that are inaccurate, offensive, indecent, or objectionable, and you agree to waive, and hereby do waive, any legal or equitable rights or remedies you have or may have against Zoobdoo with respect thereto, and agree to indemnify and hold Zoobdoo, its owners, operators, affiliates, and agents, licensors and licensees, harmless to the fullest extent allowed by law regarding all matters related to your use of the site.</p>

<p>4.2       Zoobdoo does not endorse any Third Party Submission or any opinion, recommendation, or advice expressed therein, and Zoobdoo expressly disclaims any and all liability in connection with Third Party Submissions.</p>

<p>4.3       Zoobdoo does not permit copyright infringing activities and infringement of intellectual property rights on its Website, and Zoobdoo will remove all Content and Third Party Submissions if properly notified that such Content or Third Party Submission infringes on another\'s intellectual property rights. Zoobdoo reserves the right to remove Content and Third Party Submissions without prior notice.</p>

<p>4.4       If you are a copyright owner or an agent thereof and believe that any Third Party Submission or other Content infringes upon your copyrights, you may submit a notification pursuant to the Digital Millennium Copyright Act ("DMCA") by providing our Copyright Agent with the following information in writing (<em>see</em> 17 U.S.C 512(c)(3) for further detail):</p>

<ul><li>- A physical or electronic signature of a person authorized to act on behalf of the owner of an exclusive right that is allegedly infringed;</li>
	<li>- Identification of the copyrighted work claimed to have been infringed, or, if multiple copyrighted works at a single online site are covered by a single notification, a representative list of such works at that site;</li>
	<li>- Identification of the material that is claimed to be infringing or to be the subject of infringing activity and that is to be removed or access to which is to be disabled and information reasonably sufficient to permit the service provider to locate the material;</li>
	<li>- Information reasonably sufficient to permit the service provider to contact you, such as an address, telephone number, and, if available, an electronic mail;</li>
	<li>- A statement that you have a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law;</li>
	<li>- A statement that the information in the notification is accurate, and under penalty of perjury, that you are authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.</li>
</ul><p>Zoobdoo\'s designated Copyright Agent to receive notifications of claimed infringement is:</p>

<p>Zoobdoo LLC</p>

<p>Attn: Copyright Agent</p>

<p><em>copyright@zoobdoo.com</em></p>

<p>You acknowledge that if you fail to comply with all of the requirements of this Section, your DMCA notice may not be valid.</p>

<p>4.5       Counter-Notice. If you believe that your Third Party Submission that was removed (or to which access was disabled) is not infringing, or that you have the authorization from the copyright owner, the copyright owner\'s agent, or pursuant to the law, to post and use the content in your Third Party Submission, you may send a counter-notice containing the following information to the Copyright Agent:</p>

<ul><li>- Your physical or electronic signature;</li>
	<li>- Identification of the content that has been removed or to which access has been disabled and the location at which the content appeared before it was removed or disabled;</li>
	<li>- A statement that you have a good faith belief that the content was removed or disabled as a result of mistake or a misidentification of the content;</li>
	<li>- Your name, address, telephone number, and e-mail address, a statement that you consent to the jurisdiction of the federal court in Las Vegas, Nevada, and a statement that you will accept service of process from the person who provided notification of the alleged infringement.</li>
</ul><p>If a counter-notice is received by the Copyright Agent, ZOOBDOO.COM may send a copy of the counter-notice to the original complaining party informing that person that it may replace the removed content or cease disabling it in 10 business days. Unless the copyright owner files an action seeking a court order against the content provider, member or user, the removed content may be replaced, or access to it restored, in 10 to 14 business days or more after receipt of the counter-notice, at ZOOBDOO.COM sole discretion.</p>

<p> </p>

<p><strong>5. INTELLECTUAL PROPERTY INFORMATION</strong></p>

<p>5.1       With the exception of Third Party Submissions, all content on the ZOOBDOO.COM website, including without limitation, the text, software, scripts, tools, graphics, photos, sounds, music, videos, and interactive features ("Content") and the trademarks, service marks and logos contained therein ("Marks"), are owned by or licensed to Zoobdoo. The Content and Marks are protected to the maximum extent permitted by intellectual property laws and international treaties. Content displayed on or through ZOOBDOO.COM is protected by copyright as a collective work and/or compilation, pursuant to copyrights laws, and international conventions.</p>

<p>5.2       Any reproduction, modification, creation of derivative works from or redistribution of the site or the collective work, and/or copying or reproducing the sites or any portion thereof to any other server or location for further reproduction or redistribution is prohibited without the express written consent of Zoobdoo.</p>

<p>5.3       You further agree not to reproduce, duplicate or copy Content from ZOOBDOO.COM without the express written consent of Zoobdoo, and agree to abide by any and all copyright and other legal notices displayed on ZOOBDOO.COM. You may not decompile or disassemble, reverse engineer or otherwise attempt to discover any source code contained in ZOOBDOO.COM. Without limiting the foregoing, you agree not to reproduce, duplicate, copy, sell, resell or exploit for any commercial purposes, any aspect of ZOOBDOO.COM.</p>

<p> </p>

<p><strong>6. WARRANTY DISCLAIMER</strong></p>

<p>6.1 ZOOBDOO.COM, INCLUDING ANY CONTENT, THIRD PARTY SUBMISSIONS, OR ANY SITE-RELATED SERVICE, IS PROVIDED ON AN "AS IS" AND "AS AVAILABLE" BASIS. ZOOBDOO HEREBY EXPRESSLY DISCLAIMS ALL WARRANTIES OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, IMPLIED WARRANTIES OF MERCHANTABILITY, TITLE, NON-INFRINGEMENT AND FITNESS FOR A PARTICULAR PURPOSE.</p>

<p>6.2       ZOOBDOO MAKES NO WARRANTY THAT: (I) ZOOBDOO.COM WILL MEET YOUR REQUIREMENTS, (II) ZOOBDOO.COM WILL BE UNINTERRUPTED, TIMELY, SECURE, OR ERROR-FREE, (III) THE RESULTS THAT MAY BE OBTAINED FROM THE USE OF ZOOBDOO.COM WILL BE ACCURATE OR RELIABLE, OR (IV) ANY ERRORS IN ZOOBDOO.COM WILL BE CORRECTED.</p>

<p>6.3       ZOOBDOO IS NOT RESPONSIBLE AND SHALL HAVE NO LIABILITY FOR THE CONTENT, PRODUCTS, SERVICES, ACTIONS OR INACTIONS OF ANY USER, OR ANY OTHER THIRD PARTY; AND ZOOBDOO WILL HAVE NO LIABILITY WITH RESPECT TO ANY WARRANTY DISCLAIMED HEREIN.</p>

<p>6.4       YOU ACKNOWLEDGE THAT ZOOBDOO HAS NO CONTROL OVER AND DOES NOT GUARANTEE THE QUALITY, SAFETY OR LEGALITY OF PRODUCTS ADVERTISED ON ZOOBDOO.COM, THE TRUTH OR ACCURACY OF ANY THIRD PARTY SUBMISSIONS.</p>

<p>6.5       ZOOBDOO, ITS AFFILIATES AND ITS SPONSORS ARE NEITHER RESPONSIBLE NOR LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, CONSEQUENTIAL, SPECIAL, EXEMPLARY, PUNITIVE OR OTHER DAMAGES ARISING OUT OF OR RELATING IN ANY WAY TO ZOOBDOO.COM, SITE-RELATED SERVICES AND/OR CONTENT OR INFORMATION CONTAINED WITHIN ZOOBDOO.COM. YOUR SOLE REMEDY FOR DISSATISFACTION WITH ZOOBDOO.COM AND/OR SITE-RELATED SERVICES IS TO STOP USING ZOOBDOO.COM AND/OR THOSE SERVICES.</p>

<p> </p>

<p><strong>7. Indemnity</strong></p>

<p>7.1       You agree to defend, indemnify and hold harmless Zoobdoo, its affiliated companies, officers, directors, employees and agents, from and against any and all claims, damages, obligations, losses, liabilities, costs or debt, and expenses (including but not limited to attorney\'s fees) arising from:</p>

<p>(a)        any Venue transaction in which take part in, including as a landlord or tenant;</p>

<p>(b)        your use of and access to the ZOOBDOO.COM website;</p>

<p>(c)        your participation in any interactive area of the ZOOBDOO.COM website.</p>

<p>(d)       your violation of any term of these Terms of Use;</p>

<p>(e)        your violation of any third party right, including without limitation any copyright, property, or privacy right;</p>

<p>(f)        your violation of any law, rule or regulation of the United States, any state, or any other country;</p>

<p>(g)        any claim that one of your Third Party Submissions or a Third Party Submission posted using your account caused damage to another third party.</p>

<p>(h)       any other party\'s access and use of ZOOBDOO.COM with your account.</p>

<p>7.2       This defense and indemnification obligation will survive these Terms of Use and any termination of your use of the ZOOBDOO.COM website.</p>

<p> </p>

<p><strong>8. PRIVACY POLICY</strong></p>

<p>8.1 Zoobdoo has established a Privacy Policy to explain to users how personal information is collected and used, which is located at the following web address:</p>

<p><em>http://zoobdoo.com/privacypolicy</em></p>

<p>8.2 Your use of ZOOBDOO.COM signifies acknowledgement of and agreement to our Privacy Policy. You further acknowledge and agree that Zoobdoo may, in its sole discretion, preserve or disclose content posted by you, as well as your information, such as email addresses, IP addresses, timestamps, and other user information, if required to do so by law or in the good faith belief that such preservation or disclosure is reasonably necessary to comply with legal process, enforce the Terms of Use, or respond to claims from third-parties.</p>

<p> </p>

<p><strong>9. Governing Law / Disputes</strong></p>

<p>9.1       You agree that the ZOOBDOO.COM website shall be deemed solely based in the State of Nevada.</p>

<p>9.2       The ZOOBDOO.COM website shall be deemed a passive website that does not give rise to personal jurisdiction over Zoobdoo, either specific or general, in jurisdictions other than Nevada.</p>

<p>9.3       <u>Governing Law / Jurisdiction</u>. These Terms of Use will be governed and interpreted in accordance with the internal laws of the State of Nevada applicable to agreements entered into and to be wholly performed therein, without regard to principles of conflict of laws. If any provision of this Agreement is found to be invalid or unenforceable, such provision shall be severed from the remainder of these Terms of Use, which shall remain in full force and effect. These Terms of Use are governed by a mandatory arbitration clause set out below, however, if a court is necessary in whole or in part to enforce these Terms of Use, You consent and submit to the sole and exclusive jurisdiction of the state and federal courts located in Clark County, Nevada and waive any objection to personal jurisdiction, to venue, or to convenience of forum.</p>

<p>9.4       <u>Disputes</u>. Any dispute, claim or controversy arising out of or relating to the ZOOBDOO.COM website, this Agreement or the breach, termination, enforcement, interpretation or validity thereof, including the determination of the scope or applicability of this agreement to arbitrate, shall be determined by arbitration in Las Vegas, Nevada, before one arbitrator. At the option of the first to commence an arbitration, the arbitration shall be administered either by JAMS pursuant to its Streamlined Arbitration Rules and Procedures, or by the American Arbitration Association pursuant to its Commercial Arbitration Rules. The arbitrator may not award any consequential, indirect, exemplary, special or incidental damages arising from or relating to your use of the ZOOBDOO.COM website (including, without limitation, damages for loss of business profits, business interruption, loss of business information, or other pecuniary loss). Judgment on the Award may be entered in any court having jurisdiction. You and we will each pay one-half of the costs and expenses of such arbitration, and each of the parties will separately pay their counsel fees and expenses. Notwithstanding the parties\' decision to resolve all disputes through arbitration, either party may bring an action in state or federal court to protect its intellectual property rights ("intellectual property rights" means patents, copyrights, trademarks, and trade secrets, but not privacy or publicity rights). Either party may also seek relief in a small claims court for disputes or claims within the scope of that court\'s jurisdiction.</p>

<p>9.5       <u>Class Action Waiver</u>. You agree that any arbitration shall be conducted in your individual capacity only and not as a class action or other representative action, and you expressly waive your right to file a class action or seek relief on a class basis. YOU AND Zoobdoo AGREE THAT EACH MAY BRING CLAIMS AGAINST THE OTHER ONLY IN YOUR OR ITS INDIVIDUAL CAPACITY, AND NOT AS A PLAINTIFF OR CLASS MEMBER IN ANY PURPORTED CLASS OR REPRESENTATIVE PROCEEDING.</p>

<p> </p>

<p><strong>10. Assignment / Modification</strong></p>

<p>10.1     <u>Assignment</u>. These Terms of Use, and any rights and licenses granted hereunder, may not be transferred or assigned by you, but may be assigned by Zoobdoo without restriction.</p>

<p>10.2     <u>Modification</u>. We reserve the right to amend these terms and conditions at any time. If we do this, we will post the amended Terms of Use on this page and indicate at the top of the page the date the Terms of Use were last revised. Your continued use of ZOOBDOO.COM after any such changes constitutes your acceptance of the new Terms of Use. If you do not agree to any of these terms or any future Terms of Use, do not use or access (or continue to access) ZOOBDOO.COM.</p>

<p> </p>

<p><strong>11. Ability to Accept Terms of Use</strong></p>

<p>You hereby declare, represent and warrant that you are over eighteen (18) years of age, or that you are an emancipated minor, or that you possess legal parental or guardian consent, and that you are fully able and competent to legally bind yourself to and abide by all of the terms, conditions, obligations, declarations, affirmations, representations, and warranties set forth in these Terms of Use. ZOOBDOO.COM is not intended for use of children under eighteen (18) years old. Children under eighteen (18) years of age are hereby explicitly prohibited from using the ZOOBDOO.COM website.</p>

<p> </p>

<p><strong>12. Consent</strong></p>

<p>By using ZOOBDOO.COM in any way you are agreeing to comply with these Terms of Use. In addition, when using a particular ZOOBDOO.COM service, you agree to abide by any applicable posted guidelines, which may change from time to time. Should you object to any term or condition of the Terms of Use, any guidelines, or any subsequent modifications thereto or become dissatisfied with ZOOBDOO.COM in any way, your only recourse is to immediately discontinue your use of ZOOBDOO.COM.</p>');
        $staticPage->setInSubmenu(0);
        $staticPage->setWithSubmenu(1);
        $staticPage->setCreatedDate();
        $staticPage->setUpdatedDate();
        $objectManager->persist($staticPage);
        $objectManager->flush();


        $staticPage = new StaticPage();
        $staticPage->setCode('privacy-policy');
        $staticPage->setTemplate('index');
        $staticPage->setSlug('privacy-policy');
        $staticPage->setMetaTitle('Zoobdoo: Privacy Policy');
        $staticPage->setMetaDescription('Zoobdoo: Privacy Policy');
        $staticPage->setHeaderTitle('Privacy Policy');
        $staticPage->setTitle('Privacy Policy');
        $staticPage->setContent('<p><em>This Privacy Policy was last revised on September 29, 2015.</em></p>

<p> </p>

<h4>1. How your Privacy is Protected</h4>

<p>Zoobdoo LLC ("Zoobdoo" "us" or "we") are dedicated to protecting your privacy and handling any personally identifiable information we obtain from you with care and respect.</p>

<p>This Privacy Policy sets forth our policy with respect to information, including personally identifiable information that is collected from users of and/or visitors to zoobdoo.com. When this Privacy Policy uses the term "personally identifiable information," we mean information that identifies a particular individual, such as the individual\'s name, address, e-mail, and phone number. Personally identifiable information is also referred to in this Privacy Policy as "personal information."</p>

<p>As used in this Privacy Policy, Zoobdoo includes all of its subsidiary and affiliated entities. Zoobdoo is referred to in this Privacy Policy as "we," "us," "our," and "ourselves." The term web site(s) or site(s) includes zoobdoo.com as well as any other web sites operated by Zoobdoo (collectively, “zoobdoo.com”).</p>

<p> </p>

<h4>2. Information You Provide to Us</h4>

<p>Typically the personally identifiable information we receive comes directly from users like you who are interested in obtaining various products and services from us or from third parties, such our partners. In general, this information includes the user\'s login name, real name, postal address, e-mail address, and telephone number.</p>

<p>We may also collect other types of information such as rental history, service requests, and rental terms. We collect personal information when you sign up on our website, pay or receive rent, make or receive a service request, use the services on the website, update your profile, contact us with inquiries, respond to one of our surveys, log on, or visit our web sites, including when you participate in activities on our web sites, such as any promotional offers.</p>

<p>The personally identifiable information we may collect includes without limitation your name, address, email address and other personally identifiable information. In some cases we may collect your credit card information (e.g., your credit card number and expiration date, billing address, etc.), some of which may constitute personally identifiable information, to secure certain payments.</p>

<p>In addition, we may allow third parties, including landlords and tenants, to set up pages for rental listings or requests and to collect virtually any information from you related to those postings. If you voluntarily provide information in connection with such a listing, it will be available to us and will be held by us in accordance with this Privacy Policy. In addition, such information will be delivered to the third party requesting the information.</p>

<p> </p>

<h4>3. Information Collected Through Technology</h4>

<p>We collect information through technology to make our sites more interesting and useful. For instance, when you come to one of our sites, your IP address is collected. An IP address is often associated with the network through which you enter the Internet, like your ISP or your company. At times, we also use IP addresses to collect information regarding the frequency with which our users visit various parts of our sites. We may also use your IP Address to help diagnose problems with our servers, and to administer our web sites.</p>

<p>When you view our web sites we may store a small amount of information on your computer. This information will be in the form of a "cookie" or similar file. Cookies are small files stored on your computer, not on our site. We use cookies in our interactive website areas, to deliver content specific to your interests, and so you are not required to reenter your account data every time you connect to the site. Through your web browser you can choose to have your computer warn you each time a cookie is being set, or you can choose to delete or turn off all cookies at any time. Each browser is a little different, so look at your browser\'s Help menu to learn the correct way to modify your cookie settings.</p>

<p>Our web sites may also use a variety of technical methods for tracking purposes, such as JavaScript snippets or pixel tags. We may also use these technical methods to analyze the traffic patterns on our web sites, such as the frequency with which our users visit various parts of our web sites.</p>

<p>Many advertisements are managed and placed on our web sites by third parties. Third-party advertisers who place advertisements on our web sites may also use cookies, JavaScript snippets and pixel tags to collect non-personally identifiable information when you click on or move your cursor over one of their banner advertisements. Once you\'ve clicked on an advertisement and have left our web sites, our Privacy Policy no longer applies and you must read the privacy policy of the advertiser to see how your personal information will be handled on their site.</p>

<p>We currently do not participate in any "Do Not Track" frameworks that would allow us to respond to signals or other mechanisms from you regarding the collection of your personally identifiable information.</p>

<p> </p>

<h4>4. Use of Personally Identifiable Information</h4>

<p>We may use your personally identifiable information in many ways, including sending you promotional materials, and sharing your information with third parties so that these third parties can send you promotional materials. We may also supplement personally identifiable information that we have collected directly from our users with other information that we obtain from third parties. We may also share your personal information with companies we have a relationship with that offer products and/or services.</p>

<p>In addition, we may also share your information when you participate in certain activities on our sites that are sponsored by third parties such as making specific requests for business information or participating in promotions or contests sponsored in whole or in part by a third party.</p>

<p>Finally, sometimes we hire companies to help deliver products or services, like a company that provides payment processing. In those instances, there is a need to share your information with these companies.</p>

<p>We may also take your personally identifiable information and make it non-personally identifiable, either by combining it with information about other individuals, or by removing characteristics (such as your name) that make the information personally identifiable to you. There are no restrictions under this Privacy Policy upon our right to aggregate or de-personalize your personal information, and we may use and/or share the resulting non-personally identifiable information with third parties in any way.</p>

<p>We may store personally identifiable information itself or such information may be included in databases owned and maintained by our affiliates, agents or service providers. We take what we believe to be reasonable steps to protect the personally identifiable information from loss, misuse, unauthorized access, inadvertent disclosure, alteration, and destruction. However, no Internet or e-mail transmission is ever fully secure or error free. In particular, e-mail sent to or from zoobdoo.com may not be secure. Therefore, you should take special care in deciding what information you send to us via e-mail. Please keep this in mind when disclosing any personally identifiable information via the Internet.</p>

<p> </p>

<h4>5. International Privacy Laws.</h4>

<p>If you are visiting our website or using one of our software applications from outside the United States, please be aware that you are sending information (including personally identifiable information) to the United States where our servers are located. We will hold and process your personally identifiable information in accordance with privacy laws in the United States and this Privacy Policy. Please note that privacy laws in the United States may not be the same as, and in some cases may be less protective than, the privacy laws in your country.</p>

<p> </p>

<h4>6. Purchase or Sale of Businesses</h4>

<p>From time to time we may purchase a business or one or more of our businesses may be sold and your personally identifiable information may be transferred as a part of the purchase or sale. We may also, sell assign, or otherwise transfer such information in the regular course of business. In the unlikely event of our bankruptcy, insolvency, reorganization, receivership, or assignment for the benefit of creditors, or the application of laws or equitable principles affecting creditors\' rights generally, we may not be able to control how your personal information is treated, transferred or used.</p>

<p> </p>

<h4>7. Additional Disclosures Required By Law</h4>

<p>We will disclose personal information when we believe in good faith that such disclosures are required by law, including, for example, to comply with a court order or subpoena; to enforce our Terms of Use; enforce contest, sweepstakes, promotions, and/or game rules; to protect your safety or security; and/or, protect the safety and security of our web sites, us, and/or third parties, including the safety and security of property that belongs to us or third parties.</p>

<p> </p>

<h4>8. Children\'s Information</h4>

<p>This site is not directed toward persons under eighteen (18) years of age. Zoobdoo does not knowingly collect personally identifiable information from persons under eighteen (18) years of age.</p>

<p> </p>

<h4>9. Applicability</h4>

<p>This Privacy Policy applies to personally identifiable information collected on the web sites where this Privacy Policy is posted and does not apply to any other information collected by Zoobdoo through any other means.</p>

<p> </p>

<h4>10. Changes and Updates</h4>

<p>We reserve the right to amend this Privacy Policy at any time. If we do this, we will post the amended Privacy Policy on this page and indicate at the top of the page the date the Privacy Policy was last revised. Your continued use of zoobdoo.com after any such changes constitutes your acceptance of the new Privacy Policy. If you do not agree to this Privacy Policy or any future Privacy Policy, do not use or access (or continue to access) zoobdoo.com.</p>');
        $staticPage->setInSubmenu(0);
        $staticPage->setWithSubmenu(1);
        $staticPage->setCreatedDate();
        $staticPage->setUpdatedDate();
        $objectManager->persist($staticPage);
        $objectManager->flush();


        $staticPage = new StaticPage();
        $staticPage->setCode('sitemap');
        $staticPage->setTemplate('index');
        $staticPage->setSlug('sitemap');
        $staticPage->setMetaTitle('Zoobdoo: Sitemap');
        $staticPage->setMetaDescription('Zoobdoo: Sitemap');
        $staticPage->setHeaderTitle('Sitemap');
        $staticPage->setTitle('Sitemap');
        $staticPage->setContent('<ul class="features-list">
	<li><a href="/">Home</span></a></li>
	<li><a href="/about">About us</a></li>
	<li><a href="/manager-features">Manager features</a></li>
	<li><a href="/tenant-features">Tenant Features</a></li>
	<li><a href="/faq">FAQ</a></li>
	<li><a href="/contact">Contact</a></li>
	<li><a href="/terms-of-service">Terms of Service </a></li>
	<li><a href="/privacy-policy">Privacy Policy </a></li>
	<li><a href="/property/available">Available properties</a></li>
 <li><a href="/how-it-works">How it Works</a></li></ul>');
        $staticPage->setInSubmenu(1);
        $staticPage->setWithSubmenu(1);
        $staticPage->setCreatedDate();
        $staticPage->setUpdatedDate();
        $objectManager->persist($staticPage);
        $objectManager->flush();


        $staticPage = new StaticPage();
        $staticPage->setCode('online-tenant-screening');
        $staticPage->setTemplate('index');
        $staticPage->setSlug('online-tenant-screening');
        $staticPage->setMetaTitle('Online Tenant Screening | Credit Check for Landlords | zoobdoo.com');
        $staticPage->setMetaDescription('Online tenant screening and credit checks for landlords made easy. Our software allows you to perform all necessary screening to ensure transparency.');
        $staticPage->setHeaderTitle('Online Tenant Screening');
        $staticPage->setTitle('Online Tenant Screening');
        $staticPage->setContent('<p>As a landlord, one of the most important things you can do to protect your property and tenants is initiate thorough tenant screenings that include a criminal background check, rental and eviction history and a credit check. Identifying who will be a good tenant can help you offset common tenant issues that keep landlords up at night:</p>

<ul style="list-style-type:disc;"><li>Late or missed payments</li>
	<li>Arguments with other tenants</li>
	<li>Damage to the property</li>
	<li>Identify theft</li>
	<li>Stolen goods</li>
</ul><p><img alt="online-tenant-screening.jpg" src="/assets/images/online-tenant-screening.jpg" style="float:left;margin-left:15px;margin-right:15px;" /></p>

<h2>Online Tenant Screening from Zoobdoo</h2>

<p>Zoobdoo offers full background, eviction, credit checks and criminal checks to help landlords vet applicants in the most efficient and affordable manner possible. Zoobdoo\'s pay as you go online tenant screening service allows landlords to conduct basic reports on tenants when and where they need them. Zoobdoo allows members to actively manage their property, including online tenant screening, all in one place.</p>

<p>For more information about Zoobdoo rental management software, please <a href="https://zoobdoo.com/contact" style="color:#ca171b;">contact us today</a>!</p>');
        $staticPage->setInSubmenu(0);
        $staticPage->setWithSubmenu(0);
        $staticPage->setCreatedDate();
        $staticPage->setUpdatedDate();
        $objectManager->persist($staticPage);
        $objectManager->flush();


        $staticPage = new StaticPage();
        $staticPage->setCode('post-apartment-for-rent');
        $staticPage->setTemplate('index');
        $staticPage->setSlug('post-apartment-for-rent');
        $staticPage->setMetaTitle('Post Rentals | Post Apartments for Rent | zoobdoo.com');
        $staticPage->setMetaDescription('Post your rentals today on Zoobdoo, a top rental property software, and see your listings on top rental sites like Zillow, Trulia, and Craigslist.');
        $staticPage->setHeaderTitle('Post Apartments For Rent');
        $staticPage->setTitle('Post Apartments For Rent');
        $staticPage->setContent('<p>Create and post stunning rental available listings that instantly attract potential renters to your properties. More renters conduct their searches for rental properties online than ever before. Did you know that 59% of those renters will view more than twenty properties in one search? As a landlord or property manager, you not only need an attractive ad that adequately describes the property; you need top sites to post your apartment for rent.</p>

<p><img alt="post-rental.jpg" src="/assets/images/post-rental.jpg" style="float:left;margin:0px 15px;" /></p>

<p>Zoobdoo\'s pay as you go landlord services allow members to post rentals on a network of 12 sites, including heavily trafficked sites like Zillow, Trulia, and Craigslist, at an affordable rate. All our members need to do is post apartments for rent to our property management software and those listings will automatically transfer to other rental sites. No more posting individual listings that can take up a large amount of time or money.</p>

<p>Zoobdoo makes posting your rentals and apartments for rent easy and affordable. You only pay for the properties you list. <a href="https://zoobdoo.com/contact" style="color:#ca171b;">Contact us today</a> to learn more!</p>');
        $staticPage->setInSubmenu(0);
        $staticPage->setWithSubmenu(0);
        $staticPage->setCreatedDate();
        $staticPage->setUpdatedDate();
        $objectManager->persist($staticPage);
        $objectManager->flush();


        $staticPage = new StaticPage();
        $staticPage->setCode('pay-rent-online');
        $staticPage->setTemplate('index');
        $staticPage->setSlug('pay-rent-online');
        $staticPage->setMetaTitle('Pay Rent Online | Collect Rent Online | zoobdoo.com');
        $staticPage->setMetaDescription('Collect rent payments through Zoobdoo and see tenant retention soar. Tenants can pay their rent online and never worry about finding a check again.');
        $staticPage->setHeaderTitle('Pay Rent Online');
        $staticPage->setTitle('Pay Your Rent Online');
        $staticPage->setContent('<p>Tenants never have to worry about finding a check or going to the bank to get a money order ever again. Zoobdoo provides tenants and landlords with an easy-to-use online payment system that allows tenants to pay their rental bill online quickly at anytime of the day. It is a modern solution for an increasingly digitized customer base that conducts most of their banking, shopping and bill payments online.</p>

<p><img alt="credit-card.jpg" src="/assets/images/credit-card.jpg" style="float:left;margin:0px 15px;" /></p>

<p>Tenants have the option of making a credit card or ACH payment to pay their rent online for a small $3 charge. They can set up a one-time online rent payment or opt to sign up for recurring payments, so they never have to worry about missing another rent payment again. Zoobdoo also makes tracking all payments due and paid easy and accessible on one centralized dashboard.</p>

<p>Landlords can benefit from a smarter, more affordable way to collect rent payments – no more collecting checks or using manual systems to track rent payments. Zoobdoo provides landlord with an easy way to collect rent payments online. In addition to that, they can benefit from higher tenant retention, reduced third-party fees, and fewer late or missed payments.</p>

<p><a href="https://zoobdoo.com/contact" style="color:rgb(202,23,27);">Contact us today</a> to learn more about this and our other rental management services!</p>');
        $staticPage->setInSubmenu(0);
        $staticPage->setWithSubmenu(0);
        $staticPage->setCreatedDate();
        $staticPage->setUpdatedDate();
        $objectManager->persist($staticPage);
        $objectManager->flush();
    }

}
