<?php
/*
 * Content Manager Module
 * Version: 1.1.28
 * SVN: 28
 * @version $Rev: 22 $
 * $LastChangedBy: feljun $
 * $LastChangedDate: 2015-02-12 11:26:05 -0500 (Thu, 12 Feb 2015) $
 *
 */
// Module Information
$CCModule=array(
    'ClassName'=>'ContentManager', //should the same with your class name
	'Name'=>'WL Content Manager',
	'URL'=>'',
    'Version'=>'1.1.28',
	'Description'=>'WL Content Manager gives you the ability to have greater control over your content by deleting it on a specific date, moving it to different category, repeating the post or reposting it on a given date. The goal with this module is to help you automate the ongoing "management" of your content.',
	'Author'=>'WishList Products',
	'AuthorURL'=>'http://www.wishlistproducts.com/',
	'File'=>__FILE__
);
if(!class_exists('ContentManager')){
    /**
     * Content Archiver Core Class
     */
    class ContentManager{
//settings page
        function DashboardPage($page,$wlcc){
            if($page!=get_class($this))return false;
            if($wlcc->LicenseStatus(get_class($this)) != '1'){
                $this->WPWLKey();return false;
            }
            $wlcccmerror_msg = array();
            $data = array();
            $isupdate = false;

            $mode = $_GET['mode'];
            $mode = $mode==''? 'set':$mode;
            $ptype = isset($_GET['ptype']) ? $_GET['ptype']:"post";

            $show_post = $_POST['show_post'];
            $show_poststat = isset($_POST['show_post_stat']) ? $_POST['show_post_stat']:$_GET['show_post_stat'];
            $show_poststat = $show_poststat == "" ? "publish":$show_poststat;

            $sort = isset($_POST['sort']) ? $_POST['sort']:$_GET['sort'];
            $sort = $sort == "" ? "ID":$sort;
            $asc = isset($_POST['asc']) ? $_POST['asc']:$_GET['asc'];
            $asc = $asc == "" ? 1:$asc;

            $wlcm_mode = $_SESSION['wlcmmode'];
            $mode =  ( $wlcm_mode == "set" || $wlcm_mode == "move" || $wlcm_mode == "repost") ? $wlcm_mode : $mode;

            $custom_types = get_post_types(array('public'=> true,"_builtin"=>false),"objects");
            $ptypes_array = array("post"=>"Posts","page"=>"Pages");
            foreach($custom_types as $t=>$ctype){
                $ptypes_array[$t]= $ctype->labels->name;
            }

            $wlcm_ptype = $_SESSION['wlcmptype'];
            $ptype =  array_key_exists( $wlcm_ptype, $ptypes_array) ? $wlcm_ptype : $ptype;
            $ptype = $wlcm_mode != "set" ? "post" : $ptype;
        ?>
            <ul class="wlcc-sub-menu">
                        <li <?php echo ($mode=='set' || $mode=='')?'class="current"':''; ?> ><a href="?page=ContentControl&module=ContentManager&mode=set"><?php _e('Set Posts Status','wl-contentcontrol'); ?></a></li>
                        <li <?php echo $mode=='move'?'class="current"':''; ?> ><a href="?page=ContentControl&module=ContentManager&mode=move"><?php _e('Add/Move Posts','wl-contentcontrol'); ?></a></li>
                        <li <?php echo $mode=='repost'?'class="current"':''; ?> ><a href="?page=ContentControl&module=ContentManager&mode=repost"><?php _e('Repost','wl-contentcontrol'); ?></a></li>
            </ul>
                <?php if ( $mode == "set" ) : ?>
                    <ul class="wlcc-sub-menu">
                    <?php foreach($ptypes_array as $m=>$name): ?>
                        <li <?php echo ( $ptype == $m || $ptype=='') ? 'class="current"':''; ?> ><a href="?page=ContentControl&module=ContentManager&mode=set&ptype=<?php echo $m; ?>"><?php _e($name,'wl-contentcontrol'); ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p style="text-align:right;"><em>Only Posts are allowed for this operation.</em></p>
                <?php endif; ?>
            <h2><?php _e("WishList Content Manager (<i>{$ptypes_array[$ptype]}</i>)",'wl-contentcontrol'); ?></h2>
                <?php
                    if($mode=='move')$overviewtxt = "Add/Move";
                    if($mode=='set')$overviewtxt = "Set Posts Status";
                    if($mode=='repost')$overviewtxt = "Repost ";
                ?>
                <a id="overviewlnk" href="javascript:void(0);" onclick="wlcc_show('overview','false')" ><?php  echo $overviewtxt; ?> Overview</a>
                <div id="overviewid" style="display:none;"><blockquote>
                      <?php
                            $overview['move'] = "Posts can be set to be ADDED or MOVED into new Categories based on a specifically set time and date.";
                            $overview['set'] = "Post status can be set to take on the standard WordPress settings (Published, Draft, Pending Review, Trash) at a specifically set time and date.";
                            $overview['repost'] = "Posts can be set to be Reposted onto the site based on a specifically set time and date.";
                      ?>
                     <p><?php echo $overview[$mode]; ?></p>
                    <p><a href="javascript:void(0);" onclick="wlcc_show('overview','true')" >Hide Overview</a></p>
                    </blockquote>
                </div>

                <form  name="post_manager" id="post_manager" action="?page=ContentControl&module=ContentManager&mode=<?php echo $mode; ?>" method="post">
            <?php
                        //default date
                        $wlccduedate = date_parse(date('Y-m-d H:i:s'));
                        $datenow = date('Y-m-d H:i:s',mktime(0,0,0,(int)$wlccduedate["month"],(int)$wlccduedate["day"],(int)$wlccduedate["year"]));

                if(isset($_POST['apply_due'])){
                    $post_ids = $_POST['post_id'];
                    $wlcc_duedate = $_POST['wlcc_duedate'];
                    $wlcccm_addmove = $_POST['wlcccm_addmove'];
                    $wlcccm_status = $_POST['wlcccm_status'];
                    $wlcccm_repeat = $_POST['wlcccm_repeat'];
                    $wlcccm_every = $_POST['wlcccm_every'];
                    $wlcccm_repeat_end = $_POST['wlcccm_repeat_end'];

                    if(count($post_ids) <= 0){
                        $wlcccmerror_msg[0] = __('No post selected.','wishlist-member');
                    }
                    for($i=0;$i<count($wlcc_duedate);$i++){
                        $duedate = ($wlcc_duedate[$i] == "" ? $datenow:$wlcc_duedate[$i]);
                        $wlccduedate = date_parse($duedate);
                        $date = date('Y-m-d H:i:s',mktime((int)$wlccduedate["hour"],(int)$wlccduedate["minute"],0,(int)$wlccduedate["month"],(int)$wlccduedate["day"],(int)$wlccduedate["year"]));
                        if(!$this->isvalid_date($date)){
                            $wlcccmerror_msg[] =  __('Due Date is invalid ('.$this->format_date($date).'). The Date selected has already passed. Please select a Date in the future.','wishlist-member') ;
                        }
                        $datum = array();
                        if($mode == 'move'){
                            $catname = 'wlcccm_cats' .$i;
                            $wlcccm_cats = $_POST[$catname];
                            if(count($wlcccm_cats) <= 0){
                                $wlcccmerror_msg[] = "" .$this->format_date($date) ." has no Categories selected. Please select at least 1 category where to " .$wlcccm_addmove[$i] ." the posts.";
                            }else{
                                $cats = implode('#',$wlcccm_cats);
                            }
                            $datum = array('action'=>'move','date'=>$date,'cats'=>$cats,'method'=>$wlcccm_addmove[$i]);
                        }else if($mode == 'repost'){
                            $repeat = 0;
                            if(!$this->validateint($wlcccm_repeat[$i])){
                                $wlcccmerror_msg[] = "Invalid value for the number of " .$wlcccm_every[$i];
                                $repeat = $wlcccm_repeat[$i];
                            }else{
                                $repeat = (int)$wlcccm_repeat[$i];
                                $repeat = $repeat<0? 0:$repeat;
                            }
                            if(!$this->validateint($wlcccm_repeat_end[$i])){
                                $wlcccmerror_msg[] = "Invalid value for the number of repetition.";
                                $repeat_end = $wlcccm_repeat_end[$i];
                            }else{
                                $repeat_end = (int)$wlcccm_repeat_end[$i];
                                $repeat_end = $repeat_end<0? 0:$repeat_end;
                            }
                            $datum = array('action'=>'repost','date'=>$date,'rep_num'=>$repeat,'rep_by'=>$wlcccm_every[$i],'rep_end'=>$repeat_end);
                        }else if($mode == 'set'){
                            $datum = array('action'=>'set','date'=>$date,'status'=>$wlcccm_status[$i]);
                        }else{
                            $wlcccmerror_msg[] = "Invalid action.";
                        }
                        $data[$i] = (array)$datum;
                    }
                    if(count($wlcccmerror_msg) <= 0 && count($data) >0){
                        for($x=0;$x<count($post_ids);$x++){
                            foreach((array)$data as $key=>$datum){
                                $this->SavePostManagerDate($post_ids[$x],$datum);
                            }
                        }
                        unset($data);
                        echo "<div class='updated fade'>".__('<p>Due Date/s was successfully updated for the selected posts.</p>','wishlist-member')."</div>";
                    }else{
                        echo "<div class='error fade'>".__('<p>You have errors in your Due Date settings.</p>','wishlist-member')."<p>";
                        foreach((array)$wlcccmerror_msg as $key=>$err){
                            echo '- ' .$err .'<br />';
                        }
                        echo '</p></div>';
                        unset($wlcccmerror_msg);
                    }
                }else if(isset($_POST['update_due'])){ // update due date
                    $dueid = $_POST['duedate_id'];
                    $duedate_pid = $_POST['duedate_pid'];
                    $edit_post = get_post($duedate_pid);
                    $post_date = $edit_post->post_date;

                    $wlcc_duedate = $_POST['wlcc_duedate'];
                    $wlcccm_addmove = $_POST['wlcccm_addmove'];
                    $wlcccm_status = $_POST['wlcccm_status'];
                    $wlcccm_repeat = $_POST['wlcccm_repeat'];
                    $wlcccm_every = $_POST['wlcccm_every'];
                    $wlcccm_repeat_end = $_POST['wlcccm_repeat_end'];
                        $duedate = ($wlcc_duedate[0] == "" ? $duedate_date:$wlcc_duedate[0]);
                        $wlccduedate = date_parse($duedate);
                        $date = date('Y-m-d H:i:s',mktime((int)$wlccduedate["hour"],(int)$wlccduedate["minute"],0,(int)$wlccduedate["month"],(int)$wlccduedate["day"],(int)$wlccduedate["year"]));

                    if($dueid == "" || $post_date=="" || $duedate_pid=""){
                        $wlcccmerror_msg[] = "An error occured. Inconsistent parameters. Please try again.";
                    }
                    if(!$this->isvalid_date($date)){
                        $wlcccmerror_msg[] =  __('Due Date is invalid ('.$this->format_date($date).'). The Date selected has already passed. Please select a Date in the future.','wishlist-member') ;
                    }
                    $datum = array();
                    if(count($wlcccmerror_msg)<1){
                        if($mode == 'move'){
                            $catname = 'wlcccm_cats0';
                            $wlcccm_cats = $_POST[$catname];
                            if(count($wlcccm_cats) <= 0){
                                $wlcccmerror_msg[] = "No Categories selected. Please select at least 1 category where to " .$wlcccm_addmove[0] ." the post.";
                            }else{
                                $cats = implode('#',$wlcccm_cats);
                            }
                            $datum = array('action'=>'move','date'=>$date,'cats'=>$cats,'method'=>$wlcccm_addmove[0]);
                        }else if($mode == 'repost'){
                            $repeat = 0;
                            if(!$this->validateint($wlcccm_repeat[0])){
                                $wlcccmerror_msg[] = "Invalid value for the number of " .$wlcccm_every[0];
                                $repeat = $wlcccm_repeat[0];
                            }else{
                                $repeat = (int)$wlcccm_repeat[0];
                            }
                            if(!$this->validateint($wlcccm_repeat_end[0])){
                                $wlcccmerror_msg[] = "Invalid value for the number of repetition";
                                $repeat_end = $wlcccm_repeat_end[0];
                            }else{
                                $repeat_end = (int)$wlcccm_repeat_end[0];
                            }
                            $datum = array('action'=>'repost','date'=>$date,'rep_num'=>$repeat,'rep_by'=>$wlcccm_every[0],'rep_end'=>$repeat_end);
                        }else if($mode == 'set'){
                            $datum = array('action'=>'set','date'=>$date,'status'=>$wlcccm_status[0]);
                        }
                    }
                    if(count($wlcccmerror_msg) <= 0 && count($datum) >0){
                        $this->UpdatePostManagerDate($dueid,$datum);
                        $isupdate = false;
                        unset($data);
                        echo "<div class='updated fade'>".__('<p>Due Date/s was successfully updated for the selected posts.</p>','wishlist-member')."</div>";
                    }else{
                        echo "<div class='error fade'>".__('<p>You have errors in your Due Date settings.</p>','wishlist-member')."<p>";
                        foreach((array)$wlcccmerror_msg as $key=>$err){
                            echo '- ' .$err .'<br />';
                        }
                        echo '</p></div>';
                        unset($wlcccmerror_msg);
                          //if theres and error retrieve the data to be edited again
                          $post_due_dates_update= $this->GetPostManagerDate($mode,'',$dueid);
                          if(count($post_due_dates_update)>0){
                            $post_ids = array($post_due_dates_update[0]->post_id);
                            $data[0]['action'] = $mode;
                            foreach($post_due_dates_update[0] as $key=>$d){
                                $key = ($key == "due_date"? "date":$key);
                                $key = ($key == "categories"? "cats":$key);
                                $key = ($key == "action"? "method":$key);
                                $data[0][$key] = $d;
                            }
                            $isupdate = true;
                          }
                    }
                }else if(isset($_GET['dueid']) && $_GET['action']== "remove"){  // remove due date
                    $dueid = $_GET['dueid'];
                    if($mode == "set" || $mode == "move" || $mode == "repost"){
                       $this->DeletePostManagerDate($dueid,$mode);
                       echo '<div class="updated fade"><p>Due date was successfully removed from the post.</p></div>';
                    }
                }else if(isset($_GET['pid']) && $_GET['action'] == "removeall"){ // remove all due date
                    $pid = $_GET['pid'];
                    if($mode == "set" || $mode == "move" || $mode == "repost"){
                       $post_due_dates_post = $this->GetPostManagerDate($mode,$pid);
                       foreach($post_due_dates_post as $res){
                            $this->DeletePostManagerDate($res->id,$mode);
                       }
                       echo '<div class="updated fade"><p>Due dates were successfully removed from the post.</p></div>';
                    }
                }else if(isset($_GET['dueid']) && $_GET['action'] == "update"){ // update due date
                    $dueid = $_GET['dueid'];
                    if($mode == "set" || $mode == "move" || $mode == "repost"){
                          $pid = $_GET['dueid'];
                          $post_due_dates_update= $this->GetPostManagerDate($mode,'',$dueid);
                          if(count($post_due_dates_update)>0){
                            $post_ids = array($post_due_dates_update[0]->post_id);
                            $data[0]['action'] = $mode;
                            foreach($post_due_dates_update[0] as $key=>$d){
                                $key = ($key == "due_date"? "date":$key);
                                $key = ($key == "categories"? "cats":$key);
                                $key = ($key == "action"? "method":$key);
                                $data[0][$key] = $d;
                            }
                            $isupdate = true;
                            echo '<div class="updated fade"><p>You can now edit the due date for the post below.</p></div>';
                          }
                    }
                }

                    $wlcm_showpost = $_SESSION['wlcmshowpost'];
                    $show_post =  ($wlcm_showpost == "all" || $wlcm_showpost == "due") ? $wlcm_showpost : $show_post;
                    $all = $show_post=='due' ? false:true;
                    $wlcm_showpoststat = $_SESSION['wlcmshowpoststat'];
                    $show_poststat =  ($wlcm_showpoststat == "all" || $wlcm_showpoststat == "publish" || $wlcm_showpoststat == "draft" || $wlcm_showpoststat == "pending" || $wlcm_showpoststat == "trash") ? $wlcm_showpoststat : $show_poststat;

                 /*variables for page numbers*/
                 if(isset($_POST['update_due']) || isset($_POST['apply_due'])){
                    $pagenum = isset($_POST['pagenum']) ? $_POST['pagenum']:$_GET['pagenum'];
                 }else{
                   $pagenum =$_GET['pagenum'];
                 }
                 $pagenum = ($pagenum > 0) ? absint( $pagenum ) : 0;
                 if (empty($pagenum) )
                        $pagenum = 1;
                        $per_page = 20;
                        $start = ($pagenum == '' || $pagenum < 0) ? 0 : (($pagenum - 1) * $per_page);

                  $posts = $this->GetPosts($mode,$all,$show_poststat,$ptype,$start,$per_page,$sort,$asc);
                  $cn_posts = $this->GetPosts($mode,$all,$show_poststat,$ptype);
                  $posts_count = count($cn_posts);
                        /*Prepare pagination*/
                  $num_pages = ceil($posts_count / $per_page);
                  $link_arr = array('pagenum'=>'%#%','show_level'=>$show_level,'show_post'=>$show_post,'show_post_stat'=>$show_poststat,'sort'=>$sort,'asc'=>$asc);
                  $page_links = paginate_links( array(
                          'base' => add_query_arg($link_arr),
                          'format' => '',
                          'prev_text' => __('&laquo;'),
                          'next_text' => __('&raquo;'),
                          'total' => $num_pages,
                          'current' => $pagenum
                   ));
            ?>
                <input type="hidden" name="pagenum" value="<?php echo $pagenum ?>" />
                <input type="hidden" name="sort" value="<?php echo $sort ?>" />
                <input type="hidden" name="asc" value="<?php echo $asc ?>" />
                <p>
                <select name="show_post" id="show_post" onchange="document.post_manager.submit()">
                  <option value="all" <?php echo $show_post=='all'?'selected="true"':''; ?> >Show All Posts</option>
                  <option value="due" <?php echo $show_post=='due'?'selected="true"':''; ?> >Show Posts With Due Date Only</option>
                </select>
                <?php if($all): ?>
                <select name="show_post_stat" id="show_post_stat" onchange="document.post_manager.submit()">
                  <option value="all" <?php echo $show_poststat=='all'?'selected="true"':''; ?> >All</option>
                  <option value="publish" <?php echo $show_poststat=='publish'?'selected="true"':''; ?> >Published</option>
                  <option value="draft" <?php echo $show_poststat=='draft'?'selected="true"':''; ?> >Draft</option>
                  <option value="pending" <?php echo $show_poststat=='pending'?'selected="true"':''; ?> >Pending Review</option>
                  <option value="trash" <?php echo $show_poststat=='trash'?'selected="true"':''; ?> >Trash</option>
                </select>
                <?php endif; ?>
                </p>
                <?php
                /*
                 * IF there are no post on the current selection
                 * Dont show the table and settings
                 */
                if(count($posts) > 0): ?>

                <div class="tablenav"><strong><?php  echo $overviewtxt; ?></strong><div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                        number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                        number_format_i18n( min( $pagenum * $per_page, $posts_count ) ),
                        number_format_i18n( $posts_count ),
                        $page_links
                ); echo $page_links_text; ?></div></div>
                <script type="text/javascript">
                    function wlccm_confirm_remove(lnk,all){
                        if(all){
                            remove_msg = "Are sure you want to remove the due dates of the post?";
                        }else{
                            remove_msg = "Are sure you want to remove this due date?";
                        }
                        if(confirm(remove_msg)){
                            window.location.href = lnk;
                        }
                    }
                </script>
                <table class="widefat" id='wlcc_postexpiry'>
                    <thead>
                    <tr>
                        <?php
                            $query_lnk = "page=ContentControl&module=ContentManager";
                            $query_lnk .= ($mode=="" ? "":("&mode=".$mode));
                            $query_lnk .= ($pagenum=="" ? "":("&pagenum=".$pagenum));
                            $query_lnk .= ($show_post=="" ? "":("&show_post=".$show_post));
                            $query_lnk .= ($show_poststat=="" ? "":("&show_post_stat=".$show_poststat));
                        ?>
                      <th  style="width:2%;" nowrap scope="col" class="check-column"><input type="checkbox" onClick="wpm_selectAll(this,'wpm_broadcast')" <?php echo $isupdate ? 'disabled="disabled"':''; ?> /></th>
                      <th style="width:30%;"><a href="?<?php echo $query_lnk; ?>&sort=post_title&asc=<?php echo ($sort =="post_title" && $asc == 1) ? 0:1; ?>"><?php _e('Post Title','wishlist-member-manager'); ?></a></th>
                      <th style="width:20%;" class="num"><?php _e('Category','wishlist-member-manager'); ?></th>
                      <th style="width:8%;" class="num"><?php _e('Author','wishlist-member-manager'); ?></th>
                      <th style="width:13%;" class="num"><a href="?<?php echo $query_lnk; ?>&sort=post_date&asc=<?php echo ($sort =="post_date" && $asc == 1) ? 0:1; ?>"><?php _e('Published Date','wishlist-member-manager'); ?></a></th>
                      <th style="width:25%;" class="num"><?php _e('Due Date','wishlist-member-manager'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
            <?php foreach((array)$posts as $sched_post):
                   //get all due dates associated to this post
                   $post_due_dates= $this->GetPostManagerDate($mode,$sched_post->ID);
            ?>
                        <tr class="<?php echo $alt++%2?'':'alternate'; ?>"  onMouseOver="wlcc_hideshow_menu('due-action-link-holder',<?php echo $sched_post->ID; ?>,'false')" onMouseOut="wlcc_hideshow_menu('due-action-link-holder',<?php echo $sched_post->ID; ?>,'true')">
                            <th scope="row" class="check-column"><input type="checkbox" name="post_id[]" value="<?php echo $sched_post->ID; ?>" <?php echo (in_array($sched_post->ID,(array)$post_ids) && count($data) > 0) ? 'checked="checked"':''; ?>  <?php echo $isupdate ? 'disabled="disabled"':''; ?> /></th>
                            <td ><strong ><a target="_blank" href="<?php echo admin_url().'post.php?post=' .$sched_post->ID .'&action=edit'; ?>">
                                <?php echo $sched_post->post_title; ?></a></strong>
                                <div class="due-action-holder" >
                                    <div style="display:none;" class="due-action-link-holder" id="due-action-link-holder<?php echo $sched_post->ID; ?>">
                                        <?php
                                            if($asc != "")$actionlnk = $query_lnk .($asc != ""? ("&asc=".$asc):"") .($asc != ""? ("&sort=".$sort):"");
                                            $lnk = "page=ContentControl&module=ContentManager";
                                        ?>
                                        <?php if(count($post_due_dates) >0): ?>
                                        <a onclick="wlccm_confirm_remove('?<?php echo $actionlnk ?>&action=removeall&pid=<?php echo $sched_post->ID; ?>',1)" href="javascript:void(0);">Remove All Due Date</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="num">
                                <?php
                                    $cats = get_the_category($sched_post->ID);
                                    $str = '';
                                    foreach((array)$cats as $catis=>$cat){
                                        if($catis < 3){
                                            echo '<span style="text-decoration:underline;">'.$cat->name."</span>  ";
                                        }else{
                                            $tt .= '<span style="text-decoration:underline;">'.$cat->name."</span>  ";
                                        }
                                    }
                                    if($tt !=""){
                                        echo '<a id="mores'.$sched_post->ID.'lnk" href="javascript:void(0);" onclick="wlcc_show(\'mores'.$sched_post->ID.'\',\'false\')" >MORE...</a>';
                                        echo '<span style="display:none;" id="mores'.$sched_post->ID.'id">';
                                        echo $tt;
                                        echo '<a href="javascript:void(0);" onclick="wlcc_show(\'mores'.$sched_post->ID.'\',\'true\')" >HIDE</a></span>';
                                    }
                                ?>
                            </td>
                            <td class="num">
                                <?php
                                    $user_info = get_userdata($sched_post->post_author);
                                    echo $user_info->user_login;
                                ?>
                            </td>
                            <td class="num">
                                <?php echo $this->format_date($sched_post->post_date); ?>
                                <br />
                                <?php
                                    $stats = array('publish'=>'Published','pending'=>'Pending Review','draft'=>'Draft','trash'=>'Trash');
                                    echo $stats[$sched_post->post_status];
                                ?>
                            </td>
                            <td class="num">
            <?php
                                    //create remove link
                                    if($asc != "")$remove_update_lnk = $query_lnk .($asc != ""? ("&asc=".$asc):"") .($asc != ""? ("&sort=".$sort):"");
                                    if(count($post_due_dates) > 0){
                                        foreach($post_due_dates as $key=>$post_due){
                                            echo '<strong>' .$this->format_date($post_due->due_date) .'</strong> (';
                                            echo ' <a title="Remove Due Date" style="color:#333aa;" href="javascript:void(0);" onclick="wlccm_confirm_remove(\'?'.$remove_update_lnk.'&action=remove&dueid=' .$post_due->id.'\',0)">Remove</a> ';
                                            if($isupdate && $dueid == $post_due->id){
                                                echo "";
                                            }else{
                                                echo '| <a title="Edit Due Date" style="color:#333aa;" href="?'.$remove_update_lnk.'&action=update&dueid=' .$post_due->id .'#updatetag" >Edit</a> ';
                                            }
                                            echo ")";
                                            if($mode == 'move'){
                                                 echo '<br /><strong>' .($post_due->action=='move'? 'Move':'Add') .'</strong> To: ';
                                                $cat = explode('#',$post_due->categories);
                                                $t = "";
                                                foreach((array)$cat AS $cati=>$c):
                                                    $category = get_term_by('id', $c, 'category');
                                                    if($cati < 3){
                                                        echo '<span style="text-decoration:underline;">'.$category->name."</span>  ";
                                                    }else{
                                                        $t .= '<span style="text-decoration:underline;">'.$category->name."</span>  ";
                                                    }
                                                endforeach;
                                                if($t !=""){
                                                    echo '<a id="more'.$key .$sched_post->ID.'lnk" href="javascript:void(0);" onclick="wlcc_show(\'more'.$key .$sched_post->ID.'\',\'false\')" >MORE...</a>';
                                                    echo '<span style="display:none;" id="more'.$key .$sched_post->ID.'id">';
                                                    echo $t;
                                                    echo '<a href="javascript:void(0);" onclick="wlcc_show(\'more'.$key .$sched_post->ID.'\',\'true\')" >HIDE</a></span>';
                                                }
                                                 echo $key < count($post_due_dates)-1 ? '<hr />':'';
                                            }else if($mode == 'set'){
                                                $stats = array('publish'=>'Published','pending'=>'Pending Review','draft'=>'Draft','trash'=>'Trash');
                                                echo '<br /> To: <strong>' .$stats[$post_due->status] .'</strong>';
                                                echo $key < count($post_due_dates)-1 ? '<hr />':'';
                                            }else if($mode == 'repost'){
                                                if($post_due->rep_num > 0){
                                                    $every = array('day'=>'Day/s','month'=>'Month/s','year'=>'Year');
                                                    echo '<br /> Repeat every '.$post_due->rep_num .' ' .$every[$post_due->rep_by];
                                                       $d1 = date_parse($post_due->due_date);
                                                       if($post_due->rep_by == 'day'){
                                                            $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],($d1['day']+$post_due->rep_num),$d1['year']);
                                                       }else if($post_due->rep_by == 'month'){
                                                            $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],($d1['month']+$post_due->rep_num),$d1['day'],$d1['year']);
                                                       }else if($post_due->rep_by == 'year'){
                                                           $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],($d1['year']+$post_due->rep_num));
                                                       }else{
                                                           $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],($d1['day']+$post_due->rep_num),$d1['year']);
                                                       }
                                                       echo $wlcc->Tooltip('wlcccm-repeatdate-tooltip' .$sched_post->ID .$post_due->id);
                                                       $tooltiptxt='<div style="display: none;"><div id="wlcccm-repeatdate-tooltip' .$sched_post->ID .$post_due->id.'">';
                                                       $tooltiptxt.='Next due date is <br /><strong>' .$this->format_date(date('Y-m-d H:i:s',$new_bue_date)) .'</strong>';
                                                       if($post_due->rep_end > 0){
                                                            $tooltiptxt.='<br />' .($post_due->rep_end -1) .' repetition/s left';
                                                       }else{
                                                           $tooltiptxt.='<br />No repetition limit.';
                                                       }
                                                       $tooltiptxt.='</div></div>';
                                                       echo $tooltiptxt;
                                                }else{
                                                    echo '<br />';
                                                }
                                                echo $key < count($post_due_dates)-1 ? '<hr />':'';
                                            }
                                         }
                                    }
            ?>
                            </td>
                        </tr >
            <?php endforeach; ?>
                     </tbody>
                </table>

                <div class="tablenav"><div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                        number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                        number_format_i18n( min( $pagenum * $per_page, $posts_count ) ),
                        number_format_i18n( $posts_count ),
                        $page_links
                ); echo $page_links_text; ?></div></div>
                        <?php if($isupdate && count($data)>0): //show this message when editing?>
                              <div id="updatetag" style="margin-bottom:10px;">
                               <?php
                                    $pid = $data[0]['post_id'];
                                    $edit_post = get_post($pid)
                               ?>
                                <div class="updated-clone"><p>You are currently editing due date for "<strong><?php echo $edit_post->post_title;?></strong>" which is set on <strong><?php echo $this->format_date($data[0]['date']); ?></strong>.</p></div>
                              </div>
                              <input type="hidden" name="duedate_id" value="<?php echo $data[0]['id'] ?>" />
                              <input type="hidden" name="duedate_pid" value="<?php echo $data[0]['post_id'] ?>" />
                        <?php else:  ?>
                            <p>Create <?php echo $overviewtxt; ?> Date of the post selected above.</p>
                        <?php endif; ?>
                          <p>Current Server Date and Time: <strong><?php echo $this->format_date(date('Y-m-d H:i:s'),'F j, Y g:i a'); ?></strong><?php echo $wlcc->Tooltip("wlcccm-serverdate-tooltip"); ?></p>
                          <div id="wlcclevels">
                            <table class="widefat" id='wlcc_tblpostexpiry'>
                                <thead>
                                    <tr style="width:100%;">
                                        <th style="width:30%;border-bottom: 1px solid #aaaaaa;">
                                                <?php
                                                        echo $mode == 'move'? 'Add/Move Post':'';
                                                        echo $mode == 'set'? 'Set Post Status':'';
                                                        echo $mode == 'repost'? 'Repost':'';
                                                ?>
                                        </th>
                                        <th style="width:30%;border-bottom: 1px solid #aaaaaa;">
                                            <?php echo ($isupdate && count($data)>0)?"New ":""; ?>Due Date
                                        </th>
                                        <th style="width:40%;border-bottom: 1px solid #aaaaaa;">
                                            <?php
                                                echo $mode == 'move'? 'Categories':'';
                                                echo $mode == 'set'? 'Status':'';
                                                echo $mode == 'repost'? ('Repeat ' .$wlcc->Tooltip("wlcccm-repeat-tooltip")):'';
                                            ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                               <?php if(count($data)>0): //if theres an error on saving retain the data?>
                                   <?php foreach((array)$data AS $cnt_pnt=>$datum):?>
                                            <tr style="width:100%;">
                                                 <td style="border-bottom: 1px solid #eeeeee;">&nbsp;
                                                 </td>
                                                 <td style="border-bottom: 1px solid #eeeeee;">
                                                     <input type="text" class="datepickerbox" id="wlcc_duedateid<?php echo $cnt_pnt; ?>" name="wlcc_duedate[]" value="<?php echo $this->format_date($datum['date'],'m/d/Y h:i A'); ?>" size="25" /><span class="datepicker-img" href="javascript:void(0);"><img src="<?php echo $wlcc->pluginurl.'/images/calendar.png'?>" /></span>
                                                 </td>
                                            <?php if($mode == 'move'): ?>
                                                <td style="border-bottom: 1px solid #eeeeee;">
                                                    <div>
                                                        <select name="wlcccm_addmove[]" style="float:left;">
                                                            <option value="move" <?php echo $datum['method'] == 'move'? 'selected="selected"':''; ?> >Move</option>
                                                            <option value="add" <?php echo $datum['method'] == 'add'? 'selected="selected"':''; ?> >Add</option>
                                                        </select><div style="float:left;padding:2px 2px 2px 2px;margin:0px 0px 5px 10px;width:200px; height:70px;overflow:auto;border:1px solid #eeeeee;background-color:#ffffff; ">
                                                         <?php
                                                            $cats = get_categories('hide_empty=0');
                                                            $categories = explode('#',$datum['cats']);
                                                            foreach((array)$cats AS $cats):
                                                         ?>
                                                        &nbsp;<input type='checkbox' name='wlcccm_cats<?php echo $cnt_pnt; ?>[<?php echo $cats->cat_ID; ?>]' value='<?php echo $cats->cat_ID; ?>' <?php echo in_array($cats->cat_ID, $categories)?'checked="checked"':'';?> /><label> <?php echo $cats->name; ?></label><br />
                                                         <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                    <?php if($cnt_pnt > 0){?><a onclick="deleterow(this);" class="removerow" href="javascript:void(0);" style="float:right;">remove</a><?php }?>
                                                </td>
                                            <?php elseif($mode == 'set'): ?>
                                                <td style="border-bottom: 1px solid #eeeeee;">
                                                        <select name="wlcccm_status[]">
                                                         <?php
                                                            $stats = array('publish'=>'Published','pending'=>'Pending Review','draft'=>'Draft','trash'=>'Trash');
                                                            foreach((array)$stats AS $key=>$value):
                                                         ?>
                                                                <option value="<?php echo $key; ?>" <?php echo $key == $datum['status'] ? 'selected="selected"':''; ?>><?php echo $value; ?></option>
                                                         <?php endforeach; ?>
                                                        </select>
                                                    <?php if($cnt_pnt > 0){?><a onclick="deleterow(this);" class="removerow" href="javascript:void(0);" style="float:right;">remove</a><?php }?>
                                                </td>
                                                <?php elseif($mode == 'repost'): ?>
                                                <td style="border-bottom: 1px solid #eeeeee;">
                                                    <div style="width:200px;overflow:auto;">
                                                        <div style="float:left;width:70px;">Every:</div>
                                                        <div style="float:left">
                                                           <input type="text" id="wlcccm_repeat" name="wlcccm_repeat[]" value="<?php echo $datum['rep_num'] > 0? $datum['rep_num']:0; ?>" size="3" />
                                                        </div>
                                                        <div style="float:left">
                                                            <select name="wlcccm_every[]" >
                                                                <option value="day" <?php echo $datum['rep_by'] == 'day'? 'selected="selected"':''; ?> >Day/s</option>
                                                                <option value="month" <?php echo $datum['rep_by'] == 'month'? 'selected="selected"':''; ?> >Month/s</option>
                                                                <option value="year" <?php echo $datum['rep_by'] == 'year'? 'selected="selected"':''; ?> >Year/s</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div style="width:200px;overflow:auto; margin-top: 5px">
                                                        <div style="float:left;width:70px;">Repetitions:</div>
                                                        <div style="float:left">
                                                            <input type="text" id="wlcccm_repeat_end" name="wlcccm_repeat_end[]" value="<?php echo $datum['rep_end'] > 0? $datum['rep_end']:0; ?>" size="3" />
                                                        </div>
                                                    </div>
                                                    <?php if($cnt_pnt > 0){?><a onclick="deleterow(this);" class="removerow" href="javascript:void(0);" style="float:right;">remove</a><?php }?>
                                                </td>
                                           <?php endif; ?>
                                            </tr>
                                   <?php endforeach; ?>
                               <?php else: //show normal settings table ?>
                                        <tr style="width:100%;">
                                            <td style="border-bottom: 1px solid #eeeeee;">&nbsp;
                                             </td>
                                             <td style="border-bottom: 1px solid #eeeeee;">
                                                 <input type="text" class="datepickerbox" id="wlcc_duedateid0" name="wlcc_duedate[]" value="<?php echo $this->format_date($datenow,'m/d/Y h:i A'); ?>" size="25" /><span class="datepicker-img" href="javascript:void(0);"><img src="<?php echo $wlcc->pluginurl.'/images/calendar.png'?>" /></span>
                                             </td>
                                        <?php if($mode == 'move'): ?>
                                            <td style="border-bottom: 1px solid #eeeeee;">
                                                <div>
                                                    <select name="wlcccm_addmove[]" style="float:left;">
                                                        <option value="move" >Move</option>
                                                        <option value="add" >Add</option>
                                                    </select><div style="float:left;padding:2px 2px 2px 2px;margin:0px 0px 5px 10px;width:200px; height:70px;overflow:auto;border:1px solid #eeeeee;background-color:#ffffff; ">
                                                     <?php
                                                        $cats = get_categories('hide_empty=0');
                                                        foreach((array)$cats AS $cats):
                                                     ?>
                                                    &nbsp;<input type='checkbox' name='wlcccm_cats0[<?php echo $cats->cat_ID; ?>]' value='<?php echo $cats->cat_ID; ?>' /><label > <?php echo $cats->name; ?></label><br />
                                                     <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        <?php elseif($mode == 'set'): ?>
                                            <td style="border-bottom: 1px solid #eeeeee;">
                                                    <select name="wlcccm_status[]">
                                                     <?php
                                                        $stats = array('publish'=>'Published','pending'=>'Pending Review','draft'=>'Draft','trash'=>'Trash');
                                                        foreach((array)$stats AS $key=>$value):
                                                     ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $key == 'publish' ? 'selected="selected"':''; ?>><?php echo $value; ?></option>
                                                     <?php endforeach; ?>
                                                    </select>
                                            </td>
                                            <?php elseif($mode == 'repost'): ?>
                                            <td style="border-bottom: 1px solid #eeeeee;">
                                                    <div style="width:200px;overflow:auto;">
                                                        <div style="float:left;width:70px;">Every:</div>
                                                        <div style="float:left">
                                                           <input type="text" id="wlcccm_repeat" name="wlcccm_repeat[]" value="0" size="3" />
                                                        </div>
                                                        <div style="float:left">
                                                            <select name="wlcccm_every[]" >
                                                                <option value="day" >Day/s</option>
                                                                <option value="month" >Month/s</option>
                                                                <option value="year" >Year/s</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div style="width:200px;overflow:auto; margin-top: 5px">
                                                        <div style="float:left;width:70px;">Repetitions:</div>
                                                        <div style="float:left">
                                                            <input type="text" id="wlcccm_repeat_end" name="wlcccm_repeat_end[]" value="0" size="3" />
                                                        </div>
                                                    </div>
                                            </td>
                                       <?php endif; ?>
                                        </tr>
                               <?php endif; ?>
                              </tbody>
                            </table>
                                <?php //variable for javascript
                                $txt .= '<tr style="width:100%;"><td style="border-bottom: 1px solid #eeeeee;">&nbsp;';
                                $txt .= '</td><td style="border-bottom: 1px solid #eeeeee;">';
                                $txt .= '<input type="text" class="datepickerbox" id="wlcc_duedateid" name="wlcc_duedate[]" value="' .$this->format_date($datenow,'m/d/Y h:i A') .'" size="25" /><span class="datepicker-img" href="javascript:void(0);"><img src="' .$wlcc->pluginurl .'/images/calendar.png" /></span></td>';
                                if($mode == 'move'):
                                    $txt .= '<td style="border-bottom: 1px solid #eeeeee;"><p>';
                                    $txt .= '<select name="wlcccm_addmove[]" style="float:left;">';
                                    $txt .= '<option value="move" >Move</option>';
                                    $txt .= '<option value="add" >Add</option>';
                                    $txt .= '</select><div style="float:left;padding:2px 2px 2px 2px;margin:0px 0px 5px 10px;width:200px; height:70px;overflow:auto;border:1px solid #eeeeee;background-color:#ffffff; ">';
                                                $cats = get_categories('hide_empty=0');
                                                foreach((array)$cats AS $cats):
                                                    $txt .= '&nbsp;<input type="checkbox" name="wlcccm_cats[' .$cats->cat_ID.']" value="'.$cats->cat_ID .'"/><label>'.$cats->name .'</label><br />';
                                                endforeach;
                                            $txt .= '</div></p><a onclick="deleterow(this);" class="removerow" href="javascript:void(0);" style="float:right;">remove</a></td>';
                                elseif($mode == 'set'):
                                    $txt .= '<td style="border-bottom: 1px solid #eeeeee;"><select name="wlcccm_status[]">';
                                                $stats = array('publish'=>'Published','pending'=>'Pending Review','draft'=>'Draft','trash'=>'Trash');
                                                foreach((array)$stats AS $key=>$value):
                                                    $txt .= '<option value="' .$key .'">'.$value .'</option>';
                                                endforeach;
                                            $txt .= '</select><a onclick="deleterow(this);" class="removerow" href="javascript:void(0);" style="float:right;">remove</a></td>';
                                elseif($mode == 'repost'):
                                    $txt .= '<td style="border-bottom: 1px solid #eeeeee;">';
                                    $txt .= '<div style="width:200px;overflow:auto;">';
                                    $txt .= '<div style="float:left;width:70px;">Every:</div>';
                                    $txt .= '<div style="float:left">';
                                    $txt .= '<input type="text" id="wlcccm_repeat" name="wlcccm_repeat[]" value="0" size="3" />';
                                    $txt .= '</div>';
                                    $txt .= '<div style="float:left">';
                                    $txt .= '<select name="wlcccm_every[]" >';
                                    $txt .= '<option value="day" >Day/s</option>';
                                    $txt .= '<option value="month" >Month/s</option>';
                                    $txt .= '<option value="year" >Year/s</option>';
                                    $txt .= '</select>';
                                    $txt .= '</div>';
                                    $txt .= '</div>';
                                    $txt .= '<div style="width:200px;overflow:auto; margin-top: 5px">';
                                    $txt .= '<div style="float:left;width:70px;">Repetitions:</div>';
                                    $txt .= '<div style="float:left">';
                                    $txt .= '<input type="text" id="wlcccm_repeat_end" name="wlcccm_repeat_end[]" value="0" size="3" />';
                                    $txt .= '</div>';
                                    $txt .= '</div>';
                                    $txt .= '<a onclick="deleterow(this);" class="removerow" href="javascript:void(0);" style="float:right;">remove</a></td>';
                                endif;
                                $txt .= '</tr>';
                             ?>
                             <script type="text/javascript">
                                  var row_cnt = <?php echo count($data)>0 ? (count($data)):1; ?>;
                                  var newrow = '<?php echo $txt;?>';
                                  var mode = '<?php echo $mode;?>';
                             </script>
                            </div>
               <?php if($isupdate && count($data)>0): //show this button when editing?>
                  <br />
                  <input type="submit" class="button-primary" name="update_due" id="update_due" value="<?php _e('Update Due Date','wishlist-member-sched'); ?>" />
                  <input type="submit" class="button-secondary" name="cancel_due" id="cancel_due" value="<?php _e('Cancel','wishlist-member-sched'); ?>" />
               <?php else: //show this message when editing?>
                   <div style="float:right;margin:2px;">
                       <a href="javascript:void(0);" class="addrow">Add Due Date</a>
                   </div>
                  <br />
                  <input type="submit" class="button-primary" name="apply_due" id="apply_due" value="<?php _e('Apply Values to Selected Post','wishlist-member-sched'); ?>" />
               <?php endif; ?>
             <?php
             /*
              *Show message that there is no posts to be listed on the current selection
              */
              else: ?>
                    <hr />
                    <?php if($show_post == "all"):
                        $stats = array('publish'=>'Published','pending'=>'Pending for Review','draft'=>'on Draft','trash'=>'on Trash');
                     ?>
                        <p style="text-align:center;">You currently have no posts <?php echo $stats[$show_poststat];?>.</p>
                    <?php else: ?>
                        <p style="text-align:center;">There are no <?php echo $ptypes_array[$ptype]; ?> to display.</p>
                    <?php  endif; ?>
                    <hr />
                <?php  endif; ?>
              <input type="hidden" name="frmsubmit" value="" />
              </form><br /><br />

        <?php
        }
