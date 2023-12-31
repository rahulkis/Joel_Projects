/**
* This file should contain frontend styles that
* will be applied to individual module instances.
*
* You have access to three variables in this file:
*
* $module An instance of your module class.
* $id The module's ID.
* $settings The module's settings.
*
*
*/

.fl-node-<?php echo $id; ?> #pie-chart-<?php echo $id; ?>{
    <?php if (!empty($settings->chart_size)): ?>
        height: <?php echo $settings->chart_size; ?>px;
    <?php endif; ?>

    <?php if (!empty($settings->chart_size)): ?>
        width: <?php echo $settings->chart_size; ?>px;
    <?php endif; ?>
}