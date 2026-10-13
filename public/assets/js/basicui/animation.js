(function($) {
    "use strict";
    $(function() {
        function testAnim(n) {
            $("#animateArea")
                .removeClass()
                .addClass(n + " animated")
                .one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
                    $(this).removeClass();
                });
        }
        $(document).ready(function () {
            $(".do-animation").on('click',function (n) {
                // alert('as');
                n.preventDefault(), testAnim($(".do-animation").val());
            }),
                $(".do-animation").on('change', function () {
                    testAnim($(this).val());
                });
        });
// $(".hola").click(function(){
//     alert("The paragraph was clicked.");
// });
    })
})(jQuery);
