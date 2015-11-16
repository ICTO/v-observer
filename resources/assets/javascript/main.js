(function($){
  $(function(){
    $('.button-collapse').sideNav();
    if($('#modal-errors').size()){
      $('#modal-errors').openModal();
    }
  });
})(jQuery);
