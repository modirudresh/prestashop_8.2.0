<?php

/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
class SampleBlocks {

    public function initData($base_url) {
        $content_block1 = '
            <div id="promotion-section" class="promotion-section">
                            <div class="adv-wrapper">
                                <div class="container">
                                    <div class="row">
                                        <div class="adv-inner"><a href="#"> <img class="image-responsive middle-block" src="img/cms/pixtron-adv-banner-1.jpg" alt="home-image" /></a></div>
                                        <div class="adv-inner"><a href="#"> <img class="image-responsive middle-block" src="img/cms/pixtron-adv-banner-2.jpg" alt="home-image" /></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
';
        
        $content_block2 = '
            <div id="parallax-section" class="parallax-section">
                            <div class="latest-offer-section">
                                <div class="offer-inner">
                                    <h1>Pixtron Pro Responsive Prestashop Theme</h1>
                                    <a href="#">Download Now</a></div>
                            </div>
                        </div>
';
        
        $content_block3 = '
            <div id="service-section" class="service-section">
                            <div class="home-advantage-block">
                                <div class="container">
                                    <div class="hab-row">
                                        <div class="hab-inner">
                                            <div class="home-advantage-block-inner"><span> <em class="fa fa-usd"></em> </span>
                                                <p>30-Day Money Back</p>
                                            </div>
                                        </div>
                                        <div class="hab-inner">
                                            <div class="home-advantage-block-inner"><span> <em class="fa fa-headphones"></em> </span>
                                                <p>1 year Free Support</p>
                                            </div>
                                        </div>
                                        <div class="hab-inner">
                                            <div class="home-advantage-block-inner"><span> <em class="fa fa-undo"></em> </span>
                                                <p>Free Lifetime Update</p>
                                            </div>
                                        </div>
                                        <div class="hab-inner">
                                            <div class="home-advantage-block-inner"><span> <em class="fa fa-thumbs-up"></em> </span>
                                                <p>100% Satisfaction</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
';
        
        $content_block4 = '
            <div id="testimonial-section" class="testimonial-section">
                            <div class="testimonial-block">
                                <div class="container">
                                    <div class="section-title"><strong>Testimonials</strong></div>
                                    <div class="row">
                                        <div class="testimonial-inner owl-carousel owl-theme testimonial-slider">
                                            <div class="testimonial-content">
                                                <div class="testimonial-desc">
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                                                </div>
                                                <div class="testimonial-detail"><img src="img/cms/testimonial-1.png" alt="testimonial1" />
                                                    <div class="profile">
                                                        <h4>Joh Doe</h4>
                                                        <p>Web Designer</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="testimonial-content">
                                                <div class="testimonial-desc">
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                                                </div>
                                                <div class="testimonial-detail"><img src="img/cms/testimonial-2.png" alt="testimonial1" />
                                                    <div class="profile">
                                                        <h4>Joh Doe</h4>
                                                        <p>Web Designer</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="testimonial-content">
                                                <div class="testimonial-desc">
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                                                </div>
                                                <div class="testimonial-detail"><img src="img/cms/testimonial-3.png" alt="testimonial1" />
                                                    <div class="profile">
                                                        <h4>Joh Doe</h4>
                                                        <p>Web Designer</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
';
        
        $content_block5 = '
             <div id="blog-section" class="blog-section">
                            <div class="blogpost-block">
                                <div class="container">
                                    <div class="blog-section-title"><strong>Latest Posts</strong></div>
                                    <div class="row">
                                        <div class="blog-block blog-slider">
                                            <div class="blog-inner">
                                                <div class="blog-inner-block">
                                                    <a href="#"><div class="blog-image"><img src="img/cms/pixtron-blog-banner-1.jpg" alt="blog1" /><span> 25 Jul 2016 </span></div></a>
                                                    <div class="blog-content">
                                                        <h3>Lorem ipsum is dolor sit amet.</h3>
                                                        <p>Lorem ipsum is dolor sit amet. Lorem ipsum is dolor sit amet. Lorem ipsum is dolor sit amet. Lorem ipsum is dolor sit amet.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="blog-inner">
                                                <div class="blog-inner-block">
                                                    <a href="#"><div class="blog-image"><img src="img/cms/pixtron-blog-banner-2.jpg" alt="blog2" /><span> 25 Jul 2016 </span></div></a>
                                                    <div class="blog-content">
                                                        <h3>Lorem ipsum is dolor sit amet.</h3>
                                                        <p>Lorem ipsum is dolor sit amet. Lorem ipsum is dolor sit amet. Lorem ipsum is dolor sit amet. Lorem ipsum is dolor sit amet.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="blog-inner">
                                                <div class="blog-inner-block">
                                                    <a href="#"><div class="blog-image"><img src="img/cms/pixtron-blog-banner-3.jpg" alt="blog1" /><span> 25 Jul 2016 </span></div></a>
                                                    <div class="blog-content">
                                                        <h3>Lorem ipsum is dolor sit amet.</h3>
                                                        <p>Lorem ipsum is dolor sit amet. Lorem ipsum is dolor sit amet. Lorem ipsum is dolor sit amet. Lorem ipsum is dolor sit amet.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="blog-inner">
                                                <div class="blog-inner-block">
                                                    <div class="blog-image"><img src="img/cms/pixtron-blog-banner-1.jpg" alt="blog1" /><span> 25 Jul 2016 </span></div>
                                                    <div class="blog-content">
                                                        <h3>Lorem ipsum is dolor sit amet.</h3>
                                                        <p>Lorem ipsum is dolor sit amet. Lorem ipsum is dolor sit amet. Lorem ipsum is dolor sit amet. Lorem ipsum is dolor sit amet.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
';
        
        $content_block6 = '<div id="contactinfo-section" class="contactinfo-section">
	<div class="footer-links-column">
		<div class="footer-colum-title footer-block-title">
			<h3>Quick Contact</h3>
		</div>
		<div class="footer-column-content footer-block-toggle-content">
			<div class="content">
				<p class="address"><strong> <em class="fa fa-home"></em>  <span>Address: </span> </strong> <span>123 Street, City, London</span></p>
				<p class="email"><strong> <em class="fa fa-send"></em>  <span>Mail us: </span> </strong> <span> <a href="mailto:info@domain.com" title="Mail to: info@domain.com">info@domain.com</a> </span></p>
				<p class="phone"><strong> <em class="fa fa-phone"></em>  <span>Phone: </span> </strong> <span><a href="tel:(123)-456-7890" title="Call us: (123)-456-7890">(123)-456-7890</a></span></p>
			</div>
		</div>
	</div>
</div>
';
        
        $content_block7 = '
            <div id="promotion-section2" class="promotion-section2">
                    <div class="adv-wrapper">
                        <div class="container">
                            <div class="row">
                                <div class="adv-inner"><a href="#"> <img class="image-responsive middle-block" src="img/cms/pixtron-male-watch1.png" alt="home-image" /> </a></div>
                                <div class="adv-inner"><a href="#"> <img class="image-responsive middle-block" src="img/cms/pixtron-male-watch2.png" alt="home-image" /> </a></div>
                                <div class="adv-inner"><a href="#"> <img class="image-responsive middle-block" src="img/cms/pixtron-male-watch3.png" alt="home-image" /> </a></div>
                            </div>
                        </div>
                    </div>
                </div>
';
                                $content_block8 = '<ul class="footer-payment-icon">
	<li><a href="#"><i class="fa fa-cc-visa" aria-hidden="true"></i></a></li>
	<li><a href="#"><i class="fa fa-cc-paypal" aria-hidden="true"></i></a></li>
	<li><a href="#"><i class="fa fa-cc-discover" aria-hidden="true"></i></a></li>
	<li><a href="#"><i class="fa fa-cc-amex" aria-hidden="true"></i></a></li>
	<li><a href="#"><i class="fa fa-cc-mastercard" aria-hidden="true"></i></a></li>
</ul>
';

        $displayHome = Hook::getIdByName('displayHome');
        $displayFooter = Hook::getIdByName('displayFooter');
        $displayFooter = Hook::getIdByName('displayFooterBefore');
        $displayHomeTop1 = Hook::getIdByName('displayHomeTop1');
        $displayHomeTop2 = Hook::getIdByName('displayHomeTop2');
        $displayHomeTop3 = Hook::getIdByName('displayHomeTop3');
        $displayHomeBottom1 = Hook::getIdByName('displayHomeBottom1');
        $displayHomeBottom2 = Hook::getIdByName('displayHomeBottom2');
        $displayHomeBottom3 = Hook::getIdByName('displayHomeBottom3');
        $displayFooterPaymentBlock = Hook::getIdByName('displayFooterPaymentBlock');
        $id_shop = Configuration::get('PS_SHOP_DEFAULT');

        /* install static Block */
        $result = true;
        $result &= Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'ht_staticblocks` (`id_ht_staticblocks`, `hook`, `active`) 
			VALUES
			(1, "displayHomeTop1", 1),
                        (2, "displayHomeTop2", 1),
                        (3, "displayFooterBefore", 1),
                        (4, "displayHomeBottom2", 1),
                        (5, "displayHomeBottom3", 1),
                        (6, "displayFooter", 1),
                        (7, "displayHomeTop3", 1),
                        (8, "displayFooterPaymentBlock", 1)
			;');

        $result &= Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'ht_staticblocks_shop` (`id_ht_staticblocks`, `id_shop`,`active`) 
			VALUES 
			(1,' . $id_shop . ', 1),
			(2,' . $id_shop . ', 1),
			(3,' . $id_shop . ', 1),
			(4,' . $id_shop . ', 1),
			(5,' . $id_shop . ', 1),
			(6,' . $id_shop . ', 1),
			(7,' . $id_shop . ', 1),
			(8,' . $id_shop . ', 1)
			;');

        foreach (Language::getLanguages(false) as $lang) {
            $result &= Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'ht_staticblocks_lang` (`id_ht_staticblocks`, `id_shop`, `id_lang`, `title`, `content`) 
			VALUES 
			("1", "' . $id_shop . '","' . $lang['id_lang'] . '","Home Promotion", \'' . $content_block1 . '\'),
			("2", "' . $id_shop . '","' . $lang['id_lang'] . '","Home Parallax Block", \'' . $content_block2 . '\'),
			("3", "' . $id_shop . '","' . $lang['id_lang'] . '","Home Services Block", \'' . $content_block3 . '\'),
			("4", "' . $id_shop . '","' . $lang['id_lang'] . '","Home Testimonials Block", \'' . $content_block4 . '\'),
			("5", "' . $id_shop . '","' . $lang['id_lang'] . '","Home Blogs", \'' . $content_block5 . '\'),
			("6", "' . $id_shop . '","' . $lang['id_lang'] . '","Footer Contact Block", \'' . $content_block6 . '\'),
			("7", "' . $id_shop . '","' . $lang['id_lang'] . '","Home Promotion 2", \'' . $content_block7 . '\'),
                        ("8", "' . $id_shop . '","' . $lang['id_lang'] . '","Footer Payment Block", \'' . $content_block8 . '\')
			
                ;');
        }
        return $result;
    }
}