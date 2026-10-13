<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="formModal" >Student Invoice</h2>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
       
<div class="modal-body" id="printTable">
                   
<div class="row">

                <div class="table-responsive" >
                    <table class="table table-striped " id="table-1">
                       
                        <?php
                          if (!empty($schools)):
                        ?>
                         <tr>
                            <th>Full Name</th>
                            <td><strong><?php echo $student->student_name; ?></strong></td>
                        </tr>
                          <tr>
                            <th>School Level</th>
                            <td><strong><?php echo $student->level; ?></strong></td>
                        </tr>
                           <tr>
                            <th>Class</th>
                            <td><strong><?php echo $student->class; ?></strong></td>
                        </tr>
<!--
                         <tr>
                            <th>School Fee Type</th>
                            <td><strong><?php echo $schools->feeType; ?></strong></td>
                        </tr>
-->
                         <tr>
                            <th>School Fee Price</th>
                            <td><strong><?php echo number_format($schools->price,2); ?></strong></td>
                        </tr>
                        
                        <?php else: ?>

                        <tr>Currently there is no school fees created</tr>   

                        <?php endif; ?>
                        

                    </table>
                </div>
            </div>
            
        </div>

        <div class="modal-footer bg-whitesmoke br">
         <a class="btn btn-info" href="#null"  onclick="printContent('printTable')">Print</a>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      
    </div>
</div>

<script type="text/javascript">

    function printContent(id){
        str=document.getElementById(id).innerHTML
        newwin=window.open('','printwin','left=100,top=100,width=400,height=400')
        newwin.document.write('<HTML><HEAD> <link rel=\"stylesheet\" type=\"text/css\" href=\"CSS/style.css\"/>')
        newwin.document.write('<TITLE>Print Page</TITLE>\n')
        newwin.document.write('<script>\n')
        newwin.document.write('function chkstate(){\n')
        newwin.document.write('if(document.readyState=="complete"){\n')
        newwin.document.write('window.close()\n')
        newwin.document.write('}\n')
        newwin.document.write('else{\n')
        newwin.document.write('setTimeout("chkstate()",2000)\n')
        newwin.document.write('}\n')
        newwin.document.write('}\n')
        newwin.document.write('function print_win(){\n')
        newwin.document.write('window.print();\n')
        newwin.document.write('chkstate();\n')
        newwin.document.write('}\n')
        newwin.document.write('<\/script>\n')
        newwin.document.write('</HEAD>\n')
        newwin.document.write('<BODY onload="print_win()">\n')
        newwin.document.write(str)
        newwin.document.write('</BODY>\n')
        newwin.document.write('</HTML>\n')
        newwin.document.close()
    }

</script>