//page options
        function ContentManagerOptions(){
            $post_id = $_GET['post'];
            $custom_types = get_post_types(array('public'=> true,"_builtin"=>false));
            $ptypes = array_merge(array("post","page"),$custom_types);
            $post_type = $post_id ? get_post_type($post_id):$_GET['post_type'];

            global $WishListMemberInstance,$WishListContentControl;
            //default date
            $wlccduedate = date_parse(date('Y-m-d H:i:s'));
            $wlccduedate = date('Y-m-d H:i:s',mktime(0,0,0,(int)$wlccduedate["month"],(int)$wlccduedate["day"],(int)$wlccduedate["year"]));
            $wlcc_duedate = $this->format_date($wlccduedate,'m/d/Y h:i A');
            $wlcc_error=$WishListMemberInstance->GetOption('wlcccm_error');
            if($wlcc_error != ""){
                echo $wlcc_error;
                $WishListMemberInstance->DeleteOption('wlcccm_error');
            }
        ?>
                <div class="inside">
                            <?php
                                $result_set = $this->GetPostManagerDate('set',$post_id);
                                if(count($result_set) <= 0){
                                    $status = "";
                                    $check = false;
                                    echo '<script>jQuery(document).ready(function(){disable_fields("wlcc_set");});</script>';
                                }else{
                                    $check = true;
                                }
                            ?>
                            <table class="widefat" id='wlcc_set' style="width:100%;text-align: left;" cellspacing="0">
                                <thead>
                                <tr style="width:100%;">
                                    <th style="width: 30%;border-bottom: 1px solid #aaaaaa;"><input class="wlcccm_chk_options_set"  style="float:left;vertical-align: bottom;" id="chkbxcm_set" type='checkbox' name="chkbxcm_set" value="set" <?php echo $check ? 'checked="checked"':''; ?> /><label  style="float:left;vertical-align: middle; margin-left: 4px;" for="chkbxcm_set"><?php _e(' Set Post Status','wl-contentcontrol'); ?></label></th>
                                    <th style="width: 30%;border-bottom: 1px solid #aaaaaa;text-align: center;"> <?php _e('Due Date'); ?> </th>
                                    <th style="width: 40%;border-bottom: 1px solid #aaaaaa;"> <?php _e('Status'); ?> </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if($check):?>
                                <?php foreach((array)$result_set AS $cnt_set=>$set_datum):?>
                                <tr style="width:100%;">
                                     <td style="border-bottom: 1px solid #eeeeee;">&nbsp;</td>
                                     <td style="border-bottom: 1px solid #eeeeee;text-align: center;">
                                         <input type="text" class="datepickerbox" id="wlcc_duedate_setid<?php echo $cnt_set;?>" name="wlcc_duedate_set[]" value="<?php echo $this->format_date($set_datum->due_date,'m/d/Y h:i A'); ?>" size="25" /><span class="datepicker-img" href="javascript:void(0);"><img src="<?php echo $WishListContentControl->pluginurl.'/images/calendar.png'?>" /></span>
                                     </td>
                                     <td style="border-bottom: 1px solid #eeeeee;">
                                        <select name="wlcccm_status[]">
                                             <?php
                                                $stats = array('publish'=>'Published','pending'=>'Pending for Review','draft'=>'Draft','trash'=>'Trash');
                                                foreach((array)$stats AS $key=>$value):
                                             ?>
                                                    <option value="<?php echo $key; ?>" <?php echo $key == $set_datum->status ? 'selected="selected"':''; ?>><?php echo $value; ?></option>
                                             <?php endforeach; ?>
                                            </select>
                                         <?php if($cnt_set > 0){?><a onclick="deleterowset(this);" class="removerowset" href="javascript:void(0);" style="float:right;">remove</a><?php }?>
                                     </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else:?>
                                <tr style="width:100%;">
                                     <td style="border-bottom: 1px solid #eeeeee;">&nbsp;</td>
                                     <td style="border-bottom: 1px solid #eeeeee;text-align: center;">
                                         <input type="text" class="datepickerbox" id="wlcc_duedate_setid0" name="wlcc_duedate_set[]" value="<?php echo $wlcc_duedate; ?>" size="25" /><span class="datepicker-img" href="javascript:void(0);"><img src="<?php echo $WishListContentControl->pluginurl.'/images/calendar.png'?>" /></span>
                                     </td>
                                     <td style="border-bottom: 1px solid #eeeeee;">
                                        <select name="wlcccm_status[]">
                                             <?php
                                                $stats = array('publish'=>'Published','pending'=>'Pending for Review','draft'=>'Draft','trash'=>'Trash');
                                                foreach((array)$stats AS $key=>$value):
                                             ?>
                                                    <option value="<?php echo $key; ?>" <?php echo $key == $status ? 'selected="selected"':''; ?>><?php echo $value; ?></option>
                                             <?php endforeach; ?>
                                            </select>
                                     </td>
                                </tr>
                                <?php endif;?>
                                </tbody>
                            </table>
                           <div style="width:100%; text-align: right;margin:4px 2px 10px 2px; border-bottom: 1px solid #cccccc;">
                               <p><a href="javascript:void(0);" class="addrow_set">Add Due Date</a></p>
                           </div>
                        <?php
                            $txtset ='<tr style="width:100%;">';
                            $txtset .='<td style="border-bottom: 1px solid #eeeeee;">&nbsp;</td>';
                            $txtset .='<td style="border-bottom: 1px solid #eeeeee;text-align: center;">';
                            $txtset .='<input type="text" class="datepickerbox" id="wlcc_duedate_setid" name="wlcc_duedate_set[]" value="'.$wlcc_duedate .'" size="25" /><span class="datepicker-img" href="javascript:void(0);"><img src="' .$WishListContentControl->pluginurl .'/images/calendar.png" /></span>';
                            $txtset .='</td>';
                            $txtset .='<td style="border-bottom: 1px solid #eeeeee;">';
                            $txtset .='<select name="wlcccm_status[]">';
                                $stats = array('publish'=>'Published','pending'=>'Pending for Review','draft'=>'Draft','trash'=>'Trash');
                                foreach((array)$stats AS $key=>$value):
                                    $txtset .='<option value="' .$key .'" ' .($key == $status ? 'selected="selected"':'') .'>' .$value .'</option>';
                                endforeach;
                            $txtset .='</select><a onclick="deleterowset(this);" class="removerowset" href="javascript:void(0);" style="float:right;">remove</a></td></td></tr>';
                        ?>
                             <script type="text/javascript">
                                  var row_cntset = <?php echo count($result_set)>0 ? (count($result_set)):1; ?>;
                                  var newrowset = '<?php echo $txtset;?>';
                             </script>
                            <?php
                                $result_move = $this->GetPostManagerDate('move',$post_id);
                                if(count($result_move) <= 0){
                                    $wlcc_duedate = $this->format_date($wlccduedate,'m/d/Y h:i A');
                                    $check = false;
                                    $categories = array();
                                    $addmove = "move";
                                    echo '<script>jQuery(document).ready(function(){disable_fields("wlcc_move");});</script>';
                                }else{
                                    $check = true;
                                }
                            ?>
                            <!-- ADD /MOVE -->
                            <?php if ( $post_type == "post" ) : ?>
                                <table class="widefat" id='wlcc_move' style="width:100%;text-align: left;" cellspacing="0">
                                    <thead>
                                    <tr style="width:100%;">
                                        <th style="width: 30%;border-bottom: 1px solid #aaaaaa;"><input class="wlcccm_chk_options_move" style="float:left;vertical-align: bottom;" type='checkbox' name="chkbxcm_move" id="chkbxcm_move" value="move" <?php echo $check ? 'checked="checked"':''; ?> /><label style="float:left;vertical-align: middle;margin-left: 4px;" for="chkbxcm_move"><?php _e(' Add/Move Post','wl-contentcontrol'); ?></label></th>
                                        <th style="width: 30%;border-bottom: 1px solid #aaaaaa;text-align: center;"> <?php _e('Due Date'); ?> </th>
                                        <th style="width: 40%;border-bottom: 1px solid #aaaaaa;"> <?php _e('Categories'); ?> </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if($check):?>
                                    <?php foreach((array)$result_move AS $cnt_move=>$move_datum):?>
                                    <tr style="width:100%;">
                                        <td style="border-bottom: 1px solid #eeeeee;">&nbsp;</td>
                                         <td style=";border-bottom: 1px solid #eeeeee;text-align: center;">
                                             <input type="text" class="datepickerbox" id="wlcc_duedate_moveid<?php echo $cnt_move;?>" name="wlcc_duedate_move[]" value="<?php echo $this->format_date($move_datum->due_date,'m/d/Y h:i A'); ?>" size="25" /><span class="datepicker-img" href="javascript:void(0);"><img src="<?php echo $WishListContentControl->pluginurl.'/images/calendar.png'?>" /></span>
                                         </td>
                                         <td style="border-bottom: 1px solid #eeeeee;">
                                                <select name="wlcccm_addmove[]" style="width:100% !important;">
                                                    <option value="move" <?php echo $move_datum->action == "move"?'selected="selected"':'';?> >Move</option>
                                                    <option value="add" <?php echo $move_datum->action == "add"?'selected="selected"':'';?>>Add</option>
                                                </select>
                                                <div style="float:left;padding:2px 2px 2px 2px;margin-top:10px;width:100%; height:70px;overflow:auto;border:1px solid #eeeeee;background-color:#ffffff; ">
                                                 <?php
                                                    $cats = get_categories('hide_empty=0');
                                                    $post_categories = wp_get_post_categories( $post_id );
                                                    $categories = explode('#',$move_datum->categories);
                                                    foreach((array)$cats AS $cats):
                                                 ?>
                                                    <?php if(count($categories) > 0): ?>
                                                        <input type='checkbox' name='wlcccm_cats<?php echo $cnt_move;?>[<?php echo $cats->cat_ID; ?>]' value='<?php echo $cats->cat_ID; ?>' <?php echo in_array($cats->cat_ID, $categories)?'checked="checked"':'';?> /><label> <?php echo $cats->name; ?></label><br />
                                                    <?php else: ?>
                                                        <input type='checkbox' name='wlcccm_cats<?php echo $cnt_move;?>[<?php echo $cats->cat_ID; ?>]' value='<?php echo $cats->cat_ID; ?>' <?php echo in_array($cats->cat_ID, $post_categories)?'checked="checked"':'';?> /><label> <?php echo $cats->name; ?></label><br />
                                                    <?php endif; ?>
                                                 <?php endforeach; ?>
                                                </div>
                                             <?php if($cnt_move > 0){?><a onclick="deleterowmove(this);" class="removerowmove" href="javascript:void(0);" style="float:right;">remove</a><?php }?>
                                         </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else:?>
                                    <tr style="width:100%;">
                                        <td style="border-bottom: 1px solid #eeeeee;">&nbsp;</td>
                                         <td style=";border-bottom: 1px solid #eeeeee;text-align: center;">
                                             <input type="text" class="datepickerbox" id="wlcc_duedate_moveid0" name="wlcc_duedate_move[]" value="<?php echo $wlcc_duedate; ?>" size="25" /><span class="datepicker-img" href="javascript:void(0);"><img src="<?php echo $WishListContentControl->pluginurl.'/images/calendar.png'?>" /></span>
                                         </td>
                                         <td style="border-bottom: 1px solid #eeeeee;">
                                                <select name="wlcccm_addmove[]" style="width:100% !important;">
                                                    <option value="move" <?php echo $addmove == "move"?'selected="selected"':'';?> >Move</option>
                                                    <option value="add" <?php echo $addmove == "add"?'selected="selected"':'';?>>Add</option>
                                                </select>
                                                <div style="float:left;padding:2px;margin-top:10px;width:100%; height:70px;overflow:auto;border:1px solid #eeeeee;background-color:#ffffff; ">
                                                 <?php
                                                    $cats = get_categories('hide_empty=0');
                                                    $post_categories = wp_get_post_categories( $post_id );
                                                    foreach((array)$cats AS $cats):
                                                 ?>
                                                        <input type='checkbox' name='wlcccm_cats0[<?php echo $cats->cat_ID; ?>]' value='<?php echo $cats->cat_ID; ?>' <?php echo in_array($cats->cat_ID, $post_categories)?'checked="checked"':'';?> /><label> <?php echo $cats->name; ?></label><br />
                                                 <?php endforeach; ?>
                                                </div>
                                         </td>
                                    </tr>
                                    <?php endif;?>
                                    <tbody>
                                </table>
                               <div style="width:100%; text-align: right;margin:4px 2px 10px 2px; border-bottom: 1px solid #cccccc;">
                                   <p><a href="javascript:void(0);" class="addrow_move">Add Due Date</a></p>
                               </div>
                                <?php
                                $txtmove ='<tr style="width:100%;">';
                                $txtmove .='<td style="border-bottom: 1px solid #eeeeee;">&nbsp;</td>';
                                $txtmove .='<td style=";border-bottom: 1px solid #eeeeee;text-align: center;">';
                                $txtmove .='<input type="text" class="datepickerbox" id="wlcc_duedate_moveid" name="wlcc_duedate_move[]" value="' .$wlcc_duedate .'" size="25" /><span class="datepicker-img" href="javascript:void(0);"><img src="'.$WishListContentControl->pluginurl.'/images/calendar.png" /></span>';
                                $txtmove .='</td>';
                                $txtmove .='<td style="border-bottom: 1px solid #eeeeee;">';
                                $txtmove .='<select name="wlcccm_addmove[]" style="width:100% !important;">';
                                $txtmove .='<option value="move"'.($addmove == "move"?'selected="selected"':'') .'>Move</option>';
                                $txtmove .='<option value="add"' .($addmove == "add"?'selected="selected"':'') .'>Add</option>';
                                $txtmove .='</select>';
                                $txtmove .='<div style="float:left;padding:2px 2px 2px 2px;margin-top:10px;width:100%; height:70px;overflow:auto;border:1px solid #eeeeee;background-color:#ffffff; ">';
                                 $cats = get_categories('hide_empty=0');
                                 $post_categories = wp_get_post_categories( $post_id );
                                 foreach((array)$cats AS $cats):
                                    $txtmove .='<input type="checkbox" name="wlcccm_cats[' .$cats->cat_ID .']" value="' .$cats->cat_ID .'" ' .(in_array($cats->cat_ID, $post_categories)?'checked="checked"':'') .' /><label>' .$cats->name .'</label><br />';
                                 endforeach;
                                $txtmove .='</div><a onclick="deleterowmove(this);" class="removerowmove" href="javascript:void(0);" style="float:right;">remove</a></td></tr>';
                                ?>
                                 <script type="text/javascript">
                                      var row_cntmove = <?php echo count($result_move)>0 ? (count($result_move)):1; ?>;
                                      var newrowmove = '<?php echo $txtmove;?>';
                                 </script>
                                  <?php
                                    $result_repost = $this->GetPostManagerDate('repost',$post_id);
                                    if(count($result_repost) <= 0){
                                        $wlcc_duedate = $this->format_date($wlccduedate,'m/d/Y h:i A');
                                        $check = false;
                                        echo '<script>jQuery(document).ready(function(){disable_fields("wlcc_repost");});</script>';
                                    }else{
                                        $check = true;
                                    }
                                ?>
                                <!-- REPOST -->
                                <table class="widefat" id='wlcc_repost' style="width:100%;text-align: left;" cellspacing="0">
                                    <thead>
                                    <tr style="width:100%;">
                                        <th style="width: 30%;border-bottom: 1px solid #aaaaaa;"><input class="wlcccm_chk_options_repost" style="float:left;vertical-align: bottom;" type='checkbox' id="chkbxcm_repost" name="chkbxcm_repost" value="repost" <?php echo $check ? 'checked="checked"':''; ?> /><label style="float:left;vertical-align: middle;margin-left: 4px;" for="chkbxcm_repost"><?php _e(' Repost','wl-contentcontrol'); ?></label></th>
                                        <th style="width: 30%;border-bottom: 1px solid #aaaaaa;text-align: center;"> <?php _e('Due Date'); ?> </th>
                                        <th style="width: 40%;border-bottom: 1px solid #aaaaaa;">Repeat </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if($check):?>
                                    <?php foreach((array)$result_repost AS $cnt_repost=>$repost_datum):?>
                                    <tr style="width:100%;">
                                        <td style="border-bottom: 1px solid #eeeeee;">&nbsp;</td>
                                         <td style="border-bottom: 1px solid #eeeeee;text-align: center;">
                                            <input type="text" class="datepickerbox" id="wlcc_duedate_repostid<?php echo $cnt_repost; ?>" name="wlcc_duedate_repost[]" value="<?php echo $this->format_date($repost_datum->due_date,'m/d/Y h:i A'); ?>" size="25" /><span class="datepicker-img" href="javascript:void(0);"><img src="<?php echo $WishListContentControl->pluginurl.'/images/calendar.png'?>" /></span>
                                         </td>
                                         <td style="border-bottom: 1px solid #eeeeee;">
                                            <div style="width:300px;overflow:auto;">
                                                <div style="float:left;width:70px;">Every:</div>
                                                <div style="float:left;width:70px;">
                                                   <input type="text" id="wlcccm_repeat" name="wlcccm_repeat[]" value="<?php echo $repost_datum->rep_num > 0? $repost_datum->rep_num:0; ?>" size="3" />
                                                </div>
                                                <div style="float:left;width:100px;">
                                                    <select name="wlcccm_every[]" >
                                                        <option value="day" <?php echo $repost_datum->rep_by == 'day'? 'selected="selected"':''; ?> >Day/s</option>
                                                        <option value="month" <?php echo $repost_datum->rep_by == 'month'? 'selected="selected"':''; ?> >Month/s</option>
                                                        <option value="year" <?php echo $repost_datum->rep_by == 'year'? 'selected="selected"':''; ?> >Year/s</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div style="width:200px;overflow:auto; margin-top: 5px">
                                                <div style="float:left;width:100px;">Repetitions:</div>
                                                <div style="float:left;100px;">
                                                    <input type="text" id="wlcccm_repeat_end" name="wlcccm_repeat_end[]" value="<?php echo $repost_datum->rep_end > 0? $repost_datum->rep_end:0; ?>" size="3" />
                                                </div>
                                            </div>
                                             <?php if($cnt_repost > 0){?><a onclick="deleterowrepost(this);" class="removerowrepost" href="javascript:void(0);" style="float:right;">remove</a><?php }?>
                                         </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else:?>
                                    <tr style="width:100%;">
                                        <td style="border-bottom: 1px solid #eeeeee;">&nbsp;</td>
                                         <td style="border-bottom: 1px solid #eeeeee;text-align: center;">
                                             <input type="text" class="datepickerbox" id="wlcc_duedate_repostid0" name="wlcc_duedate_repost[]" value="<?php echo $wlcc_duedate; ?>" size="25" /><span class="datepicker-img" href="javascript:void(0);"><img src="<?php echo $WishListContentControl->pluginurl.'/images/calendar.png'?>" /></span>
                                         </td>
                                         <td style="border-bottom: 1px solid #eeeeee;">
                                            <div style="width:300px;overflow:auto;">
                                                <div style="float:left;width:100px;">Every:</div>
                                                <div style="float:left;width:70px;">
                                                   <input type="text" id="wlcccm_repeat" name="wlcccm_repeat[]" value="0" size="3" />
                                                </div>
                                                <div style="float:left;width:100px;">
                                                    <select name="wlcccm_every[]" >
                                                        <option value="day" >Day/s</option>
                                                        <option value="month" >Month/s</option>
                                                        <option value="year" >Year/s</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div style="width:300px;overflow:auto; margin-top: 5px">
                                                <div style="float:left;width:100px;">Repetitions:</div>
                                                <div style="float:left;100px;">
                                                    <input type="text" id="wlcccm_repeat_end" name="wlcccm_repeat_end[]" value="0" size="3" />
                                                </div>
                                            </div>
                                         </td>
                                    </tr>
                                    <?php endif;?>
                                   </tbody>
                                </table>
                                <div style="width:100%; text-align: right;margin:4px 2px 10px 2px; border-bottom: 1px solid #cccccc;">
                                   <p><a href="javascript:void(0);" class="addrow_repost">Add Due Date</a></p>
                                </div>
                                <?php
                                $txtrepost = '<tr style="width:100%;">';
                                $txtrepost .= '<td style="border-bottom: 1px solid #eeeeee;">&nbsp;</td>';
                                $txtrepost .= ' <td style="border-bottom: 1px solid #eeeeee;text-align: center;">';
                                $txtrepost .= '<input type="text" class="datepickerbox" id="wlcc_duedate_repostid" name="wlcc_duedate_repost[]" value="'.$wlcc_duedate .'" size="25" /><span class="datepicker-img" href="javascript:void(0);"><img src="'.$WishListContentControl->pluginurl.'/images/calendar.png" /></span>';
                                $txtrepost .= '</td>';
                                $txtrepost .= '<td style="border-bottom: 1px solid #eeeeee;">';
                                $txtrepost .= '<div style="width:300px;overflow:auto;">';
                                $txtrepost .= '<div style="float:left;width:100px;">Every:</div>';
                                $txtrepost .= '<div style="float:left;width:70px;">';
                                $txtrepost .= '<input type="text" id="wlcccm_repeat" name="wlcccm_repeat[]" value="0" size="3" />';
                                $txtrepost .= '</div>';
                                $txtrepost .= '<div style="float:left;width:100px;">';
                                $txtrepost .= '<select name="wlcccm_every[]" >';
                                $txtrepost .= '<option value="day" >Day/s</option>';
                                $txtrepost .= '<option value="month" >Month/s</option>';
                                $txtrepost .= '<option value="year" >Year/s</option>';
                                $txtrepost .= '</select>';
                                $txtrepost .= '</div>';
                                $txtrepost .= '</div>';
                                $txtrepost .= '<div style="width:300px;overflow:auto; margin-top: 5px">';
                                $txtrepost .= '<div style="float:left;width:100px;">Repetitions:</div>';
                                $txtrepost .= '<div style="float:left;width:100px;">';
                                $txtrepost .= '<input type="text" id="wlcccm_repeat_end" name="wlcccm_repeat_end[]" value="0" size="3" />';
                                $txtrepost .= '</div>';
                                $txtrepost .= '</div>';
                                $txtrepost .= '<a onclick="deleterowrepost(this);" class="removerowrepost" href="javascript:void(0);" style="float:right;">remove</a></td>';
                                $txtrepost .= '</tr>';
                                ?>
                                 <script type="text/javascript">
                                      var row_cntrepost = <?php echo count($result_repost)>0 ? (count($result_repost)):1; ?>;
                                      var newrowrepost = '<?php echo $txtrepost;?>';
                                 </script>
                            <?php endif; ?>
                </div>
            <input type="hidden" name="wlcccm_option_submitted" value="true" />
        <?php
        }
