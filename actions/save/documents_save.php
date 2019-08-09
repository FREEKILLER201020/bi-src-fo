<?php
$what='documents';
    $id=($this->html->readRQ('id')*1);
    //echo $this->html->pre_display($_POST);exit;
    //foreach ($_POST as $key => $value) {$out.= $key . " => " . $value . "<br>\n";} exit
    $save_qty=($this->html->readRQ('save_qty')*1);
if ($save_qty==0) {
    $save_qty=1;
}
if ($save_qty>20) {
    $save_qty=1;
}
    

$name=$this->html->readRQ('name');
$date=$this->html->readRQd('date', 1);
$datefrom=$this->html->readRQd('datefrom', 1);
$dateto=$this->html->readRQd('dateto', 1);
$datecheck=$this->html->readRQd('datecheck', 1);
$type=$this->html->readRQn('type');
$creator=$this->html->readRQn('creator');
$initiator=$this->html->readRQn('initiator');
$executor=$this->html->readRQn('executor');
$active=$this->html->readRQn('active');
$complete=$this->html->readRQn('complete');
$qty=$this->html->readRQn('qty');
$priority=$this->html->readRQn('priority');
$confidentlevel=$this->html->readRQn('confidentlevel');
$descr=$this->html->readRQ('descr');
$addinfo=$this->html->readRQ('addinfo');
$amount=$this->html->readRQn('amount');
$accountid=$this->html->readRQn('accountid');
$transactionid=$this->html->readRQn('transactionid');
$contractid=$this->html->readRQn('contractid');
$uploads=$this->html->readRQn('uploads');
$currency=$this->html->readRQn('currency');
$parentid=$this->html->readRQn('parentid');
$docgroup=$this->html->readRQn('docgroup');
$vacationid=$this->html->readRQn('vacationid');
$block_download=$this->html->readRQn('block_download');
$have_partners=$this->html->readRQn('have_partners');


    //$out.= "<br>$name"; exit;
    $type=($this->html->readRQ('type')*1);
    $docgroup=($this->html->readRQ('docgroup')*1);
if ($docgroup==1509) {
    $this->html->error("Plaese define a correct document type");
}
    $datefrom=$this->dates->F_date($this->html->readRQ('datefrom'), 1);
    $dateto=$this->dates->F_date($this->html->readRQ('dateto'), 1);
    $datecheck=$this->dates->F_date($this->html->readRQ('datecheck'), 1);
    $descr=($this->html->readRQ('descr'));
    $addinfo=($this->html->readRQ('addinfo'));
    $addinfo =$this->html->readRQc('addinfo');
    $qty=($this->html->readRQn('qty'));
    $amount=($this->html->readRQn('amount'));
    $amount_vatable=($this->html->readRQn('amount_vatable'));
    $amount_vat=($this->html->readRQn('amount_vat'));
    $initiator=($this->html->readRQ('initiator')*1);
    $creator=($this->html->readRQ('creator')*1);
    $partnerslist=($this->html->readRQ('partnerslist'));
    $groupslist=($this->html->readRQ('groupslist'));
    $executor=($this->html->readRQ('executor')*1);
    $currency=($this->html->readRQ('currency')*1);
    $accountid=($this->html->readRQ('accountid')*1);
    $transactionid=($this->html->readRQ('transactionid')*1);
    $contractid=($this->html->readRQ('contractid')*1);
    $vacationid=($this->html->readRQ('vacationid')*1);
    $clientrequestid=($this->html->readRQ('clientrequestid')*1);
    $route=($this->html->readRQ('route')*1);
    $complete=($this->html->readRQ('complete')*1);
    $sendmail=($this->html->readRQ('sendmail')*1);
    $sendsms=($this->html->readRQ('sendsms')*1);
    $sendalert=($this->html->readRQ('sendalert')*1);
    $active=($this->html->readRQ('active')*1);
    $autodelete=($this->html->readRQ('autodelete')*1);
    $block_download=($this->html->readRQ('block_download')*1);
    $confidentlevel=($this->html->readRQ('confidentlevel')*1);
    $parentid=($this->html->readRQ('parentid'));
    $invoice_type_id=$this->html->readRQn('invoice_type_id');
    $parentid=$this->db->GetVal("select id from documents where name='$parentid'")*1;
    

if (($amount_vatable==0)&&(($amount_vat>0)&&($amount>0))) {
    $amount_vatable=$amount-$amount_vat;
}

