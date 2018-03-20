


jQuery(document).ready(function() {
    var dt = window.location.href.match(/\d{8,8}/);
    if (dt) {
        dt = dt.toString();
    } else {
        var dd = new Date();
        var dt = dd.getFullYear() + dd.getMonth() + dd.getDate();
        dt = dt.toString();
    }
    var lentt = lent(dt.substr(6, 2), dt.substr(4, 2) - 1, dt.substr(0, 4));
    if (lentt) {
        jQuery('.post').text(lentt[0]);
        jQuery('.post').attr('title', 'Монастырский устав: ' + lentt[1]);
        jQuery('.post').css('text-decoration', 'underline');
    }




    jQuery('.tx-orthodox').ajaxStart(function() {
        jQuery(this).hide();
        jQuery(this).after('<div style="text-align:center" id="spinner"><img src="/typo3conf/ext/orthodox/Resources/Public/Icons/spinner.gif"><br/>Загрузка</div>');
        SI.ClearChildren.initialize();
    }).ajaxStop(function() {
        jQuery(this).show();
        jQuery('#spinner').remove();
        //jQuery('.media a').media();
        SI.ClearChildren.initialize();
    });


    function show_all(event) {
        jQuery('#all').hide();
        jQuery('.readings_container').empty();
        jQuery('a.reading').each(function() {
            var href = jQuery(this).attr('href');
            var text = jQuery(this).text();
            jQuery('.readings_container').append(jQuery("<h3>").text(text));
            jQuery('.readings_container').append(jQuery("<div>").load(href + '?type=555 #output', function() {}));

        });
    }


    if (window.location.hash == '#all') {
        show_all();
    }

    jQuery('#all').click(function(event) {
        event.preventDefault();
        window.location.hash = 'all';
        show_all();
    });




    jQuery('.left').prepend('<div class=\"datepicker\"></div>');
    jQuery('.datepicker').datepicker({
            defaultDate: d,
            dayName: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
            dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
            monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            prevText: 'Предыдущий месяц',
            nextText: 'Следующий месяц',
            dateFormat: 'yymmdd',
            changeMonth: true,
            changeYear: true,
            showOtherMonths: true,
            selectOtherMonths: true,
            firstDay: 1,
            beforeShowDay: highlightDays,
            onSelect: function(dateText, inst) {
                var date = dateText.substr(6, 2) + '.' + dateText.substr(4, 2) + '.' + dateText.substr(0, 4);
                var url = 'https://c.psmb.ru/pravoslavnyi-kalendar/date/' + dateText;
                document.title = date + ' - Православный календарь';
                if (window.location.href.indexOf('debug') !== -1) {
                    if (history.pushState) {
                        history.pushState(null, date + ' - Православный календарь', url + '?debug=1');
                    }
                    jQuery('.tx-orthodox').load(url + '/?type=555&debug=1', function() {
                        var lentt = lent(dateText.substr(6, 2), dateText.substr(4, 2) - 1, dateText.substr(0, 4));
                        if (lentt) {
                            jQuery('.post').text(lentt[0]);
                            jQuery('.post').attr('title', 'Монастырский устав: ' + lentt[1]);
                            jQuery('.post').css('text-decoration', 'underline');
                        }

                        jQuery('#all').click(function(event) {
                            event.preventDefault();
                            window.location.hash = 'all';
                            show_all();
                        });
                    });
                } else {
                    if (history.pushState) {
                        history.pushState(null, date + ' - Православный календарь', url);
                    }
                    jQuery('.tx-orthodox').load(url + '/?type=555', function() {
                        var lentt = lent(dateText.substr(6, 2), dateText.substr(4, 2) - 1, dateText.substr(0, 4));
                        if (lentt) {
                            jQuery('.post').text(lentt[0]);
                            jQuery('.post').attr('title', 'Монастырский устав: ' + lentt[1]);
                            jQuery('.post').css('text-decoration', 'underline');
                        }

                        jQuery('#all').click(function(event) {
                            event.preventDefault();
                            window.location.hash = 'all';
                            show_all();
                        });
                    });
                }
            }
    });
});

