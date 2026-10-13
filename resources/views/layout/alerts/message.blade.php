<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

                                @if(!empty(Session::get('success')))

                                <div class="alert alert-success alert-dismissible show fade" id="success-alert">
                                   <div class="alert-body">
                                  
                                            {{Session::get('success')}}
                                    </div>
                                   </div>


                                


                                @elseif(!empty(Session::get('error')))
                            
                                   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


                                   <div class="alert alert-danger" id="success-alert">
                                              <center>Oooops!!  {{Session::get('error')}} </center>
                                   </div>


                                 @endif
                                 <script>
                                $(".alert").delay(6000).slideUp(200, function() {
                                $(this).alert(close);
                                });
                                </script>