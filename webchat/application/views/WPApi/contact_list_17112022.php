<?php 
	//echo '<pre>';print_r($contacts);echo '</pre>';
	if($contacts->transaction_status == 'error'){
		
		$playsound = !empty($contacts->sound) && !empty($contacts->sound[0]) ? $contacts->sound[0]->playsound : '';
		
		echo '<input type="hidden" value="'.$playsound.'" id="notfiy">';

		die('<div class="alert">'.$contacts->Message.'</div>');
		
	}

	$areyouthere = !empty($contacts->areyouthere) && !empty($contacts->areyouthere[0]) ? $contacts->areyouthere[0]->areyouthere : '';

	echo '<input type="hidden" value="'.$areyouthere.'" id="areyouthere">';

	$total_unread_messages = !empty($contacts->total_unread_messages) && !empty($contacts->total_unread_messages[0]) ? $contacts->total_unread_messages[0]->unread : '';

	echo '<input type="hidden" value="'.$total_unread_messages.'" id="total_unread_messages">';

	$prev_contactlist = !empty($prev_contacts) && !empty($prev_contacts->contactlist) ? $prev_contacts->contactlist : '';
	$notfiy = 0;

	$messagePreview = '';	
	if(!empty($contacts->contactlist)){
		foreach ($contacts->contactlist as $key => $contact) { ?>
			<div class="row sideBar-body user_list_chatbox" id="contact-<?php echo $contact->ContactPhoneNumber ?>">
			   <div class="col-sm-3 col-xs-3 sideBar-avatar">
			      <input type="hidden" class="wp_number" value="<?php echo $contact->ContactPhoneNumber ?>">
			      <input type="hidden" class="ContactDetail_id" value="<?php echo $contact->ContactDetail_id ?>">
			      <div class="user-icon"><span><?php echo !empty($contact->Name) ? substr($contact->Name,0,1) : ''; ?></span></div>
			   </div>
			   <div class="col-sm-9 col-xs-9 sideBar-main">
			      <div class="row">
			         <div class="col-sm-9 col-xs-9 sideBar-name">
			         	<span class="name-meta text-over"><?php echo $contact->Name ?></span><br/>
			         	<?php if($contact->FriendlyLastUpdated){ ?>
			         		<span class="lastUpdated"><?php echo $contact->FriendlyLastUpdated ?></span><br />
			         	<?php } ?>
			         	<input type="hidden" value="<?php echo $contact->LastUpdated ?>">
			         	<?php if($contact->MessagePreview){ ?>
			         		<span class="short_msg text-over"><?php echo $contact->MessagePreview ?></span>
			         	<?php } ?>
			         </div>
			         <?php if(!empty($contact->UnreadMessage)){ ?>
			         <div class="col-sm-3 col-xs-3 unread text-right">
			         	<span class="badge badge-success"><?php echo $contact->UnreadMessage ?></span>
			         </div>
				 	<?php }?>			

			      </div>
			   </div>
		   </div>
<?php		//echo 'New LastUpdated '.$contact->LastUpdated;
			if(!empty($prev_contactlist)){
				foreach ($prev_contactlist as $key => $prev_contact) {
					
					if($contact->ContactPhoneNumber == $prev_contact->ContactPhoneNumber){
						//echo "found ContactPhoneNumber $contact->LastUpdated  $prev_contact->LastUpdated <br/>";
						if($contact->LastUpdated != $prev_contact->LastUpdated){
							$notfiy = $notfiy+1;
							$messagePreview = $contact->MessagePreview;
							//echo 'date changed notfiy found';	
						}else if($contact->UnreadMessage > $prev_contact->UnreadMessage){
							$messagePreview = $contact->MessagePreview;
							$notfiy = $notfiy+1;
							//echo 'UnreadMessage notfiy not found';
						}
						else{
							//echo 'notfiy not found';
						}
					 	//echo '<br/>';
				 	 	//echo 'prev LastUpdated '.$prev_contact->LastUpdated;
					 }
				}              
			}

		}// For each

		echo '<input type="hidden" value="'.$notfiy.'" id="notfiy">';
		echo '<input type="hidden" value="'.$messagePreview.'" id="messagePreview">';
	}
?>
