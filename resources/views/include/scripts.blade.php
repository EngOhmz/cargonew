<script src="{{ asset('assets/js/all.js') }}"></script>
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>
<script src="{{asset('assets/js/jautocalc.js')}}"></script>
<script src="{{asset('assets/js/select2.min.js')}}"></script>

<script>
    $(document).ready(function(){
   /*
                * Multiple drop down select
                */
$('.m-b').select2({ width: '100%', });
 

   
    });
   </script>
<!-- Stack array for including inline js or scripts -->
@stack('plugin-scripts')

@stack('custom-scripts')
