$(document).ready(function() {
    var options = {
        beforeSubmit:  uploadStarted,
        success: uploadFinished,
    };

    $("#submit_button").prop('disabled', true);
    $("#filefield").change(function(){
        $("#submit_button").prop('disabled', false);
    });

    function uploadStarted(formData, jqForm, options) {
        $("#submit_button").prop('disabled', true);
        $("#filefield").prop('disabled', true);
        startUploadLoader();
        return true;
    };

    function startUploadLoader(){
        var progress = setInterval(function() {
                $.ajax({
                  method: "GET",
                  url: "/video/"+video_id+"/upload_progress"
                }).done(function(data){
                    if(typeof data.loaders !== 'undefined' && data.loaders){
                        var loaders_html = "";
                        data.loaders.forEach(function(loader) {
                            loaders_html = loaders_html + '<div class="center-align grey-text">'+loader.name+'</div><div class="progress"><div class="determinate" style="width: '+loader.percentage+'%"></div></div>'
                        });
                        $("#loaders").html(loaders_html);
                    }
                    if(data.redirect){
                        window.location = data.redirect;
                    }
                });
        }, 1000);
    };

    function uploadFinished(){
        $.ajax({
          method: "GET",
          url: "/video/"+video_id+"/upload_finished"
        }).done(function(data){
            data = JSON.parse(data);
        });
    }

    $('#upload_form').ajaxForm(options);

    if(typeof start_progress !== 'undefined' && start_progress){
        startUploadLoader();
    }
});
