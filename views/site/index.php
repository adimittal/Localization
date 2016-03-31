<?php
/* @var $this yii\web\View */

$this->title = 'Saas Localization';
?>
<style>
    .col-centered{
        float: none;
        margin: 0 auto;
    }
</style>
<div class="site-index">

    <div class="jumbotron">
        <h1>Saas Localization!</h1>

        <p class="lead">Use this for translations and other localization work</p>
    </div>

    <div class="body-content">

        <div class="row">
            
            <?php
            
              $projects = ['portal', 'myaccount', 'iosliteclient', 'temmandroid', 'ioclient'];
              foreach($projects as $p) {
                echo <<<HT
                <div class="col-lg-2">
                <a class="btn btn-primary" href="/translation/upload?project=$p">Upload $p To Transifex</a>
                <a class="btn btn-info" href="/translation/download?project=$p">Download $p From Transifex</a>
                <a class="btn btn-info" href="/site/upload?project=$p">Upload $p To Saas-Localization</a>
                <a class="btn btn-warning" href="/translation/projectdetails?project=$p">Show $p Details</a>
            </div>
HT;
              }
            ?>
        </div>
    </div>
</div>
