<?php

//if(($this->data->isallowed('documents',$id)==0)&&($id>0)){echo "You have no access to this document."; $this->data->DB_log("HACK on edit $what id=$id");exit;}
if ($act=='edit') {
    $sql="select * from $what WHERE id=$id";
    $res=$this->utils->escape($this->db->GetRow($sql));
    if (($res[userid]<>$userid)&&!($access['main_admin'])) {
        $this->html->error("<br><b>No Permission</b>");
    }
    //$partnerslist=$this->utils->F_tostring($this->db->GetResults("select partnerid from docs2partners where docid=$id"));
    //$partnersnamelist=$this->utils->F_tostring($this->db->GetResults("select p.name from partners p, docs2partners d where d.docid=$id and d.partnerid=p.id"));

    $groupslist=$this->utils->F_tostring($this->db->GetResults("select groupid from docs2groups where docid=$id"));
    $groupsnamelist=$this->utils->F_tostring($this->db->GetResults("select g.name from groups g, docs2groups d where d.docid=$id and d.groupid=g.id"));
    //$partnersnamelist=str_ireplace($partnersnamelist,"\t",",");
    $res[parentdoc]=$this->db->GetVal("select name from documents where id=$res[parentid]");
    if ($res[docgroup]==1509) {
        $deactivated_type="<span class='badge orange'>".$this->data->get_name('listitems', $res[type])."</span>";
    }
} else {
    $save_qty="
		<dt><label>Quantity to save new $what</label>
	<input type='text' name='save_qty'  id='' value='1'></td><br>";
    $res[active]='t';
    $res[name]=$this->data->get_new_docname();
    $refid=$this->html->readRQ('refid')*1;
    $parentid=$this->html->readRQ('parentid')*1;
    $res['controller']=$this->db->GetVal("select surname from users where id=$uid");
    $res['date']=$this->dates->F_date('', 1);
    $res['datefrom']=$this->dates->F_date('', 1);
    $days_to=14;
    $days_chk=$days_to-1;
    if (($GLOBALS[history_tail]>0)&&($GLOBALS[history_tail]<$days_to)) {
        $days_to=0;
        $days_chk=0;
    }
    $res['dateto']=$this->dates->F_dateadd($res['date'], $days_to);
    $res['datecheck']=$this->dates->F_dateadd($res['date'], $days_chk);
    $res[type]=0;
    $res[executor]=$uid;
    $res[docgroup]=0;
    if ($parentid>0) {
        $res[parentdoc]=$this->db->GetVal("select name from documents where id=$parentid");
        $res[parentid]=$parentid;
    }
    $reference=$this->html->readRQ('reference');
    $docgroup=$this->html->readRQn('docgroup');
    
    $type=$this->html->readRQ('type')*1;
    
    $res[confidentlevel]=0;
    //$ids=substr($this->utils->F_tostring($this->db->GetResults("select values from listitems where list_id=15 and id!=1500 and values!=''")),0,-1);
    //$res[docgroup]=$this->db->GetVal("select id from listitems where list_id=15 and values like '%$res[type]%'")*1;
    //if($res[docgroup]==0)$res[docgroup]=1500;
}
//$sendalert="<dt><label>Send alert to executor</label><input type='checkbox' name='sendalert' value='1' /></dt>";
//$field_name='sendalert'
//$sendalert= "<label><input type='checkbox' name='$field_name' value='1' $chk /> Send system alert</label>";

$field_name='autodelete';
$chk=($res[$field_name]=='t')?'checked':'';
$hidden='hidden';
if ($access[docs_autodelete]) {
    $hidden='';
}
$autodelete= "<label class='$hidden'><input type='checkbox' name='$field_name' value='1' $chk /> ".ucfirst($field_name)."</label>";



