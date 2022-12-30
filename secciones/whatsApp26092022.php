<?php
function base_url(){
  return 'https://login.forzagps.com/produccion_temporal/webchat/';
}
?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>WhatsApp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <link rel="stylesheet" type="text/css" href="webchat/assets/bootstrap/css/bootstrap.min.css">     
    <link rel="stylesheet" type="text/css" href="webchat/assets/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="webchat/assets/lightbox/dist/css/lightbox.min.css">
    <link rel="stylesheet" href="webchat/assets/toastr/toastr.min.css" /> 
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" id="theme-styles">
    <script src="webchat/assets/jquery/main.js" > </script>
    <script src="webchat/assets/lightbox/dist/js/lightbox-plus-jquery.min.js"></script>
    <script src="webchat/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="webchat/assets/toastr/toastr.min.js" ></script>
    <script src='https://cdn.rawgit.com/admsev/jquery-play-sound/master/jquery.playSound.js'></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <style type="text/css">

    html,body { height: 100%;  width: 100%; padding: 0;  margin: 0;  box-sizing: border-box; }
  
    #content {height: calc(100% - 110px)}
    #wp-container { /*background: url("http://shurl.esy.es/y") no-repeat fixed center;*/background-size: cover;font-family: initial;}
  .fa-2x { font-size: 1.5em;}
  .app {  position: relative;  overflow: hidden;  top: 19px;  height: calc(100% - 38px);  margin: auto;  padding: 0;  box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .06), 0 2px 5px 0 rgba(0, 0, 0, .2);}
  .app-one { background-color: #f7f7f7;  height: 100%;  overflow: hidden;
    margin: 0;  padding: 0;  box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .06), 0 2px 5px 0 rgba(0, 0, 0, .2);}

  .side { padding: 0; margin: 0;  height: 100%;}
  .side-one {  padding: 0;  margin: 0;  height: 100%;  width: 100%;  z-index: 1;  position: relative;  display: block;  top: 0;}
  .side-two {  padding: 0;  margin: 0;  height: 100%;  width: 100%;  z-index: 2;  position: relative;  top: -100%;  left: -100%;  -webkit-transition: left 0.3s ease;  transition: left 0.3s ease;}
  .heading { padding: 10px 16px 10px 15px; margin: 0; height: 60px; width: 100%;  background-color: #eee;  z-index: 1000;}
  .heading-avatar {cursor: pointer;align-items: center;margin-top: -5px;}
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
  .sideBar {  padding: 0 !important;  margin: 0 !important;  background-color: #fff;  overflow-y: auto;  border: 1px solid #f7f7f7;  height: calc(100% - 163px);}
  .sideBar-body {  position: relative;height: auto;   cursor: pointer;margin-left: 0;}
  .sideBar-body:hover { background-color: #f2f2f2;}
  .sideBar-avatar {  text-align: center;  padding: 0 !important;}
  .avatar-icon img {  border-radius: 50%;  height: 49px;  width: 49px;}
  .sideBar-main {  padding:0;}
  .sideBar-main .row {  padding: 0 !important;  margin: 0 !important;}
  .sideBar-name {  padding: 10px !important;}
  .name-meta {color: #333333;font-weight: 600;font-size: 18px;width: 190px;display: inline-block;}
  .text-over {text-overflow: ellipsis;white-space: nowrap; overflow: hidden;}
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
  #cust_scroll {  padding: 0 !important;  margin: 0 !important;  background: url("w.jpg") no-repeat fixed center;  background-size: cover;  overflow-y: auto;  border: 1px solid #f7f7f7;  height: calc(100% - 120px);width: 100%;}
  .message {height: 101% !important;}
  .message-previous {  margin : 0 !important;  padding: 0 !important;  height: auto;  width: 100%;}
  .previous {  font-size: 15px;  text-align: center;  padding: 10px !important;  cursor: pointer;}
  .previous a {  text-decoration: none;  font-weight: 700;}
  .message-body {  margin: 0 !important;  padding: 0 !important;  width: auto;  height: auto;}
  .message-main-receiver { padding: 10px 20px; max-width: 60%;}
  .message-main-sender {  padding: 3px 20px !important;  margin-left: 40% !important;  max-width: 60%;}
  .message-text {  margin: 0 !important;  padding: 5px !important;  word-wrap:break-word;  font-weight: 200;  font-size: 14px; padding-bottom: 0 !important;}
  .message-time {  margin: 0 !important;  margin-left: 50px !important;  font-size: 12px;  text-align: right;  color: #9a9a9a;}
  .receiver {  width: auto !important;max-width:100%;  padding: 4px 10px 7px !important;  border-radius: 10px 10px 10px 0;  background: #ffffff;  font-size: 12px;  text-shadow: 0 1px 1px rgba(0, 0, 0, .2);  word-wrap: break-word;  display: inline-block;}
  .sender {  float: right;  width: auto !important;max-width: inherit;  background: #dcf8c6;  border-radius: 10px 10px 0 10px;  padding: 4px 10px 7px !important;  font-size: 12px;  text-shadow: 0 1px 1px rgba(0, 0, 0, .2);  display: inline-block;  word-wrap: break-word;margin-bottom: 15px;}
  /*Reply*/
  .reply {  height: 60px;  width: 100%;  background-color: #f5f1ee;  padding: 10px 5px 10px 5px !important;  margin: 0 !important;  z-index: 1000;}
  .reply-emojis {padding: 5px !important;}
  .reply-emojis i {  text-align: center;  padding: 5px 5px 5px 5px !important;
    color: #93918f;  cursor: pointer;}
  .reply-recording { padding: 5px !important}
  .reply-recording i {  text-align: center;  padding: 5px !important;  color: #93918f;  cursor: pointer;}
  .reply-send { padding: 5px !important;text-align: center;}
  .reply-send i {  text-align: center; padding: 5px !important;  color: #93918f;}
  .reply-main { padding: 2px 5px !important;}
  .reply-main textarea {  width: 100%;  resize: none;  overflow: hidden;  padding: 5px !important;  outline: none;  border: none;  text-indent: 5px;  box-shadow: none;  height: 100%;  font-size: 16px;}
  .reply-main textarea:focus {  outline: none;  border: none;  text-indent: 5px;  box-shadow: none;}
  @media screen and (max-width: 451px) {
   .heading-name a.heading-name-meta { 
      text-overflow: ellipsis;
      width: 119px;
      white-space: nowrap;
      overflow: hidden;
   }
  }
  @media screen and (max-width: 700px) {
    .app {   top: 0;   height:calc(100% - -45px) !important; }
    .heading {   height: 100px;   background-color: #009688; }
    .fa-2x {    font-size: 2.3em !important;  }
    .heading-avatar {    padding: 0 !important;  margin-top: 30px;}
    .heading-avatar-icon img {    height: 50px;   width: 50px;  }
    .heading-compose {    padding: 5px !important;  }
    .heading-compose i {   color: #fff;    cursor: pointer;  }
    .heading-dot {   padding: 5px !important;    margin-left: 10px !important; }
    .heading-dot i {   color: #fff;   cursor: pointer; }
    .sideBar {   height: calc(100% - 130px); }
    .sideBar-body {   height: 100px; }
    .sideBar-avatar {   text-align: center;    padding: 0 8px !important; }
    .avatar-icon img {    height: 55px;    width: 55px;  }
    .sideBar-main {    padding: 0 !important;  }
    .sideBar-main .row {    padding: 0 !important;    margin: 0 !important;  }
    .sideBar-name {    padding: 10px 5px !important;  }
    .name-meta {    font-size: 16px; }
    .sideBar-time {    padding: 10px !important;  }
    .time-meta {   text-align: right;    font-size: 14px;    padding: 4% !important;    color: rgba(0, 0, 0, .4);    vertical-align: baseline;  }
    /*Conversation*/
    .conversation {    padding: 0 !important;    margin: 0 !important;    height: 100%;    /*width: 100%;*/    border-left: 1px solid rgba(0, 0, 0, .08);  /*overflow-y: auto;*/ }
    .message {  height: calc(100% - 140px); }
    .reply { height: 70px; padding: 0 !important;}
    .reply-emojis {   padding: 5px 0 !important; }
    .reply-emojis i { padding: 5px 2px !important;   font-size: 1.8em !important; }
    .reply-main {  padding: 2px 8px !important;}
    .reply-main textarea {    padding: 8px !important;    font-size: 18px;  }
    .reply-recording {   padding: 5px 0 !important; }
    .reply-recording i {   padding: 5px 0 !important;   font-size: 1.8em !important;  }
    .reply-send {    padding: 5px 0 !important; }
    .reply-send i {   padding: 5px 2px 5px 0 !important;  font-size: 1.8em !important; }
    .head-user-icon {padding-top: 15px !important;}
     #ticket_status_combobox {margin-top: 53px;}
     .heading-dot i {margin-top: 43px;}
     #back {top: 42px !important;margin-right: 15px;}
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
  #status_filter {margin-top: 10px;margin-left: 10px;width: 95%;}
  .user-icon span {padding: 13px;border-radius: 50%;background: #dcf0e4;color: #32aa60;font-weight: 700;font-size: 18px;display: inline-block;width: 50px;
      height: 50px;}
  .lastUpdated {color: #a7a7a7;font-weight: 700;}
  .unread span {background: #25d366;color: #fff;margin-top: 24px;width: 32px;}
  .short_msg {color: #a7a7a7;font-weight: 700;padding-top: 10px;display: block;width: 190px;}
  img.loader {z-index: 2;width: 100px;margin-left: 40%;position: absolute;}
  .head-user-icon span {padding: 13px;text-align: center;}
  .sideBar-body .user-icon {margin-top: 15px;}
  .media_file {
      display: flex;
      width: 350px;
      line-height: 83px;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      word-wrap: break-word;
  }
  .media_file img {max-height: 350px;max-width: 350px;}
  .message-media {margin-bottom: 7px;}
  .user_list_chatbox.active {background: #80808047;}
  div.toast {display: table-caption;}
  .media_file {display: initial;}
  #reply-send-btn {border: 0 !important;display: contents;}
  button[disabled] {cursor: not-allowed;}
  #mob_user_detail {margin-top: 15px;}
  .toast-top-center { 
     top: 50%;   
     margin: 0 auto;  
     left: 50%;   
     margin-left: -150px;
   }
   .back-btn {position: absolute;z-index: 2;font-size: 16px;top: 35px;left: 20px;}

  </style>


<div id="wp-container" class="container app">
  <div class="row app-one">
    <div class="col-sm-4 side" id="side">
      <div class="side-one">
        <div class="row heading">
         <!--  <div class="col-sm-3 col-xs-3 heading-avatar">
            <div class="heading-avatar-icon">
              <img src="https://bootdey.com/img/Content/avatar/avatar1.png">
            </div>
          </div> -->
        <!--   <div class="col-sm-1 col-xs-1">
          <span>Myself Abhimanyu</span>
          </div>
 -->
          <div class="col-sm-1 col-xs-1  heading-dot  pull-right hide">
            <i class="fa fa-ellipsis-v fa-2x  pull-right" aria-hidden="true"></i>
          </div>
          <!-- <div class="col-sm-2 col-xs-2 heading-compose  pull-right">
            <i class="fa fa-comments fa-2x  pull-right" aria-hidden="true"></i>
          </div> -->
        </div>
         <select class="form-control" id="status_filter">
            <option value="">Status Filter</option>
          </select>
        <div class="row searchBox">
          <div class="col-sm-12 searchBox-inner">
            <div class="form-group has-feedback">
              <input id="searchText" type="text" class="form-control" name="searchText" placeholder="Buscar">
              <span class="glyphicon glyphicon-search form-control-feedback"></span>
            </div>
          </div>
        </div>

        <img src="<?php echo base_url().'assets/loader.gif' ?>" class="loader side-loader">  
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
              <input id="composeText" type="text" class="form-control" name="searchText" placeholder="Buscar">
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

            <div class="col-sm-1 col-md-1 col-xs-1" id="back" style="display: none;top:7px;font-size: 25px;padding-top: 10px;color: white;">
              <i class="fa fa-arrow-left"  aria-hidden="true"></i>
            </div>
            <div class="col-sm-2 col-md-1 col-xs-1 heading-avatar" style="width: 60px" >
              <!--  <div class="heading-avatar-icon user_chat_image">
                 <img src="https://bootdey.com/img/Content/avatar/avatar6.png"> 
              </div> -->
              <div class="user-icon head-user-icon">
                 <span></span>
              </div>
            </div>
            <div class="col-sm-6 col-md-6 col-xs-3 heading-name" style="top:7px;font-size: large;">
              <a class="heading-name-meta"></a>
              <span class="heading-online">Online</span>
            </div>
            <div class="col-sm-1 col-md-1 col-xs-1 heading-dot pull-right">
              <i class="fa fa-ellipsis-v fa-2x  pull-right" aria-hidden="true"></i>
            </div>
            <div class="col-sm-3 col-md-3 col-xs-4 heading-combobox pull-right">
              <select id="ticket_status_combobox" class="form-control" disabled>
                <option value="1">Pendiente</option>
                <option value="2">Abierto</option>
                <option value="3">Cerrado</option>
              </select>
            </div>
      </div>
      <div class="row" id="cust_scroll">
        <div class="message" id="chat_box">

        <!--   <div class="row message-previous">
            <div class="col-sm-12 previous">
              <a onclick="previous(this)" id="ankitjain28" name="20">
              Show Previous Message!
              </a>
            </div>
          </div> -->     
        </div>
      </div>
      <div class="row reply" id="user_reply">
           <!--  <div class="col-sm-1 col-xs-1 reply-emojis">
              <i class="fa fa-smile-o fa-2x"></i>
            </div> -->
            <div class="col-sm-11 col-xs-11 reply-main">
              <textarea class="form-control" rows="1" id="comment" disabled></textarea>
              <input type="hidden" id="user_name" value="">
            </div>
           <!--  <div class="col-sm-1 col-xs-1 reply-recording">
              <i class="fa fa-microphone fa-2x" aria-hidden="true"></i>
            </div> -->
            <button type="button" id="reply-send-btn" disabled>
              <div class="col-sm-1 col-xs-1 reply-send">
                <i class="fa fa-send fa-2x" aria-hidden="true"></i>
              </div>
            </button>
      </div>

</div>


  <div class="col-sm-8 conversation" id="user_detail_info" style="display: none;">
       <div class="row heading" id="user_detail">
            <div class="col-sm-2 col-md-1 col-xs-3" id="back_detail" style="width: 35px;top: 11px;font-size: 20px;padding-left: 0px;color: white;">
              <i class="fa fa-arrow-left"  aria-hidden="true"></i>
            </div>
            <div class="col-sm-2 col-md-1 col-xs-3 heading-avatar" style="width: 60px" >
              <!-- <div class="heading-avatar-icon user_chat_image" >
                <img src="https://bootdey.com/img/Content/avatar/avatar6.png">
              </div> -->
              <div class="user-icon head-user-icon">
                 <span></span>
              </div>
            </div>
            <div class="col-sm-8 col-xs-7 heading-name" style="top:7px;font-size: large;">
              <a class="heading-name-meta"></a>
              <span class="heading-online">Online</span>
            </div>
            <div class="col-sm-1 col-xs-1  heading-dot pull-right">
              <i class="fa fa-ellipsis-v fa-2x  pull-right" aria-hidden="true"></i>
            </div>
      </div>
      <div class="container" id="mob_user_detail">
      </div>
</div>


     <div class="col-sm-8 conversation" id="blank_screen">
      <center><h1 style="margin-top: 250px;background: initial;">Para comenzar por favor seleccione un chat</h1></center>
     </div>

  </div>
</div>




<div id="dummyModal" class="modal modal fade" style="height: fit-content;">
  <div class="modal-dialog" style="margin-left: 965px;margin-right: 0px; width: 400px;">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #f5f1ee;">
        <button style="float: left;" type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <center><h4 class="modal-title">info. del contacto</h4></center>
      </div>
      <div class="modal-body">
       <img src="<?php echo base_url().'assets/loader.gif' ?>" class="loader info-loader" style="display: none;position: relative;width: 50px;">
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
  (function($) {
    $.fn.hasScrollBar = function() {
        return this.get(0).scrollHeight > this.height();
    }
  })(jQuery);

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

    toastr.options = {
     positionClass: 'toast-top-center'
    };

})

  var base_url = "<?php echo base_url(); ?>"; 
  var us_id = '<?php echo $_SESSION['idUsuario'] ?>';
  var gmt = '-3';
  var wati_token,wati_url;
  
	$(document).ready(function(){
         var wp_number;
         var template;
         var prev_template;
         var templateName;
         var prevContacts;
         var node;
         var page_number = 1;
         var selectedContact;
         var scrollUp = false;

         var audioElement = document.createElement('audio');
         audioElement.setAttribute('src', base_url+'assets/notification.mp3');
        
         $.ajax({
            url:"<?php echo base_url(); ?>WatiApi/getContactFilters",
                    type:'post',
                    data:{us_id:us_id},
            dataType:'json',
            success:function(result){
              $('.side-loader').hide();
              var html = '';
              if(result.transaction_status == 'ok'){
                var filters = result.contactlist;
                  //console.log(filters);
                $.each(filters,function(index,data){
                  html += '<option value="'+data.contactlistfilterid+'">'+data.contactlistfiltername+'</option>';
                });
               $("#status_filter").html(html);
               var credentials = result.credentials;
               $.each(credentials,function(index,data){
                  wati_token = data.token;
                  wati_url = data.url;
               });
              }
            }
         });

         function getContacts(){
           //$('.side-loader').show();
           var filter_id = $('#status_filter').val() || 1;
           var search = $('#searchText').val();
  		     $.ajax({
  		     		url:"<?php echo base_url(); ?>WatiApi/getContacts",
                      type:'post',
                      data:{contactlistfilterid:filter_id,us_id:us_id,gmt:gmt,search:search},
  		     		dataType:'html',
  		     		success:function(result){
                $('.side-loader').hide();
  		     			$("#user_chat_list").html(result);
                var notify = $('#notfiy').val();
                var messagePreview = $('#messagePreview').val();
                if(notify > 0){
                  console.log('notify '+notify);
                  if(notify == 1){
                    $.playSound(base_url+'assets/notification.mp3');
                    if ($("body").hasClass("hidden")) {
                      notifyMe(messagePreview);
                    }
                  }else{
                    for (var i = 0; i < notify; i++) {
                       $.playSound(base_url+'assets/notification.mp3');
                       if ($("body").hasClass("hidden")) {
                        notifyMe(messagePreview);
                       }
                    }
                  }
                  }
                if(selectedContact){
                  console.log(selectedContact);
                  $('#'+selectedContact).addClass("active");
                }

                if($('#areyouthere').val() == 1){
                  if(!$('body').hasClass('swal2-shown')){
                    Swal.fire({
                      title: 'Estás ahí todavía?',
                      confirmButtonText: 'acá estoy',
                      allowOutsideClick: false,
                      allowEscapeKey: false,
                      showConfirmButton: true,

                    }).then((result) => {
                      if (result.isConfirmed) {
                         swal.close();
                         iamhere();
                      } 
                    });
                 } 
                }else{
                   swal.close();
                }  
  		     		}
  		     });
         }
         getContacts();

          $(document).on('change','#status_filter',function(){
             //$("#chat_screen").css('display','none');
             //$("#blank_screen").css('display','block');
             //wp_number = '';
             getContacts();
          });
		      
          var currentRequest = null;    
          function getMessages(refresh = '')
          {
             if( $(window).width() < 767 ) {
                $('#side').css('display','none');
                $(".heading-name-meta").css('color','white');
                $('#back').css('display','block');
            } 

                             
            $(".newMessage-back").click();
            $("#blank_screen").css('display','none');
            $("#chat_screen").css('display','block');
          
            var url = "<?php echo base_url();?>";
            var name = node.find('.name-meta').text();
            var objDiv = document.getElementById("cust_scroll");
            var objHeight = objDiv.scrollHeight; //11;
            //console.log('objHeight='+objHeight);
            $(".heading-name-meta").html(name);
            if(name)
              $(".head-user-icon span").text(name.slice(0,1));
              
              if(refresh == 1){
                var dataString = {token:wati_token,url:wati_url,gmt:gmt,wp_number:wp_number,page_number:1,refresh:1,name:name};
              }else{
                var dataString = {token:wati_token,url:wati_url,gmt:gmt,wp_number:wp_number,page_number:page_number,refresh:0,name:name};
                if(page_number == 1){
                  $('#chat_box').html('<img src="<?php echo base_url().'assets/loader.gif' ?>" class="loader info-loader" style="display: none;position: relative;">');
                }
              }
              currentRequest = $.ajax({
                    url:'<?php echo base_url(); ?>WatiApi/getMessages',
                    type:'post',
                    data:dataString,
                    dataType:'html',
                    beforeSend : function()    {           
                        if(currentRequest != null && refresh == '') {
                            currentRequest.abort();
                        }
                    },
                    success:function(data)
                    { 
                      $('.total_pages').remove();
                      if(refresh == 1) 
                      { 
                        console.log('refreshed page 1');
                       // $('#chat_box .page-1').remove();
                        $("#chat_box").append(data);
                        wati_media(1);
                        $('#chat_box [id]').each(function () {
                          $('[id="' + this.id + '"]:gt(0)').remove();
                        });
                      }else
                      { 
                          if(page_number == 1){
                             $("#chat_box").html(data);
                             console.log('first loaded page '+ page_number);
                          }else{
                             $("#chat_box").prepend(data);
                             console.log('prepend page '+ page_number);
                          }
                          wati_media(page_number);
                          if(page_number == 1){
                             objHeight = objDiv.scrollHeight;
                             objDiv.scrollTop = objHeight;
                          }else{
                            if(scrollUp == true){
                             objHeight = objDiv.scrollHeight - objHeight;
                             objDiv.scrollTop = objHeight;
                           }
                          }
                    }
                    if(!$('#chat_box').hasScrollBar()){
                      //alert('no scroll');
                    }
                  }
             });
          }
        //if (location.hostname != "localhost"){
            setInterval(function () { getContacts();}, 10000);
            setInterval(function () { if(wp_number){ getMessages(1);} }, 10000);
            setInterval(function () { if(wp_number){ new_message_status(wp_number,1);} },10000);
         // }  
          $(document).on('click','.user_list_chatbox',function(){
            node = $(this);
            page_number = 1;
            wp_number = node.find('.wp_number').val();
            prev_template = '';
            $('#comment').val('');
            $('#chat_box').html('');
            getMessages();
            new_message_status(node.find('.wp_number').val());
            contactDetail(node);

            $(".user_list_chatbox").removeClass("active"); 
            node.addClass("active");
            selectedContact = $(this).attr('id'); 
          
          });

          var lastScrollTop = 0;
          $('#cust_scroll').on('scroll', function() {
            var div = $(this).get(0);
            //console.log(div.scrollTop);
            if(div.scrollTop  == 0) {
               scrollUp = true;
               var total_pages = $('.total_pages').val();
               if(page_number < Math.round(total_pages/10)){
                page_number++;
                console.log('scrolled up page '+page_number);
                getMessages();
               } 
              }
          });

          function wati_media(page_number){
           $('#chat_box .hasNoFile.message-media.page-'+page_number).each(function(){
              var temp = $(this);
              $(this).find('.image-loader').show();
              var fileType = $(this).find('.wati_media_filetype').val();;
              var fileName = $(this).find('.wati_media').val();
               $.ajax({
                    url:'<?php echo base_url(); ?>WatiApi/getMedia',
                      type:'post',
                      data:{token:wati_token,url:wati_url,fileType:fileType,fileName:fileName},
                      dataType:'html',
                      success:function(data)
                      {
                        temp.find('.media_file').html(data);
                        temp.find('.image-loader').hide();
                        temp.removeClass('hasNoFile');
                      }
               });
            }); 
          }

          var messageStatusRequest = null; 
          function new_message_status(wp_number,refresh = ''){
          messageStatusRequest = $.ajax({
                    url:'<?php echo base_url(); ?>WatiApi/NewMessageStatus',
                      type:'post',
                      data:{token:wati_token,url:wati_url,us_id:us_id,phone:wp_number},
                      dataType:'json',
                      beforeSend:function(){
                        if(messageStatusRequest != null) {
                          messageStatusRequest.abort();
                        }
                      },
                      success:function(result)
                      {
                        if(result.transaction_status == 'ok'){
                          var contactlist = result.contactlist;
                           $.each(contactlist,function(index,contactlist){
                            template = contactlist.Template;
                            templateName = contactlist.TemplateName;
                            //template = 0;
                            if(template == 1){

                              $('#comment').val(contactlist.textshown).prop('disabled',true);
                              $('#reply-send-btn').removeAttr('disabled');
                            }else if(template == 2){

                              $('#comment').val(contactlist.textshown).prop('disabled',true);
                              $('#reply-send-btn').prop('disabled',true);
                            
                            }else if(template == 0){
                               if(prev_template && prev_template != 0){
                                $('#comment').val('');
                               }else{
                                $('#comment').val($('#comment').val());
                               }
                               $('#comment').removeAttr('disabled');
                               $('#reply-send-btn').removeAttr('disabled');
                            }
                            if(contactlist.combobox == 1){
                              $('#ticket_status_combobox').removeAttr('disabled');
                            }else{
                              $('#ticket_status_combobox').prop('disabled',true);
                            }
                            prev_template = template;
                            $('#ticket_status_combobox').val(contactlist.currentcomboboxvalue).prop('selected',true);
                          });  
                        } 
                      }
               });
          }
          $(document).on("keyup","#composeText",function(){
                     var filter_id = 1;
                     var search = $(this).val();
                     $.ajax({
                        url:"<?php echo base_url(); ?>WatiApi/getContacts",
                                type:'post',
                                data:{contactlistfilterid:filter_id,us_id:us_id,search:search},
                        dataType:'html',
                        success:function(result){
                          $("#user_list").html(result);
                        }
                     }); 
          });

          $(document).on("keyup","#searchText",function(){
                    var search = $(this).val();
                    getContacts(search);
                    if(search){
                      $('#status_filter').prop('disabled',true);
                    }else{
                      $('#status_filter').removeAttr('disabled');
                    }     
          });
          function sendMessage(send = false){
               var messageText = $('#comment').val();
               var user_name = $('#user_name').val();
               var id = $('.user_list_chatbox.active').attr('id');
               var contactDetailId = $('#'+id).find('.ContactDetail_id').val();
               scrollUp = false;
               if(send == true){
                  var templatesent = false;
               }else{
                  var templatesent = true;
               }

               if($.trim(messageText) || send == true){    
               $.ajax({
                    url:"<?php echo base_url(); ?>WatiApi/sendMessage",
                            type:'post',
                            data:{token:wati_token,url:wati_url,us_id:us_id,phone:wp_number,messageText:messageText,template:template,templateName:templateName,user_name:user_name,contactDetailId:contactDetailId,templatesent:templatesent},
                    dataType:'json',
                    success:function(response){
                      console.log(response);
                      if(send != true){
                        getMessages();
                        var objDiv = document.getElementById("cust_scroll");
                        objDiv.scrollTop = objDiv.scrollHeight;
                        if(response.result == 'success' && template == 0){
                          $('#comment').val('').focus();
                        }else if(response.result == false){
                           toastr.warning(response.message);
                        } 
                       } 
                    }
                 }); 
              }else{
                $('#comment').focus();
              } 
          }
          $(document).on("click",'#reply-send-btn',function(){
              sendMessage();
              $("#reply-send-btn").prop('disabled',true);
               $("#comment").is(":focus")
          });

          $(document).on('keypress',function(e) {
               if(e.which == 13 && !e.shiftKey && $("#reply-send-btn").is(":disabled") == false && $("#comment").is(":focus")) {
                 e.preventDefault();
                 sendMessage();
                 $("#reply-send-btn").prop('disabled',true);
                 $('#comment').blur();
               }
          });
          function changeTicketStatus(){
            var id = $('.user_list_chatbox.active').attr('id');
            var contactDetailId = $('#'+id).find('.ContactDetail_id').val();
            //var contactDetailId = 8578;
            var newTicketStatus = $('#ticket_status_combobox').val();
             $.ajax({
                url:"<?php echo base_url(); ?>WatiApi/changeTicketStatus",
                        type:'post',
                        data:{us_id:us_id,contactDetailId:contactDetailId,newTicketStatus:newTicketStatus},
                dataType:'json',
                success:function(response){
                  if(response && response.transaction_status == 'ok'){
                     var contactlist = response.contactlist;
                     if(contactlist[0] && contactlist[0].result == 0){

                       toastr.info(contactlist[0].description);
                       $('#ticket_status_combobox').prop('selectedIndex',0);

                     }else if(contactlist[0] && contactlist[0].result == 1){
                            if(newTicketStatus == 2 || newTicketStatus == 3){
                             
                              $.each(contactlist,function(i,data){
                                templateName = data.template;
                                $('#user_name').val(data.user_name);
                                template = 1;
                                templateName = data.template;
                                sendMessage(true);
                               
                              });
                              if(newTicketStatus == 3){
                                $("#chat_screen").css('display','none');
                                $("#blank_screen").css('display','block');
                                wp_number = ''; 
                              }
                            }
                     
                     }
                  }else if(response.transaction_status == 'error'){
                     toastr.warning(response.Message);
                  } 

                }
             }); 
          }
          $(document).on("change",'#ticket_status_combobox',function(){
              changeTicketStatus();
          });
          
          var contactDetailRequest = null;
          function contactDetail(node){

            $('#dummyModal .modal-body,#mob_user_detail').html('<img src="<?php echo base_url().'assets/loader.gif' ?>" class="loader info-loader" style="display: none;position: relative;width: 50px;">');
            $('.info-loader').show();
            var contactDetailId = node.find('.ContactDetail_id').val();
            contactDetailRequest = $.ajax({
                url:"<?php echo base_url(); ?>WatiApi/contactDetail",
                        type:'post',
                        data:{us_id:us_id,contactDetailId:contactDetailId},
                dataType:'json',
                beforeSend:function(){
                  if(contactDetailRequest != null){
                    contactDetailRequest.abort();
                  }
                },
                success:function(response){
                  var html = '';
                  if(response && response.transaction_status == 'ok'){
                    html = '<div class="card" style="height: auto;"><ul class="list-group">';
                    if(response.contactlist){
                
                      $.each(response.contactlist,function(key,data){
                        var contactticketstatus = data.contactticketstatus;
                        $('#ticket_status_combobox option[value="'+contactticketstatus+'"]').prop('selected',true);

                        $.each(data,function(index,data){
                          if(index != 'contactticketstatus')
                            html += '<li class="list-group-item">'+index+' : <b>'+data+'</b></li>';
                        });
                      });
                    }
                    html += '</ul></div>';
                  }else if(response && response.transaction_status == 'error'){
                     toastr.warning(response.Message);
                  } 
                  $('#dummyModal .modal-body').html(html);
                  $('#mob_user_detail').html(html);
                }
             }); 
          }
          
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
                  $('#chat_box').html('');
                  window.location.reload();
          });

          $('.heading-name-meta,.heading-dot').click(function() {

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

          $(document).on('click','#comment',function(){
          }); 

   });


document.addEventListener('DOMContentLoaded', function() {
 if (!Notification) {
  console.log('Desktop notifications not available in your browser. Try Chromium.');
  return;
 }
 if (Notification.permission !== 'granted')
    Notification.requestPermission();
  });

function notifyMe(text) {

 if (Notification.permission !== 'granted')
  Notification.requestPermission();
 else {
   
   var notification = new Notification('New Whatsapp message', {
   
   body: text,
   icon: 'https://localizar-t.com.ar/assets/images/forza.png',
   image: 'https://localizar-t.com.ar/assets/images/forza.png'
  
  });

  notification.onclick = function() {
    window.open(base_url+'?us_id='+us_id+'&gmt='+gmt);
  };

  setTimeout(() => notification.close(), 3000);
 
 }
}

document.addEventListener("visibilitychange", onchange);

(function() {
  var hidden = "hidden";

  // Standards:
  if (hidden in document)
    document.addEventListener("visibilitychange", onchange);
  else if ((hidden = "mozHidden") in document)
    document.addEventListener("mozvisibilitychange", onchange);
  else if ((hidden = "webkitHidden") in document)
    document.addEventListener("webkitvisibilitychange", onchange);
  else if ((hidden = "msHidden") in document)
    document.addEventListener("msvisibilitychange", onchange);
  // IE 9 and lower:
  else if ("onfocusin" in document)
    document.onfocusin = document.onfocusout = onchange;
  // All others:
  else
    window.onpageshow = window.onpagehide
    = window.onfocus = window.onblur = onchange;

  function onchange (evt) {
    var v = "visible", h = "hidden",
        evtMap = {
          focus:v, focusin:v, pageshow:v, blur:h, focusout:h, pagehide:h
        };

    evt = evt || window.event;
    if (evt.type in evtMap)
      document.body.className = evtMap[evt.type];
    else
      document.body.className = this[hidden] ? "hidden" : "visible";
  }

  // set the initial state (but only if browser supports the Page Visibility API)
  if( document[hidden] !== undefined )
    onchange({type: document[hidden] ? "blur" : "focus"});
})();

function iamhere() {
   $.ajax({
        url:"<?php echo base_url(); ?>WatiApi/iamhere",
                type:'post',
                data:{us_id:us_id},
                dataType:'json',
        success:function(response){
          if(response && response.transaction_status == 'ok'){

          }
       }   
     }); 
}
</script>
