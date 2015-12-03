$(function(){
  /** Question multiple choice **/
  $(".add-option-action").click( function(){
    var option_html = $("#template-question-option").html();
    option_html = option_html.replace(/__key__/g, $(".options-wrapper .option-wrapper").length);
    $(".options-wrapper").append(option_html);
  });
});
