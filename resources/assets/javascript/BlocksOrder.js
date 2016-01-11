$(function  () {

  var options = {
    group: 'order',
    containerSelector: '.blocks-container',
    itemSelector: '.list-row-wrapper',
    draggedClass: "block-dragged",
    bodyClass: "block-dragging",
    nested: true,
    onDrop: function ($item, container, _super, event) {
      $item.removeClass(container.group.options.draggedClass).removeAttr("style");
      $("body").removeClass(container.group.options.bodyClass);
      updateFields();
    }
  };

  // doing some multiple sorting because nesting is not working good without this
  // works for 4 levels
  $(".blocks-container .blocks-container .blocks-container .blocks-container").sortable(options);
  $(".blocks-container .blocks-container .blocks-container").sortable(options);
  $(".blocks-container .blocks-container").sortable(options);
  $(".blocks-container").sortable(options);

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
