(function($){
  $(function(){
    // Materialize
    $('.button-collapse').sideNav();
    $('.dropdown-button').dropdown({
      hover: false,
      constrain_width: false,
      belowOrigin: true
    });
    $('select').material_select();
    $('.message').each(function(){
      var type = $(this).attr("data-type");
      var message = $(this).attr("data-message");
      var n = noty({
          text: message,
          type: type,
          theme: 'relax',
          layout: 'topCenter'
      });
    });

    // Moment
    moment.locale('en-gb');
    var displayDates = function(){
      $('.moment-date').each(function(){
        var date = $(this).attr("data-datetime");
        $(this).text(moment.utc(date, 'YYYY-MM-DD HH:mm:ss').local().calendar());
      });
    }
    setInterval( displayDates() , 1000);

    // numeral
    numeral.language('nl-nl');
    $('.numeral').each(function(){
      var number = $(this).attr("data-number");
      $(this).text(numeral(number).format($(this).attr("data-format")));
    });

  });
})(jQuery);
