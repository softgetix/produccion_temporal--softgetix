<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>webchat.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <script src="<?php echo base_url(); ?>assets/jquery/main.js" > </script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">     
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.min.css"> 
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lightbox/dist/css/lightbox.min.css">

	  <script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="?php echo base_url(); ?>assets/lightbox/dist/js/lightbox-plus-jquery.min.js"></script>

<style type="text/css">
  html,body,div,span { height: 100%;  width: 100%;  overflow: hidden;  padding: 0;  margin: 0;  box-sizing: border-box; }
body { background: url("http://shurl.esy.es/y") no-repeat fixed center;background-size: cover;}
.fa-2x { font-size: 1.5em;}
.app {  position: relative;  overflow: hidden;  top: 19px;  height: calc(100% - 38px);  margin: auto;  padding: 0;  box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .06), 0 2px 5px 0 rgba(0, 0, 0, .2);}
.app-one { background-color: #f7f7f7;  height: 100%;  overflow: hidden;
  margin: 0;  padding: 0;  box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .06), 0 2px 5px 0 rgba(0, 0, 0, .2);}

.side { padding: 0; margin: 0;  height: 100%;}
.side-one {  padding: 0;  margin: 0;  height: 100%;  width: 100%;  z-index: 1;  position: relative;  display: block;  top: 0;}
.side-two {  padding: 0;  margin: 0;  height: 100%;  width: 100%;  z-index: 2;  position: relative;  top: -100%;  left: -100%;  -webkit-transition: left 0.3s ease;  transition: left 0.3s ease;}
.heading { padding: 10px 16px 10px 15px; margin: 0; height: 60px; width: 100%;  background-color: #eee;  z-index: 1000;}
.heading-avatar {  padding: 0;  cursor: pointer;}
.heading-avatar-icon img {  border-radius: 50%;  height: 40px;  width: 40px;}
.heading-name {  padding: 0 !important;  cursor: pointer;}
.heading-name-meta {  font-weight: 700;  font-size: 100%;  padding: 5px;  padding-bottom: 0;  text-align: left;  text-overflow: ellipsis;  white-space: nowrap;  color: #000;  display: block;}
.heading-online {  display: none;  padding: 0 5px;  font-size: 12px;  color: #93918f;}
.heading-compose { padding: 0; }
.heading-compose i {  text-align: center;  padding: 5px;  color: #93918f;  cursor: pointer;}
.heading-dot {  padding: 0;  margin-left: 10px;}
.heading-dot i {  text-align: right;  padding: 5px;  color: #93918f;  cursor: pointer;}
.searchBox {  padding: 0 !important;  margin: 0 !important;  height: 60px;  width: 100%;}
.searchBox-inner {  height: 100%;  width: 100%;  padding: 10px !important;  background-color: #fbfbfb;}
/*#searchBox-inner input {
  box-shadow: none;
}*/
.searchBox-inner input:focus {  outline: none;  border: none;  box-shadow: none;}
.sideBar {  padding: 0 !important;  margin: 0 !important;  background-color: #fff;  overflow-y: auto;  border: 1px solid #f7f7f7;  height: calc(100% - 120px);}
.sideBar-body {  position: relative;  padding: 10px !important;  border-bottom: 1px solid #f7f7f7;  height: 72px;  margin: 0 !important;  cursor: pointer;}
.sideBar-body:hover { background-color: #f2f2f2;}
.sideBar-avatar {  text-align: center;  padding: 0 !important;}
.avatar-icon img {  border-radius: 50%;  height: 49px;  width: 49px;}
.sideBar-main {  padding: 0 !important;}
.sideBar-main .row {  padding: 0 !important;  margin: 0 !important;}
.sideBar-name {  padding: 10px !important;}
.name-meta {  font-size: 100%;  padding: 1% !important;
 text-align: left;  text-overflow: ellipsis;  white-space: nowrap;  color: #000;}
.sideBar-time {  padding: 10px !important;}
.time-meta {  text-align: right;  font-size: 12px;  padding: 1% !important;  color: rgba(0, 0, 0, .4);  vertical-align: baseline;}
/*New Message*/
.newMessage {  padding: 0 !important;  margin: 0 !important;  height: 100%;  position: relative;  left: -100%;}
.newMessage-heading {  padding: 10px 16px 10px 15px !important;  margin: 0 !important;  height: 100px;  width: 100%;  background-color: #00bfa5;  z-index: 1001;}
.newMessage-main {  padding: 10px 16px 0 15px !important;  margin: 0 !important;  height: 60px;  margin-top: 30px !important;  width: 100%;  z-index: 1001;  color: #fff;}
.newMessage-title {  font-size: 18px;  font-weight: 700;  padding: 10px 5px !important;}
.newMessage-back {  text-align: center;  vertical-align: baseline;  padding: 12px 5px !important; display: block;  cursor: pointer;}
.newMessage-back i {  margin: auto !important;}
.composeBox {  padding: 0 !important;  margin: 0 !important;  height: 60px;  width: 100%;}
.composeBox-inner {  height: 100%;  width: 100%;  padding: 10px !important;  background-color: #fbfbfb;}
.composeBox-inner input:focus {  outline: none;  border: none;  box-shadow: none;}
.compose-sideBar {  padding: 0 !important;  margin: 0 !important;  background-color: #fff;  overflow-y: auto;  border: 1px solid #f7f7f7;  height: calc(100% - 160px);}
/*Conversation*/
.conversation {  padding: 0 !important;  margin: 0 !important;  height: 100%; /*width: 100%;*/ border-left: 1px solid rgba(0, 0, 0, .08); /*overflow-y: auto;*/}
.message {  padding: 0 !important;  margin: 0 !important;  background: url("w.jpg") no-repeat fixed center;  background-size: cover;  overflow-y: auto;  border: 1px solid #f7f7f7;  height: calc(100% - 120px);}
.message-previous {  margin : 0 !important;  padding: 0 !important;  height: auto;  width: 100%;}
.previous {  font-size: 15px;  text-align: center;  padding: 10px !important;  cursor: pointer;}
.previous a {  text-decoration: none;  font-weight: 700;}
.message-body {  margin: 0 !important;  padding: 0 !important;  width: auto;  height: auto;}
.message-main-receiver {  /*padding: 10px 20px;*/  max-width: 60%;}
.message-main-sender {  padding: 3px 20px !important;  margin-left: 40% !important;  max-width: 60%;}
.message-text {  margin: 0 !important;  padding: 5px !important;  word-wrap:break-word;  font-weight: 200;  font-size: 14px; padding-bottom: 0 !important;}
.message-time {  margin: 0 !important;  margin-left: 50px !important;  font-size: 12px;  text-align: right;  color: #9a9a9a;}
.receiver {  width: auto !important;  padding: 4px 10px 7px !important;  border-radius: 10px 10px 10px 0;  background: #ffffff;  font-size: 12px;  text-shadow: 0 1px 1px rgba(0, 0, 0, .2);  word-wrap: break-word;  display: inline-block;}
.sender {  float: right;  width: auto !important;  background: #dcf8c6;  border-radius: 10px 10px 0 10px;  padding: 4px 10px 7px !important;  font-size: 12px;  text-shadow: 0 1px 1px rgba(0, 0, 0, .2);  display: inline-block;  word-wrap: break-word;}
/*Reply*/
.reply {  height: 60px;  width: 100%;  background-color: #f5f1ee;  padding: 10px 5px 10px 5px !important;  margin: 0 !important;  z-index: 1000;}
.reply-emojis {padding: 5px !important;}
.reply-emojis i {  text-align: center;  padding: 5px 5px 5px 5px !important;
  color: #93918f;  cursor: pointer;}
.reply-recording { padding: 5px !important}
.reply-recording i {  text-align: center;  padding: 5px !important;  color: #93918f;  cursor: pointer;}
.reply-send { padding: 5px !important;}
.reply-send i {  text-align: center; padding: 5px !important;  color: #93918f;  cursor: pointer;}
.reply-main { padding: 2px 5px !important;}
.reply-main textarea {  width: 100%;  resize: none;  overflow: hidden;  padding: 5px !important;  outline: none;  border: none;  text-indent: 5px;  box-shadow: none;  height: 100%;  font-size: 16px;}
.reply-main textarea:focus {  outline: none;  border: none;  text-indent: 5px;  box-shadow: none;}
@media screen and (max-width: 700px) {
  .app {   top: 0;   height: 100%; }
  .heading {   height: 70px;   background-color: #009688; }
  .fa-2x {    font-size: 2.3em !important;  }
  .heading-avatar {    padding: 0 !important;  }
  .heading-avatar-icon img {    height: 50px;   width: 50px;  }
  .heading-compose {    padding: 5px !important;  }
  .heading-compose i {   color: #fff;    cursor: pointer;  }
  .heading-dot {   padding: 5px !important;    margin-left: 10px !important; }
  .heading-dot i {   color: #fff;   cursor: pointer; }
  .sideBar {   height: calc(100% - 130px); }
  .sideBar-body {   height: 80px; }
  .sideBar-avatar {   text-align: left;    padding: 0 8px !important; }
  .avatar-icon img {    height: 55px;    width: 55px;  }
  .sideBar-main {    padding: 0 !important;  }
  .sideBar-main .row {    padding: 0 !important;    margin: 0 !important;  }
  .sideBar-name {    padding: 10px 5px !important;  }
  .name-meta {    font-size: 16px;    padding: 5% !important;  }
  .sideBar-time {    padding: 10px !important;  }
  .time-meta {   text-align: right;    font-size: 14px;    padding: 4% !important;    color: rgba(0, 0, 0, .4);    vertical-align: baseline;  }
  /*Conversation*/
  .conversation {    padding: 0 !important;    margin: 0 !important;    height: 100%;    /*width: 100%;*/    border-left: 1px solid rgba(0, 0, 0, .08);  /*overflow-y: auto;*/ }
  .message {  height: calc(100% - 140px); }
  .reply {   height: 70px; }
  .reply-emojis {   padding: 5px 0 !important; }
  .reply-emojis i { padding: 5px 2px !important;   font-size: 1.8em !important; }
  .reply-main {  padding: 2px 8px !important;}
  .reply-main textarea {    padding: 8px !important;    font-size: 18px;  }
  .reply-recording {   padding: 5px 0 !important; }
  .reply-recording i {   padding: 5px 0 !important;   font-size: 1.8em !important;  }
  .reply-send {    padding: 5px 0 !important; }
  .reply-send i {   padding: 5px 2px 5px 0 !important;  font-size: 1.8em !important; }
}
.modal.fade:not(.in).left .modal-dialog {
  -webkit-transform: translate3d(-25%, 0, 0);
  transform: translate3d(-25%, 0, 0);
}

.modal.fade:not(.in).right .modal-dialog {
  -webkit-transform: translate3d(25%, 0, 0);
  transform: translate3d(50%, 0, 100%);
}
.modal-backdrop.in {
  opacity: 0; 
}
.col-sm-8 .col-xs-7 .heading-name{top:8px;}
</style>
</head>
<body>
<div class="container app">
  <div class="row app-one">
    <div class="col-sm-4 side" id="side">
      <div class="side-one">
        <div class="row heading">
          <div class="col-sm-3 col-xs-3 heading-avatar">
            <div class="heading-avatar-icon">
              <img src="https://bootdey.com/img/Content/avatar/avatar1.png">
            </div>
          </div>
        <!--   <div class="col-sm-1 col-xs-1">
          <span>Myself Abhimanyu</span>
          </div>
 -->
          <div class="col-sm-1 col-xs-1  heading-dot  pull-right">
            <i class="fa fa-ellipsis-v fa-2x  pull-right" aria-hidden="true"></i>
          </div>
          <div class="col-sm-2 col-xs-2 heading-compose  pull-right">
            <i class="fa fa-comments fa-2x  pull-right" aria-hidden="true"></i>
          </div>
        </div>

        <div class="row searchBox">
          <div class="col-sm-12 searchBox-inner">
            <div class="form-group has-feedback">
              <input id="searchText" type="text" class="form-control" name="searchText" placeholder="Search">
              <span class="glyphicon glyphicon-search form-control-feedback"></span>
            </div>
          </div>
        </div>

        <div class="row sideBar" id="user_chat_list">
        	<!-- <div class="col-sm-4 col-xs-4 pull-right sideBar-time"><span class="time-meta pull-right">18:18</span></div></div> -->
        </div>

  </div>

      <div class="side-two">
        <div class="row newMessage-heading">
          <div class="row newMessage-main">
            <div class="col-sm-2 col-xs-2 newMessage-back">
              <i class="fa fa-arrow-left" aria-hidden="true"></i>
            </div>
            <div class="col-sm-10 col-xs-10 newMessage-title">
              New Chat
            </div>
          </div>
        </div>



        <div class="row composeBox">
          <div class="col-sm-12 composeBox-inner">
            <div class="form-group has-feedback">
              <input id="composeText" type="text" class="form-control" name="searchText" placeholder="Search People">
              <span class="glyphicon glyphicon-search form-control-feedback"></span>
            </div>
          </div>
        </div>

        <div class="row compose-sideBar" id="user_list">
        </div>

      </div>
    </div>

  <div class="col-sm-8 conversation" id="chat_screen" style="display: none;">
       <div class="row heading" id="user_detail">

            <div class="col-sm-2 col-md-1 col-xs-3" id="back" style="display: none;width: 35px;top: 11px;font-size: 20px;padding-left: 0px;color: white;">
              <i class="fa fa-arrow-left"  aria-hidden="true"></i>
            </div>
            <div class="col-sm-2 col-md-1 col-xs-3 heading-avatar" style="width: 60px" >
              <div class="heading-avatar-icon user_chat_image">
                <img src="https://bootdey.com/img/Content/avatar/avatar6.png">
              </div>
            </div>
            <div class="col-sm-8 col-xs-7 heading-name" style="top:7px;font-size: large;">
              <a class="heading-name-meta">
              </a>
              <span class="heading-online">Online</span>
            </div>
            <div class="col-sm-1 col-xs-1  heading-dot pull-right">
              <i class="fa fa-ellipsis-v fa-2x  pull-right" aria-hidden="true"></i>
            </div>
      </div>

      <div class="row message" id="chat_box">

      <!--   <div class="row message-previous">
          <div class="col-sm-12 previous">
            <a onclick="previous(this)" id="ankitjain28" name="20">
            Show Previous Message!
            </a>
          </div>
        </div> -->     
      </div>

      <div class="row reply" id="user_reply">
            <div class="col-sm-1 col-xs-1 reply-emojis">
              <i class="fa fa-smile-o fa-2x"></i>
            </div>
            <div class="col-sm-9 col-xs-9 reply-main">
              <textarea class="form-control" rows="1" id="comment"></textarea>
            </div>
            <div class="col-sm-1 col-xs-1 reply-recording">
              <i class="fa fa-microphone fa-2x" aria-hidden="true"></i>
            </div>
            <div class="col-sm-1 col-xs-1 reply-send">
              <i class="fa fa-send fa-2x" aria-hidden="true"></i>
            </div>
      </div>

</div>


  <div class="col-sm-8 conversation" id="user_detail_info" style="display: none;">
       <div class="row heading" id="user_detail">
            <div class="col-sm-2 col-md-1 col-xs-3" id="back_detail" style="width: 35px;top: 11px;font-size: 20px;padding-left: 0px;color: white;">
              <i class="fa fa-arrow-left"  aria-hidden="true"></i>
            </div>
            <div class="col-sm-2 col-md-1 col-xs-3 heading-avatar" style="width: 60px" >
              <div class="heading-avatar-icon user_chat_image" >
                <img src="https://bootdey.com/img/Content/avatar/avatar6.png">
              </div>
            </div>
            <div class="col-sm-8 col-xs-7 heading-name" style="top:7px;font-size: large;">
              <a class="heading-name-meta">
              </a>
              <span class="heading-online">Online</span>
            </div>
            <div class="col-sm-1 col-xs-1  heading-dot pull-right">
              <i class="fa fa-ellipsis-v fa-2x  pull-right" aria-hidden="true"></i>
            </div>
      </div>
      <div class="container">
            <div class="card" style="height: auto;">
              <div><h3>Description Text</h3></div>
              <div id="desc_text"></div>
             </div>
            <div class="card" style="height: auto;">
              <div><h3>Description Link</h3></div>
              <div id="desc_link"></div>
           </div>
      </div>
</div>


     <div class="col-sm-8 conversation" id="blank_screen">
      <center><h1 style="margin-top: 250px;">Start Chat</h1></center>
     </div>

  </div>
</div>




<div class="modal fade" style="height: fit-content;">
  <div class="modal-dialog" style="margin-left: 965px;margin-right: 0px; width: 400px;">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #f5f1ee;">
        <button style="float: left;" type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <center><h4 class="modal-title">Details</h4></center>
      </div>
      <div class="modal-body" style="height: 400px">
             <div class="card" style="height: auto;">
              <div><h3>Description Text</h3></div>
              <div id="desc_text"></div>
             </div>
            <div class="card" style="height: auto;">
              <div><h3>Description Link</h3></div>
              <div id="desc_link"></div>
           </div>

      </div>
     
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



</body>
</html>

<script type="text/javascript">
	$(function(){
    $(".heading-compose").click(function() {
      $(".side-two").css({
        "left": "0"
      });
    });

    $(".newMessage-back").click(function() {
      $(".side-two").css({
        "left": "-100%"
      });
    });
})

	$(document).ready(function(){
	
		     $.ajax({
		     		url:"<?php echo base_url(); ?>admin/user_chat_list",
                    type:'post',
                    data:{},
		     		dataType:'json',
		     		success:function(data){
		     			var result = "";
		     			for(var i=0;i<data.length;i++)
		     			{
		     				//result +=data[i].Gc_name; 
		     				result +='<div class="row sideBar-body user_list_chatbox"><div class="col-sm-3 col-xs-3 sideBar-avatar"><input type="hidden" class="user_id" value='+data[i].gc_id+'><div class="avatar-icon"><img src="<?php echo base_url();?>/assets/image_upload/'+data[i].Gc_icon+'"></div></div><div class="col-sm-9 col-xs-9 sideBar-main"><div class="row"><div class="col-sm-8 col-xs-8 sideBar-name"><span class="name-meta">'+data[i].Gc_name+'</span></div></div></div></div>'
		     			}
		     			$("#user_chat_list").html(result);
		     		}
		     });

		      $.ajax({
		     		url:"<?php echo base_url(); ?>admin/user_list",
                    type:'post',
                    data:{},
		     		dataType:'json',
		     		success:function(data){
		     			var result = "";
		     			for(var i=0;i<data.length;i++)
		     			{
		     				//result +=data[i].Gc_name; 
		     				result +=' <div class="row sideBar-body user_list_chatbox"><div class="col-sm-3 col-xs-3 sideBar-avatar"><input type="hidden" class="user_id" value='+data[i].gc_id+'><div class="avatar-icon"><img src="<?php echo base_url();?>/assets/image_upload/'+data[i].Gc_icon+'"></div></div><div class="col-sm-9 col-xs-9 sideBar-main"><div class="row"><div class="col-sm-8 col-xs-8 sideBar-name"><span class="name-meta">'+data[i].Gc_name+'</span></div></div></div></div>'
		     			}
		     			$("#user_list").html(result);
		     		}
		     });

          $(document).on("keyup","#composeText",function(){
                    var search = $(this).val();

                    $.ajax({
                      url:'<?php echo base_url(); ?>admin/user_list_search',
                        type:'post',
                        data:{search:search},
                        dataType:'json',
                        success:function(data)
                        {
                          if(data != "")
                          {
                            var result = "";
                            for(var i=0;i<data.length;i++)
                            {
                              //result +=data[i].Gc_name; 
                              result +=' <div class="row sideBar-body user_list_chatbox"><div class="col-sm-3 col-xs-3 sideBar-avatar"><input type="hidden" class="user_id" value='+data[i].gc_id+'><div class="avatar-icon"><img src="<?php echo base_url();?>/assets/image_upload/'+data[i].Gc_icon+'"></div></div><div class="col-sm-9 col-xs-9 sideBar-main"><div class="row"><div class="col-sm-8 col-xs-8 sideBar-name"><span class="name-meta">'+data[i].Gc_name+'</span></div></div></div></div>'
                            }
                          }else{
                            var result = "<center><p style='margin-top:150px;'>No result found.</p></center>";
                          }
                          $("#user_list").html(result);
                        }
                    });
          });

          $(document).on("keyup","#searchText",function(){
                    var search = $(this).val();

                    $.ajax({
                      url:'<?php echo base_url(); ?>admin/user_list_search',
                        type:'post',
                        data:{search:search},
                        dataType:'json',
                        success:function(data)
                        {
                          if(data != "")
                          {
                            var result = "";
                            for(var i=0;i<data.length;i++)
                            {
                              //result +=data[i].Gc_name; 
                              result +=' <div class="row sideBar-body user_list_chatbox"><div class="col-sm-3 col-xs-3 sideBar-avatar"><input type="hidden" class="user_id" value='+data[i].gc_id+'><div class="avatar-icon"><img src="<?php echo base_url();?>/assets/image_upload/'+data[i].Gc_icon+'"></div></div><div class="col-sm-9 col-xs-9 sideBar-main"><div class="row"><div class="col-sm-8 col-xs-8 sideBar-name"><span class="name-meta">'+data[i].Gc_name+'</span></div></div></div></div>'
                            }
                          }else{
                            var result = "<center><p style='margin-top:150px;'>No result found.</p></center>";
                          }
                          $("#user_chat_list").html(result);
                        }
                    });
          });
          $(window).resize(function(){
                    if( $(window).width() > 767 ) {
                        $('#side').css('display','block');
                        $('#back').css('display','none');

                        $("#user_detail_info").css("display","none");
                        $("#chat_screen").css("display","block");
                    } 
             });
         $(document).on('click','#back',function(){
                  $('#side').css('display','block');
                  window.location.reload();
            });
          $(document).on('click','.user_list_chatbox',function(){
                    if( $(window).width() < 767 ) {
                        $('#side').css('display','none');
                        $(".heading-name-meta").css('color','white');
                        $('#back').css('display','block');
                    } 

                 
                $(".newMessage-back").click();
                $("#blank_screen").css('display','none');
                $("#chat_screen").css('display','block');
                var id = $(this).find('.user_id').val();
                var url = "<?php echo base_url();?>";
                  $.ajax({
                      url:'<?php echo base_url(); ?>admin/get_data_by_user',
                        type:'post',
                        data:{id:id},
                        dataType:'json',
                        success:function(data)
                        {
                          $(".heading-name-meta").html(data[0].Gc_name);
                          $(".user_chat_image").html('<img src="<?php echo base_url();?>/assets/image_upload/'+data[0].Gc_icon+'">');
                          $("#desc_text").html(data[0].Gc_description);
                          $("#desc_link").html("<a href='#'>"+data[0].Gc_description_link+"");

                          if(data != "")
                          {

                            var result = "";
                            result +=' <div class="row message-body"><div class="col-sm-12 message-main-sender"><div class="sender"><div class="message-text">   I am doing nothing man!</div><span class="message-time pull-right">sun</span></div></div></div>';
                            for(var i=0;i<data.length;i++)
                            {
                              if(data[i].ct_text != "" && data[i].ct_text != null)
                              {
                               result +='<div class="row message-body"><div class="col-sm-12 message-main-receiver"><div class="receiver"><div class="message-text">'+data[i].ct_text+'</div><span class="message-time pull-right">'+data[i].ct_message_date+'</span></div></div></div>';
                               }

                               if(data[i].ct_image != "" && data[i].ct_image != null)
                              {
                               result +='<div class="row message-body"><div class="col-sm-12 message-main-receiver"><div class="receiver"><div class="message-text"><img src="'+url+"assets/image_upload/"+data[i].ct_image+'"></div><span class="message-time">'+data[i].ct_message_date+'</span></div></div></div>';
                               }

                               if(data[i].ct_link != "" && data[i].ct_link != null)
                              {
                               result +='<div class="row message-body"><div class="col-sm-12 message-main-receiver"><div class="receiver"><div class="message-text">'+data[i].ct_link+'</div><span class="message-time pull-right">'+data[i].ct_message_date+'</span></div></div></div>';
                               }

                            } 

                        
                               //unread start
                              if(data[0].gc_unread_image != null)
                              {
                               result +='<div class="row message-body"><div style="background-color: antiquewhite;"><center><span><h5>UNREAD MESSAGES</h5></span></center></div><div class="col-sm-12 message-main-receiver"><div class="receiver"><div class="message-text"><img src="'+url+"assets/image_upload/"+data[0].gc_unread_image+'"></div><span class="message-time pull-right"></span></div></div></div>';
                               }       
                              if(data[0].Gc_unread_text != null)
                              {
                               result +='<div class="row message-body"><div class="col-sm-12 message-main-receiver"><div class="receiver"><div class="message-text">'+data[0].Gc_unread_text+'</div><span class="message-time pull-right"></span></div></div></div>';
                               }                
                              if(data[0].Gc_unread_link != null)
                              {
                               result +='<div class="row message-body"><div class="col-sm-12 message-main-receiver"><div class="receiver"><div class="message-text">'+data[0].Gc_unread_link+'</div><span class="message-time pull-right"></span></div></div></div>';
                               }     
                               //unread end

                           

                          }else{
                            var result = "<center><p style='margin-top:150px;'>Start chat.</p></center>";
                          }

                          $("#chat_box").html(result);
                      }
                 });
          });

          $(document).on("click",'.reply-send',function(){
                  var value = $("#comment").val();
                  if(value == "")
                  {
                    return false;
                  }
                  var href = "https://api.whatsapp.com/send?phone=5491161251911&text="+value+" "; 
                  window.location = href;
          });

          $('#user_detail').click(function() {

               if( $(window).width() < 767 ) {
              $("#chat_screen").css("display","none");
              $("#user_detail_info").css("display","block");

             }else{
              $('.modal')
                  .prop('class', 'modal fade') // revert to default
                  .addClass('right');
              $('.modal').modal('show');              
             }

          });
		        $(document).on('click','#back_detail',function(){
                  $("#user_detail_info").css("display","none");
                  $("#chat_screen").css("display","block");
            });
   });
alert();
</script>
<img src="fdsf.jpg">