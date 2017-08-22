<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="robots" content="noindex,nofollow" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{asset('ksl-stat/css/style_ip.css')}}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

</head>
<body id="statistics-enter">

<?php
use yii\helpers\Html;
use Klisl\Statistics\AlertWidget;
?>


<?= AlertWidget::widget() ?>



        <div class="hentry group">

            <h3>Статистика посещений</h3>
            <?= Html::beginForm(['forms'], 'post', ['class'=>'form-horizontal']) ?>

            <div class="form-group">
                <label for="Ввод пароля" class="control-label">Ввод пароля</label>
                <input name="password" type="text">
            </div>

            <input name="enter" type="hidden" value="1">
            <button class="button-reset" type="submit">Войти</button>

            <?= Html::endForm() ?>


        </div>




</body>
</html>