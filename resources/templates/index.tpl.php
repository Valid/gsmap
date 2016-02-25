<?php $uid=md5(time()); ?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <title>Voter Guide | Grassroots Select</title>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js"></script>
        <script src="<?=($data['baseUrl'])?>js/common.js?<?=($uid)?>"></script>
        <script src="<?=($data['baseUrl'])?>js/index.js?<?=($uid)?>"></script>
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
        <link rel="stylesheet" href="<?=($data['baseUrl'])?>css/reset.css?<?=($uid)?>">
        <link rel="stylesheet" href="<?=($data['baseUrl'])?>css/common.css?<?=($uid)?>">
        <link rel="stylesheet" href="<?=($data['baseUrl'])?>css/index.css?<?=($uid)?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <div class="columnsContainer">
            <div class="leftColumn">
                <h3>Search</h3>
                <p>Find your district & voting information</p>
                <form>
                    <div>
                        <label>Street Address</label>
                        <input value="" type="text" name="street" />
                    </div>
                    <div>
                        <label>City</label>
                        <input value="" type="text" name="city" />
                    </div>
                    <div>
                        <label>State (2 letter abbreviation)</label>
                        <input value="" type="text" name="state" />
                    </div>
                    <button class="do-lookup-btn">find district</button>
                </form>
                <div class="response-container"></div>
            </div>
            <div class="rightColumn">
                <h3>Explore</h3>
                <p>Tap to expand your state, then take a look at the House and Senate candidates for your district in comparison (opens in a new tab)</p>
                <ul class="state-navigation">
                    <?php foreach ($data['states'] as $state): ?>
                        <li class="state-navigation-item accordion-toggle">
                            <a href="#"><?=($state['state']['name'])?></a>
                        </li>
                        <li class="accordion-content default">
                            <ul class="district-list">
                            <?php foreach ($state['districts'] as $district): ?>
                                <li class="one-district">
                                <?php if ($district['district'] !== '00'): ?>
                                    <a class="button" target="_new" href="<?=($data['baseUrl'])?>view/federal/H/primary/<?=($state['state']['id'])?>/<?=($district['district'])?>">
                                        US House District <?=($district['district'])?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($district['district'] === '00'): ?>
                                    <a class="button red" target="_new" href="<?=($data['baseUrl'])?>view/federal/S/primary/<?=($state['state']['id'])?>/<?=($district['district'])?>">
                                        US Senate
                                    </a>
                                <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <footer>
            <p>paid for by SuperYuuuugePAC, which is not really a PAC. Or yuuuge. It IS super, though.</p>
            <p>&copy;2016 Grassroots Select | <a href="http://grassrootsselect.org/">http://grassrootsselect.org/</a></p>
        </footer>
    </body>
</html>