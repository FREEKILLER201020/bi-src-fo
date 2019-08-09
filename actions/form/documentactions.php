<?php
if ($what == 'documentactions') {
    if ($act=='edit') {
            $sql="select * from $what WHERE id=$id";
            $res=$this->utils->escape($this->db->GetRow($sql));
        if (($res[userid]<>$userid)&&!($access['main_admin'])) {
            $this->html->error("<br><b>No Permission</b>");
        }
            $partnerslist=$this->utils->F_tostring($this->db->GetResults("select partnerid from docs2partners where docid=$id"));
            $partnersnamelist=$this->utils->F_tostring($this->db->GetResults("select p.name from partners p, docs2partners d where d.docid=$id and d.partnerid=p.id"));
            //$partnersnamelist=str_ireplace($partnersnamelist,"\t",",");
        if (($res[active]=='t')||($res[active]=='')) {
            $checked='checked';
        } else {
            $checked='';
        }
        if (($res[complete]=='t')||($res[complete]=='')) {
            $cchecked='checked';
        } else {
            $cchecked='';
        }
    } else {
        $res['controller']=$this->db->GetVal("select surname from users where id=$uid");
        $res['date']=$this->dates->F_date('', 1);
        $res[type]='1700';
        $res[docid]=$this->html->readRQ('docid')*1;
        if ($res[docid]==0) {
            $res[docid]=$this->html->readRQ('refid')*1;
        }
        $cchecked='';
        $checked='checked';
    }
            
            $today=$this->dates->F_date("", 1);
            $referring=$this->db->GetVal("select name from documents where id=$res[docid]");
            
            $type=$this->html->htlist('type', "SELECT id, name from listitems where list_id=17 ORDER by upper(name)", $res[type], 'Select Type', '');
            $executor=$this->html->htlist('executor', "SELECT id, surname||' '||firstname FROM users where active='1' and id>0 order by surname, firstname", $res[executor], 'Select Executor', '');
            /*$sql="select id, name from listitems where list_id=15 order by name";
            $partnerchooser=$this->html->htlist('partnerchooser',$sql,'','Select Partner',"onchange='itemid=this.options[this.selectedIndex].value;
            itemname=this.options[this.selectedIndex].text;
            document.getElementById(\"partnerslist\").innerHTML+=itemid+\", \";
            document.getElementById(\"partnersnamelist\").innerHTML+=itemname+\", \";'");
            */
            $out.= "
				
				<div id='stylized' class='well'>
				  <form id='form1' name='form1' method='post' action='?csrf=$GLOBALS[csrf]&act=save&what=$what' method='post'>
				    <h1>$action Document Route </h1>
				    <p>Manage Route <br>$referring</p>   
					<input type='hidden' name='id' value='$id'>
					<input type='hidden' name='docid' value='$res[docid]'> 
					  
				    <dt><label>Type</label>$type</dt>
				    <dt><label>Date</label><input name='date' value='$res[date]' id='date' data-datepicker='datepicker' class='date' type='text' placeholder='DD.MM.YYYY'/></dt>
					<dt><label>Description</label><textarea name='descr' id='descr_'>$res[descr]</textarea></dt>
					<dt><label>Quantity</label><input type='text' name='qty' value='$res[qty]'></dt>
					<dt><label>Executor</label>$executor</dt>
					$sendalert
					$sendmail
					$sendsms					
					<br>
					";
                    $field_name='complete';
    $chk=($res[$field_name]=='t')?'checked':'';
                    $out.= "<label><input type='checkbox' name='$field_name' value='1' $chk onclick='document.getElementById(\"date\").value=\"$today\";'/> ".ucfirst($field_name)."</label>";
                    
                    $field_name='active';
    $chk=($res[$field_name]=='t')?'checked':'';
                    $out.= "<label><input type='checkbox' name='$field_name' value='1' $chk /> ".ucfirst($field_name)."</label>
				 
					
					<div class='spacer'></div>
					".$this->html->form_confirmations()."
				<button type='submit' name='act' value='save' id='button' class='btn btn-primary'  onClick='document.getElementById(\"button\").innerHTML=\"Wait...\";'>Save</button><br>
				    <div class='spacer'></div>
				  </form>
				</div>
				";
    if ($act=='edit') {
        $out.= "
					<script>\n 
					//ajaxFunction(\"partnerid_\",\"?csrf=$GLOBALS[csrf]&act=append&what=partnerid&id=$res[partnerid]\");
	                  
						</script>\n";
    }
}
    
$body.=$out;