/**
 * Displays the interface where the customer can enter the license information
 */
        function WPWLKey(){
            global $WishListMemberInstance;
            $LicenseKeyOption = get_class($this) .'LicenseKey';
            $LicenseEmailOption = get_class($this) .'LicenseEmail';
                ?>
                <div class="wrap">
                <h2>WishList Products Key</h2>
                <form method="post">
                <table class="form-table">
                        <tr valign="top">
                                <td colspan="3" style="border:none"><?php _e('Please enter your WishList Products Key and Email below to activate this plugin','wl-contentcontrol'); ?></td>
                        </tr>
                        <tr valign="top">
                                <th scope="row" style="border:none;white-space:nowrap;" class="WLRequired"><?php _e('WishList Products Key','wl-contentcontrol'); ?></th>
                                <td style="border:none"><input type="text" name="LicenseKey" value="<?php echo $WishListMemberInstance->GetOption($LicenseKeyOption); ?>" size="32" /></td>
                                <td style="border:none"><?php _e('(This was sent to the email you used during your purchase)','wl-contentcontrol'); ?></td>
                        </tr>
                        <tr valign="top">
                                <th scope="row" style="border:none;white-space:nowrap" class="WLRequired"><?php _e('WishList Products Email','wl-contentcontrol'); ?></th>
                                <td style="border:none"><input type="text" name="LicenseEmail" value="<?php echo $WishListMemberInstance->GetOption($LicenseEmailOption); ?>" size="32" /></td>
                                <td style="border:none"><?php _e('(Please enter the email you used during your registration/purchase)','wl-contentcontrol'); ?></td>
                        </tr>
                </table>
                <p class="submit">
                        <input type="hidden" value="0" name="LicenseLastCheck" />
                        <input type="hidden" value="SaveLincenseKey" name="WishListControlAction" />
                        <input type="hidden" value="<?php echo get_class($this); ?>" name="WishListControlModule" />
                        <input type="submit" value="Save WishList Products Key" name="Submit" />
                </p>
                </form>
                </div>
                <?php
        }
