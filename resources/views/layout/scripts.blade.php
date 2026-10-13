<script src="{{asset('global_assets/js/main/jquery.min.js')}}"></script>
<script src="{{asset('global_assets/js/main/bootstrap.bundle.min.js')}}"></script>
	<!-- /core JS files -->


	<!-- Theme JS files -->
	<script src="{{asset('global_assets/js/plugins/visualization/d3/d3.min.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/visualization/d3/d3_tooltip.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/ui/moment/moment.min.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/pickers/daterangepicker.js')}}"></script>

	<script src="{{asset('assets2/js/app.js')}}"></script>
	<script src="{{asset('global_assets/js/demo_pages/dashboard.js')}}"></script>
	<script src="{{asset('global_assets/js/demo_charts/pages/dashboard/light/streamgraph.js')}}"></script>
	<script src="{{asset('global_assets/js/demo_charts/pages/dashboard/light/sparklines.js')}}"></script>
	<script src="{{asset('global_assets/js/demo_charts/pages/dashboard/light/lines.js')}}"></script>
	<script src="{{asset('global_assets/js/demo_charts/pages/dashboard/light/areas.js')}}"></script>
	<script src="{{asset('global_assets/js/demo_charts/pages/dashboard/light/donuts.js')}}"></script>
	<script src="{{asset('global_assets/js/demo_charts/pages/dashboard/light/bars.js')}}"></script>
	<script src="{{asset('global_assets/js/demo_charts/pages/dashboard/light/progress.js')}}"></script>
	<script src="{{asset('global_assets/js/demo_charts/pages/dashboard/light/heatmaps.js')}}"></script>
	<script src="{{asset('global_assets/js/demo_charts/pages/dashboard/light/pies.js')}}"></script>
	<script src="{{asset('global_assets/js/demo_charts/pages/dashboard/light/bullets.js')}}"></script>

		<!-- Theme JS files -->
	<script src="{{asset('global_assets/js/plugins/tables/datatables/datatables.min.js')}}"></script>

	<script src="{{asset('global_assets/js/demo_pages/datatables_basic.js')}}"></script>

    
   
<!-- /theme JS files -->
	<!-- /theme JS files -->

<script type="text/javascript" src="{{asset('assets2/js/downloadFile.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.0.0-rc.4/dist/js/tom-select.complete.min.js"></script>



<script src="{{asset('global_assets/js/demo_pages/components_modals.js')}}"></script>

   <!-- new js -->


   <!-- end new js -->


   <script src="https://logistic.ema.co.tz/assets/bundles/jquery-ui/jquery-ui.min.js"></script>
  <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script src="{{ asset('assets/js/all.js') }}"></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <script src="{{asset('assets/js/jautocalc.js')}}"></script>

    <script src="{{asset('assets/jQuery/jQuery.print.js')}}"></script>

	{!! Html::script('assets/js/loader.js') !!}
    {!! Html::script('plugins/table/datatable/datatables.js') !!}
    <!--  The following JS library files are loaded to use Copy CSV Excel Print Options-->
    {!! Html::script('plugins/table/datatable/button-ext/dataTables.buttons.min.js') !!}
    {!! Html::script('plugins/table/datatable/button-ext/jszip.min.js') !!}
    {!! Html::script('plugins/table/datatable/button-ext/buttons.html5.min.js') !!}
    {!! Html::script('plugins/table/datatable/button-ext/buttons.print.min.js') !!}
    <!-- The following JS library files are loaded to use PDF Options-->
    {!! Html::script('plugins/table/datatable/button-ext/pdfmake.min.js') !!}
    {!! Html::script('plugins/table/datatable/button-ext/vfs_fonts.js') !!}
   
    <script src="{{asset('assets/js/select2.min.js')}}"></script>
   
    <script>
        $(document).ready(function(){
            /*
                         * Multiple drop down select
                         */
            $('.m-b').select2({ width: '100%', });



        });
    </script>

<script type="text/javascript" >
    function CheckAll(obj){
        var row = obj.parentNode.parentNode;
        var inputs = row.getElementsByTagName("input");

        for(var i = 0; i < inputs.length; i++){
            if(inputs[i].type == "checkbox") {
                inputs[i].checked = obj.checked;
            }

        }
    }

    </script>

    <script>

$(document).ready(function(){

        $('#select-all').click( function()  {

    $('input[type="checkbox"]').prop('checked', this.checked);

})

});

</script>

@yield('scripts')

<!-- Session Timeout Script - Logout after 10 minutes of inactivity -->
@auth
<script>
(function() {
    let inactivityTimer;
    const INACTIVITY_TIMEOUT = 10 * 60 * 1000; // 10 minutes in milliseconds
    
    // Events that indicate user activity
    const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
    
    function logoutUser() {
        // Check if SweetAlert2 is available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Session Expired',
                text: 'Your session has expired due to inactivity. You will be logged out.',
                icon: 'warning',
                confirmButtonText: 'OK',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(function() {
                document.getElementById('logout-form').submit();
            });
        } else {
            // Fallback to standard alert if SweetAlert2 is not available
            if (confirm('Your session has expired due to inactivity. You will be logged out.')) {
                document.getElementById('logout-form').submit();
            } else {
                document.getElementById('logout-form').submit();
            }
        }
    }
    
    function resetTimer() {
        clearTimeout(inactivityTimer);
        
        // Set timer to logout after inactivity
        inactivityTimer = setTimeout(logoutUser, INACTIVITY_TIMEOUT);
    }
    
    // Reset timer on any user activity
    activityEvents.forEach(function(event) {
        document.addEventListener(event, resetTimer, true);
    });
    
    // Initialize timer when page loads
    resetTimer();
    
    // Also reset on page visibility change (when user switches tabs)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            resetTimer();
        }
    });
    
    // Reset timer when window gains focus
    window.addEventListener('focus', resetTimer);
})();
</script>
@endauth

@stack('plugin-scripts')
@stack('custom-scripts')