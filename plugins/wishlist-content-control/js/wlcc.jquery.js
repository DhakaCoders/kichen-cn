/*
 * Initialize date pickkers
 **/
jQuery(document).ready(function(){
    var datepicker_img_clck = function(){
        jQuery(this).prev().focus();
    }
    jQuery('.datepickerbox').datetimepicker({
        showButtonPanel: true
    });

    jQuery('.datepicker-img').bind('click',datepicker_img_clck);
	
    /* DASHBOARD
     * Add a row at the WL Content Manager Dashboard
     **/
    jQuery('.addrow').click(function(){
        if(confirm('Are you sure you want to add a new due date?')){
            if(mode == 'move'){
                check_class = 'wlcccm_cats' +row_cnt;
                txthtml = newrow.replace(/wlcccm_cats/gi,check_class);
            }else{
                txthtml = newrow;
            }
            datetimepicker_class = 'wlcc_duedateid' +row_cnt;
            txthtml = txthtml.replace(/wlcc_duedateid/gi,datetimepicker_class);
            row_cnt++;
            jQuery(txthtml).insertAfter('#wlcc_tblpostexpiry tbody>tr:last');
            jQuery('.datepickerbox').datetimepicker({
                showButtonPanel: true
            });
            jQuery('.datepicker-img').bind('click',datepicker_img_clck);
            jQuery('#'+datetimepicker_class).focus();
            jQuery('#wlcc_tblpostexpiry tbody>tr').removeClass('new_tr');
            jQuery('#wlcc_tblpostexpiry tbody>tr:last').addClass('new_tr');
        }
    });

    /*
     * ********************** PAGE OPTIONS *********************************
     **/

    /* PAGE OPTIONS
     * Add a row at the page options
     **/
    jQuery('.addrow_set').click(function(){ //page options for set
        if(confirm('Are you sure you want to add a new due date?')){
            txthtml = newrowset;
            datetimepicker_class = 'wlcc_duedate_setid' +row_cntset;
            txthtml = txthtml.replace(/wlcc_duedate_setid/gi,datetimepicker_class);
            row_cntset++;
            jQuery(txthtml).insertAfter('#wlcc_set tbody>tr:last');
            jQuery('.datepickerbox').datetimepicker({
                showButtonPanel: true
            });
            jQuery('.datepicker-img').bind('click',datepicker_img_clck);
            jQuery('#'+datetimepicker_class).focus();
            jQuery('#wlcc_set tbody>tr').removeClass('new_tr');
            jQuery('#wlcc_set tbody>tr:last').addClass('new_tr');
        }
    });

    jQuery('.addrow_move').click(function(){ //page options for move
        if(confirm('Are you sure you want to add a new due date?')){
            check_class = 'wlcccm_cats' +row_cntmove;
            txthtml =newrowmove.replace(/wlcccm_cats/gi,check_class);
            datetimepicker_class = 'wlcc_duedate_moveid' +row_cntmove;
            txthtml = txthtml.replace(/wlcc_duedate_moveid/gi,datetimepicker_class);
            row_cntmove++;
            jQuery(txthtml).insertAfter('#wlcc_move tbody>tr:last');
            jQuery('.datepickerbox').datetimepicker({
                showButtonPanel: true
            });
            jQuery('.datepicker-img').bind('click',datepicker_img_clck);
            jQuery('#'+datetimepicker_class).focus();
            jQuery('#wlcc_move tbody>tr').removeClass('new_tr');
            jQuery('#wlcc_move tbody>tr:last').addClass('new_tr');
        }
    });

    jQuery('.addrow_repost').click(function(){ //page options for repost
        if(confirm('Are you sure you want to add a new due date?')){
            txthtml = newrowrepost;
            datetimepicker_class = 'wlcc_duedate_repostid' +row_cntrepost;
            txthtml = txthtml.replace(/wlcc_duedate_repostid/gi,datetimepicker_class);
            row_cntrepost++;
            jQuery(txthtml).insertAfter('#wlcc_repost tbody>tr:last');
            jQuery('.datepickerbox').datetimepicker({
                showButtonPanel: true
            });
            jQuery('.datepicker-img').bind('click',datepicker_img_clck);
            jQuery('#'+datetimepicker_class).focus();
            jQuery('#wlcc_repost tbody>tr').removeClass('new_tr');
            jQuery('#wlcc_repost tbody>tr:last').addClass('new_tr');
        }
    });

    jQuery('.check').click(function(){ //for checking only
        alert(jQuery(this).parent().html());
    });
    
    jQuery('.wlcccm_chk_options_set').change(function(){
        var tbodytr = jQuery('#wlcc_set tbody>tr');
        if(jQuery(this).is(':checked')){
            jQuery('#wlcc_set').next().find('a').show();
            tbodytr.each(function(){
                var inputs = jQuery(this).find('input,select,checkbox');
                inputs.each(function(){
                    jQuery(this).attr('disabled', false);
                });
                var anchors = jQuery(this).find('img,a');
                anchors.each(function(){
                    jQuery(this).show();
                    jQuery('.datepicker-img').bind('click',datepicker_img_clck);
                });
            });
        }else{
            jQuery('#wlcc_set').next().find('a').hide();
            tbodytr.each(function(){
                var inputs = jQuery(this).find('input,select,checkbox');
                inputs.each(function(){
                    jQuery(this).attr('disabled', true);
                });
                var anchors = jQuery(this).find('img,a');
                anchors.each(function(){
                    jQuery(this).hide();
                });
            });
        }
    });

    jQuery('.wlcccm_chk_options_move').change(function(){
        var tbodytr = jQuery('#wlcc_move tbody>tr');
        if(jQuery(this).is(':checked')){
            jQuery('#wlcc_move').next().find('a').show();
            tbodytr.each(function(){
                var inputs = jQuery(this).find('input,select,checkbox');
                inputs.each(function(){
                    jQuery(this).attr('disabled', false);
                });
                var anchors = jQuery(this).find('img,a');
                anchors.each(function(){
                    jQuery(this).show();
                    jQuery('.datepicker-img').bind('click',datepicker_img_clck);
                });
            });
        }else{
            jQuery('#wlcc_move').next().find('a').hide();
            tbodytr.each(function(){
                var inputs = jQuery(this).find('input,select,checkbox');
                inputs.each(function(){
                    jQuery(this).attr('disabled', true);
                });
                var anchors = jQuery(this).find('img,a');
                anchors.each(function(){
                    jQuery(this).hide();
                });
            });
        }
    });
    /* PAGE OPTIONS DISABLE FIELDS
     * enable fields at the page/post options of Scheduler
     **/
    jQuery('.wlcccm_chk_options_repost').change(function(){
        var tbodytr = jQuery('#wlcc_repost tbody>tr');
        if(jQuery(this).is(':checked')){
            jQuery('#wlcc_repost').next().find('a').show();
            tbodytr.each(function(){
                var inputs = jQuery(this).find('input,select,checkbox');
                inputs.each(function(){
                    jQuery(this).attr('disabled', false);
                });
                var anchors = jQuery(this).find('img,a');
                anchors.each(function(){
                    jQuery(this).show();
                    jQuery('.datepicker-img').bind('click',datepicker_img_clck);
                });
            });
        }else{
            jQuery('#wlcc_repost').next().find('a').hide();
            tbodytr.each(function(){
                var inputs = jQuery(this).find('input,select,checkbox');
                inputs.each(function(){
                    jQuery(this).attr('disabled', true);
                });
                var anchors = jQuery(this).find('img,a');
                anchors.each(function(){
                    jQuery(this).hide();
                });
            });
        }
    });
    /* PAGE OPTIONS DISABLE FIELDS
     * disable fields at the page/post options of Archiver
     **/
    jQuery('.wlccca_chk_options').change(function(){
        var tbodytr = jQuery(this).parent().parent();
        if(jQuery(this).is(':checked')){
            tbodytr.each(function(){
                var inputs = jQuery(this).find('input:text,select');
                inputs.each(function(){
                    jQuery(this).attr('disabled', false);
                });
                var anchors = jQuery(this).find('img,a');
                anchors.each(function(){
                    jQuery(this).show();
                    jQuery('.datepicker-img').bind('click',datepicker_img_clck);
                });
            });
        }else{
            tbodytr.each(function(){
                var inputs = jQuery(this).find('input:text,select');
                inputs.each(function(){
                    jQuery(this).attr('disabled', true);
                });
                var anchors = jQuery(this).find('img,a');
                anchors.each(function(){
                    jQuery(this).hide();
                });
            });
        }
    });
    /* PAGE OPTIONS SHOW HIDE SHORTCODE INFO
     * disable fields at the page options
     **/
    jQuery('.wlcctogglenext').click(function(){
        var nextholder = jQuery(this).parent().next();
        event.preventDefault();
        nextholder.toggle();
        return false;
    });
    jQuery('.wlcc_exptype').change(function(){
        var parentholder = jQuery(this).parent().parent();
        if(jQuery(this).val() == 'days'){
            parentholder.find('.datepickerbox').hide();
            parentholder.find('span').hide();
            parentholder.find('#wlcc_expdays').show();
        }else if(jQuery(this).val() == 'date'){
            parentholder.find('.datepickerbox').show();
            parentholder.find('span').show();
            parentholder.find('#wlcc_expdays').hide();
        }
    });
});


