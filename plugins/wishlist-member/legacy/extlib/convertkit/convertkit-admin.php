<?php
    $forms = array();
    if (class_exists('WLM_AUTORESPONDER_CONVERTKIT_INIT')) {
        $api_secret = $data[$__index__]['ckapi'];
        if ( $api_secret != "" ) {
            $WLM_AUTORESPONDER_CONVERTKIT_INIT = new WLM_AUTORESPONDER_CONVERTKIT_INIT;
            $forms = $WLM_AUTORESPONDER_CONVERTKIT_INIT->ck_get_forms( $api_secret );
        }
    }
?>
<form method="post">
    <input type="hidden" name="saveAR" value="saveAR" />
    <h2 class="wlm-integration-steps">Step 1. Configure the ConvertKit API SECRET :</h2>
    <p><?php _e('<strong>API SECRET</strong> can be found below the <em>API Key</em> within your ConvertKit account using the following link:', 'wishlist-member'); ?><br><a href="https://app.convertkit.com/account/edit" target="_blank">https://app.convertkit.com/account/edit</a></p>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php _e('ConvertKit API SECRET', 'wishlist-member'); ?></th>
            <td>
                <input type="text" name="ar[ckapi]" value="<?php echo $data[$__index__]['ckapi']; ?>" size="60" />
                <?php echo $this->Tooltip("integration-autoresponder-convertkit-tooltips-API-Secret"); ?>
                <br />
                <small>Make sure to use the <strong>API SECRET</strong>, not the API KEY</small>
            </td>
        </tr>
    </table>
    <h2 class="wlm-integration-steps">Step 2. Assign the Membership Levels to the corresponding ConvertKit Form:</h2>
    <p>Membership Levels can be assigned to your Forms by selecting a Form Name in the corresponding area below.</p>
    <table class="widefat">
        <thead>
            <tr>
                <th scope="col" style="width:38%;"><?php _e('Membership Level', 'wishlist-member'); ?></th>
                <th scope="col" style="width:30%;"><?php _e('ConvertKit Forms', 'wishlist-member'); ?>
                </th>
                <th class="col" style="width:2%;">&nbsp;</th>
                <th class="col" style="width:30%;"><?php _e('If Removed/Cancelled from Level', 'wishlist-member'); ?>
                </th>
            </tr>
        </thead>
        <tbody valign="top">
            <?php foreach ((array) $wpm_levels AS $levelid => $level): ?>
                <?php
                    $form_id = isset( $data[$__index__]['ckformid'][$levelid] ) ? trim( $data[$__index__]['ckformid'][$levelid] ) : false;
                    $form_id = $form_id ? $form_id : false; //make sure its not empty
                ?>
                <tr class="<?php echo ++$autoresponder_row % 2 ? 'alternate' : ''; ?>">
                    <th scope="row"><?php echo $level['name']; ?></th>
                    <td>
                        <select class='ar_ck_forms' name="ar[ckformid][<?php echo $levelid; ?>]" style="width:100%;" >
                            <option value='' >- Select a List -</option>
                            <?php
                            foreach ( (array) $forms as $form ) {
                                $selected = $form_id == $form['id'] ? "selected='selected'" : "";
                                echo "<option value='{$form['id']}' {$selected}>{$form['name']}</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                    <?php $ckOnRemCan = isset($data[$__index__]['ckOnRemCan'][$levelid]) ? $data[$__index__]['ckOnRemCan'][$levelid] : ""; ?>
                    <td >
                        <select class='wlmmcAction ar_mc_remove' name="ar[ckOnRemCan][<?php echo $levelid; ?>]" style="width:100%;">
                            <option value='' <?php echo $ckOnRemCan == "" ? "selected='selected'" : ""; ?> >- Select an Action -</option>
                            <option value='nothing' <?php echo $ckOnRemCan == "nothing" ? "selected='selected'" : ""; ?> >Do Nothing</option>
                            <option value='unsub' <?php echo $ckOnRemCan == "unsub" ? "selected='selected'" : ""; ?> >Unsubscribe from ConvertKit</option>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Settings', 'wishlist-member'); ?>" />
    </p>
</form>