<?php
//save docs2obj
$ref_id=$this->html->readRQn('ref_id');
$doc_id=$this->html->readRQn('doc_id');
$doc_id_list=$this->html->readRQn('doc_id_list');
$obj_id_list=$this->html->readRQn('obj_id_list');
$ref_table=$this->html->readRQ('ref_table');
$type_id=$this->html->readRQn('type_id');
if($doc_id_list>0)$doc_id=$doc_id_list;
if($obj_id_list>0)$ref_id=$obj_id_list;
$name=$this->html->readRQ('name');
//foreach ($_POST as $key => $value) {$out.= $key . " => " . $value . "<br>\n";} exit;
if($name!='')$doc_id=$this->db->GetVal("select id from documents where name like '%$name%' order by id desc limit 1")*1;
$count=$this->db->GetVal("select count(*) from docs2obj where ref_id=$ref_id and  doc_id=$doc_id and ref_table='$ref_table';")*1;
if($count==0){
	$id=$this->db->GetVal("select id from docs2obj where ref_id=$ref_id and doc_id=$doc_id and ref_table='$ref_table';")*1;	
}
$vals=array(
	'ref_id'=>$ref_id,
	'doc_id'=>$doc_id,
	'ref_table'=>$ref_table,
	'type_id'=>$type_id,
);
//echo $this->html->pre_display($vals,'ID: '.$id); exit;
if($id==0){$id=$this->db->insert_db($what,$vals);}else{$this->db->update_db($what,$id,$vals);}
$this->project->update_document($doc_id);
$body.=$out;
