<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/blocklayered.php');

if (substr(Tools::encrypt('blocklayered/index'),0,10) != Tools::getValue('token') || !Module::isInstalled('blocklayered'))
	die;

echo BlockLayered::indexUrl((int)Tools::getValue('id_category'), (int)Tools::getValue('truncate'));