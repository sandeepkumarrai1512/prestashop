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
class BusinessdirectoriesEditAdModuleFrontController extends ModuleFrontControllerCore
{
    public function init()
    {
        $this->page_name = 'Edit Ad';
        $this->disableBlocks();
        parent::init();
    }
    protected function disableBlocks()
    {
        $this->display_column_left = false;
    }
    public function initContent()
    {
        parent::initContent();
        $base_url = Tools::getHttpHost(true).__PS_BASE_URI__;
        
        if (Tools::getValue('updateadd') == 'update' && !empty((int)Tools::getValue('id_ad'))) {

            if (Tools::getValue('never_expire')) {
                $never_expire = '1';
            } else {
                $never_expire = '';
            }

            	 
            $id_customer = Tools::getValue('id_customer');
            $id_ad = Tools::getValue('id_ad');
            $title = Tools::getValue('title');
            $purchase_type = Tools::getValue('purchase_type');
            $price_start = Tools::getValue('price_start');
            $price_end = Tools::getValue('price_end');
            //$shipping_to = Tools::getValue('shipping_to');
            $expire_date = Tools::getValue('expire_date');
            $never_expire = Tools::getValue('never_expire');
            $show_text = Tools::getValue('show_text');
            $description = Tools::getValue('description');
            
            // Delete Tag
            $deltags = 'DELETE FROM `'._DB_PREFIX_.'businessdirectories_ad_tags` WHERE `id_ad` = "'.$id_ad.'"';
            Db::getInstance()->execute($deltags);
            if (!empty(Tools::getValue('tags'))) {
                $explode_tags = explode(',', Tools::getValue('tags'));
                foreach ($explode_tags as $single_tag) {            
                    $checktag = Db::getInstance()->getRow('SELECT id_tag FROM `'._DB_PREFIX_.'businessdirectories_tags` where name="'.Tools::strtolower(trim($single_tag)).'"');
                    if (!empty($checktag)) {
                        $tag_id = $checktag['id_tag'];
                    } else {
                        $add_tag = Db::getInstance()->insert('businessdirectories_tags', array(
                            'name' => Tools::strtolower(trim($single_tag)),
                            'date_add' => date('Y-m-d')
                        ));
                        $tag_id = Db::getInstance()->Insert_ID();
                    }
                    Db::getInstance()->insert('businessdirectories_ad_tags', array(
                        'id_ad' => (int)$id_ad,
                        'id_tag' => (int)$tag_id
                    ));
                }
            }

            // Gallery images
            if (!empty(Tools::getValue('existing_images'))) {
                $delimg = "DELETE FROM `"._DB_PREFIX_."businessdirectories_ad_images` WHERE `id_ad` = '".$id_ad."' ";
                $galleryimg = Db::getInstance()->execute( $delimg );
                $explode_images_ids = explode(',', Tools::getValue('existing_images'));
                foreach ($explode_images_ids as $single_id) {                    
                    Db::getInstance()->insert('businessdirectories_ad_images', array(
                        'id_ad' => (int)$id_ad,
                        'id_image' => (int)$single_id
                    ));
                }
                
            }
            // Upload Files
            if (!empty($_FILES)) {
                foreach ($_FILES as $single_file) {
                    for($i=0; $i<count($single_file['name']); $i++) {
                        $target_dir = _PS_MODULE_DIR_."businessdirectories/uploads/";
                        $file_name1 = str_replace(" ","",basename($single_file["name"][$i]));
                        if (!empty($file_name1)) {
                            $file_name = rand().$file_name1;
                            $target_file = $target_dir . $file_name;
                            move_uploaded_file($single_file["tmp_name"][$i], $target_file);
                            $add_new_img = Db::getInstance()->insert('businessdirectories_images', array(
                                'id_customer' => (int)$this->context->customer->id,
                                'name' => $file_name,
                                'date_add' => date('Y-m-d')
                            ));
                            $image_id = Db::getInstance()->Insert_ID();
                            Db::getInstance()->insert('businessdirectories_ad_images', array(
                                'id_ad' => (int)$id_ad,
                                'id_image' => (int)$image_id
                            ));
                        }
					}
                }
            }
            // Types
            $deltype = "DELETE FROM `"._DB_PREFIX_."businessdirectories_ad_types` WHERE `id_ad` = '".$id_ad."' ";
            $delres = Db::getInstance()->execute( $deltype );
            if (!empty(Tools::getValue('types'))) {
                foreach (Tools::getValue('types') as $single_type) {
                    $res = Db::getInstance()->insert('businessdirectories_ad_types', array(
                        'id_ad' => (int)$id_ad,
                        'id_type' => $single_type
                    ));
                }
            }


            //shipping
            $delshipping = "DELETE FROM `"._DB_PREFIX_."businessdirectories_ad_shipping` WHERE `id_ad` = '".$id_ad."' ";

            $delshipping_res = Db::getInstance()->execute( $delshipping );
            if (!empty(Tools::getValue('shipping_to'))) {
                foreach (Tools::getValue('shipping_to') as $single_shipping) {
                    
                    

                    $res1 = Db::getInstance()->insert('businessdirectories_ad_shipping', array(
                        'id_ad' => (int)$id_ad,
                        'id_shipping' => $single_shipping
                    ));
                }
            }



            // Update ad information
            Db::getInstance()->update('businessdirectories_ads', array(
                'title' => pSQL(Tools::getValue('title')),
                'description' => pSQL(htmlentities(Tools::getValue('description'))),
                'purchase_type' => pSQL(Tools::getValue('purchase_type')),
                'currency_type' => Tools::getValue('currency_type'),
                'price_start' => pSQL(Tools::getValue('price_start')),
                'price_end' => pSQL(Tools::getValue('price_end')),
                //'shipping_to' => pSQL(Tools::getValue('shipping_to')),
                'show_text' => pSQL(Tools::getValue('show_text')),
                'expire_date' => pSQL(Tools::getValue('expire_date')),
                'never_expire' => pSQL(Tools::getValue('never_expire'))
                ), 'id_ad = '.(int)Tools::getValue('id_ad'));
         
            $this->context->smarty->assign(array(
                'success' => 'Ad Successfully updated',
            ));
            $redirect_url = $base_url."my-account/editad?id_ad=".Tools::getValue('id_ad');
            $this->context->smarty->assign(array(
                'success' => 'Ad Successfully updated',
            ));

           
            sleep(2);
            Tools::redirect($redirect_url);
            
        }

        /*if (!$this->context->customer->isLogged() && $this->php_self != 'authentication' && $this->php_self != 'password') {
            Tools::redirect('index.php?controller=authentication?back=my-account');
        } else {*/

        if (!empty((int)Tools::getValue('id_ad'))) {
            $obj = new TypesListing();
            $object = new AdsListing();
            $base_url = Tools::getHttpHost(true).__PS_BASE_URI__;
        


            $get_ad = $object->getAd((int)Tools::getValue('id_ad'));
            
            $id_ad = Tools::getValue('id_ad');
            $prices = array();
            $priceresult = Db::getInstance()->getRow('SELECT price_start, price_end FROM `'._DB_PREFIX_.'businessdirectories_ads` WHERE `id_ad` = "'.$id_ad.'"');
            foreach($priceresult as $resultprice ){
             $prices['price_start'] = $resultprice['price_start']; 
             $prices['price_end'] = $resultprice['price_end']; 
            }
            //print_r($price_start);die('exit..');
            //$price_start = Db::getInstance()->getRow('SELECT price_start FROM `'._DB_PREFIX_.'businessdirectories_ads` WHERE `id_ad` = "'.$id_ad.'"');
            //$price_start = $object->getAd((int)Tools::getValue('price_start'));
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

             // get shippings
            $get_ad_shippings = $obj->getAdvShippings(Tools::getValue('id_ad'));
            $adv_shippings = array();
            if (!empty($get_ad_shippings)) {
                foreach ($get_ad_shippings as $single_ad_shipping) { 
                   
                    $adv_shippings[$single_ad_shipping['id_shipping']] = $single_ad_shipping['name'];
                }
            }


            $types = $obj->getAdTypes();
            $shippings = $obj->getAdShippings();
        
            // get images
            $images = $obj->getAllImages((int)$this->context->customer->id);

	    
	        $getAdvideos =$obj->getAdvideosByadsId(Tools::getValue('id_ad'));
            $get_ad_images = $obj->getAdvimages(Tools::getValue('id_ad'));
            
            $ad_images = array();
            if (!empty($get_ad_images)) {
                foreach ($get_ad_images as $single_ad_image) {
                    $ad_images[$single_ad_image['id_image']] = $single_ad_image['name'];
                }
            }

		if(!empty($ad_images)) { 
		 		
		 $excitimageids=implode(",",array_keys($ad_images));
		} else {
		 $excitimageids='';
		}


            $this->context->smarty->assign(array(
                'base_url' => $base_url,
                'get_ad' => $get_ad,
                'ad_tags' => $ad_tags,
                'adv_types' => $adv_types,
                'types'     => $types,
                'adv_shippings' => $adv_shippings,
                'shippings'     => $shippings,
                'images'    => $images,
		        'videos'    =>$getAdvideos,
                'prices' => $prices,
                'customer_id' => $this->context->customer->id,
                'ad_images' => $ad_images,
		        'exc_imageids' =>$excitimageids,
                'id_ad' => (int)Tools::getValue('id_ad')
            ));
            if (Businessdirectories::isPs17()) {
                $this->setTemplate('module:businessdirectories/views/templates/front/edit-my-ad.tpl');
            }
        } else {
            Tools::redirect('index.php');
        }
    //}
    
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