if (($amount_vatable==0)&&($amount>0)) {
    $amount_vatable=$amount;
}
if (($amount_vatable>0)&&($amount==0)) {
    $amount=$amount_vatable;
}
if ($qty==0) {
    $qty=1;
}
if ($creator==0) {
    $creator=$uid;
}
if (($access[main_admin])||($access[block_download])) {
    $sqlupd="block_download='$block_download',";
    $sqlins1="block_download,";
    $sqlins2="'$block_download',";
}
    
if ($id>0) {
    $document=$this->data->get_row('documents', $id);
    $name=$document[name];
    if ($name=='') {
        $name=$this->project->get_new_docname($date);
    }
    $actname="SAVE EDITED";
} else {
    $adding='adding';
    $date=$this->dates->F_date($this->html->readRQ('date'), 1);
    $name=$this->project->get_new_docname($date);
    $actname="INSERT NEW";
}
    $vals=array(
    'name'=>$name,
    'date'=>$date,
    'datefrom'=>$datefrom,
    'dateto'=>$dateto,
    'datecheck'=>$datecheck,
    'type'=>$type,
    'creator'=>$creator,
    'initiator'=>$initiator,
    'executor'=>$executor,
    'active'=>$active,
    'complete'=>$complete,
    'qty'=>$qty,
    'priority'=>$priority,
    'confidentlevel'=>$confidentlevel,
    'descr'=>$descr,
    'addinfo'=>$addinfo,
    'amount'=>$amount,
    'amount_vatable'=>$amount_vatable,
    'amount_vat'=>$amount_vat,
    'accountid'=>$accountid,
    'transactionid'=>$transactionid,
    'contractid'=>$contractid,
    'uploads'=>$uploads,
    'currency'=>$currency,
    'parentid'=>$parentid,
    'docgroup'=>$docgroup,
    'vacationid'=>$vacationid,
    'block_download'=>$block_download,
    'have_partners'=>$have_partners
    );
