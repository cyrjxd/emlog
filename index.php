<?php
/**
 * @package EMLOG
 * 
 */

require_once 'init.php';

$emDispatcher = Dispatcher::getInstance();
$emDispatcher->dispatch();
View::output();
