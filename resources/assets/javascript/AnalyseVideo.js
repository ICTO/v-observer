$(function(){
    // @TODO : use own namespace analysisVideo = {}

    // global vars
    var activePart = 0;
    var videoPosition = 0;
    var player = videojs('player');

    // copy timeline into player
    var timeline_html = $('#chapters-wrapper-copy').html();
    //$('#chapters-wrapper-copy').remove();
    $(".vjs-progress-control .vjs-progress-holder").prepend(timeline_html);

    // set the active timeline
    var setActiveTimeline = function(part){
        $(".chapters-wrapper .chapter").removeClass("active");
        var apart = $(".chapters-wrapper .chapter").get(part);
        $(apart).addClass("active");
    }

    // set the done part timeline
    var setPartDone = function(part){
        var dpart = $(".part").get(part);
        if(!$(dpart).hasClass("done")){
            var p = $(".chapters-wrapper .chapter").get(part);
            $(p).addClass("done");
            $(dpart).addClass("done");
            if(part+1 < $("#chapters-wrapper-copy .chapters-wrapper .chapter").length ){
                startPart(part+1);
            }
            checkAllDone();
        }
    }

    // set the active part
    var startPart = function(part){
        activePart = part;
        var item = $(".chapters-wrapper .chapter").get(part);
        player.currentTime($(item).attr("data-start"));
        showInterface(part);
    }

    // loop a part
    var loopPart = function(part){
        var active = $(".chapters-wrapper .chapter").get(part);
        if($(active).attr("data-start") > videoPosition || videoPosition > $(active).attr("data-end")){
            startPart(part);
        }
    }

    // set the active questionnaire
    var showQuestionnaire = function(part){
        if($(".part:visible").not($("#part-"+part)).length){
            if($(".part:visible").length){
                $(".part:visible").hide();
                $("#part-"+part).show();
            }
        } else {
            $("#part-"+part).fadeIn("fast");
        }
    }

    // get the part based on the position
    var getPart = function(position){
        $(".chapters-wrapper .chapter").each(function(index, value){
            if($(this).attr("data-start") <= position && position < $(this).attr("data-end")){
                return index;
            }
        })
    }

    // show interface of part
    var showInterface = function(part){
        setActiveTimeline(part);
        showQuestionnaire(part);
    }

    // check for parts that are done
    var checkPartsDone = function(){
        $(".part").each(function(index){
            var part = true;
            $(this).find(".question").each(function(){
                if(!$(this).hasClass("has-answer")) {
                    part = false;
                }
            });
            if(part){
                setPartDone(index);
            }
        });
    }

    var checkAllDone = function(){
        var done = true;
        $(".part").each(function(index){
            if(!$(this).hasClass("done")) {
                done = false;
            }
        });
        if(done){
            if(urlFinished){
                window.location = urlFinished;
            }
        }
    }

    // get the position of the video
    player.on('timeupdate', function() {
        videoPosition = player.currentTime()
        loopPart(activePart);
    });

    // change the active part when clicked on a chapter
    $('.chapters-wrapper .chapter').click(function(){
        part = $(this).attr('data-part');
        if(activePart != part){
            startPart(part);
        }
    });

    // change the active part when keypress
    $("body").keydown(function(e) {
      if(e.keyCode == 37) { // left
        if(activePart != 0 ){
            var part = parseInt(activePart) - 1;
            startPart(part);
        }
      }
      else if(e.keyCode == 39) { // right
          if(activePart < $(".part").length - 1 ){
              var part = parseInt(activePart) + 1;
              startPart(part);
          }
      }
    });

    // set the height of the full scroll
    var setFullScroll = function (){
        var width = $(window).width();
        if(width >= 993){
            $(".full-scroll").css("max-height",$(window).height()-40);
        } else {
            $(".full-scroll").css("max-height","auto");
        }
    }
    setFullScroll();
    $( window ).resize(function() {
      setFullScroll();
    });

    // set the initial part to 0
    startPart(0);

    // check for parts that are done
    checkPartsDone()

    // make all form post with ajax
    var options = {
        dataType: 'json',
        success: requestFinished
    };
    function requestFinished (data, statusText){
        $("#question-"+data.answer.part+"-"+data.answer.block_id).addClass("has-answer");
        setTimeout(checkPartsDone(), 1000); // first show the finished icon and then navigate
    }

    $('input[name="answer"]').change(function(){
        $(this).parent().ajaxSubmit(options);
    });

});
