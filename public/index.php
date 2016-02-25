<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
require_once('../env.php');
require_once('../bootstrap.php');

define('BASE_URL', 'http://chrismbaker.com/gsmap/public/');
define('CANDIDATE_CARD_WIDTH', 250);
define('CANDIDATE_CARD_HEIGHT', 500);

$templateEngine = new \grmule\tpldotphp\PhpEngine(
    [
        TEMPLATE_PATH
    ],
    new \grmule\tpldotphp\TemplateUtilities(),
    true
);

$app = new Silex\Application();
$app['debug'] = true;

$stateLister = function () {
    $candidates = GSMap\DataFactory::db(DATA_PATH.'primaryCandidates.json', '\GSMap\Candidate');
    $states = GSMap\DataFactory::db(DATA_PATH.'states.json', '\GSMap\State');
    $districts = GSMap\DataFactory::db(DATA_PATH.'districts.json', '\GSMap\District');

    $statesOut = array();
    foreach ($candidates as $candidate) {
        if (array_key_exists($candidate->state, $statesOut) === false) {
            $state = $states->row($candidate->state);
            $statesOut[$candidate->state] = array(
                'state'=>$state->toArray(),
                'districts'=>array(),
                'hasHouse'=>false,
                'hasSenate'=>false
            );
            ksort($statesOut);
        }

        if (array_key_exists($candidate->district, $statesOut[$candidate->state]['districts']) === false) {
            $district = $districts->row($candidate->state.$candidate->district);
            if ($candidate->seat == 'H') {
                $statesOut[$candidate->state]['hasHouse'] = true;
            } else {
                $statesOut[$candidate->state]['hasSenate'] = true;
            }
            $statesOut[$candidate->state]['districts'][$candidate->district] = $district->toArray();
            ksort($statesOut[$candidate->state]['districts']);
        }
    }
    return $statesOut;
};

$app->get('/addressLookup/{address}', function($address) use ($templateEngine,$stateLister) {
   $address = urldecode($address);
   $data = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&key='.GOOGLE_MAPS_API_KEY);
   $geodata = @json_decode(
       $data, true
   );

    if (is_array($geodata) === false || $geodata['status'] != 'OK') {
        //var_dump($data);
        //var_dump($geodata);
        //die('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&key='.GOOGLE_MAPS_API_KEY);
        die(json_encode(array(
            'status'=>'ERROR',
            'message'=>'There was a problem looking up that address. There might be a problem with one of our lookup services, but make sure it is entered correctly. (e01)'
        )));
    }

    $firstLocationResult = array_shift($geodata['results']);
    $location = $firstLocationResult['geometry']['location'];

    $districtLocationRawData = file_get_contents('http://congress.api.sunlightfoundation.com/districts/locate?latitude='.$location['lat'].'&longitude='.$location['lng'].'&apikey='.SUNLIGHT_FOUNDATION_API_KEY);
    $districtLocationData = @json_decode(
        $districtLocationRawData,
        true
    );

    if (is_array($districtLocationData) === false || $districtLocationData['count'] < 1) {
        die(json_encode(array(
            'status'=>'ERROR',
            'message'=>'There was a problem looking up that address. There might be a problem with one of our lookup services, but make sure it is entered correctly.  (e02)'
        )));
    }

    $firstPollingLocationResult = array_shift($districtLocationData['results']);

    $states = GSMap\DataFactory::db(DATA_PATH.'states.json', '\GSMap\State');
    $theState = $states->row($firstPollingLocationResult['state']);

    $statesList = $stateLister();
    $thisStateData = false;
    $thisDistrictData = false;
    if (array_key_exists($firstPollingLocationResult['state'], $statesList) === true) {
        $thisStateData = $statesList[$firstPollingLocationResult['state']];
        if (array_key_exists($firstPollingLocationResult['district'], $thisStateData['districts']) === true) {
            $thisDistrictData = $thisStateData['districts'][$firstPollingLocationResult['district']];
        }
    }
    //print '<pre>';
//var_dump($thisStateData);
    die(json_encode(array(
        'status'=>'OK',
        'message'=>$templateEngine->template(
            'districtInfo',
            array(
                'rawDistrict'=>$firstPollingLocationResult['district'],
                'stateName'=>$theState->name,
                'gsState'=>$thisStateData !== false && $thisDistrictData !== false ? $thisStateData['state'] : false,
                'gsDistrict'=>$thisStateData !== false && $thisDistrictData !== false ? $thisDistrictData : false,
                'hasSenate'=>$thisStateData !== false && $thisDistrictData !== false ? $thisStateData['hasSenate'] : false,
                'hasHouse'=>$thisStateData !== false && $thisDistrictData !== false ? $thisStateData['hasHouse'] : false,
                'baseUrl'=>BASE_URL
            )
        ),
    )));

});


