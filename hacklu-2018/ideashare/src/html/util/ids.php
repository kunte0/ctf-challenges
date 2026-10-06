<?php


require_once __DIR__ . '/../IDS/Converter.php';
require_once __DIR__ . '/../IDS/Filter.php';
require_once __DIR__ . '/../IDS/Filter/Storage.php';
require_once __DIR__ . '/../IDS/Init.php';
require_once __DIR__ . '/../IDS/Event.php';
require_once __DIR__ . '/../IDS/Monitor.php';
require_once __DIR__ . '/../IDS/Report.php';


function getPhpIDSImpact() {
    $ids_path = __DIR__ . '/../IDS/';
    $init = IDS\Init::init($ids_path . 'Config/Config.ini.php');
    $init->config['General']['tmp_path'] = '/tmp';
    $init->config['General']['filter_path'] = $ids_path . 'default_filter.xml';
    $init->config['Caching']['caching'] = 'none';
    $ids = new IDS\Monitor($init);
    $request = array('GET' => $_GET, 'POST' => $_POST, 'COOKIE' => $_COOKIE);
    $ids_result = $ids->run($request);
    $impact = ($ids_result->isEmpty()) ? 0 : $ids_result->getImpact();
    return $impact;
}