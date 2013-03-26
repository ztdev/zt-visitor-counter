<?php
/**
 * @package ZT Counter module
 * @author Hiepvu
 * @copyright(C) 2013 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/
// no direct access
defined('_JEXEC') or die;
?>

<div id="ztvc-visitor-counter<?php echo $module->id; ?>" class="ztvc-visitor-counter <?php echo $moduleclass_sfx; ?>">
<?php if ($showDigit) { ?>
    <div class="digit-counter"><?php echo $digits; ?></div>
<?php } ?>

    <div class="ztvc-content-counter">

        <?php if ($showIcons) { ?>
            <div class="ztvc-left ztvc-icons">
                <?php echo $help->renderIcons($show); ?>
            </div>
        <?php } ?>
        <?php if ($showTitles) { ?>
            <div class="ztvc-left titles">
                <?php echo $help->renderTitles($show); ?>
            </div>
        <?php } ?>

        <?php if ($showTotals) { ?>
            <div class="ztvc-left totals txt-right">
                <?php echo $help->renderTotalVisit($show, $totals); ?>
            </div>
        <?php } ?>

        <div class="clearfix"></div>
    </div>
    <hr class="ztvc-space">
<?php if ($showForeCast && $totals["foreCast"] > 0) { ?>
    <div class="ztvc-content-counter">
        <?php if ($showIcons) { ?>
            <div class="ztvc-left ztvc-icons">
                <div class="ztvc-row ztvc-icon-forecast"></div>
            </div>
        <?php } ?>

        <?php if ($showTitles) { ?>
            <div class="ztvc-left titles">
                <div class="ztvc-row"> <?php echo $show["foreCast"];?></div>
            </div>
        <?php } ?>

        <?php if ($showTotals) { ?>
            <div class="ztvc-left totals txt-right">
                <div class="ztvc-row"><?php echo $totals['foreCast'];?></div>
            </div>
        <?php } ?>
    </div>
    <hr class="ztvc-space">
<?php } ?>

<?php if ($showAgent) { ?>
    <div class="ztvc-icon-agent">
        <?php echo $userAgents;?>
    </div>
<?php } ?>
<?php if ($showOnline) {

    $guest = JText::plural('MOD_ZT_VISITOR_COUNTER_GUESTS', $count['guest']);
    $member = JText::plural('MOD_ZT_VISITOR_COUNTER_MEMBERS', $count['member']);
    ?>
    <div class="ztvc-count-online">
        <?php
        if ($showAllOnline) {
            echo JText::sprintf('MOD_ZT_VISITOR_COUNTER_ONLINE_S_MINUTES_AGO', $duration) . ":" . $count['total_online'];
        }
        if ($showGuestOnline) {
            echo  "<br>" . $guest;
        }
        if ($showMemberOnline) {
            echo "<br>" . $member;
        }

        ?>
    </div>
<?php } ?>

<?php if ($ip) {
    echo "<br>" . $ip;
}

echo base64_decode($params->get('zt-copyright'));