$app->get('/import', function () {
    $row = 1;
    $import = array();
    if (($handle = fopen(DATA_PATH.'import.csv', "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $import[$row] = array();
            $num = count($data);
            for ($c=0; $c < $num; $c++) {
                $import[$row][$c] = $data[$c];
            }
            $row++;
        }
        fclose($handle);
    }

    array_shift($import);
    array_shift($import);
    //print '<pre>';
    //var_dump($import);
    $template =  array(
        "id"=>"",
        "name"=>"",
        "image"=>"",
        "state"=>"",
        "district"=>"00",
        "seat"=>"",
        "points"=>array(),
        "issues"=>array()
    );

    $candidateCounter = 1;
    $candidatesOut = array();
    $districtsOut = array();
    foreach ($import as $candidateImport) {
        //$maxCols = count($candidateImport);
        if ($candidateImport[0] == '.0.') {
            break;
        }

        $thisCandidate = $template;
        $thisCandidate['id'] = trim($candidateImport[0]).$candidateCounter;
        $thisCandidate['name'] = trim($candidateImport[4]);
        $thisCandidate['image'] = trim($candidateImport[5]);
        $thisCandidate['state'] = trim($candidateImport[1]);
        $thisCandidate['district'] = trim($candidateImport[2]);
        $thisCandidate['seat'] = trim($candidateImport[3]);
        $thisCandidate['points'] = array(
            trim($candidateImport[6]),
            trim($candidateImport[7]),
            trim($candidateImport[8]),
        );

        $candidateCounter++;
        $candidatesOut[$thisCandidate['id']] = $thisCandidate;

        $districtId = $thisCandidate['state'].$thisCandidate['district'];
        if (array_key_exists($districtId, $districtsOut) === false) {
            $districtsOut[$districtId] = array(
                'id'=>$districtId,
                'district'=>$thisCandidate['district'],
                'state'=>$thisCandidate['state']
            );
        }

    }

    file_put_contents(DATA_PATH.'primaryCandidates.json', json_encode($candidatesOut, JSON_PRETTY_PRINT));
    file_put_contents(DATA_PATH.'districts.json', json_encode($districtsOut, JSON_PRETTY_PRINT));
    return 'done';
});
$app->get('/map', function () use ($templateEngine) {
    $html = $templateEngine->template('map', array(

    ));
    return $html;
});

$makeViewerPage = function ($level, $election, $seat, $stateId, $districtId) use ($templateEngine) {
    $html = $templateEngine->template('view', array(
        'level'=>$level,
        'election'=>$election,
        'seat'=>$seat,
        'stateId'=>$stateId,
        'districtId'=>$districtId,
        'levelLabel'=>'US '.($level == 'H' ? 'House' : 'Senate'),
        'baseUrl'=>BASE_URL
    ));
    return $html;
};
$app->get('/view/{level}/{seat}/{election}/{stateId}/{districtId}', $makeViewerPage);

$app->get('/', function () use ($templateEngine, $stateLister) {
    //print '<pre>';
    //var_dump($stateLister());
    $html = $templateEngine->template('index', array(
        'states'=>$stateLister(),
        'baseUrl'=>BASE_URL
    ));
    return $html;
});


$makeImage = function (Silex\Application $app, $seat, $stateId, $districtId) use ($base) {
    $candidates = GSMap\DataFactory::db(DATA_PATH.'primaryCandidates.json', '\GSMap\Candidate');
    $workWith = $candidates->where(array(
        array('state', $stateId),
        array('district', $districtId),
        array('seat', $seat)
    ));

    if (count($workWith) < 1) {
        die('frown image');
    }

    if (count($workWith) === 1) {
        die('only one candidate image');
    }

    $states = GSMap\DataFactory::db(DATA_PATH.'states.json', '\GSMap\State');
    $state = $states->row($stateId);



    $finalWidth = (count($workWith))*CANDIDATE_CARD_WIDTH;

    $mainCanvas = new \GSMap\Canvas($finalWidth, CANDIDATE_CARD_HEIGHT, array(0,0,0));

    $candidateCount = 0;
    $candidate = false;
    $top = new \GSMap\Canvas(IMAGE_PATH.'headerGradient.png');

    foreach ($workWith as $candidate) {
        $candidateCount++;
        $candidateOffset = (($candidateCount-1)*CANDIDATE_CARD_WIDTH);
        $candidateCard = new \GSMap\Canvas(CANDIDATE_CARD_WIDTH, CANDIDATE_CARD_HEIGHT);

        $candidateImage = new \GSMap\Canvas(CANDIDATE_IMAGE_PATH.$candidate->image);
        $candidateImage->scale(120, 120);

        $candidateCard->addXY($candidateImage, 65, 25);
        $candidateCard->addXY(IMAGE_PATH.'candidate.png', 0, 0);

        $candidateCard->addText($candidate->name, 22, 138, 206, 42, 'center', 'center', 20, array(255,255,255));

        $message = "• ".implode("\n\n• ", $candidate->points);
        $candidateCard->addText($message, 30, 194, 188, 400, 'left', 'top', 16, array(255,255,255));

        $mainCanvas->addXY($candidateCard, $candidateOffset, 0);

        if ($candidateCount > 1) {
            $x = (($candidateCount-1)*CANDIDATE_CARD_WIDTH)-38;
            $mainCanvas->addXY(IMAGE_PATH.'vs.png', $x, 50);
            $top->addToSide(IMAGE_PATH.'headerGradient.png');
        }
    }

    $headerRibbonFile = IMAGE_PATH.'3candidate.png';
    if ($candidateCount > 1 && $candidateCount < 3) {
        $headerRibbonFile = IMAGE_PATH.$candidateCount.'candidate.png';
    }
    $headerRibbon = new \GSMap\Canvas($headerRibbonFile);
    $top->addXY($headerRibbon, 0, -10);


    $top->addText(' WHICH CANDIDATE', 1, 5, $finalWidth, 100, 'left', 'top', 32, array(50,50,50));
    $top->addText(' WHICH CANDIDATE', 0, 5, $finalWidth, 100, 'left', 'top', 32, array(255,255,255));
    $top->addText('           SHOULD I VOTE FOR?', 1, 36, $finalWidth, 100, 'left', 'top', 28, array(50,50,50));
    $top->addText('           SHOULD I VOTE FOR?', 0, 35, $finalWidth, 100, 'left', 'top', 28, array(255,255,255));

    $raceDescription = strtoupper(($candidate->seat == 'H' ? 'US House of Representatives' : 'US Senate').'     ');
    $top->addText($raceDescription, 1, 71, $finalWidth, 100, 'right', 'top', 20, array(50,50,50));
    $top->addText($raceDescription, 0, 70, $finalWidth, 100, 'right', 'top', 20, array(255,255,255));

    $raceLine2= strtoupper(($districtId !== '00' ? 'District '.$districtId.', ' : '').$state->name).' ';
    $top->addText($raceLine2, 1, 91, $finalWidth, 100, 'right', 'top', 28, array(50,50,50));
    $top->addText($raceLine2, 0, 90, $finalWidth, 100, 'right', 'top', 28, array(255,255,255));

    $mainCanvas->addToTop($top);

    $footer = new \GSMap\Canvas($finalWidth, 150, array(5,22,38));

    //$mainCanvas->addToBottom(IMAGE_PATH.count($workWith).'candidateFooter.png');
    $mainCanvas->addToBottom($footer);

    // Send Image to Browser
    header('Content-type: image/png');
    imagepng($mainCanvas->getCanvas());
    return '';
};
$app->get('/federal/{seat}/primary/{stateId}/{districtId}.png', $makeImage);
$app->get('/federal/{seat}/primary/{stateId}/{districtId}', $makeImage);



$app->run();


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
        'size'=>28,
        'conflict'=>array(
            array(
                'x'=>5,
                'y'=>80,
                'text'=>'[left.name] [left.positions]'
            ),
            array(
                'x'=>5,
                'y'=>110,
                'text'=>'healthcare for all'
            ),
            array(
                'x'=>5,
                'y'=>140,
                'text'=>'[right.name] [right.positions] it'
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


?>