<?php $uid=md5(time()); ?><!DOCTYPE html>
<html>
<head>
    <title>Voter Guide | Grassroots Select</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js"></script>
    <script src="js/common.js?<?=($uid)?>"></script>
    <script src="js/index.js?<?=($uid)?>"></script>
    <link rel="stylesheet" href="css/common.css?<?=($uid)?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div class="container" >
    <div class="left_column">
        <h3>Search</h3>
        <p>Find your district & voting information</p>
        <form>
            <div style="display: block;">
                <label>Street Address</label>
                <input value="" type="text" name="street" />
            </div>
            <div style="display: block;">
                <label>City</label>
                <input value="" type="text" name="city" />
            </div>
            <div style="display: block;">
                <label>State (2 letter abbreviation)</label>
                <input value="" type="text" name="state" />
            </div>
            <button class="do-lookup-btn">find district</button>
        </form>


        <p class="panel">
            developer note: use the address 316 S Taylor St Amarillo, TX to see what it does when we have info on the users's district.<br>
            <br>
            use your own address to see what it does when we don't have information... unless you happen to live in Amarillo, in which case... make something up.
        </p>

        <div class="response-container"></div>
    </div>
    <div class="right_column">
        <h3>Explore</h3>
        <p>Tap to expand your state, then take a look at the House and Senate candidates for your district in comparison (opens in a new tab)</p>
        <ul class="state-navigation">
        <?php foreach ($data['states'] as $state): ?>
            <li class="state-navigation-item accordion-toggle">
                <a href="#"><?=($state['state']['name'])?></a>
            </li>
            <li class="accordion-content default">
                <?php foreach ($state['districts'] as $district): ?>
                    <strong>District <?=($district['id'])?></strong>
                    <p>
                        <?php if ($district['hasHouse'] === true): ?>
                        <a target="_new" href="/gsmap/public/federal/H/primary/<?=($state['state']['id'])?>/<?=($district['id'])?>">
                            US House
                        </a>
                        <?php endif; ?>
                        <?php if ($district['hasHouse'] === true && $district['hasSenate'] === true): ?> | <?php endif; ?>
                        <?php if ($district['hasSenate'] === true): ?>
                            <a target="_new" href="/gsmap/public/federal/S/primary/<?=($state['state']['id'])?>/<?=($district['id'])?>">
                                US Senate
                            </a>
                        <?php endif; ?>
                    </p>
                <?php endforeach; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
    <div style="clear:both;"></div>
    <footer class="footer">
        <p>paid for by SuperYuuuugePAC, which is not really a PAC. Or yuuuge. It IS super, though.</p>
    </footer>
</div>

</body>
</html>
