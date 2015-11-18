(function($){
  $(function(){
    // Materialize
    $('.button-collapse').sideNav();
    $('.dropdown-button').dropdown({ hover: false });
    if($('#modal-messages').size()){
      $('#modal-messages').openModal();
    }

    // Moment
    moment.locale('en');
    var displayDates = function(){
      $('.moment-date').each(function(){
        var date = $(this).attr("data-datetime");
        $(this).text(moment.utc(date, 'YYYY-MM-DD HH:mm:ss').local().calendar());
      });
    }
    setInterval( displayDates() , 1000);
  });
})(jQuery);
