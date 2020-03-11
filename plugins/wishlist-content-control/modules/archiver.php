<?php
/*
 * Content Archiver Module
 * Version: 1.1.28
 * SVN: 28
 * @version $Rev: 26 $
 * $LastChangedBy: feljun $
 * $LastChangedDate: 2015-08-17 10:27:50 -0400 (Mon, 17 Aug 2015) $
 *
 */
// Module Information
$CCModule=array(
        'ClassName'=>'ContentArchiver',
	'Name'=>'WL Content Archiver',
	'URL'=>'',
        'Version'=>'1.1.28',
	'Description'=>'WL Content Archiver allows you to specify a post "expiration" for each membership level. This gives you the ability to place content in the "archives" so that only members who belonged to that level during the time it was released will be allowed to have ongoing access to the "archived content". New members who did not belong to your membership when the "archived content" was released would not have access.',
	'Author'=>'WishList Products',
	'AuthorURL'=>'http://www.wishlistproducts.com/',
	'File'=>__FILE__
);
if(!class_exists('ContentArchiver')){
	/**
	 * Content Archiver Core Class
	 */
	class ContentArchiver{
    //settings page
            function DashboardPage($page,$wlcc){
                if($page!=get_class($this))return false;
                if($wlcc->LicenseStatus(get_class($this)) != '1'){
                    $this->WPWLKey();return false;
                }
                global $WishListMemberInstance;
                $custom_types = get_post_types(array('public'=> true,"_builtin"=>false),"objects");
                
                    $wlcccmerror_msg = array();
                    $data = array();
                    $isupdate = false;
                    $post_ids =array();
                    
                $wpm_levels = $WishListMemberInstance->GetOption('wpm_levels'); // get WLM levels
                $show_level = isset($_POST['show_level']) ? $_POST['show_level']:$_GET['show_level'];
                $show_post = isset($_POST['show_post']) ? $_POST['show_post']:$_GET['show_post'];
                $sort = isset($_POST['sort']) ? $_POST['sort']:$_GET['sort'];
                $sort = $sort == "" ? "ID":$sort;
                $asc = isset($_POST['asc']) ? $_POST['asc']:$_GET['asc'];
                $asc = $asc == "" ? 1:$asc;
                $exp_sort = false;

                $menu_modes = array("page"=>"Pages","post"=>"Posts");
                foreach($custom_types as $t=>$ctype){
                    $menu_modes[$t]= $ctype->labels->name;
                }
                $menu_modes["settings"] = "Settings";

                $mode = $_GET['mode'];
                $mode =  !in_array($mode,array_keys($menu_modes)) ? 'post' : $mode;

                $wlccexpdate = date_parse(date('Y-m-d H:i:s'));
                $datenow = date('Y-m-d H:i:s',mktime(0,0,0,(int)$wlccexpdate["month"],(int)$wlccexpdate["day"],(int)$wlccexpdate["year"]));
            ?>
          
                <ul class="wlcc-sub-menu">
                    <?php foreach($menu_modes as $m=>$menu_mode): ?>
                        <li <?php echo ($mode==$m || $mode=='')?'class="current"':''; ?> ><a href="?page=ContentControl&module=ContentArchiver&mode=<?php echo $m; ?>"><?php _e($menu_mode,'wl-contentcontrol'); ?></a></li>
                    <?php endforeach; ?>
                </ul>
                          
                <h2><?php _e("WishList Content Archiver (<i>{$mode}</i>)",'wl-contentcontrol'); ?></h2>
                <form  name="post_exp" id="post_exp" action="?page=ContentControl&module=ContentArchiver&mode=<?php echo $mode; ?>" method="post">
            <?php
                if(isset($_POST['apply_expiry'])){
                    $post_ids = $_POST['post_id'];
                    $wlcc_expdate = $_POST['wlcc_expdate'];

                    if(count($post_ids) <= 0){
                        $wlcccmerror_msg[0] = __('No post selected.','wishlist-member');
                    }
                   $wlccexpdate = ($wlcc_expdate == "" ? $datenow:$wlcc_expdate);
                   $wlccexpdate = date_parse($wlccexpdate);
                   $date = date('Y-m-d H:i:s',mktime((int)$wlccexpdate["hour"],(int)$wlccexpdate["minute"],0,(int)$wlccexpdate["month"],(int)$wlccexpdate["day"],(int)$wlccexpdate["year"]));
                   for($i=0;$i<count($post_ids);$i++){
                       if(!$this->isvalid_date($date,$post_ids[$i])){
                           $wlcccmerror_msg[] =  __('Archive Date is invalid ('.$this->format_date($date).'). The Date selected has already passed the published date of the post. Please select a Date after the post\'s published date.','wishlist-member') ;
                       }
                   }

                   $data[] = array("expdate"=>$date);
                    if(count($wlcccmerror_msg) <= 0){
                        for($i=0;$i<count($post_ids);$i++){
                            $this->SavePostExpiryDate($post_ids[$i],$show_level,$data[0]);
                        }
                        unset($data);
                        echo "<div class='updated fade'>".__('<p>Archive Date(s) was successfully updated for the selected posts.</p>','wishlist-member')."</div>";
                    }else{
                        echo "<div class='error fade'>".__('<p>You have errors in your Archive settings.</p>','wishlist-member')."<p>";
                        foreach((array)$wlcccmerror_msg as $key=>$err){
                            echo '- ' .$err .'<br />';
                        }
                        echo '</p></div>';
                        unset($wlcccmerror_msg);
                    }
                }else if(isset($_POST['update_expiry'])){ // update due date
                    $expdate_id = $_POST['expdate_id'];
                    $expdate_pid = $_POST['expdate_pid'];
                    $edit_post = get_post($expdate_pid);
                    $post_date = $edit_post->post_date;
                    $wlcc_expdate = $_POST['wlcc_expdate'];
                    $post_ids = array($expdate_pid);
                    
                   $wlccexpdate = ($wlcc_expdate == "" ? $datenow:$wlcc_expdate);
                   $wlccexpdate = date_parse($wlccexpdate);
                   $date = date('Y-m-d H:i:s',mktime((int)$wlccexpdate["hour"],(int)$wlccexpdate["minute"],0,(int)$wlccexpdate["month"],(int)$wlccexpdate["day"],(int)$wlccexpdate["year"]));
                   for($i=0;$i<count($post_ids);$i++){
                       if(!$this->isvalid_date($date,$post_ids[$i])){
                           $wlcccmerror_msg[] =  __('Archive Date is invalid ('.$this->format_date($date).'). The Date selected has already passed the published date of the post. Please select a Date after the post\'s published date.','wishlist-member') ;
                       }
                   }
                   $data[] = array("expdate"=>$date,"pid"=>$expdate_pid,"expid"=>$expdate_id);
    
                    if(count($wlcccmerror_msg) <= 0){
                        for($i=0;$i<count($post_ids);$i++){
                            $this->SavePostExpiryDate($post_ids[$i],$show_level,$data[0]);
                        }
                        unset($data);
                        $isupdate = false;
                        echo "<div class='updated fade'>".__('<p>Archive Date was successfully updated for the selected posts.</p>','wishlist-member')."</div>";
                    }else{
                        echo "<div class='error fade'>".__('<p>You have errors in your Archive settings.</p>','wishlist-member')."<p>";
                        foreach((array)$wlcccmerror_msg as $key=>$err){
                            echo '- ' .$err .'<br />';
                        }
                        echo '</p></div>';
                        $isupdate = true;
                        unset($wlcccmerror_msg);
                    }
                }else if(isset($_GET['pid']) && $_GET['action']== "remove"){  // remove due date
                    $pid = $_GET['pid'];
                       $this->DeletePostExpiryDate($pid,$show_level);
                       echo '<div class="updated fade"><p>Archive Date was successfully removed from the post.</p></div>';
                }else if(isset($_GET['pid']) && $_GET['action'] == "update"){ // update due date
                     $pid = $_GET['pid'];
                          $post_expiry_dates_update= $this->GetPostExpiryDate($pid,'');
                          if(count($post_expiry_dates_update)>0){
                            $data[] = array("expdate"=>$post_expiry_dates_update[0]->exp_date,"pid"=>$post_expiry_dates_update[0]->post_id,"expid"=>$post_expiry_dates_update[0]->id);
                            $post_ids = array($post_expiry_dates_update[0]->post_id);
                            $isupdate = true;
                            echo '<div class="updated fade"><p>You can now edit the archive date for the post below.</p></div>';
                          }
                }elseif(isset($_POST['save_settings']) && isset($_POST['wlcc_archived_error_page'])){
                    if($_POST['wlcc_archived_error_page'] == "url"){
                        update_option("wlcc_archived_error_page",$_POST['wlcc_archived_error_page']);
                        update_option("wlcc_archived_error_page_url",$_POST['wlcc_archived_error_page_url']);
                    }else{
                        //make sure that we are processing an integer
                        $int_pid = (int)$_POST['wlcc_archived_error_page'];
                        if(is_int($int_pid) && $int_pid >0){
                            update_option("wlcc_archived_error_page",$int_pid);
                            update_option("wlcc_archived_error_page_url","");
                        }
                    }
                    if ( isset( $_POST['wlcc_archived_post_visibility'] ) ) {
                      update_option("wlcc_archived_post_visibility",$_POST['wlcc_archived_post_visibility']);
                    } else {
                      update_option("wlcc_archived_post_visibility",false);
                    }

                    if ( isset( $_POST['wlcc_non_users_access'] ) ) {
                      update_option("wlcc_non_users_access",$_POST['wlcc_non_users_access']);
                    } else {
                      update_option("wlcc_non_users_access",false);
                    }
                    echo "<div class='updated fade'>".__('<p>Settings Saved.</p>','wl-contentcontrol')."</div>";
                }
                    //get the last selected filters
                    $wlcc_showpost = $_SESSION['wlcceshowpost'];
                    $wlcc_showlevel = $_SESSION['wlcceshowlevel'];
            ?>

            <?php if($mode=='settings'): //Settings Management
                $wlm_magicpage = (int) $WishListMemberInstance->MagicPage(false);
                $scontents = $this->GetPostExpiryDate();
                $ex_pages = array($wlm_magicpage);
                foreach($scontents as $scontent){
                    $ex_pages[] = (int)$scontent->post_id;
                }
                $pages = get_pages("exclude=" .implode(',',$ex_pages));
                $wlcc_archived_error_page = get_option("wlcc_archived_error_page");
                $wlcc_archived_error_page = $wlcc_archived_error_page ? $wlcc_archived_error_page: "url";
                if($wlcc_archived_error_page){
                    $wlcc_archived_error_page_url = $wlcc_archived_error_page == "url" ? get_option("wlcc_archived_error_page_url") : "";
                    $wlcc_archived_error_page_url = $wlcc_archived_error_page_url ? $wlcc_archived_error_page_url:"";
                }

                $wlcc_archived_post_visibility = get_option("wlcc_archived_post_visibility");
                $wlcc_archived_post_visibility = is_array( $wlcc_archived_post_visibility ) ? $wlcc_archived_post_visibility : array();

                $wlcc_non_users_access = get_option("wlcc_non_users_access");
            ?>
                <p>&nbsp;</p>
                <h3>Archived Content Redirect Page</h3>
                <p>Please specify the page or url you want to redirect the people who tries to access an archived content.</p>
                <blockquote>
                  <p>
                      <select name="wlcc_archived_error_page" id="wlcc_archived_error_page">
                          <option value="url" <?php echo $wlcc_archived_error_page=="url;"?'selected="true"':''; ?> >Enter URL below&nbsp;&nbsp;&nbsp;&nbsp;</option>
                      <?php foreach((array)$pages AS $id=>$page):?>
                          <option value="<?php echo $page->ID ?>" <?php echo $wlcc_archived_error_page==$page->ID?'selected="true"':''; ?> ><?php echo $page->post_title;?>&nbsp;&nbsp;&nbsp;&nbsp;</option>
                      <?php endforeach;?>
                      </select><br />
                      <input type="text" name="wlcc_archived_error_page_url" value="<?php echo $wlcc_archived_error_page_url; ?>" size="60" />
                  <p>
                </blockquote>

                <p>&nbsp;</p>
                <h3>Non-users Content Access</h3>
                <p>Allows you to display <strong>archived post/page</strong> in post listings for the following users.</p>
                <blockquote>
                  <p>
                    <?php $checked = $wlcc_non_users_access ? "" : "checked='checked'"; ?>
                    <input type='radio' name="wlcc_non_users_access" value='0' <?php echo $checked; ?> />
                    Automatically hide content with archive date to non-users
                  </p>
                  <p>
                    <?php $checked = $wlcc_non_users_access ? "checked='checked'" : ""; ?>
                    <input type='radio' name="wlcc_non_users_access" value='1' <?php echo $checked; ?> />
                    Only archived content are hidden to non-users
                  </p>
                </blockquote>

                <p>&nbsp;</p>
                <h3>Archived Content Visibility</h3>
                <p>Allows you to display <strong>archived post/page</strong> in post listings for the following users.</p>
                <blockquote>
                  <p>
                    <?php $checked = in_array( "non_users", $wlcc_archived_post_visibility ) ? "checked='checked'" : ""; ?>
                    <input type='checkbox' name="wlcc_archived_post_visibility[]" value='non_users' <?php echo $checked; ?> />
                    <strong>Non-users</strong>. <em>Users who are not logged in.</em>
                  </p>
                  <p>
                    <?php $checked = in_array( "non_members", $wlcc_archived_post_visibility ) ? "checked='checked'" : ""; ?>
                    <input type='checkbox' name="wlcc_archived_post_visibility[]" value='non_members' <?php echo $checked; ?> />
                    <strong>Non-members</strong>. <em>Logged-in users with no active membership level.</em>
                  </p>
                  <p>
                    <?php $checked = in_array( "members", $wlcc_archived_post_visibility ) ? "checked='checked'" : ""; ?>
                    <input type='checkbox' name="wlcc_archived_post_visibility[]" value='members' <?php echo $checked; ?> />
                    <strong>Members</strong>. <em>Logged-in users whose level have no access to the archived content.</em>
                  </p>
                </blockquote>
                <p><br />
                    <input type="submit" name="save_settings" id="save_settings" value="<?php _e('Save Settings','wishlist-member-sched'); ?>" class="button-secondary" />
                </p>
            <?php else: ?>
            <?php //Archiver Page/Post Management
                $show_post = ($wlcc_showpost == "all" || $wlcc_showpost == "expiry" || $wlcc_showpost == "noexpiry" || $wlcc_showpost == "protected") ? $wlcc_showpost : $show_post;
                $show_level = (!empty($wlcc_showlevel)) ? $wlcc_showlevel : $show_level;
                foreach((array)$wpm_levels AS $id=>$level){
                    if($show_level == ''){$show_level = $id;}
                }

                /*variables for page numbers*/
                 $pagenum = isset($_POST['pagenum']) ? $_POST['pagenum']:$_GET['pagenum'];
                 $pagenum = ($pagenum > 0) ? absint( $pagenum ) : 0;
                 if (empty($pagenum) )
                        $pagenum = 1;
                        $per_page = 20;
                        $start = ($pagenum == '' || $pagenum < 0) ? 0 : (($pagenum - 1) * $per_page);

                      if($sort == "exp_date"){
                        $exp_sort = true;
                        $sort_holder = "ID";
                      }else{$sort_holder = $sort;}
                      
                $posts = $this->GetPosts($show_post,$mode,$show_level,$start,$per_page,$sort_holder,$asc); //  get the posts
                $cn_posts = $this->GetPosts($show_post,$mode,$show_level); // get post count for pagination

                  $posts_count = count($cn_posts);
                  /*Prepare pagination*/
                  $num_pages = ceil($posts_count / $per_page);
                  $link_arr = array('pagenum'=>'%#%','show_level'=>$show_level,'show_post'=>$show_post,'sort'=>$sort,'asc'=>$asc);
                  $page_links = paginate_links( array(
                          'base' => add_query_arg($link_arr),
                          'format' => '',
                          'prev_text' => __('&laquo;'),
                          'next_text' => __('&raquo;'),
                          'total' => $num_pages,
                          'current' => $pagenum
                   ));
            ?>
                <p>
                <select name="show_level" id="show_level" onchange="wlcc_submit(this.form,false,false)">
                <?php foreach((array)$wpm_levels AS $id=>$level):?>
                    <option value="<?php echo $id ?>" <?php echo $show_level==$id?'selected="true"':''; ?> ><?php echo $level['name'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
                <?php endforeach;?>
                </select>
                <select name="show_post" id="show_post" onchange="document.post_exp.submit()">
                  <option value="expiry" <?php echo $show_post=='expiry'?'selected="true"':''; ?> >Show <?php echo $menu_modes[$mode]; ?> with Archive Date</option>
                  <option value="noexpiry" <?php echo $show_post=='noexpiry'?'selected="true"':''; ?> >Show <?php echo $menu_modes[$mode]; ?> without an Archive Date</option> 
                  <option value="all" <?php echo ($show_post=='all' || $all)?'selected="true"':''; ?> >Show All <?php echo $menu_modes[$mode]; ?></option>
                  <option value="protected" <?php echo $show_post=='protected'?'selected="true"':''; ?> >Show All Protected <?php echo $menu_modes[$mode]; ?></option>
                </select>
                </p>
                <?php if(count($posts) > 0):?>
                <p>Viewing Membership Level: <strong><?php  echo $wpm_levels[$show_level]['name']; ?></strong></p>
                <div class="tablenav">Please select the post(s) you wish to archive.<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                        number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                        number_format_i18n( min( $pagenum * $per_page, $posts_count ) ),
                        number_format_i18n( $posts_count ),
                        $page_links
                ); echo $page_links_text; ?></div></div>
                <script type="text/javascript">
                    function wlcce_confirm_remove(lnk){
                        remove_msg = "Are sure you want to remove the archive date of the post?";
                        if(confirm(remove_msg)){
                            window.location.href = lnk;
                        }
                    }
                </script>
                <table class="widefat" id='wlcc_postexpiry'>
                    <thead>
                    <tr>
                        <?php
                            $query_lnk = "page=ContentControl&module=ContentArchiver";
                            $query_lnk .= ($mode=="" ? "":("&mode=".$mode));
                            $query_lnk .= ($pagenum=="" ? "":("&pagenum=".$pagenum));
                            $query_lnk .= ($show_post=="" ? "":("&show_post=".$show_post));
                            $query_lnk .= ($show_level=="" ? "":("&show_level=".$show_level));
                        ?>
                      <th  style="width:2%;" nowrap scope="col" class="check-column"><input type="checkbox" onClick="wpm_selectAll(this,'wpm_broadcast')" <?php echo $isupdate ? 'disabled="disabled"':''; ?> /></th>
                      <th style="width:30%;"><a href="?<?php echo $query_lnk; ?>&sort=post_title&asc=<?php echo ($sort =="post_title" && $asc == 1) ? 0:1; ?>"><?php _e('Post Title','wishlist-member-archiver'); ?></a></th>
                      <th style="width:20%;" class="num"><?php _e('Category','wishlist-member-archiver'); ?></th>
                      <th style="width:8%;" class="num"><?php _e('Author','wishlist-member-archiver'); ?></th>
                      <th style="width:13%;" class="num"><a href="?<?php echo $query_lnk; ?>&sort=post_date&asc=<?php echo ($sort =="post_date" && $asc == 1) ? 0:1; ?>"><?php _e('Start Date','wishlist-member-archiver'); ?></a></th>
                      <th style="width:25%;" class="num"><a href="?<?php echo $query_lnk; ?>&sort=exp_date&asc=<?php echo ($sort =="exp_date" && $asc == 1) ? 0:1; ?>"><?php _e('Archive Date','wishlist-member-archiver'); ?></a></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                            $wlccca_posts = array();
                            foreach((array)$posts as $k=>$arr_posts){
                                $post_expiry = $this->GetPostExpiryDate($arr_posts->ID,$show_level);
                                if(count($post_expiry) > 0){
                                    $wlccca_posts[$k] = (object) array("post_title"=>$arr_posts->post_title,"ID"=>$arr_posts->ID,"post_author"=>$arr_posts->post_author,"post_date"=>$arr_posts->post_date,"post_status"=>$arr_posts->post_status,"exp_date"=>$post_expiry[0]->exp_date);
                                }else{
                                    $wlccca_posts[$k] = (object) array("post_title"=>$arr_posts->post_title,"ID"=>$arr_posts->ID,"post_author"=>$arr_posts->post_author,"post_date"=>$arr_posts->post_date,"post_status"=>$arr_posts->post_status,"exp_date"=>'');
                                }
                                if($exp_sort)$wlccca_posts = $this->subval_sort($wlccca_posts,"exp_date",true, $asc);
                            }
                        ?>
            <?php foreach((array)$wlccca_posts as $sched_post): ?>
                        <tr class="<?php echo $alt++%2?'':'alternate'; ?>">
                            <th scope="row" class="check-column"><input type="checkbox" name="post_id[]" value="<?php echo $sched_post->ID; ?>" <?php echo (in_array($sched_post->ID,(array)$post_ids) && count($data) > 0) ? 'checked="checked"':''; ?>  <?php echo $isupdate ? 'disabled="disabled"':''; ?> /></th>
                            <td ><a target="_blank" href="<?php echo admin_url().'post.php?post=' .$sched_post->ID .'&action=edit'; ?>"><?php echo $sched_post->post_title; ?></a></td>
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
                                    if($sched_post->exp_date != ""){
                                            echo (($sched_post->exp_date != "" && !$this->isvalid_date($sched_post->exp_date))? "Last":"On") .' <strong>' .$this->format_date($sched_post->exp_date) .'</strong> (';
                                            echo ' <a title="Remove Archive Date" style="color:#333aa;" href="javascript:void(0);" onclick="wlcce_confirm_remove(\'?'.$remove_update_lnk.'&action=remove&pid=' .$sched_post->ID.'\')">Remove</a> ';
                                            if($isupdate && in_array($sched_post->ID,(array)$post_ids)){
                                                echo "";
                                            }else{
                                                echo '| <a title="Edit Archive Date" style="color:#333aa;" href="?'.$remove_update_lnk.'&action=update&pid=' .$sched_post->ID .'#updatetag" >Edit</a> ';
                                            }
                                            echo ")";
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
                                    $pid = $data[0]['pid'];
                                    $edit_post = get_post($pid)
                               ?>
                                <div class="updated-clone">
                                    <?php
                                         $expstr = " on <strong>" .$this->format_date($data[0]['expdate']) ."</strong>";
                                    ?>
                                    <p>You are currently editing archive date for "<strong><?php echo $edit_post->post_title;?></strong>" which is set to expire <?php echo $expstr; ?>
                                    </p>
                                </div>
                              </div>
                              <input type="hidden" name="expdate_id" value="<?php echo $data[0]['expid'] ?>" />
                              <input type="hidden" name="expdate_pid" value="<?php echo $data[0]['pid'] ?>" />
                        <?php else:  ?>
                            <p>Please select the date that you would like the selected post(s) to be archived.</p>
                        <?php endif; ?>
                    <p>Current Server Date and Time: <strong><?php echo $this->format_date(date('Y-m-d H:i:s'),'F j, Y g:i a'); ?></strong><?php echo $wlcc->Tooltip("wlcccm-serverdate-tooltip"); ?></p>
                        <div id="wlcclevels">
                            <table class="widefat" id='wlcc_postexpiry'>
                                <thead>
                                    <tr style="width:100%;">
                                        <th style="width: 60%;border-bottom: 1px solid #aaaaaa;"><?php echo ($isupdate && count($data)>0)?"New ":""; ?><?php _e('Archive Date'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="width:100%;" class="<?php echo $alt++%2?'':'alternate'; ?>">
                                         <td style="width: 40%;border-bottom: 1px solid #eeeeee;">
                                            <input type="text" class="datepickerbox" id="wlcc_expdate" name="wlcc_expdate" value="<?php if(count($data)>0){echo $this->format_date($data[0]['expdate'],'m/d/Y h:i A');}else{echo $this->format_date($datenow,'m/d/Y h:i A');} ?>" size="25" /><span class="datepicker-img"><img src="<?php echo $wlcc->pluginurl.'/images/calendar.png'; ?>" /></span>
                                         </td>
                                    </tr>
                                </tbody>
                            </table>
                       </div><br />
               <?php if($isupdate && count($data)>0): //show this button when editing ?>
                  <input type="submit" class="button-primary" name="update_expiry" id="update_expiry" value="<?php _e('Update Expiration','wishlist-member-archiver'); ?>" />
                  <input type="submit" class="button-secondary" name="cancel_expiry" id="cancel_expiry" value="<?php _e('Cancel','wishlist-member-archiver'); ?>" />
               <?php else: //show this message when editing?>
                  <input type="submit" class="button-primary" name="apply_expiry" id="apply_expiry" value="<?php _e('Apply Expiration to Selected Post','wishlist-member-archiver'); ?>" />
               <?php endif; ?>
                    <input type="hidden" name="pagenum" value="<?php echo $pagenum ?>" />
                    <input type="hidden" name="sort" value="<?php echo $sort ?>" />
                    <input type="hidden" name="asc" value="<?php echo $asc ?>" />
                <?php else: ?>
                    <hr />
                    <?php if($show_post == "all"): ?>
                        <p style="text-align:center;">You currently have no <?php echo $menu_modes[$mode]; ?>.</p>
                    <?php else: ?>
                        <p style="text-align:center;">There are currently no <?php echo $menu_modes[$mode]; ?> to show for this level.</p>
                    <?php  endif; ?>
                    <hr />
                <?php  endif; ?>
            <?php endif; //ARchiver Page/Post Management ?>
              <input type="hidden" name="frmsubmit" value="" />
              </form>
                <br /><br />
            <?php
            }
     //page options
            function ContentArchOptions(){
                $post_id = $_GET['post'];
                $custom_types = get_post_types(array('public'=> true,"_builtin"=>false));
                $ptypes = array_merge(array("post","page"),$custom_types);
                $post_type = $post_id ? get_post_type($post_id):$_GET['post_type'];
                if($post_type){
                     if (!in_array($post_type,$ptypes) )return false; //do not display option on pages
                }else{
                    return false;
                }

                global $WishListMemberInstance,$WishListContentControl;
                $wpm_levels=$WishListMemberInstance->GetOption('wpm_levels');

                //default date
                $wlccexpdate = date_parse(date('Y-m-d H:i:s'));
                $wlccexpdate = date('Y-m-d H:i:s',mktime(0,0,0,(int)$wlccexpdate["month"],(int)$wlccexpdate["day"],(int)$wlccexpdate["year"]));
                $wlcc_expdate = $this->format_date($wlccexpdate,'m/d/Y h:i A');
                $wlcc_error=$WishListMemberInstance->GetOption('wlccca_error');
                if($wlcc_error != ""){
                    echo $wlcc_error;
                    $WishListMemberInstance->DeleteOption('wlccca_error');
                }
            ?>
                      <div class="inside">
                          <span style="font-style: italic;"><?php _e('Please select the Membership Level and specify the date this Post will be archived.','wl-contentcontrol'); ?></span> <br /><br />
                                    <table class="widefat" id='wlcc_ca' style="text-align: left;" cellspacing="0">
                                        <thead>
                                        <tr style="width:100%;">
                                            <th style="width: 60%;"> <?php _e('Membership Level/s'); ?></th>
                                            <th style="width: 40%;"> <?php _e('Archive Date'); ?> </th>
                                        </tr>
                                        </thead>
                                    </table>
                                <div id="wlcclevels_ca" style="text-align:left;height:165px;overflow:auto;">
                                    <table class="widefat" id="wlcc_ca" cellspacing="0" style="text-align:left;">
                                        <tbody>
                                        <?php foreach((array)$wpm_levels AS $id=>$level){              
                                            $post_expiry = $this->GetPostExpiryDate($post_id,$id);
                                            if(count($post_expiry) > 0 && $post_id){
                                                $date = $this->format_date($post_expiry[0]->exp_date,'m/d/Y h:i A');
                                                $check = true;
                                            }else{
                                                $date = $wlcc_expdate;
                                                $check = false;
                                                echo '<script>jQuery(document).ready(function(){wlcca_disable_fields("' .$id.'");});</script>';
                                            }
                                        ?>
                                        <tr id="tr<?php echo $id;?>" style="width:100%;" class="<?php echo $alt++%2?'':'alternate'; ?>">
                                            <td style="width: 60%;border-bottom: 1px solid #eeeeee;"><input class="wlccca_chk_options" type='checkbox' name='exp[<?php echo $id; ?>]' value='exp' <?php echo $check ? 'checked="checked"':''; ?> /><label>  <strong><?php echo $level['name']; ?></strong></label></td>
                                             <td style="width: 40%;border-bottom: 1px solid #eeeeee;">
                                                 <input type="text" class="datepickerbox" id="wlcc_expiry<?php echo $id;?>" name="wlcc_expiry[<?php echo $id;?>]" value="<?php echo $date; ?>" size="25" /><span class="datepicker-img" href="javascript:void(0);"><img src="<?php echo $WishListContentControl->pluginurl.'/images/calendar.png'?>" /></span>
                                             </td>
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                                </div>
                        </div>
                        <input type='hidden' name='wlccca_save_marker' value='1'>
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
                $table = $wpdb->prefix."wlcc_contentarchiver";
                $structure ="CREATE TABLE IF NOT EXISTS " .$table ." (
                    `id` bigint(20) NOT NULL auto_increment,
                    `post_id` bigint(20) NOT NULL,
                    `mlevel` varchar(15) NOT NULL,
                    `exp_date` datetime,
                    `date_added` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
                    PRIMARY KEY  (`id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
                $wpdb->query($structure);
            }
    //save content archiver options
            function SaveContentArchOptions(){
                global $WishListMemberInstance,$WishListContentControl;
                $post_ID = $_POST['post_ID'];
                $custom_types = get_post_types(array('public'=> true,"_builtin"=>false));               
                $ptypes = array_merge(array("post","page"),$custom_types);                
                $post_type = $post_ID ? get_post_type($post_ID):$_GET['post_type'];
                if($post_type){
                     if (!in_array($post_type,$ptypes) )return false; //do not display option on pages
                }else{
                    return false;
                }

                $wlccca_save_marker= $_POST['wlccca_save_marker'];
                if($wlccca_save_marker != 1) return false;

                $wpm_levels=$WishListMemberInstance->GetOption('wpm_levels');
                $exp = $_POST['exp'];
                $wlcc_expiry = $_POST['wlcc_expiry'];
                            $data = array();
                            $errs = "";
                $wlccexpdate = date_parse(date('Y-m-d H:i:s'));
                $datenow = date('Y-m-d H:i:s',mktime(0,0,0,(int)$wlccexpdate["month"],(int)$wlccexpdate["day"],(int)$wlccexpdate["year"]));

                foreach((array)$wpm_levels AS $id=>$level){
                     if($exp[$id] == 'exp'){
                        $date = "";
                       $wlccexpiry = ($wlcc_expiry[$id] == "" ? $datenow:$wlcc_expiry[$id]);
                       $wlccexpdate = date_parse($wlccexpiry);
                       $date = date('Y-m-d H:i:s',mktime((int)$wlccexpdate["hour"],(int)$wlccexpdate["minute"],0,(int)$wlccexpdate["month"],(int)$wlccexpdate["day"],(int)$wlccexpdate["year"]));
                       if(!$this->isvalid_date($date,$post_ID)){
                           $errs .= __('<p>-Archive Date for <b>' .$wpm_levels[$id]['name'] .'</b> is invalid ('.$this->format_date($date).').</p>','wishlist-member') ;
                       }
                        $data[$id] = array("expdate"=>$date);
                     }else{
                        $data[$id] = array();
                     }
                }
                if($errs == ""){
                     foreach((array)$data AS $level=>$datum){
                         if(count($datum)>0){
                            $this->SavePostExpiryDate($post_ID,$level,$datum);
                         }else{
                            $this->DeletePostExpiryDate($post_ID,$level);
                         }
                     }
                }else{
                    $e =  __('<p>You have errors in your WL Content Manager settings.</p>','wishlist-member');
                     $errs = $e .$errs;
                     $e = __('<p>The Date selected has already passed the published date of the post. Please select a Date after the published date of the post.</p>','wishlist-member');
                     $errs = $errs .$e;
                     $wlcc_errors .= "<div class='error fade'>" .__($errs) ."</div>";
                     $WishListMemberInstance->SaveOption('wlccca_error',$wlcc_errors);
                }
            }
    //save post expiry date
             function SavePostExpiryDate($post_id,$mlevel,$d){
                global $wpdb;
                $table = $wpdb->prefix ."wlcc_contentarchiver";
                $data = $d;
                if(count($this->GetPostExpiryDate($post_id,$mlevel)) > 0){
                        $q = "UPDATE $table SET exp_date = '" .$data['expdate'] ."' WHERE mlevel='" .$mlevel ."' AND post_id=" .$post_id;
                }else{
                        $q = "INSERT INTO $table(post_id,mlevel,exp_date) VALUES('" .$post_id ."','" .$mlevel."','".$data['expdate']."')";
                }
                $wpdb->query($q);
            }
    //get post expiry date
            function GetPostExpiryDate($post_id='',$mlevel='',$start=0,$limit=0){
                global $wpdb;
                $table = $wpdb->prefix ."wlcc_contentarchiver";
                if(is_array($mlevel)){
                        $q_mlevel = " mlevel IN ('" .implode('\',\'',$mlevel) ."') ";
                }else{
                        $q_mlevel = " mlevel='" .$mlevel ."' ";
                }

                if($post_id!='' && $mlevel!=''){
                        $q = "SELECT * FROM $table WHERE post_id=" .$post_id ." AND $q_mlevel";
                }else if($post_id!=''){
                        if($limit > 0){
                                $q = "SELECT * FROM $table WHERE post_id=" .$post_id ." ORDER BY date_added DESC LIMIT  " .$start ."," .$limit;
                        }else{
                                $q = "SELECT * FROM $table WHERE post_id=" .$post_id;
                        }
                }else if($mlevel!=''){
                        if($limit > 0){
                                $q = "SELECT * FROM $table WHERE $q_mlevel ORDER BY date_added DESC LIMIT  " .$start ."," .$limit;
                        }else{
                                $q = "SELECT * FROM $table WHERE $q_mlevel";
                        }
                }else if($limit > 0){
                        $q = "SELECT * FROM $table ORDER BY date_added DESC LIMIT  " .$start ."," .$limit;
                }else{
                        $q = "SELECT * FROM $table ORDER BY date_added DESC";
                }
                return $wpdb->get_results($q);
            }
    //delete post expiry date
            function DeletePostExpiryDate($post_id,$mlevel=''){
                global $wpdb;
                $table = $wpdb->prefix."wlcc_contentarchiver";
                if(is_array($post_id)){
                                $post_ids = implode(',',$post_id);
                                if($mlevel !=''){
                                        $q = "DELETE FROM $table WHERE mlevel='" .$mlevel ."' AND post_id IN (" .$post_ids .")";
                                }else{
                                        $q = "DELETE FROM $table WHERE post_id IN (" .$post_ids .")";
                                }
                }else{
                    if($post_id != ""){
                        if($mlevel !=''){
                                $q = "DELETE FROM $table WHERE  mlevel='" .$mlevel ."' AND post_id=" .$post_id;
                        }else{
                                $q = "DELETE FROM $table WHERE post_id=" .$post_id;
                        }
                    }
                }
                $wpdb->query($q);
            }
	/**
	 * Function to get Protected|Expired|ALL Posts
         * Return: Array()
	 */
            function GetPosts($show_post,$ptype,$show_level='',$start=0,$per_page=0,$sort="ID",$asc=1){
                global $wpdb,$WishListMemberInstance;
                $table1 = $wpdb->prefix."posts";
                $table2 = $wpdb->prefix."wlcc_contentarchiver";
                $limit = "";
                if($per_page >0) $limit =  " LIMIT " .$start ."," .$per_page;
                $order = " ORDER BY " .$sort .($asc == 1 ? " ASC":" DESC");
                if($show_post == 'all' || $show_post == ''){
                        $q = "SELECT ID,post_author,post_status,post_date,post_modified,post_title,post_content FROM $table1 WHERE post_type= '{$ptype}' AND post_status='publish'" .$order .$limit;
                }else if($show_post == 'expiry'){
                   if($show_level == ''){
                        $q = "SELECT DISTINCT $table1.ID,$table1.post_author,$table1.post_status,$table1.post_date,$table1.post_modified,$table1.post_title,$table1.post_content FROM $table1 INNER JOIN $table2 ON  $table1.ID=$table2.post_id AND $table1.post_type='{$ptype}' AND $table1.post_status='publish'" .$order .$limit;
                   }else{
                       $q = "SELECT DISTINCT $table1.ID,$table1.post_author,$table1.post_status,$table1.post_date,$table1.post_modified,$table1.post_title,$table1.post_content FROM $table1 INNER JOIN $table2 ON  $table1.ID=$table2.post_id AND $table1.post_type='{$ptype}' AND $table1.post_status='publish' AND $table2.mlevel = '$show_level'" .$order .$limit;
                   }
                }else if($show_post == 'noexpiry'){
                   if($show_level == ''){
                       $q = "SELECT DISTINCT $table1.ID,$table1.post_author,$table1.post_status,$table1.post_date,$table1.post_modified,$table1.post_title,$table1.post_content FROM $table1 WHERE $table1.ID NOT IN (SELECT post_id FROM $table2) AND $table1.post_type='{$ptype}' AND $table1.post_status='publish'" .$order .$limit;
                   }else{
                       $q = "SELECT DISTINCT $table1.ID,$table1.post_author,$table1.post_status,$table1.post_date,$table1.post_modified,$table1.post_title,$table1.post_content FROM $table1 WHERE $table1.ID NOT IN (SELECT post_id FROM $table2 WHERE $table2.mlevel = '$show_level') AND $table1.post_type='{$ptype}' AND $table1.post_status='publish'" .$order .$limit;
                   }
                }else if($show_post == 'protected'){
                    //get users protected post  for this level
                    //get users unprotected content for this user
                    $wpm_levels=$WishListMemberInstance->GetOption('wpm_levels');
                    $ids = array(); $has_all_access = false;
                    //check if the level has all access to post
                    if($wpm_levels[$show_level]['allposts']){
                        $has_all_access = true;
                    }
                    if($has_all_access){ //if the user has all access to posts
                        $q = "SELECT ID,post_author,post_date,post_status,post_modified,post_title,post_content FROM $table1 WHERE post_type= '{$ptype}' AND post_status='publish'" .$order .$limit;
                    }else{
                       $x=$WishListMemberInstance->GetMembershipContent($ptype,$show_level);
                       $q = "SELECT ID,post_author,post_date,post_status,post_modified,post_title,post_content FROM $table1 WHERE post_type= '{$ptype}' AND post_status='publish' AND ID IN('" .implode(',',$x)."')" .$order .$limit;
                    }
                }
                return $wpdb->get_results($q);
            }

     //function to get the expired post for the member
            function GetExpiredPost(){
                global $WishListMemberInstance;
                $date_today       = date('Y-m-d H:i:s'); // get date today
                $wpm_current_user = wp_get_current_user();
                $levels           = array();
                $pplevel          = array();
                $user_pp_posts    = array();
                $expired_posts    = array();
                $unexpired_posts  = array();
                $wlcc_non_users_access = get_option("wlcc_non_users_access");

                if ( $wpm_current_user->ID > 0 ) {
                  $levels = $this->get_users_level( $wpm_current_user->ID ); // get users membership levels
                  //remove payper post membership level
                  foreach((array)$levels as $id=>$level){
                      if(strpos($level, "U") !== false){
                          $pplevel[] = $level;
                          unset($levels[$id]);
                      }
                  }
                  if( method_exists( $WishListMemberInstance, 'GetUser_PayPerPost' ) &&  count($pplevel) > 0 ) {
                      $user_pp_posts = $WishListMemberInstance->GetUser_PayPerPost( $pplevel, false , null, true );
                  }
                }
                //get the post with expiration date
                if ( count( $levels ) > 0 ) {
                    $mlevel_post = $this->GetPostExpiryDate('',$levels); //get all the post with expiry date
                } else {
                    $mlevel_post = $this->GetPostExpiryDate(); //if not logged in or dont have membership level
                }

                // start checking the posts with expiration date if the user has access
                foreach((array)$mlevel_post as $lvl_post){

                  $postdate_diff = $this->date_diff( $lvl_post->exp_date , $date_today, 86400 ); //+ result means expired
                  if( count( $levels ) <= 0 ) { //non users, or non members

                    if ( $wlcc_non_users_access ) {
                      if ( $postdate_diff > 0 ) { //show expired content
                          $expired_posts[] = $lvl_post->post_id;
                      }
                    } else {
                      //do not show content with expiration date
                      $expired_posts[] = $lvl_post->post_id;
                    }

                  } else {
                    //get level registration date of the user
                    //$user_leveldate = date('Y-m-d H:i:s',$WishListMemberInstance->UserLevelTimeStamp($wpm_current_user->ID,$lvl_post->mlevel));
                    $user_leveldate = gmdate('Y-m-d H:i:s', $WishListMemberInstance->UserLevelTimestamp($wpm_current_user->ID, $lvl_post->mlevel) + $WishListMemberInstance->GMT);
                    $leveldate_diff = $this->date_diff( $lvl_post->exp_date , $user_leveldate, 86400 ); //+ result means user cannot access this post

                    if ( $postdate_diff > 0 ) { // check if the post is expired and if the user has previous access to the post.
                      if ( $leveldate_diff > 0 ) {
                        $expired_posts[] = $lvl_post->post_id;
                      }
                    } else {
                        $unexpired_posts[] = $lvl_post->post_id;
                    }
                  }

                }

                $unexpired_posts = array_unique( $unexpired_posts ); //remove duplicate post id from unexpired post
                $expired_posts = array_diff( $expired_posts, $unexpired_posts ); // take out post if the user still has access on it using different membership level
                $expired_posts = array_unique( $expired_posts ); //remove duplicate post id from expired post

                //remove users pp post from the list
                if ( count( $user_pp_posts ) > 0 ) {
                  $expired_posts = array_diff( $expired_posts, $user_pp_posts );
                }

                return $expired_posts;
            }
        /*
         * FUNCTION being called to add page/post metabox (otions)
        */
            function ContentArchiverMetaBoxes(){
                add_meta_box(
                    'wlccca-meta-box',
                    __( 'WishList Content Archiver', 'wl-contentcontrol-archiver' ),
                    array(&$this,'ContentArchOptions'),
                    'post'
                );
                add_meta_box(
                    'wlccca-meta-box',
                    __( 'WishList Content Archiver', 'wl-contentcontrol-archiver' ),
                    array(&$this,'ContentArchOptions'),
                    'page'
                );
                //get custom post type
                $custom_types = get_post_types(array('public'=> true,"_builtin"=>false));
                foreach($custom_types as $i=>$ctype){
                  add_meta_box(
                      'wlccca-meta-box',
                      __( 'WishList Content Archiver', 'wl-contentcontrol-archiver' ),
                      array(&$this,'ContentArchOptions'),
                      $ctype
                  );
                }
            }

            //redirect user to error page if it is scheduled
            function PreGetPost($query){
                global $wpdb;
                $is_single = is_single() || is_page() ? true:false;
                //if this is not a single post or page or its in the admin area, dont try redirect
                if ( ! $is_single || is_admin() ) return $query;

                //retrieve the post id and post name (if needed)
                $pid = false;
                $name = false;
                if ( is_page() ) {
                    $pid = isset($query->query['page_id']) ? $query->query['page_id']:false;
                    $name = !$pid && isset($query->query['pagename']) ? $query->query['pagename']:"";
                } elseif ( is_single() ) {
                    $pid = isset($query->query['p']) ? $query->query['p']:false;
                    $name = isset($query->query['name']) ? $query->query['name']:"";
                } else {
                  $pid = false;
                  $name = "";
                }
                //get the post id based from the post name we got
                $name_array = explode("/", $name);
                $name = array_slice($name_array, -1, 1); //get the last element
                $name = $name[0];
                if ( $name ) {
                    $pid = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name='{$name}'");
                } else {
                    return $query;
                }

                //if theres a postid, lets redirect
                if ( $pid ) {
                    $archived_content = $this->GetExpiredPost();
                    if(in_array($pid,$archived_content)){
                        //get settings
                        $wlcc_archived_error_page = get_option("wlcc_archived_error_page");
                        $wlcc_archived_error_page = $wlcc_archived_error_page ? $wlcc_archived_error_page: "url";
                        if($wlcc_archived_error_page){
                            $wlcc_archived_error_page_url = $wlcc_archived_error_page_url == "url" ? get_option("wlcc_archived_error_page_url") : "";
                            $wlcc_archived_error_page_url = $wlcc_archived_error_page_url ? $wlcc_archived_error_page_url:"";
                        }

                        if($wlcc_archived_error_page == "url"){
                            if($wlcc_archived_error_page_url != ""){
                                $url = trim($wlcc_archived_error_page_url);
                                $p_url = parse_url($url);
                                if(!isset($p_url['scheme'])) $url = "http://" .$url;                              
                                wp_redirect($url);
                                exit(0);
                            }
                        }else{
                            $r_pid = (int) $wlcc_archived_error_page;
                            if(is_int($r_pid) && $r_pid > 0 && !isset($archived_content[$r_pid])){
                                $url = get_permalink($r_pid);
                                if($url){
                                    wp_redirect($url);
                                    exit(0);
                                }
                            }
                        }
                    }
                }

                return $query;
            }

    //activate module
            function Activate(){
                $this->CreateSchedTable();
                //insert Content Archiver Options when creating or editing a post
                //add_action('edit_form_advanced',array(&$this,'ContentArchOptions'));
               // add_action('edit_page_form',array(&$this,'ContentArchOptions'));
                add_action( 'admin_init',array(&$this,'ContentArchiverMetaBoxes'));
                //save Content Archiver Options when savign the post
                add_action('wp_insert_post',array(&$this,'SaveContentArchOptions'));
                //post filters
                add_filter('posts_where',array(&$this,'PostExpirationWhere'));
                add_filter('get_next_post_where',array(&$this,'PostExpirationAdjacentWhere'));
                add_filter('get_previous_post_where',array(&$this,'PostExpirationAdjacentWhere'));

                //filter for get_pages function because it does not use WP_Query
                add_filter('get_pages', array(&$this, 'GetPages'),9999,2);

                add_filter('pre_get_posts', array(&$this, 'PreGetPost'));
            }
    //deactivate module
            function Deactivate(){ //remove filters and actions
                //insert Content Archiver Options when creating or editing a post
               // remove_action('edit_form_advanced',array(&$this,'ContentArchOptions'));
               // remove_action('edit_page_form',array(&$this,'ContentArchOptions'));
                remove_action( 'admin_init',array(&$this,'ContentArchiverMetaBoxes'));
                //save Content Archiver Options when savign the post
                remove_action('wp_insert_post',array(&$this,'SaveContentArchOptions'));
                //post filters
                remove_filter('posts_where',array(&$this,'PostExpirationWhere'));
                remove_filter('get_next_post_where',array(&$this,'PostExpirationAdjacentWhere'));
                remove_filter('get_previous_post_where',array(&$this,'PostExpirationAdjacentWhere'));
                remove_filter('get_pages', array(&$this, 'GetPages'));
                remove_filter('pre_get_posts', array(&$this, 'PreGetPost'));
            }
    /*
        FUNCTIONS FOR FILTERING POSTS
    */
    //functions used to filter the posts
            function PostExpirationWhere($where){
                global $wpdb,$WishListMemberInstance;
                $wpm_current_user=wp_get_current_user();
                $table = $wpdb->prefix."posts";
                $levels = array();
                $utype = "non_users";
                $w = $where;
                if ( ! $wpm_current_user->caps['administrator'] ) { // disregard content expiry for admin

                    //determine the user type
                    if ( $wpm_current_user->ID > 0 ) {
                      $levels = $this->get_users_level( $wpm_current_user->ID ); // get users membership levels
                      //remove payper post membership level
                      foreach((array)$levels as $id=>$level){
                          if(strpos($level, "U") !== false){
                              unset($levels[$id]);
                          }
                      }

                      if( count( $levels ) > 0 ) {
                        $utype = "members";
                      } else {
                        $utype = "non_members";
                      }
                    }

                    $wlcc_archived_post_visibility = get_option("wlcc_archived_post_visibility");
                    $wlcc_archived_post_visibility = is_array( $wlcc_archived_post_visibility ) ? $wlcc_archived_post_visibility : array();

                    $is_single = is_single() || is_page() ? true:false;
                    if ( ! $is_single ) {
                      if ( in_array( $utype, $wlcc_archived_post_visibility ) ) {
                        $expired_posts = array(); //if post listing and settings allow viewing
                      } else {
                        $expired_posts = $this->GetExpiredPost();
                      }
                    } else {
                      $expired_posts = $this->GetExpiredPost();
                    }

                    //filter the post thats not to be shown
                    if(count($expired_posts)>0){
                        $w .= " AND $table.ID NOT IN (" .implode(',',$expired_posts) .")";
                    }
                }
                return $w;
            }
    //functions used to filter the next and previous links
            function PostExpirationAdjacentWhere($where){
                global $wpdb,$WishListMemberInstance,$post;
                $wpm_current_user=wp_get_current_user();
                $current_post_date = $post->post_date;
                $w = $where;
                if ( ! $wpm_current_user->caps['administrator'] ) { // disregard content expiry for admin
                    $expired_posts = $this->GetExpiredPost();
                    //filter the post thats not to be shown
                    if ( count($expired_posts) > 0 ) {
                        $postids = implode(',',$expired_posts) .',' .$post->ID;
                        $w .= " AND p.ID NOT IN (" .$postids .") ";
                    }
                }
                return $w;
            }
    //functions used to filter the get_pages function
            function GetPages( $pages, $args ) {
                global $wpdb, $WishListMemberInstance;
                if ( count( (array) $pages ) <= 0 ) return $pages;
                $wpm_current_user = wp_get_current_user();
                $levels = array();
                $utype = "non_users";
                if ( ! $wpm_current_user->caps['administrator'] ) { // disregard archive content for admin

                    //determine the user type
                    if ( $wpm_current_user->ID > 0 ) {
                      $levels = $this->get_users_level( $wpm_current_user->ID ); // get users membership levels
                      //remove payper post membership level
                      foreach((array)$levels as $id=>$level){
                          if(strpos($level, "U") !== false){
                              unset($levels[$id]);
                          }
                      }

                      if( count( $levels ) > 0 ) {
                        $utype = "members";
                      } else {
                        $utype = "non_members";
                      }
                    }

                    $wlcc_archived_post_visibility = get_option("wlcc_archived_post_visibility");
                    $wlcc_archived_post_visibility = is_array( $wlcc_archived_post_visibility ) ? $wlcc_archived_post_visibility : array();

                    $is_single = false; //post listing always
                    if ( ! $is_single ) {
                      if ( in_array( $utype, $wlcc_archived_post_visibility ) ) {
                        $expired_posts = array(); //if post listing and settings allow viewing
                      } else {
                        $expired_posts = $this->GetExpiredPost();
                      }
                    }

                    if ( count( $expired_posts ) > 0 ) {
                        foreach ( $pages as $pid=>$page ) {
                            if ( in_array( $page->ID, $expired_posts ) ) {
                                unset( $pages[$pid] );
                            }
                        }
                    }
                }
                return $pages;
            }
    /*
        OTHER FUNCTIONS NOT CORE OF CONTENT ARCHIVER GOES HERE
    */
        /*
         * FUNCTION to users membership levels
        */
        function get_users_level( $uid ) {
          global $WishListMemberInstance;
          static $levels = false;
          static $user_id = false;
          if ( $user_id && $user_id == $uid && is_array( $levels ) ) {
            return $levels;
          }

          $user_id = $uid;
          if ( $user_id > 0 ) {
            if ( method_exists( $WishListMemberInstance, 'GetMemberActiveLevels' ) ) {
              $levels = $WishListMemberInstance->GetMemberActiveLevels( $user_id ); // get users membership levels
            } else {
              $levels = $WishListMemberInstance->GetMembershipLevels( $user_id , false, true ); // get users membership levels
            }
          } else {
            $levels = array();
          }

          return $levels;
        }
        /*
         * FUNCTION to Save The current selection
         * on the filter at the WL Content Archiver Dashboard
        */
            function SaveView(){
                $wpm_current_user=wp_get_current_user();
                    if(!session_id()){
                        session_start();
                    }
                if($wpm_current_user->caps['administrator']){
                    if(isset($_POST['frmsubmit'])){
                        $show_level = isset($_POST['show_level']) ? $_POST['show_level']:$_GET['show_level'];
                        $show_post = isset($_POST['show_post']) ? $_POST['show_post']:$_GET['show_post'];
                        $_SESSION['wlcceshowlevel'] = $show_level;
                        $_SESSION['wlcceshowpost'] = $show_post;
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
    //function to format the date
        function format_date($date,$format='M j, Y g:i a'){
            $d1 = date_parse($date);
            $pdate = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],$d1['year']);
            $date = date($format,$pdate);
            return $date;
        }
//function to get date difference needs php5.2
        function isvalid_date($date,$pid=0){
            $ret = false;
            if($pid <= 0){
                if($date > date('Y-m-d H:i:s')){
                    $ret = true;
                }
            }else if($this->validateint($pid)){
               $post_details = get_post($pid);
               $post_date = $post_details->post_date;
               $post_date_arr = date_parse($post_date);
               $pdate = date('Y-m-d H:i:s',mktime((int)$post_date_arr["hour"],(int)$post_date_arr["minute"],0,(int)$post_date_arr["month"],(int)$post_date_arr["day"],(int)$post_date_arr["year"]));
                if($date > $pdate){
                    $ret = true;
                }
            }
            return $ret;
        }
        /*
         * FUNCTION to Sort Multidimensional Arrays
        */
            function subval_sort($a,$subkey,$sort=true,$asc=true) { //sort the multidimensional array by key
                global $WishListMemberInstance;
                $c = array();
                    if(count($a) > 0){
                        foreach($a as $k=>$v) {
                               $b[$k] = $v->$subkey;
                        }
                        if($asc)
                            arsort($b);
                        else
                            asort($b);
                        foreach($b as $key=>$val) {
                                $c[] = $a[$key];
                                //save the post arrangement
                                $d[] = $a[$key]->ID;
                        }
                        //save this if viewing post
                        if(!is_single() && $sort){
                            $WishListMemberInstance->SaveOption('wlcc_post_arr',$d);
                        }
                    }
                    return $c;
            }
    //function to get date difference needs php5.2
            function date_diff($start, $end, $divisor=0){
                $d1 = date_parse($start);
                $sdate = mktime($d1['hour'],$d1['minute'],$d1['second'],$d1['month'],$d1['day'],$d1['year']);
                $d2 = date_parse($end);
                $edate = mktime($d2['hour'],$d2['minute'],$d2['second'],$d2['month'],$d2['day'],$d2['year']);
                $time_diff = $edate - $sdate;
                return $time_diff/$divisor;
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
if(!isset($ContentArchiver)){
   $ContentArchiverInstance = new ContentArchiver();
   if(is_admin()){
        add_action('wl-contentcontrol_dashboard',array(&$ContentArchiverInstance,'DashboardPage'),10,2);
        add_action('wl-contentcontrol_hook',array(&$ContentArchiverInstance,'SaveView'),10,1);
   }
   
   add_action('wl-contentcontrol_hook',array(&$ContentArchiverInstance,'WLCCHook'),10,1);
}
?>