if ($res[type]==1652) {
    $smsalert="<dt><label>Send SMS notice</label><input type='checkbox' name='sendsms' value='1' /></dt>";
}
if (($res[active]=='t')||($res[active]=='')) {
    $checked='checked';
} else {
    $checked='';
}
if (($res[complete]=='t')||($res[complete]=='1')) {
    $cchecked='checked';
} else {
    $cchecked='';
}
$sql="SELECT id, name FROM groups ORDER by name";
$groupid=$this->html->htlist('groupid', $sql, $id, 'Select Group', "onchange='itemid=this.options[this.selectedIndex].value;
itemname=this.options[this.selectedIndex].text;
document.getElementById(\"groupslist\").innerHTML+=itemid+\", \";
document.getElementById(\"groupsnamelist\").innerHTML+=itemname+\", \";'");
$currency=$this->html->htlist('currency', "SELECT id, name from listitems where list_id=6 ORDER by id", $res[currency], 'Select Currency', '');


$docgroup=$this->html->htlist('docgroup', "SELECT id, name from listitems where list_id=15 ORDER by name", $res[docgroup], 'Select Document Group', "onchange='docgroupid=this.options[this.selectedIndex].value;
docgroupname=this.options[this.selectedIndex].text+\" \"; 
ajaxFunction(\"type_\",\"?csrf=$GLOBALS[csrf]&act=append&what=doctypelist&value=\"+docgroupid+\"&id=$res[type]\");'");

//$type=$this->html->htlist('type',"SELECT id, name from listitems where list_id=16 ORDER by name",$res[type],'Select Type',"onchange='typeid=this.options[this.selectedIndex].value; typename=this.options[this.selectedIndex].text+\" \";'");

//$route=$this->html->htlist('route',"SELECT id, name from listitems where list_id=18 ORDER by name",'1800','','');
//$initiator=$this->html->htlist('initiator',"SELECT id, name FROM directory WHERE active='1' and initiator='1' ORDER by name",$res[initiator],'Select Initiator (Make it active in Directory)','');
$executor=$this->html->htlist('executor', "SELECT id, surname||' '||firstname||' ('||username||')' FROM users where active='1' and id>=0 ORDER by surname", $res[executor], 'Select Executor', '');
/*$sql="select id, name from listitems where list_id=15 order by name";
$partnerchooser=$this->html->htlist('partnerchooser',$sql,'','Select Partner',"onchange='itemid=this.options[this.selectedIndex].value;
itemname=this.options[this.selectedIndex].text;
document.getElementById(\"partnerslist\").innerHTML+=itemid+\", \";
document.getElementById(\"partnersnamelist\").innerHTML+=itemname+\", \";'");
*/

///=====FORM=========\\\\\


$out.= "

	<div class='well columns form-wrap'>
	<form id='form1' name='form1' method='post' action='?csrf=$GLOBALS[csrf]&act=save&what=documents' method='post'>
	<h1>$action $what</h1>
<p>Manage $what <br>$referring </p>   
<input type='hidden' name='id' value='$id'>
	<input type='hidden' name='accountid' value='$res[accountid]'> 
	<input type='hidden' name='transactionid' value='$res[transactionid]'>
	<input type='hidden' name='contractid' value='$res[contractid]'> 
	<input type='hidden' name='vacationid' value='$res[vacationid]'>
	<input type='hidden' name='clientrequestid' value='$res[clientrequestid]'> ";

/*
$form_opt['well_class']="span11 columns form-wrap";
$out.=$this->html->form_start($what,$id,'',$form_opt);
$out.=$this->html->form_hidden('accountid',$res[accountid]);
$out.=$this->html->form_hidden('transactionid',$res[transactionid]);
$out.=$this->html->form_hidden('contractid',$res[contractid]);
$out.=$this->html->form_hidden('vacationid',$res[vacationid]);
$out.=$this->html->form_hidden('clientrequestid',$res[clientrequestid]);
*/

$out.= "

	<div class='row' style='margin-left:0px;'>
		<div class='span4'>
		
			<label class='badge-top'>General</label>
			<fieldset class='lookup'>
				<dt><label>Document Number</label><div id='nameinp'><input type='text' name='name'  id='name_id' value='$res[name]' disabled></div></dt>
				<dt><label>Document Group</label>$docgroup</dt>
				<dt><label>Type</label><span id='type_'></span>$deactivated_type $type</dt>
				<fieldset class='lookup'>
					<dt><label>Date of record</label><input type='text' name='date'  value='$res[date]' class='date' disabled></dt>
					<dt><label>Start date (Document date)</label><input name='datefrom' value='$res[datefrom]' data-datepicker='datepicker' class='date' type='text' placeholder='DD.MM.YYYY'/></dt>
					<dt><label>Check date</label><input name='datecheck' value='$res[datecheck]' data-datepicker='datepicker' class='date' type='text' placeholder='DD.MM.YYYY'/></dt>				    			    
					<dt><label>End date</label><input name='dateto' value='$res[dateto]' data-datepicker='datepicker' class='date' type='text' placeholder='DD.MM.YYYY'/> $autodelete</dt>
				</fieldset>
				<dt><label>Description</label><textarea name='descr' id='descr_'>$res[descr]</textarea></dt>
				<dt><label>Quantity</label><input type='text' name='qty' value='$res[qty]'></dt>
				

				<fieldset class='lookup'>
					<div id='hiddenfield' style='display: none;'>
					<dt><label>Parties:</label><textarea name='partnerslist' id='partnerslist'>$partnerslist</textarea></dt>
				</div>
				<dt><label>Parties: ".$this->data->help(13).$this->data->help(14)."<br><img src='".ASSETS_URI."/assets/img/custom/cancel.png' onclick='document.getElementById(\"partnerslist\").innerHTML=\"\";
				document.getElementById(\"partnersnamelist\").innerHTML=\"\";'></label><textarea name='partnersnamelist' id='partnersnamelist' disabled>$partnersnamelist</textarea></dt>
				<dt><label>Partner Search</label><input type='text' name='partnerid' id='narrowpartner' class='date' value='' onchange='itemid=this.value;ajaxFunction(\"partnerid_\",\"?csrf=$GLOBALS[csrf]&act=append&what=partneridlist&value=\"+itemid);'>
				
				<span onclick='itemid=narrowpartner.value;ajaxFunction(\"partnerid_\",\"?csrf=$GLOBALS[csrf]&act=append&what=partneridlist&value=\"+itemid);' class='icon-search'></span>
				</dt>
				
				<dt><label>Partner</label><div id='partnerid_'><input type='text' name='dummy' value='Narrow search' disabled></div></dt>					
				</fieldset>
			</fieldset>
		</div>	
		<div class='span4'>
		
			<label class='badge-top'>Details</label>
			<fieldset class='lookup'>
				
				<fieldset class='lookup'>
					<dt><label>Executor</label>$executor</dt>
					$sendalert
					$sendmail
					$sendsms
					</fieldset>					
				
				<dt><label>Currency</label>$currency</dt>
				<dt><label>Amount before VAT<br><span class='d'>(leave blank if you know VAT)</span></label><input type='text' name='amount_vatable'  value='$res[amount_vatable]'></dt>
				<dt><label>Amount VAT</label><input type='text' name='amount_vat'  value='$res[amount_vat]'></dt>
				<dt><label>Amount</label><input type='text' name='amount'  value='$res[amount]'></dt>
				

				<fieldset class='lookup'>
					<div id='hiddenfield' style='display: none;'>
					<dt><label>Groups:</label><textarea name='groupslist' id='groupslist'>$groupslist</textarea></dt>
				</div>
				<dt><label>Groups:<br><img src='".ASSETS_URI."/assets/img/custom/cancel.png' onclick='document.getElementById(\"groupslist\").innerHTML=\"\";
				document.getElementById(\"groupsnamelist\").innerHTML=\"\";'></label><textarea name='groupsnamelist' id='groupsnamelist' >$groupsnamelist</textarea></dt>
				<dt><label>Groups</label>$groupid</dt>
				</fieldset>

				
				<dt><label>Additional Info</label><textarea name='addinfo' id='addinfo_'>$res[addinfo]</textarea></dt>
				<dt><label>Parent Document</label><div id='nameinp'><input type='text' name='parentid'  id='parentid_id' value='$res[parentdoc]'></div></dt>
			</fieldset>
		</div>
		
		<div class='span4'>
			<label class='badge-top'>Tools</label>
			<fieldset class='lookup'>
				";
                $field_name='complete';
$chk=($res[$field_name]=='t')?'checked':'';
                $out.= "<label><input type='checkbox' name='$field_name' value='1' $chk /> ".ucfirst($field_name)."</label>";

                $field_name='active';
$chk=($res[$field_name]=='t')?'checked':'';
                $out.= "<label><input type='checkbox' name='$field_name' value='1' $chk /> ".ucfirst($field_name)."</label>";

                //if($access[block_download]){
if (($access[main_admin])||($access[block_download])) {
    $out.=$this->html->form_chekbox('block_download', $res[block_download], 'Block downloads');
}
            $out.= "		
			</fieldset>
		</div>
	</div>
	
	</fieldset>	
	<fieldset>";
    



    $out.= "


		<div class='spacer'></div>
	<hr>
		$save_qty
		".$this->html->form_confirmations()."
		<button type='submit' name='act' value='save' id='button' class='btn btn-primary'  onClick='document.getElementById(\"button\").innerHTML=\"Wait...\";'>Save</button><br>
	<div class='spacer'></div>
	</form>
	</div>
	<script>							
		setTimeout('ajaxFunction(\"type_\",\"?csrf=$GLOBALS[csrf]&act=append&what=doctypelist&value=$res[docgroup]&id=$res[type]\")',100);
	</script>
	";
    /*
    $out.=$save_qty;
    $out.=$this->html->form_confirmations();
    $out.=$this->html->form_submit('Save');
    $out.=$this->html->form_end();
    $out.= "
    <script>
        setTimeout('ajaxFunction(\"type_\",\"?csrf=$GLOBALS[csrf]&act=append&what=doctypelist&value=$res[docgroup]&id=$res[type]\")',100);
    </script>
    ";
*/


    $body.=$out;
