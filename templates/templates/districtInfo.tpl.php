<h3><?=($data['stateName'])?></h3>
<p>
    You are in <strong>district <?=($data['rawDistrict'])?></strong>.
</p>
<p>
    <a target="_new" href="http://voteforbernie.org/state/<?=(strtolower($data['stateName']))?>/">
        Find out more about how and when to vote in your state</a>
        <?php if ($data['gsState'] !== false && ($data['gsDistrict']['hasHouse'] === true || $data['gsDistrict']['hasSenate'] === true)): ?>
, or explore the candidate comparisons available here.
</p>
<p>
    <?php if ($data['gsDistrict']['hasHouse'] === true): ?>
        <a target="_new" href="/gsmap/public/federal/H/primary/<?=($data['gsState']['id'])?>/<?=($data['gsDistrict']['id'])?>">
            US House
        </a>
    <?php endif; ?>
    <?php if ($data['gsDistrict']['hasHouse'] === true && $data['gsDistrict']['hasSenate'] === true): ?> | <?php endif; ?>
    <?php if ($data['gsDistrict']['hasSenate'] === true): ?>
        <a target="_new" href="/gsmap/public/federal/S/primary/<?=($data['gsState']['id'])?>/<?=($data['gsDistrict']['id'])?>">
            US Senate
        </a>
    <?php endif; ?>
</p>
        <?php else : ?>
.
</p>
        <?php endif; ?>
