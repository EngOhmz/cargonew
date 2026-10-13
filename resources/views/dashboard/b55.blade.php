<script>
 $(document).ready(function() {
    
       $(document).on('change', '.item_quantity', function() {
            var id = $(this).val();

    console.log(id);
            $.ajax({
                url: '{{url("management/findNameItem2")}}',
                type: "GET",
                data: {
                    id: id,
                },
                dataType: "json",
                success: function(data) {
                  console.log(data);
                 $("#save").attr("disabled", false);
                 if (data != '') {
               $("#save").attr("disabled", true);
    } else {
      
    }
                
           
                }
    
            });
    
        });
    
    
    
    });
               

  
</script>