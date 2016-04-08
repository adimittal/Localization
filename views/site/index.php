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
            echo <<<HT
              <br /><br />
              <div class="col-lg-4">
                <p><a class="btn btn-primary" href="/README.php?project=myaccount&host=http://hercules.dev.itsoninc.com:55578">Readme for Curl calls</a></p>
                <p><a class="btn btn-primary" href="/translation/upload">Upload To Saas-Localization</a></p>
                <p><a class="btn btn-info" href="/translation/download">Download from Saas-Localization</a></p>
            </div>
HT;
              $projects = ['portal', 'myaccount', 'iosliteclient', 'temmandroid', 'ioclient'];
              foreach($projects as $p) {
                echo <<<HT
                <div class="col-lg-3">
                <p><a class="btn btn-primary" href="/translation/uploadtotransifex?project=$p">Upload $p To Transifex</a></p>
                <p><a class="btn btn-info" href="/translation/downloadfromtransifex?project=$p">Download $p From Transifex</a></p>
                <p><a class="btn btn-warning" href="/translation/projectdetails?project=$p">Show $p Details</a></p>
            </div>
HT;
              }
              
            ?>
        </div>
    </div>
</div>
