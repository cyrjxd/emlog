<?php
/**
 * templates
 * @package EMLOG
 * 
 */

/**
 * @var string $action
 * @var object $CACHE
 */

require_once 'globals.php';

$Template_Model = new Template_Model();

if ($action === '') {
    $nonce_template = Option::get('nonce_templet');
    $nonce_template_data = @file(TPLS_PATH . $nonce_template . '/header.php');

    $templates = $Template_Model->getTemplates();

    include View::getAdmView('header');
    require_once View::getAdmView('template');
    include View::getAdmView('footer');
    View::output();
}

if ($action === 'use') {
    LoginAuth::checkToken();
    $tplName = isset($_GET['tpl']) ? addslashes($_GET['tpl']) : '';

    Option::updateOption('nonce_templet', $tplName);
    $CACHE->updateCache('options');
    $Template_Model->initCallback($tplName);

    emDirect("./template.php?activated=1");
}

if ($action === 'del') {
    LoginAuth::checkToken();
    $tplName = isset($_GET['tpl']) ? addslashes($_GET['tpl']) : '';

    $nonce_templet = Option::get('nonce_templet');
    if ($tplName === $nonce_templet) {
        emMsg('不能删除正在使用的模板');
    }

    $Template_Model->rmCallback($tplName);

    $path = preg_replace("/^([\w-]+)$/i", "$1", $tplName);
    if ($path && true === emDeleteFile(TPLS_PATH . $path)) {
        emDirect("./template.php?activate_del=1#tpllib");
    } else {
        emDirect("./template.php?error_f=1#tpllib");
    }
}
