$(document).ready(function(){
    $(".show-login").click(function(){
        $(".w3-modal-content").children("div").hide();
        /*$("#registration").hide();
        $("#edit").hide();
        $("#create").hide();*/
        $("#login").show();
        $("#modal").show();
    });
});

$(document).ready(function(){
    $(".show-reg").click(function(){
        $(".w3-modal-content").children("div").hide();
        $("#registration").show();
        $("#modal").show();
    });
});

$(document).ready(function () {
    $(".edit_article").click(function () {
        $(".w3-modal-content").children("div").hide();
        CKEDITOR.instances['editor_edit'].setData($(this).data('abstract'));
        $(".articleId").val($(this).data('id'));
        $("#edit").show();
        $("#modal").show();
    });
});

$(document).ready(function () {
    $(".create_article").click(function () {
        $(".w3-modal-content").children("div").hide();
        $("#create").show();
        $("#modal").show();
    });
});

$(document).ready(function () {
    $(".assign-review").click(function () {
        $(".w3-modal-content").children("div").hide();
        $(".articleId").val($(this).data('id'));
        $("#assign").show();
        $("#modal").show();
    });
});

$(document).ready(function () {
    $(".review").click(function () {
        $(".w3-modal-content").children("div").hide();
        $(".articleId").val($(this).data('id'));
        $("#review").show();
        $("#modal").show();
    });
});

$(document).ready(function(){
    $(".hide-modal").click(function(){
        $("#modal").hide();
    });
});
/////////////////////////// End of Modal content handlers

$(document).ready(function () {
    $(".accordion-btn").click(function () {
        $(this).parent().children("div").addClass("w3-hide");
        $(this).next("div").removeClass("w3-hide");
    });
});

$(document).ready(function () {
    $(".home").click(function () {
        $("#userContent").hide();
        $("#users").hide();
        $("#home").show();
    });
});

$(document).ready(function () {
    $(".userContent").click(function () {
        $("#home").hide();
        $("#users").hide();
        $("#userContent").show();
    });
});

$(document).ready(function () {
    $(".users").click(function () {
        $("#home").hide();
        $("#userContent").hide();
        $("#users").show();
    });
});

$(document).ready(function(){
    $(".toggle-btn").click(function(){
        $(this).next().toggle();
    });
});
