/**
 * Created by majid on 4/30/17.
 */
$(document).ready(function(){
    $(".navbar-toggle").on("click", function () {
        $(this).toggleClass("active");
    });

    $(document).click(function (event) {
        var clickover = $(event.target);
        var _opened = $(".navbar-collapse:visible").hasClass("in");
        if (_opened === true && !clickover.hasClass("navbar-toggle")) {
            $(".navbar-toggle:visible").click();
        }
    });
});