<?php
require_once(__DIR__ . '/../../php/dbfuncs.php');
$activeStudentCount = GetActiveStudentCount();
$pendingUserCount = GetPendingUserCount();
$inactiveStudentCount = GetInactiveStudentCount();
?>
<div class="dashboard-cards">
    <div class="card">
        <h2>Students<i></i></h2>
        <div class="stats">
            <div>
                <h3><?php echo $activeStudentCount; ?></h3>
                <hr style="--clr:#0f0;">
                <span>Active</span>
            </div>
            <div>
                <h3><?php echo $pendingUserCount; ?></h3>
                <hr style="--clr:#f90;">
                <span>Pending</span>
            </div>
            <div>
                <h3><?php echo $inactiveStudentCount; ?></h3>
                <hr style="--clr:#f00;">
                <span>Disabled</span>
            </div>
        </div>
        <div class="ratio-bar" style="
            --value1:<?php echo $activeStudentCount; ?>;
            --value2:<?php echo $pendingUserCount; ?>;
            --value3:<?php echo $inactiveStudentCount; ?>;
        ">
            <i style="background-color:#0f0;"></i>
            <i style="background-color:#f90;"></i>
            <i style="background-color:#f00;"></i>
        </div>
    </div>
    <div class="card">
        <h2>Recent student</h2>
        <?php if($pendingUserCount > 0) { ?>
            <div class="user-row">
                <div class="details">
                    <h4>Smith, John</h4>
                    <!-- <span>WS123456</span> -->
                </div>
                <input type="text" maxlength="16" placeholder="Student number">
                <div class="user-row-btns">
                    <button class="primary">Accept</button>
                    <button class="to-error">Decline</button>
                </div>
            </div>
        <?php } else { ?>
            <h3>N/A</h3>
        <?php }?>
    </div>
    <div class="card">
        <h2>Demo<i></i></h2>
        <h3>N/A</h3>
        <div class="loader-bar"><i></i></div>
    </div>
    <div class="card">
        <h2>Quiz results<i></i></h2>
        <div class="bar-chart bar-chart-align-right">
            <div class="bar-chart-graph">
                <div class="bar-chart-bar" style="--value:100;"><i></i></div>
                <div class="bar-chart-bar" style="--value:60;"><i></i></div>
                <div class="bar-chart-bar" style="--value:40;"><i></i></div>
                <div class="bar-chart-bar" style="--value:80;"><i></i></div>
                <div class="bar-chart-bar" style="--value:20;"><i></i></div>
                <div class="bar-chart-bar" style="--value:60;"><i></i></div>
                <div class="bar-chart-bar" style="--value:40;"><i></i></div>
                <div class="bar-chart-bar" style="--value:80;"><i></i></div>
                <div class="bar-chart-bar" style="--value:20;"><i></i></div>
                <div class="bar-chart-bar" style="--value:60;"><i></i></div>
                <div class="bar-chart-bar" style="--value:40;"><i></i></div>
                <div class="bar-chart-bar" style="--value:80;"><i></i></div>
                <div class="bar-chart-bar" style="--value:20;"><i></i></div>
                <div class="bar-chart-bar" style="--value:60;"><i></i></div>
                <div class="bar-chart-bar" style="--value:40;"><i></i></div>
                <div class="bar-chart-bar" style="--value:80;"><i></i></div>
            </div>
            <div class="bar-chart-values">
                <span>100%</span>
                <span>75%</span>
                <span>50%</span>
                <span>25%</span>
                <span>0%</span>
            </div>
        </div>
    </div>
    <div class="card">

    </div>
</div>