/* PAGE OPTIONS DISABLE FIELDS
 * disable fields at the page/post options of Archiver
 **/
function wlcca_disable_fields(id){
    var tbodytr = jQuery('#tr'+id);
    tbodytr.each(function(){
        var inputs = jQuery(this).find('input:text,select');
        inputs.each(function(){
            jQuery(this).attr('disabled', true);
        });
        var anchors = jQuery(this).find('img,a');
        anchors.each(function(){
            jQuery(this).hide();
        });
    });
}
/* PAGE OPTIONS DISABLE FIELDS
 * disable fields at the page/post options of Scheduler
 **/
function disable_fields(id){
    var tbodytr = jQuery('#'+id+' tbody>tr');
    jQuery('#'+id).next().find('a').hide();
    tbodytr.each(function(){
        var inputs = jQuery(this).find('input,select,checkbox');
        inputs.each(function(){
            jQuery(this).attr('disabled', true);
        });
        var anchors = jQuery(this).find('img,a');
        anchors.each(function(){
            jQuery(this).hide();
        });
    });
}

/* PAGE OPTIONS DELETE FIELDS
 * Delete fields at the page/post options of MAnager
 **/
function deleterowrepost(inst){ // delete a row for repost
    if(confirm('Are you sure you want to remove this due date?')){
        jQuery(inst).parent().parent().remove();
        jQuery('#wlcc_repost tbody>tr').each(function(index) {
            datetimepicker_class = 'wlcc_duedate_repostid' +index;
            txthtml_repost = jQuery(this).html();

            txtbox_child = jQuery(this).find('input:text');
            if(txtbox_child){
                var txtbox_val = new Array();
                txtbox_child.each(function(txt_index){
                    txtbox = jQuery(this);
                    txtbox_val[txt_index] = txtbox.val();
                });
            }
            select_child = jQuery(this).find('select');
            if(select_child){
                var select_val = jQuery(this).find('select option:selected').val();
            }

            newhtml_repost = txthtml_repost.replace(/wlcc_duedate_repostid\d*/g,datetimepicker_class);
            jQuery(this).html(newhtml_repost);

            txtbox_child = jQuery(this).find('input:text');
            if(txtbox_child.hasClass('hasDatetimepicker')){
                txtbox_child.removeClass('hasDatetimepicker');
            }
            if(txtbox_child){
                txtbox_child.each(function(txt_index){
                    txtbox = jQuery(this);
                    txtbox.val(txtbox_val[txt_index]);
                });
            }
            select_child = jQuery(this).find('select');
            if(select_child){
                jQuery(this).find('select option[value="' +select_val +'"]').attr('selected', 'selected');
            }
            row_cntrepost = index+1;
        });
        jQuery('.datepickerbox').datetimepicker({
            showButtonPanel: true
        });
    }
}
/* PAGE OPTIONS
 * remove a row at the page options
 **/
