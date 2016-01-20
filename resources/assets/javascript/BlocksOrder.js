$(function  () {

  $(".blocks-container").each( function(){
    var sort = Sortable.create(this, {
      group: "blocks",
      animation: 150, // ms, animation speed moving items when sorting, `0` — without animation
      draggable: ".list-row-wrapper", // Specifies which items inside the element should be sortable
      onSort: function (evt/**Event*/){
         updateFields();
      }
    });
  });


  // update input fields when something changed.
  updateFields = function(){
    $(".blocks-container").each(function(){
      var parent_id = $(this).parent().parent().parent().parent().attr("data-block-id");
      $(this).children().each(function(i){
        var block_id = $(this).attr("data-block-id");
        var order = i;
        $('input[name="blocks['+block_id+'][parent_id]"]').val(parent_id);
        $('input[name="blocks['+block_id+'][order]"]').val(order);
      });
    })
  }
});
