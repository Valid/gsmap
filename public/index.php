<?php

$base = realpath(__DIR__.'/../').DIRECTORY_SEPARATOR;

require_once $base.'vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

/*
$templateEngine = new \GRMule\TplDptPhp\PhpEngine([
    __DIR__.'/templates'
]);
*/

$issues = array(
    'medical'=>array(
        'card'=>'medical.png',
        'textColor'=>array(0,0,0),
        'size'=>28,
        'conflict'=>array(
            array(
                'x'=>128,
                'y'=>90,
                'text'=>'[left.name] [left.positions]'
            ),
            array(
                'x'=>112,
                'y'=>125,
                'text'=>'healthcare for all'
            ),
            array(
                'x'=>127,
                'y'=>160,
                'text'=>'[right.name] [right.positions] it'
            ),
        ),
        'match'=>array(
            array(
                'x'=>125,
                'y'=>50,
                'text'=>'both candidates [left.position]'
            ),
            array(
                'x'=>112,
                'y'=>90,
                'text'=>'healthcare for all'
            )
        )
    ),
    'education'=>array(
        'card'=>'education.png',
        'textColor'=>array(0,0,0),
        'size'=>20,
        'conflict'=>array(
            array(
                'x'=>0,
                'y'=>0,
                'text'=>'[left.name] [left.positions]'
            ),
            array(
                'x'=>0,
                'y'=>0,
                'text'=>'healthcare for all'
            ),
            array(
                'x'=>0,
                'y'=>0,
                'text'=>'[right.name] [right.position] it'
            ),
        ),
        'match'=>array(
            array(
                'x'=>0,
                'y'=>0,
                'text'=>'both candidates [left.position]'
            ),
            array(
                'x'=>0,
                'y'=>0,
                'text'=>'healthcare for all'
            )
        )
    )
);

$candidates = array(
    1 => array(
        'name'      => 'Homer Simpson',
        'image'=>'candidateA.png',
        'points'=>array(
            'Who\'s brave enough to fly into something we all keep calling a death sphere?',
            'Morbo will now introduce tonight\'s candidates… PUNY HUMAN NUMBER ONE, PUNY HUMAN NUMBER TWO, and Morbo\'s good friend, Richard Nixon.',
            'What kind of a father would I be if I said no?'
        ),
        'issues'=>array(
            'medical'=>'oppose',
            'education'=>'oppose'
        )
    ),
    2 => array(
        'name'      => 'Phillip J. Fry',
        'image'=>'candidateB.png',
        'points'=>array(
            'Isn\'t it true that you have been paid for your testimony?',
            'What are their names?',
            'The key to victory is discipline, and that means a well made bed. You will practice until you can make your bed in your sleep.'
        ),
        'issues'=>array(
            'medical'=>'support',
            'education'=>'support'
        )
    ),
);
define('RIGHT_POINT', 251);
define('CANDIDATE_IMAGE_PATH', $base.'candidateImages'.DIRECTORY_SEPARATOR);
define('BORDER_PATH', $base.'templates/images/border.png');
define('FONT_PATH', $base.'templates/opensans.ttf');
define('ISSUE_CARD_PATH', $base.'templates/images'.DIRECTORY_SEPARATOR);
define('ISSUE_CARD_START_VERTICAL_OFFSET', 836);
define('ISSUE_CARD_HEIGHT', 294);
define('OUTPUT_WIDTH', 500);

$app->get('/', function () {
    return file_get_contents('templates/index.tpl');
});

$app->get('/race/{id}', function (Silex\Application $app, $id) use ($base, $candidates, $issues) {
    // Create Image From Existing File
    $top = imagecreatefrompng($base.'templates/images/top.png');

    $left = $candidates[1];
    $right = $candidates[2];

    $top = drawCandidateName($left, $top, 0);
    $top = drawCandidateName($right, $top, RIGHT_POINT);

    $top = drawCandidatePoints($left, $top, 0);
    $top = drawCandidatePoints($right, $top, RIGHT_POINT);

    $top = drawCandidatePicture($left, $top, 0);
    $top = drawCandidatePicture($right, $top, RIGHT_POINT);


    $cardCount = 0;
    foreach ($issues as $issueKey=>$issue) {
        $top = drawCandidateIssue($issueKey, $issue, $cardCount, $candidates, $top, 0);
        $cardCount++;
    }

    // Send Image to Browser
    header('Content-type: image/jpeg');
    imagejpeg($top);

    // Clear Memory
    imagedestroy($top);
});

