<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex,nofollow" />
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <link href="{{asset('ksl-stat/css/style_ip.css')}}" rel="stylesheet">

</head>
<body>
    <h3 class="stat_center">Статистика посещений по IP</h3>
    <div id="stat_ip">


        <?php if (isset($status)) : ?>
            <div class="alert alert-success">
                <?php echo $status; ?>
            </div>
        <?php endif ?>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif ?>



        <?php echo $this->render('table',[
            'count_ip'=> $count_ip,
            'stat_ip' => $stat_ip,
        ]); ?>




		{!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}
		{{ Form::hidden('reset', true)}}
		{!! Form::button('Сбросить фильтры',['class'=>'button-reset','type'=>'submit']) !!}
		{!! Form::close() !!}
		<hr>



		<h3>Сформировать за указанную дату</h3>
		{!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}
		{!! Form::text('date_ip', '',['class'=>'date_ip']) !!}

		{!! Form::button('Отфильтровать',['class'=>'button-reset','type'=>'submit']) !!}
		{!! Form::close() !!}
	    <hr>




		<h3>Сформировать за выбранный период </h3>
		{!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}

		<div class="form-group">
            {{ Form::label('Начало', null, ['class' => 'control-label']) }}
            {!! Form::text('start_time', '',['class'=>'date_ip']) !!}
		</div>

        <div class="form-group">
            {{ Form::label('Конец', null, ['class' => 'control-label']) }}
            {!! Form::text('stop_time', '',['class'=>'date_ip']) !!}
        </div>

        {{ Form::hidden('period', true)}}
		{!! Form::button('Отфильтровать',['class'=>'button-reset','type'=>'submit']) !!}
		{!! Form::close() !!}
		<hr>



        <h3>Сформировать по определенному IP</h3>
        {!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}

        <div class="form-group">
            {{ Form::label('IP', null, ['class' => 'control-label']) }}
            {!! Form::text('ip', null, ['placeholder' => '127.0.0.1']) !!}
        </div>

        {{ Form::hidden('search_ip', true)}}
        {!! Form::button('Отфильтровать',['class'=>'button-reset','type'=>'submit']) !!}
        {!! Form::close() !!}
        <hr>



        <h3>Черный список IP</h3>
        <p>Под черным списком понимаются IP, по которым не нужна статистика, например IP администратора сайта.
        <br>По данным IP статистика не будет сохраняться с момента добавления в черный список.</p>

        <table>
            <tr class='tr_small'>

            <h4>Сейчас в черном списке:</h4>
            @foreach($black_list as $key=>$value)
                <td> {{$value['ip']}}
                @if(!empty($value['comment']))
                    - {{$value['comment']}}
                @endif
                </td>
            @endforeach

            @if(count($black_list)==0)
                <td>Черный список пуст.</td>
            @endif

            </tr>
        </table>
        <br>




        {!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}
        <div class="form-group">
            {{ Form::label('IP', null, ['class' => 'control-label']) }}
            {!! Form::text('ip', null, ['placeholder' => '127.0.0.1']) !!}
        </div>
        <div class="form-group">
            {{ Form::label('Комментарий', null, ['class' => 'control-label']) }}
            {!! Form::text('comment') !!}
        </div>

        {{ Form::hidden('add_black_list', true)}}
        {!! Form::button('Добавить в черный список',['type'=>'submit']) !!}
        {!! Form::close() !!}
        <br>




        {!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}
        <div class="form-group">
            {{ Form::label('IP', null, ['class' => 'control-label']) }}
            {!! Form::text('ip', null, ['placeholder' => '127.0.0.1']) !!}
        </div>

        {{ Form::hidden('del_black_list', true)}}
        {!! Form::button('Удалить из черного списка',['type'=>'submit']) !!}
        {!! Form::close() !!}
        <hr>




        <h3>Очистка базы данных <span class="font_min">(старше 90 дней)</span></h3>

        {!! Form::open(['url'=>route('forms'), 'class'=>'form-horizontal','method' => 'POST']) !!}
        {{ Form::hidden('del_old', true)}}
        {!! Form::button('Удалить старые данные',['type'=>'submit']) !!}
        {!! Form::close() !!}
        <br>



        <script type="text/javascript">

            $.datepicker.regional['ru'] = {
                closeText: 'Закрыть',
                prevText: '&#x3c;Пред',
                nextText: 'След&#x3e;',
                currentText: 'Сегодня',
                monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
                    'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
                monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
                    'Июл','Авг','Сен','Окт','Ноя','Дек'],
                dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
                dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
                dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
                dateFormat: 'dd.mm.yy',
                firstDay: 1,
                isRTL: false
            };
            $.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );


            $('.date_ip').datepicker({

                dateFormat: "dd-mm-yy", //формат даты
                minDate: "-1y", // выбор не более чем за последний год
                maxDate: "+0d" // максимальная дата выбора - сегодняшняя
            });

        </script>

        <div id="sub-footer">
            <div class="container">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="copyright">
                            <p class="text-center"><a href="http://klisl.com/" target="_blank"><b>&copy; KSL</b></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


</body>
</html>