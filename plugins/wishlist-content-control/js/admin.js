function wlcc_showHideLevels(o){
    var s=o.selectedIndex;
    var d=document.getElementById('days_delay');
    var c=document.getElementById('do_action');
    if(s==1){
        d.style.display='none';
        c.style.display='';
    }else if(s==2){
        d.style.display='';
        c.style.display='';
    }else{
        d.style.display='none';
        c.style.display='none';
    }
}

function wlcc_submit(f,bulk_op,go){
    
    if(bulk_op){
        var a=f.bulk_op.options[f.bulk_op.selectedIndex].text;
        var s=f.bulk_op.selectedIndex;
        var msg='';
        if(s==1){
            msg = 'Are you sure you want to remove the delay on your selected posts below?';
        }else if(s==2){
            var value=f.days_delay.value;
            if(value <= 0 || isNaN(value)){msg = 'Setting the value to 0 or BLANK will remove the delay on your selected posts below. Do you want to proceed?';
            }else{msg = 'Are you sure you want to delay the posting of your selected posts below for ' +value +' day/s?';}

       }else if(s==0){
            msg = 'Are you sure you want to apply the delay below?';
       }
            if(go && s>0){
                if(confirm(msg)){
                    f.frmsubmit.value = "go";
                    f.submit();
                }
            }else if(!go){
               if(confirm(msg)){
                    f.frmsubmit.value = "save";
                    f.submit();
               }
            }
    }else{
       if(f.bulk_op)f.bulk_op.selectedIndex=0;
       f.submit();
    }
}
function wlcc_show(mod,hide){
    var divholder = mod +"id";
    var lnkholder = mod +"lnk";
    var c=document.getElementById(divholder);
    var d = document.getElementById(lnkholder);
    if(hide == 'true'){
        c.style.display='none';
        d.style.display='';
    }else{
        c.style.display='';
        d.style.display='none';
    }
}
function wlcc_hideshow_menu(mod,id,hide){
    var linkholder = mod +id;
    if(hide=='true'){
        document.getElementById(linkholder).style.display='none';
    }else{
        document.getElementById(linkholder).style.display='';
    }
}