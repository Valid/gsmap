<?php
    session_start();
    require_once BASE_PATH.'vendor/autoload.php';
    require_once(BASE_PATH.'classes'.DIRECTORY_SEPARATOR.'Autoloader.php');
    \grmule\AutoLoader::register();
    \grmule\AutoLoader::registerDirectory(BASE_PATH.'classes','GSMap');
    \grmule\AutoLoader::registerDirectory(BASE_PATH.'vendor\grmule\tpldotphp\src','grmule\tpldotphp');
?>