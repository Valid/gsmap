<?php $uid=md5(time()); ?><!DOCTYPE html>
<html>
<head>
    <title><?=($data['stateId'])?> <?=($data['levelLabel'])?> District <?=($data['districtId'])?> | Grassroots Select</title>
    <meta name="viewport"               content="width=device-width, initial-scale=1">
    <meta property="og:url"             content="<?=($data['baseUrl'].'view/'.$data['level'].'/'.$data['seat'].'/'.$data['election'].'/'.$data['stateId'].'/'.$data['districtId'])?>" />
    <meta property="og:type"            content="article" />
    <meta property="og:title"           content="<?=($data['stateId'])?> <?=($data['levelLabel'])?> District <?=($data['districtId'])?> | Grassroots Select" />
    <meta property="og:description"     content="Comparing the candidates running to represent <?=($data['stateId'])?> district <?=($data['districtId'])?> in the <?=($data['levelLabel'])?>" />
    <meta property="og:image"           content="<?=($data['baseUrl'].$data['level'].'/'.$data['seat'].'/'.$data['election'].'/'.$data['stateId'].'/'.$data['districtId'])?>.png" />

    <meta name="twitter:card"           content="summary_large_image" />
    <meta name="twitter:site"           content="@grmule" />
    <meta name="twitter:title"          content="<?=($data['stateId'])?> <?=($data['levelLabel'])?> District <?=($data['districtId'])?>" />
    <meta name="twitter:image"          content="<?=($data['baseUrl'])?><?=($data['level'])?>/<?=($data['seat'])?>/<?=($data['election'])?>/<?=($data['stateId'])?>/<?=($data['districtId'])?>.png" />
    <meta name="twitter:url"            content="<?=($data['baseUrl'].'view/'.$data['level'].'/'.$data['seat'].'/'.$data['election'].'/'.$data['stateId'].'/'.$data['districtId'])?>" />


    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js"></script>

    <script src="<?=($data['baseUrl'])?>/js/common.js?<?=($uid)?>"></script>
    <script src="<?=($data['baseUrl'])?>/js/view.js?<?=($uid)?>"></script>
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
    <link rel="stylesheet" href="<?=($data['baseUrl'])?>/css/reset.css?<?=($uid)?>">
    <link rel="stylesheet" href="<?=($data['baseUrl'])?>/css/common.css?<?=($uid)?>">
    <link rel="stylesheet" href="<?=($data['baseUrl'])?>/css/view.css?<?=($uid)?>">
</head>
<body>
    <img class="main-img" src="<?=($data['baseUrl'])?><?=($data['level'])?>/<?=($data['seat'])?>/<?=($data['election'])?>/<?=($data['stateId'])?>/<?=($data['districtId'])?>" />
    <div class="social-media-icons">
        <a class="social-icon" href="https://www.facebook.com/sharer/sharer.php?u=<?=(urlencode($data['baseUrl'].'view/'.$data['level'].'/'.$data['seat'].'/'.$data['election'].'/'.$data['stateId'].'/'.$data['districtId']))?>" target="_blank">
            <img src="<?=($data['baseUrl'])?>images/facebook-icon.png" />
        </a>
        <a class="social-icon" href="http://twitter.com/share?text=<?=(urlencode('Comparing the candidates running to represent '.$data['stateId'].' district '.$data['districtId'].' in the '.$data['levelLabel']))?>&url=<?=(urlencode($data['baseUrl'].'view/'.$data['level'].'/'.$data['seat'].'/'.$data['election'].'/'.$data['stateId'].'/'.$data['districtId']))?>">
            <img src="<?=($data['baseUrl'])?>images/twitter-icon.png" />
        </a>
        <a class="social-icon" href="https://plus.google.com/share?url=<?=(urlencode($data['baseUrl'].'view/'.$data['level'].'/'.$data['seat'].'/'.$data['election'].'/'.$data['stateId'].'/'.$data['districtId']))?>">
            <img src="<?=($data['baseUrl'])?>images/google-icon.png" />
        </a>
    </div>
</body>
</html>