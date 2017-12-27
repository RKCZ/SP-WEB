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

$(document).ready(function () {
    $(".accordion-btn").click(function () {
        $(this).parent().children("div").addClass("w3-hide");
        $(this).next("div").removeClass("w3-hide");
    });
});

$(document).ready(function () {
    $(".home").click(function () {
        $("#userContent").hide();
        $("#home").show();
    });
});

$(document).ready(function () {
    $(".userContent").click(function () {
        $("#home").hide();
        $("#userContent").show();
    });
});

$(document).ready(function(){
    $(".toggle-btn").click(function(){
        $(this).next().toggle();
    });
});