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
        $redurl = $base_url."my-account/editad?id_ad=".Tools::getValue('id_ad');
        
        if (isset($_POST['updateadd']) && !empty((int)Tools::getValue('id_ad') ) ) {

            $id_customer = Tools::getValue('id_customer');
            $id_ad = Tools::getValue('id_ad');
            $title = Tools::getValue('title');
            $purchase_type = Tools::getValue('purchase_type');
            $price_start = Tools::getValue('price_start');
            $price_end = Tools::getValue('price_end');
            $expire_date = Tools::getValue('expire_date');
            $show_text = Tools::getValue('show_text');
            $description = Tools::getValue('description');
            //$tags = Tools::getValue('tags');

            $explode_tags = explode(',', Tools::getValue('tags'));

            $deltags = 'DELETE FROM `'._DB_PREFIX_.'businessdirectories_ad_tags` WHERE `id_ad` = "'.$id_ad.'"';
            Db::getInstance()->executeS($deltags);

            foreach ($explode_tags as $single_tag) { 

            

            

            $checktag = Db::getInstance()->getRow('SELECT id_tag FROM `'._DB_PREFIX_.'businessdirectories_tags` where name="'.$single_tag.'"');

                if (!empty($checktag)) {
                    $tag_id = $checktag['id_tag'];
                } else {
                    $add_tag = Db::getInstance()->insert('businessdirectories_tags', array(
                        'name' => $single_tag,
                        'date_add' => date('Y-m-d')
                    ));
                    $tag_id = Db::getInstance()->Insert_ID();
                }
                Db::getInstance()->insert('businessdirectories_ad_tags', array(
                    'id_ad' => (int)$id_ad,
                    'id_tag' => (int)$tag_id
                ));
            }

            //die('hi..');

            if (!empty(Tools::getValue('existing_images'))) {
                $delimg = "DELETE FROM `"._DB_PREFIX_."businessdirectories_ad_images` WHERE `id_ad` = '".$id_ad."' ";
                
                $galleryimg = Db::getInstance()->executeS( $delimg );

                $explode_images_ids = explode(',', Tools::getValue('existing_images'));
                foreach ($explode_images_ids as $single_id) {                    

                    Db::getInstance()->insert('businessdirectories_ad_images', array(
                        'id_ad' => (int)$id_ad,
                        'id_image' => (int)$single_id
                    ));
                }
                
            }


            if (!empty($_FILES)) {

                foreach ($_FILES as $single_file) {

                    $target_dir = _PS_MODULE_DIR_."businessdirectories/uploads/";
                    $file_name = rand().str_replace(" ","",basename($single_file["name"][0]));
                    $target_file = $target_dir . $file_name;
                    move_uploaded_file($single_file["tmp_name"][0], $target_file);
                    $add_new_img = Db::getInstance()->insert('businessdirectories_images', array(
                        'id_customer' => (int)$this->context->customer->id,
                        'name' => $file_name,
                        'date_add' => date('Y-m-d')
                    ));



            $selimagequery = 'SELECT * FROM `'._DB_PREFIX_.'businessdirectories_ad_images` WHERE `id_ad`  = "'.$id_ad.'" ';

            //Db::getInstance()->executeS($selimagequery);


            /*echo 'SELECT * FROM `ps_businessdirectories_images` WHERE `id_image` = 26 ORDER BY `id_image` DESC';

            $id_tag = Db::getInstance()->getValue('SELECT id_tag FROM `ps_businessdirectories_ad_tags` WHERE `id_ad` = "'.$id_ad.'"');*/


            /*echo 'image: '.'SELECT * FROM `ps_businessdirectories_ad_images` WHERE `id_ad` = 10';            
            echo 'SELECT * FROM `ps_businessdirectories_images` WHERE `id_image` = 35 ORDER BY `id_image` DESC';
                die('hi..');*/

                    //Db::getInstance()->executeS('SELECT bi.id_image, bi.name FROM `'._DB_PREFIX_.'businessdirectories_ad_images` as bai INNER JOIN `'._DB_PREFIX_.'businessdirectories_images`as bi ON (bi.id_image=bai.id_image) where bai.id_ad="'.$id_ad.'"');


                    /*$delimg = "DELETE FROM `ps_businessdirectories_ad_images` WHERE `id_ad` = '".$id_ad."' ";

                    $editres = Db::getInstance()->executeS( $delimg );*/

                    $image_id = Db::getInstance()->Insert_ID();
                    Db::getInstance()->insert('businessdirectories_ad_images', array(
                        'id_ad' => (int)$id_ad,
                        'id_image' => (int)$image_id
                    ));
                }
            }

        

        $selectdata = "SELECT * FROM `"._DB_PREFIX_."businessdirectories_ads` WHERE `id_ad` = '".$id_ad."' ";
        $editres = Db::getInstance()->executeS( $selectdata );

        /*foreach($editres as $editresults){
           echo "<pre>";
           print_r($editresults); 

        }*/


        $editquery = "UPDATE `"._DB_PREFIX_."businessdirectories_ads` SET `title` = '".$title."', `description` = '".$description."', `purchase_type` = '".$purchase_type."', `price_start` = '".$price_start."', `price_end` = '".$price_end."', `show_text` = '".$show_text."', `expire_date` = '".$expire_date."' WHERE `"._DB_PREFIX_."businessdirectories_ads`.`id_ad` = '".$id_ad."' ";

         $editres = Db::getInstance()->executeS( $editquery );


         $deltag = "DELETE FROM `"._DB_PREFIX_."businessdirectories_ad_types` WHERE `id_ad` = '".$id_ad."' ";

         $editres = Db::getInstance()->executeS( $deltag );

          if (!empty($id_ad)) {
                foreach (Tools::getValue('types') as $single_type) {

                    $res = Db::getInstance()->insert('businessdirectories_ad_types', array(
                        'id_ad' => (int)$id_ad,
                        'id_type' => $single_type
                    ));
                }
            }


        $id_tag = Db::getInstance()->getValue('SELECT id_tag FROM `'._DB_PREFIX_.'businessdirectories_ad_tags` WHERE `id_ad` = "'.$id_ad.'"');


       $updatetag = Db::getInstance()->executeS("UPDATE `'._DB_PREFIX_.'businessdirectories_tags` SET `name` = '".$tags."' WHERE `'._DB_PREFIX_.'businessdirectories_tags`.`id_tag` = '".$id_tag."' ");

       $this->context->smarty->assign(array(
                'success' => 'Ad Successfully updated',
            ));

       header( "refresh:1;url= $redurl " );




         


        }

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

            $types = $obj->getAdTypes();
        
            // get images
            $images = $obj->getAllImages((int)$this->context->customer->id);




            $get_ad_images = $obj->getAdvimages(Tools::getValue('id_ad'));
            $ad_images = array();
            if (!empty($get_ad_images)) {
                foreach ($get_ad_images as $single_ad_image) {
                    $ad_images[$single_ad_image['id_image']] = $single_ad_image['name'];
                }
            }

           
            $this->context->smarty->assign(array(
                'base_url' => $base_url,
                'get_ad' => $get_ad,
                'ad_tags' => $ad_tags,
                'adv_types' => $adv_types,
                'types'     => $types,
                'images'    => $images,
                'prices' => $prices,
                'customer_id' => $this->context->customer->id,
                'ad_images' => $ad_images,
                'id_ad' => (int)Tools::getValue('id_ad')
            ));

            if (Businessdirectories::isPs17()) {
                $this->setTemplate('module:businessdirectories/views/templates/front/edit-my-ad.tpl');
            }
        } else {
            Tools::redirect('index.php');
        }


    }
    
    /**
     * @return array
     */
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }
}
