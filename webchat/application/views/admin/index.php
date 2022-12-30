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
</style>
</head>
<body>
	
<a href="<?php echo base_url(); ?>admin/logout" style="float: right;" class="btn btn-success">Logout</a>
    <div class="container">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-6">
						<h2>Manage <b>Details</b></h2>
					</div>
					<div class="col-sm-6">
						<a href="<?php echo base_url();?>content/" class="btn btn-success"><span>Content View</span></a>

						<a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Add New</span></a>
						<!-- <a href="#deleteEmployeeModal" class="btn btn-danger" data-toggle="modal"><i class="material-icons">&#xE15C;</i> <span>Delete</span></a>	 -->					
					</div>
                </div>
            </div>
            <table id="table" class="table table-striped table-hover">
					<thead>
		            <tr><th>Id</th><th>Name</th><th>Icon</th><th>Last Connection</th><th>Description</th><th>Description Link</th><th>Action</th></tr>
					
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
							<label>Name</label>
							<input type="text" required class="form-control" name="name">
						</div>
						<div class="form-group">
							<label>Icon</label>
							<input type="file" required class="form-control" name="icon">
						</div>
						<div class="form-group">
							<label>Last connection</label>
							<input type="text" class="form-control" name="last_connection">
						</div>	
						<div class="form-group">
							<label>Description</label>
							<textarea class="form-control" name="description"></textarea>
						</div>
						<div class="form-group">
							<label>Description Link</label>
							<input type="text" class="form-control" name="description_link">
						</div>	
						<div class="form-group">
							<label>Unread image</label>
							<input type="file" class="form-control" name="unread_image">
						</div>	
						<div class="form-group">
							<label>Unread text</label>
							<input type="text" class="form-control" name="unread_text">
						</div>
						<div class="form-group">
							<label>Unread Link</label>
							<input type="text" class="form-control" name="unread_link">
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
	
			// Select/Deselect checkboxes
			var checkbox = $('table tbody input[type="checkbox"]');
			$("#selectAll").click(function(){
				if(this.checked){
					checkbox.each(function(){
						this.checked = true;                        
					});
				} else{
					checkbox.each(function(){
						this.checked = false;                        
					});
				} 
			});
			checkbox.click(function(){
				if(!this.checked){
					$("#selectAll").prop("checked", false);
				}
			});


		$(document).on('submit','#tbl_group_content',function(e){
			e.preventDefault();
			var datas = new FormData(this);
		     $.ajax({
		     		url:"<?php echo base_url(); ?>admin/save_tbl_group_content",
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
				        "sAjaxSource": "<?php echo base_url('admin/tbl_group_content_fetch'); ?>",
				        "aoColumns": [
				              { mData: 'gc_id' } ,
				              { mData: 'Gc_name' }, 
				              { mData: 'Gc_icon' },
				              { mData: 'Gc_last_connection' },
				              { mData: 'Gc_description'},
				              { mData: 'Gc_description_link' },
				               { mData: 'action' }

				            ],

				        'columnDefs': [ {
				        'targets': [6], // column index (start from 0)
				        'orderable': false, // set orderable false for selected columns
				            }]


				       });

					}

	});
</script>                               		                            