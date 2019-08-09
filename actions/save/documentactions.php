<?php
if ($what == 'documentactions') {
    $id=($this->html->readRQ('id')*1);
    //$name=$this->html->readRQ('name');
    $date=$this->dates->F_date($this->html->readRQ('date'), 1);
    $type=($this->html->readRQ('type')*1);
    $creator=$uid;
    $executor=($this->html->readRQ('executor')*1);
    $docid=($this->html->readRQ('docid')*1);
    $active=($this->html->readRQ('active')*1);
    $complete=($this->html->readRQ('complete')*1);
    $sendmail=($this->html->readRQ('sendmail')*1);
    $sendsms=($this->html->readRQ('sendsms')*1);
    $sendalert=($this->html->readRQ('sendalert')*1);
    $qty=($this->html->readRQ('qty')*1);
    $descr=($this->html->readRQ('descr'));
    $name=$this->db->GetVal("select name from listitems where id=$type");
    //$documentactions=array();
    //$documentactions=$this->db->GetRow("select * from $what where id=$id");
    //if($id==0)$documentactions[complete]=$complete;
    //$out.= "<pre>";print_r($documentactions);$out.= "</pre>";exit;
    $vals=array(
            'name'=>$name,
            'date'=>$date,
            'type'=>$type,
            'creator'=>$creator,
            'executor'=>$executor,
            'docid'=>$docid,
            'active'=>$active,
            'complete'=>$complete,
            'qty'=>$qty,
            'descr'=>$descr
        );
        //
    if ($id==0) {
        $id=$this->db->insert_db($what, $vals);
    } else {
        $id=$this->db->update_db($what, $id, $vals);
    }
        
    
//$out.= "$documentactions[complete]!=$complete<br>"; exit;
//$documentactions[complete]=to_binary($documentactions[complete]);
//if(($complete=='1')&&($documentactions[complete]!=$complete)){$this->db->GetVal("update $what set date=now() where id=$id");}
//$out.= "$sql<br>";
    $field='complete';
    $value=$this->db->GetVal("select $field from $what where id=$id");
    $docid=$this->db->GetVal("select docid from $what where id=$id");
    $document=$this->db->GetRow("select * from documents where id=$docid");
    $doc=$document;
    $count=$this->db->GetVal("select count(*) from $what where docid=$docid");
    $positive=$this->db->GetVal("select count(*) from $what where docid=$docid and $field='1'");
//$negative=$this->db->GetVal("select count(*) from $table where docid=$docid and $field!='$value'");
    if ($positive==$count) {
        $value='1';
    } else {
        $value='0';
    }
    $sql="UPDATE documents SET $field='$value' where id=$docid";
    if (!(($document[type]==1618)||($document[type]==1698))) {
        $line=$this->db->GetVar($sql);
    }
//$out.= "Today:$today<br>";
    $datedif=$this->dates->F_datediff($document[dateto], $date);
    if ($datedif>0) {
        $sql="update documents set dateto='$date' where id=$docid";
        //$out.= "Expired:$datedif, $sql";
        if ($document[type]!=1618) {
            $line=$this->db->GetVar($sql);
        }
    }
//$cur= $this->db->GetVar($sql);

    $_POST[sendalert]=0;
    $_POST[sendsms]=0;
    $_POST[sendmail]=0;

    if (($sendsms>0)&&($executor>0)) {
        $mobile=$this->db->GetVal("select mobile from users where id=$executor");
        $docname=$this->db->GetVal("select name from documents where id=$id");
        $text="Action: $name ($descr) in document $doc[name]. $username";
        //$out.= "$text";
        if ($mobile!='') {
            $click=sendsms($mobile, $text);
        }
        //$out.= "$click ($docname)-$mobile";
    }
    if (($sendalert>0)&&($executor>0)) {
        //$mobile=$this->db->GetVal("select mobile from users where id=$executor");
        $docname=$this->db->GetVal("select name from documents where id=$id");
        $text="Action: $name ($descr) in document $doc[name]. $username";
        //$out.= "$text";
        //if($mobile!='')$click=sendsms($mobile,$text);
        $sql="insert into useralerts (userid, fromuserid, date, time, tablename, refid, descr) values ($executor, $uid, now(),now(), '$what', $id, '$text')";
        $dummy=$this->db->GetVal($sql);
        //$out.= "$sql";
    }
    if (($sendmail>0)&&($executor>0)) {
        $to=$this->db->GetVal("select email from users where id=$executor");
        $from=$this->db->GetVal("select email from users where id=$uid");
        $docname=$this->db->GetVal("select name from documents where id=$id");
        $text="From user <b>$username</b><br>Action: $name ($descr) in document $doc[name]<br>$doc[descr]<br>$doc[addinfo]";
        //$out.= "$text";
        $subject="System alert";
        if (($to!='')&&($from!='')) {
            $mail=$this->utils->sendmail_html($to, $from, $subject, $text);
        }
    }

/*

if(($sendalert>0)&&($executor>0)){
    //$mobile=$this->db->GetVal("select mobile from users where id=$executor");
    $docname=$this->db->GetVal("select name from documents where id=$docid");
    $text="New Route $name ($descr) in document $docname is created for you by $username";
    ///$out.= "$text";
    //if($mobile!='')$click=sendsms($mobile,$text);
    $sql="insert into useralerts (userid, fromuserid, date, time, tablename, refid, descr) values ($executor, $uid, now(),now(), 'documents', $docid, '$text')";
    $dummy=$this->db->GetVal($sql);
    //$out.= "$sql";
}
*/
    $logtext.=" name=$name, ID=$id";
}





?>
$body.=$out;