function deleterowset(inst){ // delete a row for set
    if(confirm('Are you sure you want to remove this due date?')){
        jQuery(inst).parent().parent().remove();

        jQuery('#wlcc_set tbody>tr').each(function(index) {
            datetimepicker_class = 'wlcc_duedate_setid' +index;
            txthtml_set = jQuery(this).html();

            txtbox_child = jQuery(this).find('input:text');
            if(txtbox_child){
                var txtbox_val = txtbox_child.val();
            }
            select_child = jQuery(this).find('select');
            if(select_child){
                var select_val = jQuery(this).find('select option:selected').val();
            }
            newhtml_set = txthtml_set.replace(/wlcc_duedate_setid\d*/g,datetimepicker_class);
            jQuery(this).html(newhtml_set);

            txtbox_child = jQuery(this).find('input:text');
            if(txtbox_child.hasClass('hasDatetimepicker')){
                txtbox_child.removeClass('hasDatetimepicker');
            }
            if(txtbox_child){
                txtbox_child.val(txtbox_val);
            }
            select_child = jQuery(this).find('select');
            if(select_child){
                jQuery(this).find('select option[value="' +select_val +'"]').attr('selected', 'selected');
            }
            row_cntset = index+1;
        });
        jQuery('.datepickerbox').datetimepicker({
            showButtonPanel: true
        });
    }
}
/* PAGE OPTIONS
 * remove a row at the page options
 **/
