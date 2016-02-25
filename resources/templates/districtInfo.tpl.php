<h3><?=($data['stateName'])?></h3>
<p>
    You are in <strong>district <?=($data['rawDistrict'])?></strong>.
</p>
<p>
    <a target="_new" href="http://voteforbernie.org/state/<?=(strtolower($data['stateName']))?>/">
        Find out more about how and when to vote in your state</a><?php if ($data['hasHouse'] === true && $data['hasSenate'] === true): ?>, or explore the candidate comparisons available here.
</p>
<p>
    <?php if ($data['hasHouse'] === true): ?>
        <a class="button" style="max-width:48%; display:inline-block;" target="_new" href="<?=($data['baseUrl'])?>view/federal/H/primary/<?=($data['gsState']['id'])?>/<?=($data['gsDistrict']['district'])?>">
            US House
        </a>
    <?php endif; ?>
    <?php if ($data['hasSenate'] === true): ?>
        <a class="button red" style="max-width:48%;display:inline-block;"  target="_new" href="<?=($data['baseUrl'])?>view/federal/S/primary/<?=($data['gsState']['id'])?>/00">
            US Senate
        </a>
    <?php endif; ?>
<?php endif; ?>
</p>