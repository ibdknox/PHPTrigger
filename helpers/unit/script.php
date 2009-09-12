    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js"> </script>
    <script type="text/javascript">
        $(document).ready( function() {
            $("a.failToggle").click( function(event) {

                $(this).toggleClass("onlyFailed").toggleClass("allTests");
                $("ul.passed").toggle('fast');

                if( $(this).hasClass("allTests") ) {
                    $(this).text("Show all tests");
                } else {
                    $(this).text("Failed tests only");
                }

                return false;

            });

        });
    </script>
