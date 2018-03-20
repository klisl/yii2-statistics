jQuery(document).ready(function($){


    $('#stat_ip').on('click', '#api', function(e){

        var ip = $(e.target).attr('href');

        if(ip === '127.0.0.1'){
            showStickyErrorToast(null);
            return false;
        }

        $.ajax({
            url:'http://api.sypexgeo.net/json/' + ip,
            // data: data,
            type: 'GET',
            datatype: 'JSON', //формат данных которые должен передать сервер

            success: function(data){
                if(data.country){
                    // alert(data)
                    showStickyErrorToast(data); //всплывающее окно со ссылкой
                    //Если ошибка
                } else {
                    showStickyErrorToast(null);
                    console.log(data);
                }
            },
            //Ошибка AJAX
            error: function(data){
                showStickyErrorToast(null);
                console.log(data);
            }
        });

        return false;
    })


    //Плагин всплывающего окна
    function showStickyErrorToast(data) {

        if(data){
            var html = "<p>Страна: " + data.country.name_ru + "</p>" +
                "<p>Регион: " + data.region.name_ru + "</p>" +
                "<p>Город: " + data.city.name_ru + "</p>";
        } else {
            var html = "<p>Информация недоступна.</p>";
        }

        $().toastmessage('showToast', {
            text     : html,
            sticky   : true,
            position : 'top-right',
            type     : 'notice',
            closeText: '',
            close    : function () {
                // console.log("toast is closed ...");
            }
        });
    }

});