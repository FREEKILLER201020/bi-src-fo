<?php
    //Edit partners
    if ($act=='edit'){
        $sql="select * from $what WHERE id=$id";
        $res=$this->db->GetRow($sql);
    }else{
        $sql="select * from $what WHERE id=$refid";
        $res2=$this->db->GetRow($sql);
        $res[active]='t';
    }

    $form_opt['well_class']="span11 columns form-wrap";

    $out.=$this->html->form_start($what,$id,'',$form_opt);
    $out.="<hr>";

    $out.=$this->html->form_hidden('reflink',$reflink);
    $out.=$this->html->form_hidden('id',$id);
    $out.=$this->html->form_hidden('reference',$reference);
    $out.=$this->html->form_hidden('refid',$refid);

    

$out.=$this->html->form_text('name',$res[name],'Name','',0,'span12');
$out.=$this->html->form_chekbox('physical',$res[physical],'Physical','',0,'span12');

$out.=$this->html->form_text('surname',$res[surname],'Surname','',0,'span12');
$out.=$this->html->form_text('salutation',$res[salutation],'Salutation','',0,'span12');

    $type_id=$this->data->listitems('type_id',$res[type_id],'partners','span12');
        $out.= "<label>Type</label>$type_id";


$out.=$this->html->form_text('email',$res[email],'Email','',0,'span12');
$out.=$this->html->form_text('mobile',$res[mobile],'Mobile','',0,'span12');
$out.=$this->html->form_text('tel',$res[tel],'Tel','',0,'span12');
$out.=$this->html->form_textarea('address',$res[address],'Address','',0,'','span12');
$out.=$this->html->form_text('passport',$res[passport],'Passport','',0,'span12');

    $country_id=$this->data->listitems('country_id',$res[country_id],'country','span12');
        $out.= "<label>Country</label>$country_id";
$out.=$this->html->form_date('birth_date',$res[birth_date],'Birth date','',0,'span12');

$out.=$this->html->form_textarea('descr',$res[descr],'Descr','',0,'','span12');

$out.=$this->html->form_chekbox('active',$res[active],'Active','',0,'span12');


    $out.=$this->html->form_confirmations();
    $out.=$this->html->form_submit('Save');
    $out.=$this->html->form_end();
    
    $body.=$out;
    