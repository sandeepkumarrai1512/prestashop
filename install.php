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
$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'businessdirectories` (
    `id_businessdirectories` int(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY  (`id_businessdirectories`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'businessdirectories_types` (
    `id_type` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `date_add` date NOT NULL,
    PRIMARY KEY  (`id_type`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'businessdirectories_tags` (
    `id_tag` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `date_add` date NOT NULL,
    PRIMARY KEY  (`id_tag`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'businessdirectories_images` (
    `id_image` int(11) NOT NULL AUTO_INCREMENT,
    `id_customer` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `type` INT NOT NULL,
    `date_add` date NOT NULL,
    PRIMARY KEY  (`id_image`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'businessdirectories_ads` (
    `id_ad` int(11) NOT NULL AUTO_INCREMENT,
    `id_customer` int(11) NOT NULL,
    `id_shop` int(11) NOT NULL,
    `title` TEXT NOT NULL,
    `description` TEXT NOT NULL,
    `purchase_type` varchar(255) NOT NULL,    
    `currency_type` int(11) NOT NULL,
    `price_start` varchar(255) NOT NULL,
    `price_end` varchar(255) NOT NULL,
    `shipping_to` VARCHAR(255) NOT NULL,
    `show_text` TEXT DEFAULT NULL,
    `expire_date` date DEFAULT NULL,
    `never_expire` VARCHAR(255) NOT NULL,
    `created_date` date NOT NULL,
    PRIMARY KEY  (`id_ad`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'businessdirectories_shipping` (
    `id_shipping` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `date_add` date NOT NULL,
    PRIMARY KEY  (`id_shipping`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'businessdirectories_ad_shipping` (
    `id_ad_shipping` int(11) NOT NULL AUTO_INCREMENT,
    `id_ad` int(11) NOT NULL,
    `id_shipping` int(11) NOT NULL,
    PRIMARY KEY  (`id_ad_shipping`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'businessdirectories_ad_types` (
    `id_ad_type` int(11) NOT NULL AUTO_INCREMENT,
    `id_ad` int(11) NOT NULL,
    `id_type` int(11) NOT NULL,
    PRIMARY KEY  (`id_ad_type`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'businessdirectories_ad_tags` (
    `id_ad_tag` int(11) NOT NULL AUTO_INCREMENT,
    `id_ad` int(11) NOT NULL,
    `id_tag` int(11) NOT NULL,
    PRIMARY KEY  (`id_ad_tag`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'businessdirectories_ad_images` (
    `id_ad_image` int(11) NOT NULL AUTO_INCREMENT,
    `id_ad` int(11) NOT NULL,
    `id_image` int(11) NOT NULL,
    PRIMARY KEY  (`id_ad_image`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';



foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
