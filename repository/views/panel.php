<?php

defined('PAR_EXEC') OR die('Payzito Repository Restricted Access');

use payzitoRepository\libraries\helper;

/**
 * @var array $menus
 * @var array $chartData
 * @var array $installData
 * @var array $pluginData
 * @var array $cdns
 */

?>

<?php $this->loadView('header'); ?>

<div class="pa-panel">
    <div class="pa-right">
        <div class="pa-right">
            <div class="pa-logo">
                <img src="<?php echo helper::imagesUri() ?>logo/04.png" />
            </div>
        </div>
        <div class="pa-left">
            <div class="pa-pa-description" count="3">
                <div class="pa-extension-desc">
                    <div class="pa-right"><?php echo helper::_('PA_COMPONENT_VERSION') ?></div>
                    <div class="pa-left">
                        <span class="pa-current-version"><?php echo helper::modifyNumber($pluginData['Version']); ?></span>
                        <span><?php echo helper::_('PA_PAYZITO_PRO'); ?></span>
                        <span> - </span>
                        <span>
                            <a class="pa-link-to-changelogs" href="#" target="_blank"><?php echo helper::_('PA_LINK_TO_CHANGELOGS'); ?></a>
                        </span>
                    </div>
                    <div class="pa-clear"></div>
                </div>
                <div class="pa-extension-desc">
                    <div class="pa-right"><?php echo helper::_('PA_PAYZITO_COPY_WRITE') ?></div>
                    <div class="pa-left">
                        <a href="#" target="_blank">
                            <span><?php echo helper::modifyNumber(helper::_('PA_COMPANY_FA',['{YEAR}' => helper::getDate('Y')])); ?></span>
                        </a>
                    </div>
                    <div class="pa-clear"></div>
                </div>
                <div class="pa-extension-desc">
                    <div class="pa-right"><?php echo helper::_('PA_PAYZITO_INFO') ?></div>
                    <div class="pa-left"><?php echo helper::_('PA_PAYZITO_INFO_COMPANY') ?></div>
                    <div class="pa-clear"></div>
                </div>
                <div class="pa-extension-desc">
                    <div class="pa-update-msg pa-update-msg-success">
                        <span class="pa-status-loading">
                            <i class="pa-icon-spinner"></i>
                        </span>
                        <span class="pa-status-msg"><?php echo helper::_('PA_YOUR_PAYZITO_IS_UPDATED'); ?></span>
                        <span class="pa-check-new-status-again">
                            <i class="pa-icon-refresh"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="pa-clear"></div>
    </div>
    <div class="pa-left">
        <div class="pa-trans-info pa-right">
            <div class="pa-right" sid="10">
                <i class="pa-icon-check"></i>
            </div>
            <div class="pa-right">
                <div class="pa-trans-1"><?php echo helper::_('PA_TRANSACTION_PREFIX').' '.helper::_('PA_TRANSACTION_10'); ?></div>
                <div class="pa-trans-2">75</div>
            </div>
            <div class="pa-clear"></div>
        </div>
        <div class="pa-trans-info pa-left">
            <div class="pa-right" sid="1">
                <i class="pa-icon-minus"></i>
            </div>
            <div class="pa-right">
                <div class="pa-trans-1"><?php echo helper::_('PA_TRANSACTION_PREFIX').' '.helper::_('PA_TRANSACTION_1'); ?></div>
                <div class="pa-trans-2">56</div>
            </div>
            <div class="pa-clear"></div>
        </div>
        <div class="pa-trans-info pa-right">
            <div class="pa-right" sid="2">
                <i class="pa-icon-file-text-o"></i>
            </div>
            <div class="pa-right">
                <div class="pa-trans-1"><?php echo helper::_('PA_TRANSACTION_PREFIX').' '.helper::_('PA_TRANSACTION_2'); ?></div>
                <div class="pa-trans-2">5</div>
            </div>
            <div class="pa-clear"></div>
        </div>
        <div class="pa-trans-info pa-left">
            <div class="pa-right" sid="3">
                <i class="pa-icon-ban"></i>
            </div>
            <div class="pa-right">
                <div class="pa-trans-1"><?php echo helper::_('PA_TRANSACTION_PREFIX').' '.helper::_('PA_TRANSACTION_3'); ?></div>
                <div class="pa-trans-2">8</div>
            </div>
            <div class="pa-clear"></div>
        </div>
        <div class="pa-clear"></div>
    </div>
    <div class="pa-clear"></div>
</div>

