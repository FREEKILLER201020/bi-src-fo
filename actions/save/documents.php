<?php
$id=$this->html->readRQn('id');
$save_qty=$this->html->readRQn('save_qty');
if($save_qty==0)$save_qty=1;
if($save_qty>=40)$save_qty=1;
if($id>0)$save_qty=1;
for ($j=1; $j <= $save_qty; $j++){
	if(!isset($reservredpostdata))$reservredpostdata=$_POST;
	$_POST=$reservredpostdata;
	if($this->html->readRQ('duplicate')>0){
		echo "dupe1 id:$id Gid:". $GLOBALS[old_id]."<br>";// exit;
	}
		echo "saving:$id<br>";// exit;
	$this->save('documents_save');
	$id=($this->db->GetVal("select max(id) from documents")*1);
}
//exit;