$app->run();

function drawCandidateIssue($issueKey, $issueCard, $cardCount,  $data, $image, $offset) {
    $image = extendCanvass($image, ISSUE_CARD_HEIGHT);
    $src = imagecreatefrompng(ISSUE_CARD_PATH.$issueCard['card']);
    //imagealphablending($image, false);
    imagesavealpha($image, true);
    $cardTop = ISSUE_CARD_START_VERTICAL_OFFSET + (ISSUE_CARD_HEIGHT * $cardCount);
    imagecopymerge($image, $src, $offset, $cardTop, 0, 0, 500, ISSUE_CARD_HEIGHT, 100);

    $left = $data[1];
    $right = $data[2];

    foreach ($issueCard['conflict'] as $textLine) {
        $box = new \GDText\Box($image);
        $box->setFontFace(FONT_PATH);
        $box->setFontColor(new \GDText\Color(0, 0, 0));
        $box->setFontSize($issueCard['size']);
        $box->setBox($textLine['x'], $cardTop+$textLine['y'], OUTPUT_WIDTH - $textLine['x'], 62);
        $box->setTextAlign('left', 'top');
        $parsedText = $textLine['text'];

        $leftPosition = $left['issues'][$issueKey];
        $parsedText = str_replace('[left.name]', $left['name'], $parsedText);
        $parsedText = str_replace('[left.position]', $leftPosition, $parsedText);
        $parsedText = str_replace('[left.positions]', $leftPosition.'s', $parsedText);

        $rightPosition = $right['issues'][$issueKey];
        $parsedText = str_replace('[right.name]', $right['name'], $parsedText);
        $parsedText = str_replace('[right.position]', $rightPosition, $parsedText);
        $parsedText = str_replace('[right.positions]', $rightPosition.'s', $parsedText);

        $box->draw($parsedText);
    }


    return $image;
}

function extendCanvass($image, $addToHeight) {
    $oldw = imagesx($image);
    $oldh = imagesy($image);
    $newimage = imagecreatetruecolor($oldw, $oldh+$addToHeight); // Creates a black image
    // Fill it with white (optional)
    $white = imagecolorallocate($newimage, 255, 255, 255);
    imagefill($newimage, 0, 0, $white);
    imagecopy($newimage, $image, 0, 0, 0, 0, $oldw, $oldh);
    return $newimage;
}

function drawCandidatePicture($data, $image, $offset) {
    $src = imagecreatefrompng(CANDIDATE_IMAGE_PATH.$data['image']);
    imagealphablending($image, false);
    imagesavealpha($image, true);
    imagecopymerge($image, $src, $offset, 224, 0, 0, 250, 145, 100);
    return $image;
}

function drawCandidateName($data, $image, $offset) {
    $box = new \GDText\Box($image);
    $box->setFontFace(FONT_PATH);
    $box->setFontColor(new \GDText\Color(255, 255, 255));
    $box->setFontSize(20);
    $box->setBox($offset, 362, 250, 62);
    $box->setTextAlign('center', 'center');
    $box->draw($data['name']);
    $box->draw($data['name']);
    return $image;
}
function drawCandidatePoints($data, $image, $offset) {
    $box = new \GDText\Box($image);
    $box->setFontFace(FONT_PATH);
    $box->setFontColor(new \GDText\Color(0, 0, 0));
    $box->setFontSize(20);
    $box->setBox($offset+10, 428, 240, 62);
    $box->setTextAlign('left', 'top');
    $message = "• ".implode("\n\n• ", $data['points']);
    $box->draw($message);
    return $image;
}


?>