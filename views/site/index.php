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
            <div class="col-lg-3">
                <a class="btn btn-primary">Upload MyAccount</a>
                <a class="btn btn-info">Download MyAccount</a>
            </div>
            <div class="col-lg-3">
                <a class="btn btn-primary">Upload IOS</a>
                <a class="btn btn-info">Download IOS</a>
            </div>
            <div class="col-lg-3">
                <a class="btn btn-primary">Upload Portal</a>
                <a class="btn btn-info">Download Portal</a>
            </div>
            <div class="col-lg-3">
                <a class="btn btn-primary">Upload Android</a>
                <a class="btn btn-info">Download Android</a>
            </div>
        </div>
        <br /><br />
        <div class="row">
            <div class="col-lg-1 col-centered">
              <a class="btn btn-warning" href="/transifex/projects">Show Transifex Projects</a>
            </div>
        </div>

    </div>
</div>
