<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bootstrap CRUD Data Table for Database with Modal Form</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <script src="<?php echo base_url(); ?>assets/jquery/main.js" > </script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">     
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.min.css"> 
    <script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script> 

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/DataTables/datatables.min.css"/>
 
<script type="text/javascript" src="<?php echo base_url(); ?>assets/DataTables/datatables.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<style type="text/css">
    body {color: #566787;background: #f5f5f5;font-family: 'Varela Round', sans-serif;font-size: 13px;}
	.table-wrapper {background: #fff;padding: 20px 25px;margin: 30px 0;border-radius: 3px;box-shadow: 0 1px 1px rgba(0,0,0,.05);}
	.table-title {padding-bottom: 15px;background: #435d7d;color: #fff;padding: 16px 30px;margin: -20px -25px 10px;border-radius: 3px 3px 0 0;}
    .table-title h2 {margin: 5px 0 0;font-size: 24px;}
	.table-title .btn-group {float: right;}
	.table-title .btn {color: #fff;float: right;font-size: 13px;border: none;min-width: 50px;border-radius: 2px;border: none;outline: none !important;margin-left: 10px;}
	.table-title .btn i {float: left;font-size: 21px;margin-right: 5px;}
	.table-title .btn span {float: left;margin-top: 2px;}
   	
	/* Modal styles */
	.modal .modal-dialog {max-width: 400px;}
	.modal .modal-header, .modal .modal-body, .modal .modal-footer {padding: 20px 30px;}
	.modal .modal-content {border-radius: 3px;}
	.modal .modal-footer {background: #ecf0f1;border-radius: 0 0 3px 3px;}
    .modal .modal-title {display: inline-block;}
	.modal .form-control {border-radius: 2px;box-shadow: none;border-color: #dddddd;}
	.modal textarea.form-control {resize: vertical;}
	.modal .btn {border-radius: 2px;min-width: 100px;}	
	.modal form label {font-weight: normal;}	
     #addEmployeeModal .modal-body .select2-container{ width: 338px !important;}
</style>
</head>
<body>
	
<a href="<?php echo base_url(); ?>admin/logout" style="float: right;" class="btn btn-success">Logout</a>
    <div class="container">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-6">
						<h2>Manage <b>Content</b></h2>
					</div>
					<div class="col-sm-6">
						<a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Add New</span></a>
				    <a href="<?php echo base_url();?>admin/" class="btn btn-success"><span>Home</span></a>
						<!-- <a href="#deleteEmployeeModal" class="btn btn-danger" data-toggle="modal"><i class="material-icons">&#xE15C;</i> <span>Delete</span></a>	 -->					
					</div>
                </div>
            </div>
            <table id="table" class="table table-striped table-hover">
					<thead>
		            <tr><th>Number</th><th>Name</th><th>Read Text</th><th>Read Image</th><th>Read Link</th><th>Message Time</th><th>Action</th></tr>
					
					</thead>
					<tbody>
						
					</tbody>
            </table>
    </div>
	<!-- Edit Modal HTML -->
	<div id="addEmployeeModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form id="tbl_group_content" enctype="multipart/form-data">
					<div class="modal-header">						
						<h4 class="modal-title">Add</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						<div class="form-group">
							<label>Name</label><br>
						
							<select class="form-control js-example-basic-single" id="group_name" name="name">
								<option value="0">Select User</option>
								<?php
									foreach ($user as $key => $value) {
								?>
								<option value="<?php echo $value['gc_id']; ?>"><?php echo $value['Gc_name']; ?></option>
								<?php		
									}
								?>
							</select>
						</div>
						<div class="form-group">
							<label>text</label>
							<input type="text" id="ctext" class="form-control" name="read_text">
						</div>
						<div class="form-group">
							<label>Image</label>
							<input type="file" id="cimage"  class="form-control" name="read_image">
						</div>
						<div class="form-group">
							<label>Link</label>
							<input type="text" id="clink" class="form-control" name="read_link">
						</div>						
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" id="save_tbl_group_content" class="btn btn-success" value="Add" >
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Edit Modal HTML -->
	<div id="editEmployeeModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form>
					<div class="modal-header">						
						<h4 class="modal-title">Edit</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						<div class="form-group">
							<label>Name</label>
							<input type="text" class="form-control" name="">
						</div>
						<div class="form-group">
							<label>Email</label>
							<input type="email" class="form-control" name="">
						</div>
						<div class="form-group">
							<label>Address</label>
							<textarea class="form-control" name=""></textarea>
						</div>
						<div class="form-group">
							<label>Phone</label>
							<input type="text" class="form-control" name="">
						</div>					
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-info" value="Save">
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Delete Modal HTML -->
	<div id="deleteEmployeeModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form>
					<div class="modal-header">						
						<h4 class="modal-title">Delete</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						<p>Are you sure you want to delete these Records?</p>
						<p class="text-warning"><small>This action cannot be undone.</small></p>
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-danger" value="Delete">
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html> 

<script type="text/javascript">
	$(document).ready(function(){
				// Activate tooltip
			$('[data-toggle="tooltip"]').tooltip();
		$('#group_name').select2();

		$(document).on('submit','#tbl_group_content',function(e){
			e.preventDefault();
			var user = $("#group_name").val();
			var read_text = $("#ctext").val();
			var read_image = $("#cimage").val();
			var read_link = $("#clink").val();
 
			if(user == 0){alert("please select user.");return false;}
			if(read_text == "" && read_image == "" && read_link == ""){alert("please fill one of field.");return false;}



			var datas = new FormData(this);
		     $.ajax({
		     		url:"<?php echo base_url(); ?>content/save_tbl_content",
                    type:'post',
                    data:datas,
		     		dataType:'json',
		     		cache: false,
                    contentType:false,
	                processData: false,
                    async:false,
		     		success:function(data){
		     			if(data=="success")
		     			{ window.location.reload(); }
		     		    else
		     		    { alert("error"); }
		     		}
		     });
		});

		  datatable();

				function datatable(){

				       $('#table').DataTable({
				        "bProcessing": true,
				        "sAjaxSource": "<?php echo base_url('content/tbl_content_fetch'); ?>",
				        "aoColumns": [
				              { mData: 'number' } ,
				              { mData: 'Gc_name' }, 
				              { mData: 'ct_text' },
				              { mData: 'Gc_icon' },
				              { mData: 'ct_link' },
				              { mData: 'ct_message_date' },
				              { mData: 'action' }

				            ],

				        'columnDefs': [ {
				        'targets': [6], // column index (start from 0)
				        'orderable': false, // set orderable false for selected columns
				            }]


				       });

					}

		$(document).on('click','.delete_message',function(){
				var value = $(this).data("value");
				 $.ajax({
		     		url:"<?php echo base_url(); ?>content/delete_message",
                    type:'post',
                    data:{id:value},
		     		dataType:'json',
		     	    success:function(data)
		     	    { 
		     	    	if(data=="success")
		     			{ window.location.reload(); }
		     		    else
		     		    { alert("error"); }
		     	    }   
		     	    }); 	   
		  });			

	});
</script>                               		                            