<div class="pa-panel-charts">
    <div class="pa-chart-parent pa-chart-report pa-right">
        <div>
            <p><?php echo helper::_('PA_CHART_THIS_MONTH_AMOUNT').' '.helper::getCurrency(); ?></p>
        </div>
        <div class="pa-chart-data">
            <span dir="ltr">7,256,500</span>
        </div>
    </div>
    <div class="pa-chart-parent pa-chart-report pa-left">
        <div>
            <p><?php echo helper::_('PA_CHART_GROWTH_RATE').' ('.helper::getDate('F Y',time()-30*24*60*60).') '.helper::_('PA_CHART_RATE').(helper::_('PA_MONTH_AGO')).' ('.helper::getDate('F Y',time()-2*30*24*60*60).')'; ?></p>
        </div>
        <div class="pa-chart-data pa-chart-data-1">
            <span>+</span>
            <span class="pa-ltr"><?php echo helper::modifyNumber(78); ?></span>
            <span>%</span>
        </div>
    </div>
    <div class="pa-chart-parent pa-right">
        <div id="pa-chart-1" class="pa-ltr">
            <div class="pa-loading-panel-chart"></div>
        </div>
        <div>
            <p><?php echo helper::_('PA_CHART_TRANSACTION_NUMBER_7'); ?></p>
        </div>
    </div>
    <div class="pa-chart-parent pa-left">
        <div id="pa-chart-2" class="pa-ltr">
            <div class="pa-loading-panel-chart"></div>
        </div>
        <div>
            <p><?php echo helper::_('PA_CHART_TRANSACTION_AMOUNT_7').' '.helper::getCurrency(); ?></p>
        </div>
    </div>
    <div class="pa-chart-parent pa-right">
        <div id="pa-chart-3" class="pa-ltr">
            <div class="pa-loading-panel-chart"></div>
        </div>
        <div>
            <p><?php echo helper::_('PA_CHART_TRANSACTION_NUMBER_30'); ?></p>
        </div>
    </div>
    <div class="pa-chart-parent pa-left">
        <div id="pa-chart-4" class="pa-ltr">
            <div class="pa-loading-panel-chart"></div>
        </div>
        <div>
            <p><?php echo helper::_('PA_CHART_TRANSACTION_AMOUNT_30').' '.helper::getCurrency(); ?></p>
        </div>
    </div>
    <div class="pa-clear"></div>
</div>

<div class="pa-plans-box" style="display: none">
    <div class="pa-plans">
        <div class="pa-plans-content">
            <div class="pa-plans-close">
                <i class="pa-icon-times"></i>
            </div>
            <div class="pa-plans-title"><?php echo helper::_('PA_PLANS_TITLE'); ?></div>
            <?php for($i=1; $i<=3; $i++): ?>
                <div class="pa-right">
                    <div class="pa-plan-name"><?php echo helper::_('PA_PLAN_'.$i.'_TITLE'); ?></div>
                    <div class="pa-plan-price"><?php echo helper::_('PA_PLAN_'.$i.'_PRICE'); ?></div>
                    <div class="pa-plan-desc"><?php echo helper::_('PA_PLAN_'.$i.'_DESC'); ?></div>
                    <?php for($b=1; $b<=5; $b++): ?>
                        <div class="pa-plan-row">
                            <?php if($i == 1 && $b == 1): ?>
                                <i class="fa pa-icon-check" style="color: #ffd012;"></i>
                            <?php elseif(($i == 1 && $b == 2) || ($i == 2 && $b == 2)): ?>
                                <i class="fa pa-icon-times" style="color: #ff2b02;"></i>
                            <?php else: ?>
                                <i class="fa pa-icon-check" style="color: #2fb118;"></i>
                            <?php endif; ?>
                            <?php echo helper::_('PA_PLAN_'.$i.'_ROW_'.$b) ?>
                        </div>
                    <?php endfor; ?>
                    <div class="pa-plan-btn">
                        <?php if($i != 1): ?>
                            <a href="#" target="_blank">
                        <?php endif; ?>
                        <button<?php echo $i == 1 ? ' class="pa-free-btn"' : ''; ?>><?php echo helper::_('PA_PLAN_'.$i.'_BTN'); ?></button>
                        <?php if($i != 1): ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php if($i == 1): ?>
                        <div class="pa-install-msg"></div>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
            <div class="pa-clear"></div>
        </div>
    </div>
</div>

<script>
    var PAData = typeof(PAData) == 'undefined' ? {} : PAData;
    PAData['chart'] = <?php echo json_encode($chartData); ?>;
    var PAFontAwesomeCdns = <?php echo json_encode($cdns); ?>;
    var PAKeywords = {
        'PA_INSTALLING' : '<?php echo helper::_('PA_INSTALLING') ?>',
    };
    var PAInstallData = <?php echo json_encode($installData); ?>;
</script>

<?php $this->loadView('footer'); ?>