$(function(){
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
    var setDoneTimeline = function(part){
        var dpart = $(".chapters-wrapper .chapter").get(part);
        $(dpart).addClass("done");
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

    // set the active questionaire
    var showQuestionaire = function(part){
        $(".part").hide();
        $("#part-"+part).show();
    }

    // get the part based on the position
    var getPart = function(position){
        $(".chapters-wrapper .chapter").each(function(index, value){
            if($(this).attr("data-start") <= position && position < $(this).attr("data-end")){
                return index;
            }
        })
        console.log("invalid");
    }

    // show interface of part
    var showInterface = function(part){
        setActiveTimeline(part);
        showQuestionaire(part);
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

});
