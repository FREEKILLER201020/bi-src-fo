<?php
//function update_document
$res=$this->db->GetRow("select * from documents where id=$id");

$partners=$this->db->GetVal("select count(*) from docs2obj where ref_table='partners' and doc_id=$id")*1;

$value=($partners>0 ? 'true' : 'false');

$sql="update documents set have_partners='$value' where id=$id";
//echo "$sql";
$tmp=$this->db->GetVal($sql);

$sql="select count(*) from uploads where refid=$res[id] and tablename='documents' and active='t'";
$count=$this->db->GetVal($sql)*1;
$sql="update documents set uploads=$count where id=$res[id]";
$c=$this->db->GetVal($sql);

if(($res[type]==1618)||($res[type]==1698)){
	$grantor_id=$this->db->GetVal("select ref_id from docs2obj where ref_table='partners' and doc_id=$res[id] and type_id=5722 order by id desc limit 1")*1;
	$attorney_id=$this->db->GetVal("select ref_id from docs2obj where ref_table='partners' and doc_id=$res[id] and type_id=5709 order by id desc limit 1")*1;
	$event_id=$this->db->GetVal("select id from events where type=1309 and reference='partners' and refid=$grantor_id and partnerid=$attorney_id and datefrom='$res[datefrom]' and dateto='$res[dateto]' order by id desc limit 1")*1;
	if($event_id==0){
		$att_name=$this->data->get_name('partners',$attorney_id);
		$vals=array(
			'name'=>'Appoint Attorney',
			'date'=>$GLOBALS[today],
			'datefrom'=>$res[datefrom],
			'dateto'=>$res[dateto],
			'datecheck'=>$GLOBALS[today],
			'type'=>'1309',
			'creator'=>$res[creator],
			'initiator'=>0,
			'executor'=>$res[creator],
			'refid'=>$grantor_id,
			'reference'=>'partners',
			'active'=>'1',
			'complete'=>'1',
			'done'=>'0',
			'users'=>'',
			'parties'=>'',
			'amount'=>'0',
			'interest'=>'0',
			'text1'=>$att_name,
			'text2'=>'',
			'text3'=>'Auto inserted from doc_id='.$res[id],
			'partnerid'=>$attorney_id,
			'qty'=>'0',
			'price'=>'0',
			'date1'=>$GLOBALS[today],
			'documentid'=>$res[id],
		);
		$event_id=$this->db->insert_db('events',$vals);
	}
	$event=$this->data->detalize('events',$event_id);
}

?>