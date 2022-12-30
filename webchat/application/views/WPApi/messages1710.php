<?php //echo '<pre>';print_r($messages);echo '</pre>'; ?>
<?php 
  if($messages->transaction_status == 'ok'){
    $total_pages = !empty($messages->link->total) ? $messages->link->total : 0 ;
  ?>  
    <input type="hidden" class="total_pages" value="<?php echo $total_pages ?>">
  <?php }elseif($messages->transaction_status == 'error'){
    //die($messages->Message);
    die;
  }
  ?>
<?php
  if(!empty($messages->getmessages)){
    $items = $messages->getmessages;

    if(!empty($prev_messages) && !empty($prev_messages->getmessages)){
      $prev_items =  json_decode(json_encode($prev_messages->getmessages),true);
      $prev_items_ids = !empty($prev_items) ? array_column($prev_items, 'wi_post') : '';
      if(!empty($prev_items_ids)){
        foreach ($prev_items_ids as $key => $prev_items_id) {
          $prev_items_ids1[] = json_decode($prev_items_id,true);
        }
        $prev_items_ids = array_column($prev_items_ids1,'id');
      }
      //echo '<pre>';print_r($prev_items_ids);echo '</pre>'; 
    }
   
      foreach (array_reverse($items) as $key => $item) {
            $item = json_decode($item->wi_post);
            if(!empty($item) && $page_number == 1 && $refresh == 1 && !empty($prev_items) && !empty($prev_items_ids)){
                $item_id = $item->id;
                $skey = array_search($item_id, $prev_items_ids);
                if($skey !== false) continue;
            }

            if(!empty($item) && !empty($item->eventType) && $item->eventType == 'message' || $item->eventType == 'sessionMessageSent') 
            { 
              //echo '<pre>';print_r($item);echo '</pre>';
              //echo 'owner='.var_dump($item->owner);
              if($item->owner == true) 
              { 
                     
                ?>
                    <div id="<?php echo $item->id ?>" class="row message-body page-<?php echo $page_number; ?>" data-total="<?php echo $total_pages ?>">
                      <div class="col-sm-12 message-main-sender">
                        <div class="sender">
                            
                            <?php 
                             if($item->type == 'text'){ ?>
                              <div class="message-text">
                                <?php echo $item->text; ?>
                              </div>
                            <?php
                             }else{ ?>
                              <div class="message-media page-<?php echo $page_number; ?>">
                                 <img src="<?php echo base_url('assets/loader.gif') ?>" class="loader image-loader" style="display: none;"> 

                                <?php
                                /* if($item->type == 'image'){
                                  echo "<img src='".$item->data."' alt='image' class='example-image' alt='image-1' />";
                                }elseif($item->type == 'audio'){
                                  echo '<audio class="media_audio" controls="controls"><source src="'.$item->data.'" type="audio/ogg"><source src="'.$item->data.'" type="audio/mpeg">Your browser does not support the audio element.</audio>';
                                }elseif($item->type == 'video'){
                                  echo '<video width="320" height="240" controls><source src="'.$item->data.'" type="video/mp4 "><source src="'.$$item->data.'" type="video/ogg">Your browser does not support the video tag.</video>';
                                }else{

                                 echo '<a class="link" href="'.$item->data.'" download>descargar archivo</a>';
                                }*/ 
                                
                                  $fileurl = $item->data;
                                  $url_components = parse_url($fileurl);
                                  parse_str($url_components['query'], $params); 
                                  
                                ?>
                                <input type="hidden" name="filename" class="wati_media" value="<?php echo $params['fileName'] ?>">
                                <input type="hidden" name="filename" class="wati_media_filetype" value="<?php echo $item->type ?>">  
                                  <span class="media_file"></span>
                              </div>
                            <?php }    
                            ?>
                              
                          <span class="message-time pull-right">
                           <?php
                            if(!empty($gmt)){
                              echo gmdate("d/m/Y h:i A",$item->timestamp + 3600*($gmt));
                            }else{ 
                              echo date('d/m/Y h:i A',$item->timestamp);
                             }  ?>  
                            </span>
                        </div>
                      </div>
                    </div>

              <?php 
                
                
              }else{ ?>
                    <div id="<?php echo $item->id ?>" class="row message-body page-<?php echo $page_number; ?>">
                        <div class="col-sm-12 message-main-receiver">
                          <div class="receiver">
                           <?php 
                             if($item->type == 'text'){ ?>
                              <div class="message-text">
                                <?php echo $item->text; ?>
                              </div>
                              <?php
                               }else{ ?>
                                <div class="message-media hasNoFile page-<?php echo $page_number; ?>">
                                   <img src="<?php echo base_url('assets/loader.gif') ?>" class="loader image-loader" style="display: none;">   
                                  <?php 
                                    /*if($item->type == 'image'){
                                     echo "<img src='".$item->data."' alt='image' class='example-image' alt='image-1' />";
                                    }elseif($item->type == 'audio'){
                                      echo '<audio class="media_audio" controls="controls"><source src="'.$item->data.'" type="audio/ogg"><source src="'.$item->data.'" type="audio/mpeg">Your browser does not support the audio element.</audio>';
                                    }elseif($item->type == 'video'){
                                      echo '<video width="320" height="240" controls><source src="'.$item->data.'" type="video/mp4 "><source src="'.$item->data.'" type="video/ogg">Your browser does not support the video tag.</video>';
                                    }else{

                                      echo '<a class="link" href="'.$item->data.'" download>descargar archivo</a>';
                                    } */
                                    $fileurl = $item->data;
                                    $url_components = parse_url($fileurl);
                                    parse_str($url_components['query'], $params); 
                                  ?>

                                  <input type="hidden" class="wati_media" value="<?php echo $params['fileName'] ?>"> 
                                  <input type="hidden" name="filename" class="wati_media_filetype" value="<?php echo $item->type ?>">   
                                    <span class="media_file"></span>
                                </div>
                              <?php }    
                              ?>
                              <span class="message-time pull-right">
                                <?php
                                  if(!empty($gmt)){
                                    echo gmdate("d/m/Y h:i A",$item->timestamp + 3600*($gmt));
                                  }else{ 
                                    echo date('d/m/Y h:i A',$item->timestamp);
                                   }  ?>  
                                </span>
                           </div>
                        </div>
                    </div> 

              <?php }  ?>
                 
              
              <?php
                  }elseif(!empty($item) && !empty($item->eventType) && $item->eventType == 'sentMessageDELIVERED' || $item->eventType == 'sentMessageREAD'){ ?>
                          <div id="<?php echo $item->id ?>" class="row message-body page-<?php echo $page_number; ?>">
                        <div class="col-sm-12 message-main-sender">
                          <div class="sender">
                           <?php 
                             if($item->type == 'text'){ ?>
                              <div class="message-text">
                                <?php echo $item->text; ?>
                              </div>
                              <?php
                               }else{ ?>
                                <div class="message-media hasNoFile page-<?php echo $page_number; ?>">
                                   <img src="<?php echo base_url('assets/loader.gif') ?>" class="loader image-loader" style="display: none;">   
                                  <?php 
                                   
                                    $fileurl = $item->data;
                                    $url_components = parse_url($fileurl);
                                    parse_str($url_components['query'], $params); 
                                  ?>

                                  <input type="hidden" class="wati_media" value="<?php echo $params['fileName'] ?>"> 
                                  <input type="hidden" name="filename" class="wati_media_filetype" value="<?php echo $item->type ?>">   
                                    <span class="media_file"></span>
                                </div>
                              <?php }    
                              ?>
                              <span class="message-time pull-right">
                                <?php
                                  if(!empty($gmt)){
                                    echo gmdate("d/m/Y h:i A",$item->timestamp + 3600*($gmt));
                                  }else{ 
                                    echo date('d/m/Y h:i A',$item->timestamp);
                                   }  ?>  
                                </span>
                           </div>
                        </div>
                    </div> 

             <?php }elseif(!empty($item) && !empty($item->eventType) && $item->eventType=='broadcastMessage' || $item->eventType == 'templateMessageSent')
                  { 
                    //echo '<pre>';print_r($item);echo '</pre>';
              ?>
                <div id="<?php echo $item->id ?>" class="row message-body page-<?php echo $page_number; ?>">
                  <div class="col-sm-12 message-main-sender">
                    <div class="sender">
                     <?php 
                       if($item->finalText){ ?>
                        <div class="message-text">
                          <?php echo $item->finalText; ?>
                        </div>
                      <?php }elseif($item->text){
                        echo '<div class="message-text">'.$item->text.'</div>';
                      } ?>
                        <span class="message-time pull-right">
                          <?php
                          if(!empty($gmt)){
                            echo gmdate("d/m/Y h:i A", strtotime($item->created) + 3600*($gmt));
                          }else{ 
                            echo date('d/m/Y h:i A',strtotime($item->created));
                           }  ?>
                          </span>
                       
                          
                     </div>
                  </div>
                </div>       
              <?php
              }elseif(!empty($item) && !empty($item->eventType) && $item->eventType=='ticket')
              
              {
                
              ?>
               <div id="<?php echo $item->id ?>" class="row message-body page-<?php echo $page_number; ?>" style="display: none;">
                  <div class="col-sm-12 message-main-receiver">
                    <div class="receiver">
                     <?php 
                       if($item->eventDescription){ ?>
                        <div class="message-text">
                          <?php echo $item->eventDescription; ?>
                        </div>
                        <span class="message-time pull-right">
                          <?php
                          if(!empty($gmt)){
                            echo gmdate("d/m/Y h:i A", strtotime($item->created) + 3600*($gmt));
                          }else{ 
                            echo date('d/m/Y h:i A',strtotime($item->created));
                           }  ?>
                          </span>
                        <?php
                         } ?>
                          
                     </div>
                  </div>
                </div>       
        <?php  }
           
        } // foreach

       
      }// !empty()
    ?>