//echo $this->html->pre_display($_POST,'Post'); echo $this->html->pre_display($vals,'Vals');exit;
    if ($id==0) {
        $id=$this->db->insert_db($what, $vals);
    } else {
        $id=$this->db->update_db($what, $id, $vals);
    }

    //echo "$sql<br>";exit;
    if ($id==0) {
        $id=$this->db->GetVar("select max(id) from documents;");
        $ins=1;
    }
    $doc=$this->db->GetRow("select * from documents where id=$id");
    //$out.= "INS=$ins<br>";
    if ($clientrequestid>0) {
        $sql="select count(*) from docs2requests where clientrequestid=$clientrequestid and  docid=$id;";
        $count=$this->db->GetVal($sql)*1;
        if ($count==0) {
            $sql="insert into docs2requests (clientrequestid, docid)values($clientrequestid,$id);";
            //$out.= "$sql";
            $dummy=$this->db->GetVal($sql);
        }
    }
    if ($transactionid>0) {
        $sql="select count(*) from docs2transactions where transactionid=$transactionid and  docid=$id;";
        $count=$this->db->GetVal($sql)*1;
        if ($count==0) {
            $sql="insert into docs2transactions (transactionid, docid)values($transactionid,$id);";
            //$out.= "$sql";
            $dummy=$this->db->GetVal($sql);
        }
    }
    $_POST[sendalert]=0;
    $_POST[sendsms]=0;
    $_POST[sendmail]=0;

    if (($sendsms>0)&&($executor>0)) {
        $mobile=$this->db->GetVal("select mobile from users where id=$executor");
        $docname=$this->db->GetVal("select name from documents where id=$id");
        $text="$docname is created for you by $username ()";
        //$out.= "$text";
        if ($mobile!='') {
            $click=sendsms($mobile, $text);
        }
        //$out.= "$click ($docname)-$mobile";
    }
    if (($sendalert>0)&&($executor>0)) {
        //$mobile=$this->db->GetVal("select mobile from users where id=$executor");
        $docname=$this->db->GetVal("select name from documents where id=$id");
        $text="$docname is created for you by $username ($descr)";
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
        $text="$doc[name] is created for you by $username<br>$doc[descr]<br>$doc[addinfo]";
        //$out.= "$text";
        $subject="System alert";
        if (($to!='')&&($from!='')) {
            $mail=$this->utils->sendmail_html($to, $from, $subject, $text);
        }
    }
    //exit;
    if ($adding!='') {
        $i=0;
        //$cur= $this->db->GetVar("delete from docs2partners where docid=$id");
        $partners=array_filter(explode(",", $partnerslist));
        
        foreach ($partners as $partner) {
            $i++;
            $partner=$partner*1;
            $ref_type_id=0;

            $vals=array(
                'ref_id'=>$partner,
                'doc_id'=>$id,
                'ref_table'=>'partners',
                'type_id'=>$ref_type_id,
            );
            echo $this->html->pre_display($vals, 'ID: '.$id); //exit;
            if ($partner>0) {
                $this->db->insert_db('docs2obj', $vals);
            }
        }
    }
    
    if ($executor>0) {
        $exgid=$this->db->GetVal("select groupid from user_group where userid=$executor");
    }
    $hasmygroup=0;
    $hasexgroup=0;
    $hasusergroup=0;
    $cur= $this->db->GetVar("delete from docs2groups where docid=$id");
    $groups=explode(",", $groupslist);
    foreach ($groups as $groupid) {
        $groupid=$groupid*1;
        $sql="insert into docs2groups (docid, groupid)values($id,$groupid);";

        if (($executor>0)&&($exgid==$groupid)) {
            $hasexgroup++;
        }
        if ($groupid==$gid) {
            $hasmygroup++;
        }
        if ($groupid==3) {
            $hasusergroup++;
        }
        if ($groupid>0) {
            $out.= "$sql<br>";
            $cur= $this->db->GetVar($sql);
        }
    }
    //$count=$this->db->GetVal("Select count(*) from docs2groups where docid=$id and groupid=$gid")*1;
    //if($count==0){$this->db->GetVar("insert into docs2groups (docid, groupid)values($id,$gid)");}
    $count=$this->db->GetVal("Select count(*) from docs2groups where docid=$id")*1;
    if ($count==0) {
        $this->db->GetVar("insert into docs2groups (docid, groupid)values($id,0)");
    } else {
        if (($hasusergroup==0)&&($gid!=3)) {
            $this->db->GetVar("insert into docs2groups (docid, groupid)values($id,3)");
        }
        if ($hasmygroup==0) {
            $this->db->GetVar("insert into docs2groups (docid, groupid)values($id,$gid)");
        }
        if (($executor>0)&&($hasexgroup==0)) {
            $this->db->GetVar("insert into docs2groups (docid, groupid)values($id,$exgid)");
        }
    }

    //$out.= "My group = $gid($hasmygroup), Exgroup=$exgid($hasexgroup)<br>"; exit;
    $_POST[docid]=$id;
    $_POST[date]=$datecheck;
    if ($ins==1) {
        $typevalues=$this->db->GetVal("select values from listitems where id=$route");
        //$out.= "routes=$typevalues<br>";
        $childs=explode(",", $typevalues);
        foreach ($childs as $child) {
            if ($child>0) {
                $_POST[type]=$child;
                $_POST[name]=$this->db->GetVal("select name from listitems where id=$child");
                $out.=$this->save('documentactions');
            }
        }
    }
    

    
    $complete=$this->db->GetVal("select complete from documents where id=$id");
    if ($complete=='t') {
        $complete=$this->db->GetVal("update documentactions set complete='t' where docid=$id");
    }
    
    
    if ($this->html->readRQ('duplicate')>0) {
        $sql="select * from docs2obj where doc_id=$GLOBALS[old_id]";
        if (!($cur = pg_query($sql))) {
            $this->html->SQL_error($sql);
        }
        while ($row = pg_fetch_array($cur)) {
            $sql="insert into docs2obj (ref_id, doc_id, ref_table)values($row[ref_id],$id,'$row[ref_table]');";
            $this->db->GetVal($sql);
        }
        $sql="select * from documentactions where docid=$GLOBALS[old_id]";
        if (!($cur = pg_query($sql))) {
            $this->html->SQL_error($sql);
        }
        while ($row = pg_fetch_array($cur)) {
            $vals=array(
                'name'=>$row[name],
                'date'=>$row[date],
                'type'=>$row[type],
                'creator'=>$row[creator],
                'executor'=>$row[executor],
                'docid'=>$id,
                'active'=>$row[active],
                'complete'=>$row[complete],
                'qty'=>$row[qty],
                'descr'=>$row[descr]
            );
            $this->db->insert_db('documentactions', $vals);
        }
    }
    
    
    
    // if($type==1602){
    //  if($invoice_type_id==0){$this->utils->post_error("Please select the type of the invoice");}
    //  if($amount==0){$this->utils->post_error("Please enter the amount of the invoice");}
    // }

    $logtext.=" name=$name";

    //$out.= "Press back button.";
    //exit;
    $body.=$out;
