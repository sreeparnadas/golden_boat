<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

<script src="jquery-3.3.1/jquery-ui.min.js"></script>
<script src="angularjs/angularjs_1.6.4_angular.min.js"></script>
<script src="angularjs/angularjs_1.6.4_angular-route.js"></script>
<script src="angularjs/autocomplete.js"></script>
<script src="angularjs/angular-ui-bootstrap/0.6.0/ui-bootstrap-tpls.js"></script>
<script src = "node_modules/moment/min/moment.min.js"></script>






<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script src="js/parallax.min.js"></script>
<script src="js/wow.min.js"></script>
<script src="js/jquery.easing.min.js"></script>
<script type="text/javascript" src="js/fliplightbox.min.js"></script>
<script src="js/functions.js"></script>




<script src="js/md5/md5_js.js"></script>

<script>
    wow = new WOW({}).init();
</script>


<script>
    // Get the modal
    var modal = document.getElementById('id01');

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<!--<script src="js/general_script.js"></script>-->
<script type="text/javascript">
    var data = [{a:1,b:10}, {a:2,b:20}, {a:1,b:30}];
    var res = alasql('SELECT a, SUM(b) AS b FROM ? GROUP BY a',[data]);
   
</script>


<script src="js/login_script.js"></script>

<script src="angularjs_controller/myAppController.js"></script>
<!--<script src="angularjs_controller/kolkataController.js"></script>-->
<script src="angularjs_controller/vendorController.js"></script>
<script src="angularjs_controller/customerController.js"></script>
<script src="angularjs_controller/productController.js"></script>
<script src="angularjs_controller/saleController.js"></script>
<script src="angularjs_controller/bill2Controller.js"></script>
<script src="angularjs_controller/loanController.js"></script>
<script src="js/jwerty.js"></script>
<script type="text/javascript">
    jwerty.key('f10', function () {
        window.location='#!/sale';
    });
    jwerty.key('f9', function () {
        window.location='#!/bill2';
    });

    jwerty.key('f8', function () {
        window.location='#!/loan';
    });


</script>



</body>
</html>