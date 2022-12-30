<!DOCTYPE html>
<html>
<head>
	<title></title>
  <script src="<?php echo base_url(); ?>assets/jquery/main.js" > </script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">     
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.min.css"> 
    <script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script> 
</head>
<body>


 <div class="container">
       <div class="row">
            <div class="col-md-6" style="background-color: bisque;">
                        <div class="msg">
                            <div class="msg_title" style="display: inline-block;">
                               <center> <h2>Edit Here !</h2></center>
                            </div>
                             <div class="msg_title" style="color:red;display: inline-block;">
                              <h5 style="color:red;" id="result"></h5>
                            </div>
                        </div>

<form id="form" method="post" action="<?php echo base_url();?>admin/update" enctype="multipart/form-data">

          	 <div class="form-group">
                      <?php
                      $id = $_GET['id'];

                      ?>
                      <input type="hidden" id="update_id" name="id" value="<?php echo $id; ?>">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Name</label>
                        <input type="text" id="name" class="form-control" name="name">
                      </div>
                      <div class="form-group">
                        <label>Icon</label>
                        <input type="file"  class="form-control" name="icon"> 
                         <input type="hidden" id="icon" class="form-control" name="icon_old">  
                      </div>
                      <div class="form-group">
                        <label>Last connection</label>
                        <input type="text" id="last_connection" class="form-control" name="last_connection">
                      </div>  
                      <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                      </div>
                      <div class="form-group">
                        <label>Description Link</label>
                        <input type="text" class="form-control" id="description_link" name="description_link">
                      </div>  
                      <div class="form-group">
                        <label>Unread image</label>
                        <input type="file" class="form-control" name="unread_image">
                        <input type="hidden" class="form-control" id="unread_image" name="old_unread_image">
                      </div>  
                      <div class="form-group">
                        <label>Unread text</label>
                        <input type="text" class="form-control" id="unread_text" name="unread_text">
                      </div>
                      <div class="form-group">
                        <label>Unread Link</label>
                        <input type="text" class="form-control" id="unread_link" name="unread_link">
                      </div>        
                      <div class="form-group">
                        <input type="submit" class="form-control" value="Update">
                      </div> 
                    </div>

             </div>
       
      </form>
        <br>
        </div>
    </div>
</div>
</body>
</html>
<script type="text/javascript">
  $(document).ready(function(){
        var id = $("#update_id").val();
                  $.ajax({
                      url:'<?php echo base_url(); ?>admin/edit_data_by_id',
                        type:'post',
                        data:{id:id},
                        dataType:'json',
                        success:function(data)
                        {
                          $("#name").val(data[0].Gc_name);
                          $("#icon").val(data[0].Gc_icon);
                          $("#last_connection").val(data[0].Gc_last_connection);
                          $("#description").val(data[0].Gc_description);
                          $("#description_link").val(data[0].Gc_description_link);
                          $("#unread_image").val(data[0].gc_unread_image);
                          $("#unread_text").val(data[0].Gc_unread_text);
                          $("#unread_link").val(data[0].Gc_unread_link);

                        }
                  });
  });
</script>