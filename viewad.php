<?php
/**
* 2007-2019 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once(_PS_MODULE_DIR_ . 'businessdirectories/classes/AdsListing.php');
require_once(_PS_MODULE_DIR_ . 'businessdirectories/classes/TypesListing.php');
class BusinessdirectoriesViewAdModuleFrontController extends ModuleFrontControllerCore
{
    public function init()
    {   
        $this->page_name = 'View Ad';
        $this->disableBlocks();
        parent::init();

    }

    protected function disableBlocks()
    {
        $this->display_column_left = false;
    }

    public function initContent()
    {   echo '<link href="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.js"></script>
  <script src="https://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
';
        parent::initContent();        

        if (!empty((int)Tools::getValue('id_ad'))) {
            $obj = new TypesListing();
            $object = new AdsListing();
            $base_url = Tools::getHttpHost(true).__PS_BASE_URI__;
            $get_ad = $object->getAd((int)Tools::getValue('id_ad'));
            // get tags
            $get_ad_tags = $obj->getAdTags(Tools::getValue('id_ad'));
		    if (!empty($get_ad_tags)) {
                $tags = array();
                foreach ($get_ad_tags as $single_tag) {
                    $tags[] = $single_tag['name'];
                }
                $ad_tags = implode(",", $tags);
            } else {
                $ad_tags = '';
            }
            // get types
            $get_ad_types = $obj->getAdvTypes(Tools::getValue('id_ad'));
            $adv_types = array();
            if (!empty($get_ad_types)) {
                foreach ($get_ad_types as $single_ad_type) {
                    $adv_types[$single_ad_type['id_type']] = $single_ad_type['name'];
                }
            }

            //get shiping
            $get_ad_shippings = $obj->getAdvShippings(Tools::getValue('id_ad'));
            
            $adv_shipings = array();
            if (!empty($get_ad_shippings)) {
                foreach ($get_ad_shippings as $single_ad_shipping) {
                    $adv_shipings[$single_ad_shipping['id_shipping']] = $single_ad_shipping['name'];
                }
            }
        
            // get images
            $get_ad_images = $obj->getAdvimages(Tools::getValue('id_ad')); 


            

            $ad_images = array();
            if (!empty($get_ad_images)) {
                foreach ($get_ad_images as $single_ad_image) {
                    $ad_images[$single_ad_image['id_image']] = $single_ad_image['name'];
                }
            }

            $get_ad_videos = $obj->getAdvideos(Tools::getValue('id_ad'));

            $ad_videos = array();
            if (!empty($get_ad_videos)) {
                foreach ($get_ad_videos as $single_ad_video) {
                    $ad_videos[$single_ad_video['id_image']] = $single_ad_video['name'];
                }
            }


           
            $this->context->smarty->assign(array(
                'base_url' => $base_url,
                'get_ad' => $get_ad,
                'ad_tags' => $ad_tags,
                'adv_types' => $adv_types,
                'adv_shipings' => $adv_shipings,
                'ad_images' => $ad_images,
                'ad_videos' => $ad_videos,
                'id_ad' => (int)Tools::getValue('id_ad')
            ));

                        

            if (Businessdirectories::isPs17()) {
                $this->setTemplate('module:businessdirectories/views/templates/front/view-my-ad.tpl');
            }
        } else {
            Tools::redirect('index.php');
        }
        if (!empty(Tools::getValue('send_enquiry'))) {

            $customer_detail = Db::getInstance()->getRow('SELECT firstname, lastname, email FROM `'._DB_PREFIX_.'customer` where id_customer="'.(int)$get_ad['id_customer'].'"');

           

			$agent_mail = $customer_detail['email'];
			$shop_email = Configuration::get('PS_SHOP_EMAIL');

            $visitor_email = Tools::getValue('email');            

			// Admin mail
            Mail::Send(1, 'enquiryadminmail', Mail::l('Enquiry For Item!'),
                array(
                    '{firstname}' => Tools::getValue('first_name'),
                    '{lastname}' => Tools::getValue('last_name'),
                    '{item_title}' => Tools::getValue('item_title'),
                    '{message}' => Tools::getValue('message'),
                    '{email}' => Tools::getValue('email'),
                ), $shop_email, Tools::getValue('first_name').' '.Tools::getValue('last_name'), null, null, null, null,  _PS_MODULE_DIR_.'businessdirectories/mails/');
            // Visitor Email
            Mail::Send(1, 'enquirycustomermail', Mail::l('Enquiry For Item!'),
                array(
                    '{firstname}' => Tools::getValue('first_name'),
                    '{lastname}' => Tools::getValue('last_name'),
                    '{item_title}' => Tools::getValue('item_title'),
                    '{message}' => Tools::getValue('message'),
                    '{email}' => Tools::getValue('email'),
                ), $visitor_email, Tools::getValue('first_name').' '.Tools::getValue('last_name'), null, null, null, null,  _PS_MODULE_DIR_.'businessdirectories/mails/');

            // Agent Email
            Mail::Send(1, 'enquiryagentmail', Mail::l('Enquiry For Item!'),
                array(
                    '{firstname}' => Tools::getValue('first_name'),
                    '{lastname}' => Tools::getValue('last_name'),
                    '{item_title}' => Tools::getValue('item_title'),
                    '{message}' => Tools::getValue('message'),
                    '{email}' => Tools::getValue('email'),
                    '{agent_firstname}' => $customer_detail['firstname'],
                    '{agent_lastname}' => $customer_detail['lastname'],
                ), $agent_mail, $customer_detail['firstname'].' '.$customer_detail['lastname'], null, null, null, null,  _PS_MODULE_DIR_.'businessdirectories/mails/');

            $this->context->smarty->assign(array(
                'success' => 'Enquiry Form Send Successfully',
            ));
        }
    }
    
    /**
     * @return array
     */
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        if($this->context->customer->isLogged()){

        return $breadcrumb;
    }
    }


}
