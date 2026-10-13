<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="formModal" >Taarifa za mteja</h2>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
       
<div class="modal-body" id="printTable">
                    <div class="">                            
                        <p class="form-control-static" style="text-align:center;"><strong>Jina la Mteja  : </strong><?php echo $data2->mteja; ?></p>
                    </div>
                    <div class="">
                            <p class="form-control-static" style="text-align:center;"><strong>Jina la Mpokeaji  : </strong><?php echo $data2->mpokeaji; ?></p>    
                    </div>
 <hr><br>                   
<div class="row">
<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="card" >
                <div class="card-header">
                    <h6>Taarifa za mteja na mizigo yake</h6>
                </div>
                <div class="card-body table-responsive" >
                    <table class="table table-bordered table-striped bg-light">
                       
                        <?php
                          if (!empty($data)):foreach ($data as $row):
                        ?>
                         <tr>
                            <th>Jina la Mzigo:</th>
                            <td><strong><?php echo $row->name; ?></strong></td>
                        </tr>
                         <tr>
                            <th>Kutoka:</th>
                            <td><strong><?php echo $row->mzigo_unapotoka; ?></strong></td>
                        </tr>
                        <tr>
                            <th>Kwenda:</th>
                            <td><strong><?php echo $row->mzigo_unapokwenda; ?></strong></td>
                        </tr>
                         <tr>
                            <th>Idadi ya Mizigo:</th>
                            <td><strong><?php echo $row->idadi; ?></strong></td>
                        </tr>
                        <tr>
                            <th>Kiasi cha Tozo cha kila Mzigo:</th>
                            <td><strong><?php echo $row->bei; ?></strong></td>
                        </tr>
                         <tr>
                            <th>Kiasi cha Tozo kilichopokelewa cha  Mizigo:</th>
                            <td><strong><?php echo $row->ela_iliyopokelewa; ?></strong></td>
                        </tr>
                        <tr>
                            <th>Jumla ya fedha ya Mzigo kinachotakiwa:</th>
                            <td><strong><?php echo $row->jumla; ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>

                        <tr>Currently there is no school fees created</tr>   

                        <?php endif; ?>
                        

                    </table>
                </div>
            </div>
            </div><!-- ********************Allowance End ******************-->

<!-- ********************Deduction End  ******************-->

         

         
           

</div>
        </div>
        <div class="modal-footer">
        <a class="btn btn-warning" href="#null"  onclick="printContent('printTable')">Print</a>
        </div>
        <div class="modal-footer bg-whitesmoke br">
         
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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