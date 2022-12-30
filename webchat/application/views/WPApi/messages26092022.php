<?php //echo '<pre>';print_r($messages);echo '</pre>'; ?>
<?php 
  if($messages->result == 'success'){
    $total_pages = !empty($messages->link->total) ? $messages->link->total : 0 ;
  ?>  
    <input type="hidden" class="total_pages" value="<?php echo $total_pages ?>">
  <?php }else{
    die($messages->info);
  }
  ?>
<?php
  if(!empty($messages->messages)){
    $items = $messages->messages->items;

    if(!empty($prev_messages) && !empty($prev_messages->messages)){
      $prev_items =  json_decode(json_encode($prev_messages->messages->items),true);
      $prev_items_ids = !empty($prev_items) ? array_column($prev_items, 'id') : '';
       //echo '<pre>';print_r($prev_items_ids);echo '</pre>'; 
    }
   
      foreach (array_reverse($items) as $key => $item) {

      
            if($page_number == 1 && $refresh == 1 && !empty($prev_items) && !empty($prev_items_ids)){
                $skey = array_search($item->id, $prev_items_ids);
                if($skey !== false) continue;
            }

            if(!empty($item->eventType) && $item->eventType == 'message') 
            { 
            
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
                                <input type="hidden" name="filename" class="wati_media" value="<?php echo $item->data ?>">
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
                                  <input type="hidden" class="wati_media" value="<?php echo $item->data ?>"> 
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
                  }elseif(!empty($item->eventType) && $item->eventType=='broadcastMessage')
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
              <?php
              }elseif(!empty($item->eventType) && $item->eventType=='ticket')
              
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