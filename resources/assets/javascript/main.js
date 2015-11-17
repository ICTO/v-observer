(function($){
  $(function(){
    $('.button-collapse').sideNav();
    $('.dropdown-button').dropdown({ hover: false });
    if($('#modal-messages').size()){
      $('#modal-messages').openModal();
    }
  });
})(jQuery);