//wl-contentconrol hook for modules
        function WLCCHook($wlcc){
            global $WishListMemberInstance;
            //get the module status
            $wlcc_status=$WishListMemberInstance->GetOption('wlcc_status');
            if($wlcc_status[get_class($this)]){
                $this->Activate();
            }else{
                 $this->Deactivate();
            }
        }
//create table
        function CreateSchedTable(){
                global $wpdb;
                $table1 = $wpdb->prefix."wlcc_contentmanager_repost";
                $table2 = $wpdb->prefix."wlcc_contentmanager_move";
                $table3 = $wpdb->prefix."wlcc_contentmanager_set";
                $structure1 ="CREATE TABLE IF NOT EXISTS " .$table1 ." (
                    `id` bigint(20) NOT NULL auto_increment,
                    `post_id` bigint(20) NOT NULL,
                    `due_date` datetime NOT NULL,
                    `rep_num` int,
                    `rep_by` varchar(10),
                    `rep_end` int,
                    `date_added` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
                    PRIMARY KEY  (`id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
                $structure2 ="CREATE TABLE IF NOT EXISTS " .$table2 ." (
                    `id` bigint(20) NOT NULL auto_increment,
                    `post_id` bigint(20) NOT NULL,
                    `action` varchar(15) NOT NULL,
                    `categories` text NOT NULL,
                    `due_date` datetime NOT NULL,
                    `date_added` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
                    PRIMARY KEY  (`id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
                $structure3 ="CREATE TABLE IF NOT EXISTS " .$table3 ." (
                    `id` bigint(20) NOT NULL auto_increment,
                    `post_id` bigint(20) NOT NULL,
                    `due_date` datetime NOT NULL,
                    `status`varchar(10) NOT NULL,
                    `date_added` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
                    PRIMARY KEY  (`id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
                $wpdb->query($structure1);
                $wpdb->query($structure2);
                $wpdb->query($structure3);
            }
//save content manager options
        function SaveContentManagerOptions(){
            global $WishListMemberInstance,$WishListContentControl;
            $post_ID = $_POST['post_ID'];
            if($post_ID == "") return false;//do not save if theres no post id

            $chkbxcm_repost =$_POST['chkbxcm_repost'];
            $chkbxcm_move =$_POST['chkbxcm_move'];
            $chkbxcm_set =$_POST['chkbxcm_set'];
            $wlcccmerror_msg = array();
            $data_repost = array();
            $data_move = array();
            $data_set = array();
            $data = array();
            $errs = "";

            //repost
            if(isset($_POST['chkbxcm_repost']) && $chkbxcm_repost=='repost'){
                $wlcc_duedate_reposts = $_POST['wlcc_duedate_repost'];
                    $wlcccm_repeat = $_POST['wlcccm_repeat'];
                    $wlcccm_every = $_POST['wlcccm_every'];
                    $wlcccm_repeat_end = $_POST['wlcccm_repeat_end'];
                foreach($wlcc_duedate_reposts as $key_rep=>$duedate_reposts):
                    $wlccduedate = date_parse($duedate_reposts);
                    $repost_date = date('Y-m-d H:i:s',mktime((int)$wlccduedate["hour"],(int)$wlccduedate["minute"],0,(int)$wlccduedate["month"],(int)$wlccduedate["day"],(int)$wlccduedate["year"]));
                    if(!$this->isvalid_date($repost_date)){
                        $wlcccmerror_msg[] = __('Due Date is invalid ('.$this->format_date($repost_date).'). The Date selected has already passed. Please select a Date in the future.','wishlist-member') ;
                    }

                    if(!$this->validateint($wlcccm_repeat[$key_rep])){
                        $wlcccmerror_msg[] = "Invalid value for the number of " .$wlcccm_every[$key_rep];
                        $repeat = $wlcccm_repeat[$key_rep];
                    }else{
                        $repeat = (int)$wlcccm_repeat[$key_rep];
                        $repeat = $repeat<0? 0:$repeat;
                    }
                    if(!$this->validateint($wlcccm_repeat_end[$key_rep])){
                        $wlcccmerror_msg[] = "Invalid value for the number of repetition";
                        $repeat_end = $wlcccm_repeat_end[$key_rep];
                    }else{
                        $repeat_end = (int)$wlcccm_repeat_end[$key_rep];
                        $repeat_end = $repeat_end<0? 0:$repeat_end;
                    }
                    $data_repost[$key_rep] = array('action'=>'repost','date'=>$repost_date,'rep_num'=>$repeat,'rep_by'=>$wlcccm_every[$key_rep],'rep_end'=>$repeat_end);
                endforeach;
                if(count($wlcccmerror_msg) <= 0 && count($data_repost) >0){
                    $this->DeletePostManagerDate_byPostId($post_ID,'repost');
                    $data['repost'] = $data_repost;
                    foreach((array)$data_repost as $key_repost=>$datum_repost){
                        $this->SavePostManagerDate($post_ID,$datum_repost);
                    }
                }else{
                    $errs .='<p>Repost</p><p>';
                    foreach((array)$wlcccmerror_msg as $key=>$err){
                        $errs .='- ' .$err .'<br />';
                    }
                    echo '</p>';
                    unset($wlcccmerror_msg);
                }
            }else{
                $this->DeletePostManagerDate_byPostId($post_ID,'repost');
            }
            //move
            if(isset($_POST['chkbxcm_move']) && $chkbxcm_move=='move'){
                $wlcc_duedate_moves = $_POST['wlcc_duedate_move'];
                foreach($wlcc_duedate_moves as $key_move=>$duedate_moves):
                    $wlcccm_cats = $_POST['wlcccm_cats'];
                    $wlcccm_addmove = $_POST['wlcccm_addmove'];
                    $wlccduedate = date_parse($duedate_moves);
                    $move_date = date('Y-m-d H:i:s',mktime((int)$wlccduedate["hour"],(int)$wlccduedate["minute"],0,(int)$wlccduedate["month"],(int)$wlccduedate["day"],(int)$wlccduedate["year"]));
                    if(!$this->isvalid_date($move_date)){
                        $wlcccmerror_msg[] = __('Due Date is invalid ('.$this->format_date($move_date).'). The Date selected has already passed. Please select a Date in the future.','wishlist-member') ;
                    }

                    $catname = 'wlcccm_cats' .$key_move;
                    $wlcccm_cats = $_POST[$catname];
                    if(count($wlcccm_cats) <= 0){
                        $wlcccmerror_msg[] = "[" .$this->format_date($move_date) ."] has no Categories selected. Please select at least 1 category where to " .$wlcccm_addmove[$i] ." the posts.";
                        $cats = "";
                    }else{
                        $cats = implode('#',$wlcccm_cats);
                    }
                    $data_move[$key_move] = array('action'=>'move','date'=>$move_date,'cats'=>$cats,'method'=>$wlcccm_addmove[$key_move]);
                endforeach;
                if(count($wlcccmerror_msg) <= 0 && count($data_move) >0){
                    $this->DeletePostManagerDate_byPostId($post_ID,'move');
                    $data['move'] = $data_move;
                    foreach((array)$data_move as $key_move=>$datum_move){
                        $this->SavePostManagerDate($post_ID,$datum_move);
                    }
                }else{
                    $errs .='<p>Add/Move Post</p><p>';
                    foreach((array)$wlcccmerror_msg as $key=>$err){
                        $errs .='- ' .$err .'<br />';
                    }
                    echo '</p>';
                    unset($wlcccmerror_msg);
                }
            }else{
                $this->DeletePostManagerDate_byPostId($post_ID,'move');
            }
            //set
            if(isset($_POST['chkbxcm_set']) && $chkbxcm_set=='set'){
                $wlcc_duedate_sets = $_POST['wlcc_duedate_set'];
                $wlcccm_status = $_POST['wlcccm_status'];
                foreach($wlcc_duedate_sets as $key_set=>$duedate_sets):
                    $wlccduedate = date_parse($duedate_sets);
                    $set_date = date('Y-m-d H:i:s',mktime((int)$wlccduedate["hour"],(int)$wlccduedate["minute"],0,(int)$wlccduedate["month"],(int)$wlccduedate["day"],(int)$wlccduedate["year"]));
                    if(!$this->isvalid_date($set_date)){
                        $wlcccmerror_msg[] = __('Due Date is invalid ('.$this->format_date($set_date).'). The Date selected has already passed. Please select a Date in the future.','wishlist-member') ;
                    }
                    $data_set[$key_set] = array('action'=>'set','date'=>$set_date,'status'=>$wlcccm_status[$key_set]);
                endforeach;
                if(count($wlcccmerror_msg) <= 0 && count($data_set) >0){
                    $this->DeletePostManagerDate_byPostId($post_ID,'set');
                    $data['set'] = $data_set;
                    foreach((array)$data_set as $key_set=>$datum_set){
                        $this->SavePostManagerDate($post_ID,$datum_set);
                    }
                }else{
                    $errs .='<p>Set Post</p><p>';
                    foreach((array)$wlcccmerror_msg as $key=>$err){
                        $errs .='- ' .$err .'<br />';
                    }
                    echo '</p>';
                    unset($wlcccmerror_msg);
                }
            }else{
                $this->DeletePostManagerDate_byPostId($post_ID,'set');
            }
            //print_r($data); exit();
           if($errs != ""){
             $wlcc_errors = "<div class='error fade'>".__('<p>You have errors in your WL Content Manager settings.</p>','wishlist-member')."<p>";
             $wlcc_errors .= __($errs) ."</div>";
             $WishListMemberInstance->SaveOption('wlcccm_error',$wlcc_errors);
           }
        }
//save post expiry date
        function UpdatePostManagerDate($id,$data){
            global $wpdb;
            $table1 = $wpdb->prefix."wlcc_contentmanager_repost";
            $table2 = $wpdb->prefix."wlcc_contentmanager_move";
            $table3 = $wpdb->prefix."wlcc_contentmanager_set";

            if($data['action'] =='move'){
                    $q = "UPDATE $table2 SET due_date='" .$data['date'] ."',categories='" .$data['cats'] ."',action='" .$data['method'] ."' WHERE id=" .$id;
            }else if($data['action'] =='repost'){
                    $q = "UPDATE $table1 SET due_date='" .$data['date'] ."',rep_num=" .$data['rep_num'] .",rep_by='" .$data['rep_by'] ."',rep_end=" .$data['rep_end'] ." WHERE id=" .$id;
            }else if($data['action'] =='set'){
                   $q = "UPDATE $table3 SET due_date='" .$data['date'] ."',status='" .$data['status'] ."' WHERE id=" .$id;
            }
            $wpdb->query($q);
        }
//save post expiry date
        function SavePostManagerDate($post_id,$data){
            global $wpdb;
            $table1 = $wpdb->prefix."wlcc_contentmanager_repost";
            $table2 = $wpdb->prefix."wlcc_contentmanager_move";
            $table3 = $wpdb->prefix."wlcc_contentmanager_set";

            if($data['action'] =='move'){
                if(is_array($post_id)){
                    foreach($post_id as $key=>$value){
                        $q = "INSERT INTO $table2(post_id,due_date,categories,action) VALUES('" .$post_id ."','" .$data['date'] ."','" .$data['cats'] ."','" .$data['method']."')";
                        $wpdb->query($q);
                    }
                }else{
                    $q = "INSERT INTO $table2(post_id,due_date,categories,action) VALUES('" .$post_id ."','" .$data['date'] ."','" .$data['cats'] ."','" .$data['method']."')";
                    $wpdb->query($q);
                }
            }else if($data['action'] =='repost'){
                if(is_array($post_id)){
                    foreach($post_id as $key=>$value){
                        $q = "INSERT INTO $table1(post_id,due_date,rep_num,rep_by,rep_end) VALUES('" .$post_id ."','" .$data['date'] ."'," .$data['rep_num'] .",'" .$data['rep_by'] ."'," .$data['rep_end'].")";
                        $wpdb->query($q);
                    }
                }else{
                    $q = "INSERT INTO $table1(post_id,due_date,rep_num,rep_by,rep_end) VALUES('" .$post_id ."','" .$data['date'] ."'," .$data['rep_num'] .",'" .$data['rep_by']."'," .$data['rep_end'].")";
                    $wpdb->query($q);
                }
            }else if($data['action'] =='set'){
                if(is_array($post_id)){
                    foreach($post_id as $key=>$value){
                        $q = "INSERT INTO $table3(post_id,due_date,status) VALUES('" .$post_id ."','" .$data['date'] ."','" .$data['status']."')";
                        $wpdb->query($q);
                    }
                }else{
                    $q = "INSERT INTO $table3(post_id,due_date,status) VALUES('" .$post_id ."','" .$data['date'] ."','" .$data['status']."')";
                    $wpdb->query($q);
                }
            }
        }
//get post expiry date of the post
        function GetPostManagerDate($action,$post_id='',$due_id='',$start=0,$limit=0){
            global $wpdb;
            $table = $wpdb->prefix."wlcc_contentmanager_" .$action;
            $limit = $limit > 0 ? " LIMIT " .$start ."," .$limit : "";
            $where = "";
            if($post_id != ''){
                $where = " WHERE post_id" .(is_array($post_id)? (" IN (" .implode(',',$post_id) .") "):("=" .$post_id ." "));
            }else if($due_id != ''){
                $where = " WHERE id" .(is_array($due_id)? (" IN (" .implode(',',$due_id) .") "):("=" .$due_id ." "));
            }else{
                return array();
            }
            $q = "SELECT * FROM $table $where ORDER BY due_date ASC". $limit;
            return $wpdb->get_results($q);
        }
//get due date
        function GetDueDate($action,$dueid='',$start=0,$limit=0){
            global $wpdb;
            $table = $wpdb->prefix."wlcc_contentmanager_" .$action;
            $limit = $limit > 0 ? " LIMIT " .$start ."," .$limit : "";

            if(is_array($dueid)){
                $post_ids = implode(',',$dueid);
                $q = "SELECT * FROM $table WHERE id IN (" .$dueid .") ORDER BY due_date ASC". $limit;
            }else{
                if($dueid!=''){
                    $q = "SELECT * FROM $table WHERE id=" .$dueid ." ORDER BY due_date ASC". $limit;
                }else{
                    $q = "SELECT * FROM $table ORDER BY date_added DESC". $limit;
                }
            }
            return $wpdb->get_results($q);
        }
//delete post expiry date by id
        function DeletePostManagerDate($id,$action){
            global $wpdb;
            $table = $wpdb->prefix."wlcc_contentmanager_" .$action;
                if(is_array($id)){
                    $ids = implode(',',$id);
                    $q = "DELETE FROM $table WHERE id IN (" .$ids .")";
                }else{
                    $q = "DELETE FROM $table WHERE id=" .$id;
                }
            $wpdb->query($q);
        }
//delete post expiry date by id
        function DeletePostManagerDate_byPostId($id,$action){
            global $wpdb;
            $table = $wpdb->prefix."wlcc_contentmanager_" .$action;
                if(is_array($id)){
                    $ids = implode(',',$id);
                    $q = "DELETE FROM $table WHERE post_id IN (" .$ids .")";
                }else{
                    $q = "DELETE FROM $table WHERE post_id=" .$id;
                }
            $wpdb->query($q);
        }
//retrieve all posts or with expiry only
            function GetPosts($action,$show_all=false,$show_poststat='all',$ptype='post',$start=0,$per_page=0,$sort="ID",$asc=1){
                global $wpdb,$WishListMemberInstance;
                $table1 = $wpdb->prefix."posts";

                $limit = "";
                if($per_page >0) $limit =  " LIMIT " .$start ."," .$per_page;
                $order = " ORDER BY " .$sort .($asc == 1 ? " ASC":" DESC");

                if($show_all){
                    if($show_poststat == "all"){
                        $post_status_filter = " AND post_status IN ('publish','draft','trash','pending')";
                        $q = "SELECT ID,post_author,post_date,post_status,post_modified,post_title,post_content FROM $table1 WHERE post_type= '{$ptype}'" .$post_status_filter  .$order .$limit;
                    }else{
                        $post_status_filter = ($show_poststat!="")?(" AND post_status='" .$show_poststat ."'"):(" AND post_status='publish'");
                        $q = "SELECT ID,post_author,post_date,post_status,post_modified,post_title,post_content FROM $table1 WHERE post_type= '{$ptype}'" .$post_status_filter .$order .$limit;
                    }
                }else{
                    $table2 = $wpdb->prefix."wlcc_contentmanager_" .$action;
                    $q = "SELECT DISTINCT $table1.ID,$table1.post_author,$table1.post_date,$table1.post_status,$table1.post_modified,$table1.post_title,$table1.post_content FROM $table1 INNER JOIN $table2 ON  $table1.ID=$table2.post_id AND $table1.post_type='{$ptype}'" .$order .$limit;
                }

                return $wpdb->get_results($q);
            }

//retrieve all posts or with expiry only
        function ApplyDueDate(){
            global $wpdb,$WishListMemberInstance;
            $table = $wpdb->prefix."posts";
            $table1 = $wpdb->prefix."wlcc_contentmanager_repost";
            $table2 = $wpdb->prefix."wlcc_contentmanager_move";
            $table3 = $wpdb->prefix."wlcc_contentmanager_set";
            $wlcc_status=$WishListMemberInstance->GetOption('wlcc_status');
            if(!$wlcc_status['ContentManager'])return false; // skip when disabled

           $q = "SELECT * FROM $table1 WHERE due_date <= '" .date('Y-m-d H:i:s') ."'";
           $res = $wpdb->get_results($q);
           foreach((array)$res as $result):
                   $wpdb->query("UPDATE $table SET post_date='" .$result->due_date ."', post_date_gmt='" .$result->due_date ."' WHERE ID=" .$result->post_id);
                   //check for repetition
                   $rep_num = $result->rep_num;
                   $rep_end = $result->rep_end;
                   if($rep_num > 0){
                       $d1 = date_parse($result->due_date);
                       if($result->rep_by == 'day'){
                            $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],($d1['day']+$rep_num),$d1['year']);
                       }else if($result->rep_by == 'month'){
                            $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],($d1['month']+$rep_num),$d1['day'],$d1['year']);
                       }else if($result->rep_by == 'year'){
                           $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],($d1['year']+$rep_num));
                       }else{
                           $new_bue_date = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],($d1['day']+$rep_num),$d1['year']);
                       }
                       if($rep_end > 0){
                          if($rep_end == 1){
                              $this->DeletePostManagerDate($result->id,'repost');
                          }else{
                              $rep_end = $rep_end-1;
                          }
                       }
                       $datum = array('action'=>'repost','date'=>date('Y-m-d H:i:s',$new_bue_date),'rep_num'=>$rep_num,'rep_by'=>$result->rep_by,'rep_end'=>$rep_end);
                       $this->UpdatePostManagerDate($result->id,$datum);
                   }else{ //if not repeated then delete
                    $this->DeletePostManagerDate($result->id,'repost');
                   }
           endforeach;

           $q = "SELECT * FROM $table2 WHERE due_date <= '" .date('Y-m-d H:i:s') ."'";
           $res = $wpdb->get_results($q);
           foreach((array)$res as $result):
               $cat = explode('#',$result->categories);
               if($result->action == "add"){
                   $cur_cat = wp_get_post_categories($result->post_id);
                   $x = array_merge((array)$cat,(array)$cur_cat);
                   $cat = array_unique((array)$x);
               }
               $catpost = array();
               $catpost['ID'] = $result->post_id;
               $catpost['post_category'] = $cat;
               $ret = wp_update_post($catpost);
               $this->DeletePostManagerDate($result->id,'move');
           endforeach;

           $q = "SELECT * FROM $table3 WHERE due_date <= '" .date('Y-m-d H:i:s') ."'";
           $res = $wpdb->get_results($q);
           foreach((array)$res as $result):
                   $wpdb->query("UPDATE $table SET post_status='" .$result->status ."' WHERE ID=" .$result->post_id);
                   $this->DeletePostManagerDate($result->id,'set');
           endforeach;
        }
        /*
         * FUNCTION being called to add page/post metabox (otions)
        */
            function ContentManagerMetaBoxes(){
                add_meta_box(
                    'wlcccm-meta-box',
                    __( 'WishList Content Manager', 'wl-contentcontrol-manager' ),
                    array(&$this,'ContentManagerOptions'),
                    'post'
                );
                add_meta_box(
                    'wlcccm-meta-box',
                    __( 'WishList Content Manager', 'wl-contentcontrol-manager' ),
                    array(&$this,'ContentManagerOptions'),
                    'page'
                );
                //get custom post type
                $custom_types = get_post_types(array('public'=> true,"_builtin"=>false));
                foreach($custom_types as $i=>$ctype){
                  add_meta_box(
                    'wlcccm-meta-box',
                    __( 'WishList Content Manager', 'wl-contentcontrol-manager' ),
                    array(&$this,'ContentManagerOptions'),
                      $ctype
                  );
                }
            }
//activate module
        function Activate(){
            $this->CreateSchedTable();
            //insert Content Archiver Options when creating or editing a post
            //add_action('edit_form_advanced',array(&$this,'ContentManagerOptions'));
            //add_action('edit_page_form',array(&$this,'ContentManagerOptions'));
            add_action( 'admin_init',array(&$this,'ContentManagerMetaBoxes'));
            //save Content Archiver Options when savign the post
            add_action('wp_insert_post',array(&$this,'SaveContentManagerOptions'));

        }
//deactivate module
        function Deactivate(){ //remove filters and actions
            //insert Content Archiver Options when creating or editing a post
            //remove_action('edit_form_advanced',array(&$this,'ContentManagerOptions'));
            //remove_action('edit_page_form',array(&$this,'ContentManagerOptions'));
            remove_action( 'admin_init',array(&$this,'ContentManagerMetaBoxes'));
            //save Content Archiver Options when savign the post
            remove_action('wp_insert_post',array(&$this,'SaveContentManagerOptions'));

        }
/*
    OTHER FUNCTIONS NOT CORE OF CONTENT ARCHIVER GOES HERE
*/
        //Save current selection in the dropdown
        function SaveView(){
            $wpm_current_user=wp_get_current_user();
                if(!session_id()){
                    session_start();
                }
            if($wpm_current_user->caps['administrator']){
                $mode = isset($_POST['mode']) ? $_POST['mode']:$_GET['mode'];
                if($mode != ""){
                    $_SESSION['wlcmmode'] = $mode;
                }
                $ptype = isset($_POST['ptype']) ? $_POST['ptype']:$_GET['ptype'];
                if($ptype != ""){
                    $_SESSION['wlcmptype'] = $ptype;
                }
                if(isset($_POST['frmsubmit'])){
                    $show_post = isset($_POST['show_post']) ? $_POST['show_post']:$_GET['show_post'];
                    $show_post_stat = isset($_POST['show_post_stat']) ? $_POST['show_post_stat']:$_GET['show_post_stat'];

                    $_SESSION['wlcmshowpost'] = $show_post;
                    if($show_post == 'all' && $show_post_stat !=""){
                        $_SESSION['wlcmshowpoststat'] = $show_post_stat;
                    }
                }
            }else{
                if(session_id()){
                    session_destroy();
                }
            }
        }
//function for string
        function cut_string($str, $length, $minword){
            $sub = '';
            $len = 0;
            foreach (explode(' ', $str) as $word){
                $part = (($sub != '') ? ' ' : '') .$word;
                $sub .= $part;
                $len += strlen($part);
                if (strlen($word) > $minword && strlen($sub) >= $length)
                break;
            }
            return $sub . (($len < strlen($str)) ? '...' : '');
        }
//function to get date difference needs php5.2
        function isvalid_date($date){
            $ret = false;
            if($date > date('Y-m-d H:i:s')){
                $ret = true;
            }
            return $ret;
        }
    //function to format the date
        function format_date($date,$format='M j, Y g:i a'){
            $d1 = date_parse($date);
            $pdate = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],$d1['year']);
            $date = date($format,$pdate);
            return $date;
        }
       //validate integer
        function validateint($inData) {
            $intRetVal = false;
          $IntValue = intval($inData);
          $StrValue = strval($IntValue);
          if($StrValue == $inData) {
            $intRetVal = true;
          }

          return $intRetVal;
        }
    }//End of ContentArchiver Class
}
if(!isset($ContentManager)){
   $ContentManagerInstance = new ContentManager();
   if(is_admin()){
        add_action('wl-contentcontrol_dashboard',array(&$ContentManagerInstance,'DashboardPage'),10,2);
        add_action('wl-contentcontrol_hook',array(&$ContentManagerInstance,'SaveView'),10,1);
   }

   add_action('wl-contentcontrol_hook',array(&$ContentManagerInstance,'WLCCHook'),10,1);
   add_action('wl-contentcontrol_hook',array(&$ContentManagerInstance,'ApplyDueDate'),10,1);

}
?>