<?php
/*
* 2007-2013 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Feeder extends Module
{
	private $_postErrors = array();
	
	public function __construct()
	{
		$this->name = 'feeder';
		$this->tab = 'front_office_features';
		$this->version = 0.2;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		
		$this->_directory = dirname(__FILE__).'/../../';
		parent::__construct();
		
		$this->displayName = $this->l('RSS products feed');
		$this->description = $this->l('Generate a RSS products feed');
	}
	
	public function install()
	{
		if (!parent::install())
			return false;
		if (!$this->registerHook('header'))
			return false;
		return true;
	}
	
	public function hookHeader($params)
	{
		global $smarty, $cookie;
		
		$id_category = (int)(Tools::getValue('id_category'));
		
		if (!$id_category)
		{
			if (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], Tools::getHttpHost()) && preg_match('!^(.*)\/([0-9]+)\-(.*[^\.])|(.*)id_category=([0-9]+)(.*)$!', $_SERVER['HTTP_REFERER'], $regs))
			{
				if (isset($regs[2]) AND is_numeric($regs[2]))
					$id_category = (int)($regs[2]);
				elseif (isset($regs[5]) AND is_numeric($regs[5]))
					$id_category = (int)($regs[5]);
			}
			elseif ($id_product = (int)(Tools::getValue('id_product')))
			{
				$product = new Product($id_product);
				$id_category = $product->id_category_default;
			}
		}
		$category = new Category($id_category);
		$orderBy = Tools::getProductsOrder('by', Tools::getValue('orderby'));
		$orderWay = Tools::getProductsOrder('way', Tools::getValue('orderway'));
		$smarty->assign(array(
			'feedUrl' => Tools::getShopDomain(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/rss.php?id_category='.$id_category.'&amp;orderby='.$orderBy.'&amp;orderway='.$orderWay,
		));
		return $this->display(__FILE__, 'feederHeader.tpl');
	}

	public function getContent()
	{
		/* display the module name */
		$this->_html = '<h2>'.$this->displayName.'</h2><br />';
		$this->_html .= $this->l('Url for example:').'<br />';

		$orderBy = Tools::getProductsOrder('by');
		$orderWay = Tools::getProductsOrder('way');
		$this->_html .= Tools::getShopDomain(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/rss.php?id_category=<span style="color:red;">{id_category}</span>&amp;orderby='.$orderBy.'&amp;orderway='.$orderWay;
		$this->_html .= '<br /><br />'.$this->l('Replace').' <span style="color:red;">{id_category}</span> '.$this->l('by the id category current or "0"');

		return $this->_html;
	}
}
