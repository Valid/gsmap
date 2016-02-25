<?php
    $base = realpath(__DIR__).DIRECTORY_SEPARATOR;
    define('BASE_PATH', $base);

    define('GOOGLE_MAPS_API_KEY','AIzaSyDQQEuzyzZUuhUNcWiGPCs2fxHxlvLopXI');
    define('SUNLIGHT_FOUNDATION_API_KEY','c1d7c83ceb694aeebe1341403ced3942');
    define('BASE_URL', 'http://chrismbaker.com/gsmap/public/');

    // Could probably get rid of these if I weren't lazy
    define('CANDIDATE_CARD_WIDTH', 250);
    define('CANDIDATE_CARD_HEIGHT', 500);


    define('RESOURCE_PATH', BASE_PATH.'resources'.DIRECTORY_SEPARATOR);
    define('IMAGE_PATH', RESOURCE_PATH.'images'.DIRECTORY_SEPARATOR);
    define('CANDIDATE_IMAGE_PATH', RESOURCE_PATH.'candidateImages'.DIRECTORY_SEPARATOR);
    define('BORDER_IMAGE_PATH', IMAGE_PATH);
    define('FONT_PATH', RESOURCE_PATH);
    define('ISSUE_CARD_PATH', RESOURCE_PATH.'issueCards'.DIRECTORY_SEPARATOR);
    define('DATA_PATH', RESOURCE_PATH.'data'.DIRECTORY_SEPARATOR);
    define('TEMPLATE_PATH', RESOURCE_PATH.'templates'.DIRECTORY_SEPARATOR);
?>