function deleterowmove(inst){ //delete a row for move
    if(confirm('Are you sure you want to remove this due date?')){
        jQuery(inst).parent().parent().remove();
        jQuery('#wlcc_move tbody>tr').each(function(index) {
            newcheck_class = 'wlcccm_cats' + index;
            datetimepicker_class = 'wlcc_duedate_moveid' +index;
            txthtml_move = jQuery(this).html();

            txtbox_child = jQuery(this).find('input:text');
            if(txtbox_child){
                var txtbox_val = txtbox_child.val();
            }
            chkbox_child = jQuery(this).find('input:checkbox');
            if(chkbox_child){
                var cb_arr= new Array();
                chkbox_child.each(function(cb_index){
                    cb = jQuery(this);
                    cb_arr[cb_index] = cb.is(":checked")? 'checked':'';
                });
            }
            select_child = jQuery(this).find('select');
            if(select_child){
                var select_val = jQuery(this).find('select option:selected').val();
            }

            newhtml_move = txthtml_move.replace(/wlcccm_cats\d*/g,newcheck_class);
            newhtml_move = newhtml_move.replace(/wlcc_duedate_moveid\d*/g,datetimepicker_class);
            jQuery(this).html(newhtml_move);

            txtbox_child = jQuery(this).find('input:text');
            if(txtbox_child.hasClass('hasDatetimepicker')){
                txtbox_child.removeClass('hasDatetimepicker');
            }
            if(txtbox_child){
                txtbox_child.val(txtbox_val);
            }
            chkbox_child = jQuery(this).find('input:checkbox');
            if(chkbox_child){
                chkbox_child.each(function(cb_index){
                    cb = jQuery(this);
                    if(cb_arr[cb_index] == "checked"){
                        cb.attr('checked', true);
                    }
                });
            }
            select_child = jQuery(this).find('select');
            if(select_child){
                jQuery(this).find('select option[value="' +select_val +'"]').attr('selected', 'selected');
            }
            row_cntmove = index+1;
        });
        jQuery('.datepickerbox').datetimepicker({
            showButtonPanel: true
        });
    }
}


/* DASHBOARD
 * Remove a row at the WL Content Manager Dashboard
 **/
function deleterow(inst){
    if(confirm('Are you sure you want to remove this due date?')){
        jQuery(inst).parent().parent().remove();
        jQuery('#wlcc_tblpostexpiry tbody>tr').each(function(index) {
            newcheck_class = 'wlcccm_cats' + index;
            datetimepicker_class = 'wlcc_duedate' +index;
            txthtml = jQuery(this).html();

            txtbox_child = jQuery(this).find('input:text');
            if(txtbox_child){
                var txtbox_val = new Array();
                txtbox_child.each(function(txt_index){
                    txtbox = jQuery(this);
                    txtbox_val[txt_index] = txtbox.val();
                });
            }
            chkbox_child = jQuery(this).find('input:checkbox');
            if(chkbox_child){
                var cb_arr= new Array();
                chkbox_child.each(function(cb_index){
                    cb = jQuery(this);
                    cb_arr[cb_index] = cb.is(":checked")? 'checked':'';
                });
            }
            select_child = jQuery(this).find('select');
            if(select_child){
                var select_val = jQuery(this).find('select option:selected').val();
            }

            newhtml = txthtml.replace(/wlcccm_cats\d*/g,newcheck_class);
            newhtml = newhtml.replace(/wlcc_duedateid\d*/g,datetimepicker_class);
            jQuery(this).html(newhtml);

            txtbox_child = jQuery(this).find('input:text');
            if(txtbox_child.hasClass('hasDatetimepicker')){
                txtbox_child.removeClass('hasDatetimepicker');
            }
            txtbox_child = jQuery(this).find('input:text');
            if(txtbox_child){
                txtbox_child.each(function(txt_index){
                    txtbox = jQuery(this);
                    txtbox.val(txtbox_val[txt_index]);
                });
            }
            chkbox_child = jQuery(this).find('input:checkbox');
            if(chkbox_child){
                chkbox_child.each(function(cb_index){
                    cb = jQuery(this);
                    if(cb_arr[cb_index] == "checked"){
                        cb.attr('checked', true);
                    }
                });
            }
            select_child = jQuery(this).find('select');
            if(select_child){
                jQuery(this).find('select option[value="' +select_val +'"]').attr('selected', 'selected');
            }
            row_cnt = index+1;
        });
        jQuery('.datepickerbox').datetimepicker({
            showButtonPanel: true
        });
    }
}