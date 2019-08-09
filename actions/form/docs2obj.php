<?php
//form docs2obj
$ref_table=$this->html->readRQ('ref_table');
$ref_id=$this->html->readRQn('ref_id');
//if($ref_id==0)$ref_id=$this->html->readRQn('id');
$doc_id=$this->html->readRQn('doc_id');
if ($act=='edit') {
    $sql="select * from $what WHERE id=$id";
    $res=$this->utils->escape($this->db->GetRow($sql));
    $link=$this->data->detalize($res[ref_table], $res[ref_id]);
    $doc=$this->data->detalize('documents', $res[doc_id]);
    $hidden_fields.= "
		<input type='hidden' name='doc_id' value='$res[doc_id]'>
		<input type='hidden' name='ref_table' value='$res[ref_table]'>
		<input type='hidden' name='ref_id' value='$res[ref_id]'>
		<input type='hidden' name='id' value='$id'>";
    $type_id=$this->data->listitems('type_id', $res[type_id], 'link_roles');
    $edit.= "<label>Type</label>$type_id";
    $join_to="Edit link of $doc to $link";
} else {
    $type_id=$this->data->listitems('type_id', $res[type_id], 'link_roles');
    if (($doc_id==0)&&($ref_table!='')&&($ref_id>0)) {
        $res=$this->db->GetRow("select * from $ref_table where id=$ref_id");
        $name=$res[name];
        $sql='';
        if ($ref_table=='transactions') {
            $sql="SELECT distinct d.id, d.name||' ('||substr(t.name,0,40)||') '||substr(d.descr,0,40), d.datefrom FROM documents d, docs2obj p, listitems t
			WHERE p.doc_id=d.id and (p.ref_id=$res[sender] or p.ref_id=$res[receiver]) 
			and ref_table='partners' 
			and d.type=t.id
			and d.datefrom<='$res[date]' and d.dateto>='$res[date]' 
			and d.id not in (select doc_id from docs2obj where ref_table='$ref_table' and ref_id=$ref_id) 
			ORDER by d.datefrom desc, d.id desc";
        }
        if ($ref_table=='clientrequests') {
            $sql="SELECT distinct d.id, d.name||' dd:'||d.datefrom||' ('||substr(t.name,0,40)||') '||substr(d.descr,0,40)||' amnt:'||d.amount, d.datefrom FROM documents d, docs2obj p, listitems t
			WHERE p.doc_id=d.id and p.ref_id=$res[partnerid] 
			and ref_table='partners' 
			and d.type=t.id 
			and date(datefrom  - interval '5 days') <=date('$res[date]') and d.dateto>='$res[date]' 
			--and date(datefrom  - interval '15 days') >=date('$res[date]') 
			and d.id not in (select doc_id from docs2obj where ref_table='$ref_table' and ref_id=$ref_id) 
			ORDER by d.datefrom desc, d.id desc";
        }
        if ($sql!='') {
            $type=$this->html->htlist('doc_id_list', $sql, 0, "Select relevant", '');
            $doc_input="<dt><label>or choose</label>$type</dt>";
        }
        $join_to.=  "Join existing document to $ref_table $name";
        $hidden_fields.= "
			<input type='hidden' name='ref_id' value='$ref_id'>
			<input type='hidden' name='ref_table' value='$ref_table'>";
        $add.=  "
			<label>Document ID</label><input type='text' name='doc_id'  id='name_id' value=''>
		<label>or</label>
		<dt><label>Document Name</label><input type='text' name='name'  id='name_id' value='' placeholder='00-00-0000'>
		$doc_input";
        $add.="<label>Type</label>$type_id";
    } else {
        $res=$this->db->GetRow("select * from documents where id=$doc_id");
        $name=$res[name];
        $sql='';
        if ($ref_table=='transactions') {
            $sql="SELECT distinct t.id, t.name||'|'||t.valuedate||'|'||t.samount||' '||c.name||'|'||substr(s.name,0,5)||'->'||substr(r.name,0,5) FROM $ref_table t, listitems c, partners s, partners r
			WHERE c.id=t.scurrency
			and s.id=t.sender
			and r.id=t.receiver
			and t.valuedate<='$res[dateto]' and t.valuedate>='$res[datefrom]'
			and (t.sender in (select p1.ref_id from docs2obj p1 where ref_table='partners' and doc_id=$doc_id) or t.receiver in (select p1.ref_id from docs2obj p1 where ref_table='partners' and doc_id=$doc_id)) 
			and t.id not in (select ref_id from docs2obj where ref_table='$ref_table' and doc_id=$doc_id) 
			ORDER by t.id";
        }
        if ($ref_table=='clientrequests') {
            $sql="SELECT distinct o.id, o.name||'|'||to_char(o.date,'DD.MM.YYYY')||'|'||c.name||'|'||substr(p.name,0,5)||'|'||substr(o.descr,0,15) FROM $ref_table o, listitems c, partners p
			WHERE c.id=o.type
			and p.id=o.partnerid

			and o.date<='$res[dateto]' and o.date>='$res[datefrom]'
			and o.partnerid in (select p1.ref_id from docs2obj p1 where ref_table='partners' and doc_id=$doc_id)
			and o.id not in (select ref_id from docs2obj where ref_table='$ref_table' and doc_id=$doc_id) 
			ORDER by o.id";
        }
        if ($sql!='') {
            $type=$this->html->htlist('obj_id_list', $sql, 0, $ref_table, '');
            $obj_input="<dt><label>or choose from</label>$type</dt>";
        }
        
        $join_to.=  "Join document $name to $ref_table";
        $hidden_fields.= "
			<input type='hidden' name='doc_id' value='$doc_id'>
			<input type='hidden' name='ref_table' value='$ref_table'>";

        //$add.="<label>$ref_table ID</label><input type='text' name='ref_id'  id='name_id' value=''>";
        $obj_id=$this->data->object_form($ref_table, 'ref_id', $value, $ref_table);

        $add.=$obj_id[out];
        $add.=$obj_input;
        $add.="<label>Type</label>$type_id</dt>";
    }
}
$out.=  "
	<div class='well span11 columns form-wrap'>";
$out.= "<form class='' action='?csrf=$GLOBALS[csrf]&act=save&what=$what' method='post' name='$what'>
	<h1>$join_to</h1>
<p>id:$id</p>
<hr>
	<fieldset>
	$hidden_fields
	$add
	$edit
	".$this->html->form_confirmations()."
	<button type='submit' class='btn btn-primary' name='act' value='save'>Submit</button> 
<div class='spacer'></div>      

</fieldset>
</form>";

$out.= '				
<script>$(document).ready(function(){

	'.$obj_id[wait].'
		'.$obj_id[load].'	

	});</script>
	';

    //$out.= "$sql";
//

    $body.=$out;
