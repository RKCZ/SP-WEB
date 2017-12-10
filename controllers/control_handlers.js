$(document).ready(function(){
    $(".show-login").click(function(){
        $("#registration").hide();
        $("#login").show();
        $("#modal").show();
    });
});

$(document).ready(function(){
    $(".show-reg").click(function(){
        $("#login").hide();
        $("#registration").show();
        $("#modal").show();
    });
});

$(document).ready(function(){
    $(".hide-modal").click(function(){
        $("#modal").hide();
    });
});