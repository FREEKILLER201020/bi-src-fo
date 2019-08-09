<?php
if ($what == 'documents') {
    global $tomorrow, $today;
    
    //echo $this->html->pre_display($_POST,'POST'); echo $this->html->pre_display($_GET,'GET'); exit;
    //foreach ($_POST as $key => $value) {$out.= $key . " => " . $value . "<br>\n";} $limit=30; //exit;
    $incdocs="1612, 1613, 1614, 1616, 1620, 1624, 1625, 1626, 1627, 1628";
    $incdocs=$this->db->GetVal("select values from listitems where id=1503");
    
    $allowed_pids=$GLOBALS['allowed_pids'];
    if ($GLOBALS[allowed_related_pids]!='') {
        $allowed_pids=$allowed_pids.','.$GLOBALS[allowed_related_pids];
    }
    //if ($GLOBALS['workgroup']['administrator_id']>0){$sql = "$sql and  (d.id in (select doc_id from docs2obj where ref_id in (".$GLOBALS['allowed_pids'].") and ref_table='partners')) ";}
    
    $type = ($this->html->readRQ("type")*1);
    $active = ($this->html->readRQ("active")*1);
    $actionlist = ($this->html->readRQ("actionlist")*1);
    $opt=$this->html->readRQ("opt");
    $page=$this->html->readRQn("page");
    if ($gid>43) {
        $sql.=" and confidentlevel=1";
        //$sql.= " and (confidentlevel=1 or d.id in (select distinct d2g.docid from docs2groups d2g where d2g.groupid=$gid))";
    }
    //if(($gid==3)||($gid==20)||($gid==23))$showord=1;
    //if($showord!=1){$sql = "$sql and d.docgroup!=1501";}
    
    if ($GLOBALS['history_tail']>0) {
        $sql = "$sql and  dateto>=now() - INTERVAL '$GLOBALS[history_tail] days'";
    }
    
    if (!$access['view_docs_oth']) {
        $sql = "$sql and d.docgroup!=1500";
    }
    if (!$access['view_docs_ord']) {
        $sql = "$sql and d.docgroup!=1501";
    }
    if (!$access['view_docs_int']) {
        $sql = "$sql and d.docgroup!=1502";
    }
    if (!$access['view_docs_inc']) {
        $sql = "$sql and d.docgroup!=1503";
    }
    if (!$access['view_docs_inv']) {
        $sql = "$sql and d.docgroup!=1504";
    }
    if (!$access['view_docs_trs']) {
        $sql = "$sql and d.docgroup!=1505";
    }
    if (!$access['view_docs_ctr']) {
        $sql = "$sql and d.docgroup!=1506";
    }
    $secure_group='1501,1502,1505';
    //if(!$access['view_docs_ids']){$sql = "$sql and d.docgroup!=1507";}
    //if(!$access['view_docs_poa']){$sql = "$sql and d.docgroup!=1508";}
    
    if (($GLOBALS['regdate'] <> '01.01.1999')&&($type==1602)) {
        $sql = "$sql and  date>='".$GLOBALS['regdate']."'";
    }
    
    $sql.= " and (d.id in (select distinct d2g.docid from docs2groups d2g where d2g.groupid=$gid or d2g.groupid=0))";
    $tmp=$this->html->readRQ("incorrect")*1;
    if ($tmp ==1) {
        $sql = "$sql and d.id in (select dd.parentid from documents dd) and d.docgroup!=1501 and d.docgroup!=1502";
    }
    if ($tmp ==2) {
        $sql = "$sql and d.parentid in (select dd.id from documents dd where dd.docgroup!=1501 and dd.docgroup!=1502)";
    }
    $filter = $this->html->readRQ("filter");
    if ($active>0) {
        $sql.=" and active='1'";
    }
    $tmp=$this->html->readRQ("type")*1;
    if ($tmp >0) {
        $excl=$this->html->readRQ("nottype")*1;
        $not=$excl>0?"!=":"=";
        $sql = "$sql and  type $not $tmp";
    }
    $tmp=$this->html->readRQcsv('ids');
    if ($tmp >0) {
        $sql = "$sql and  d.id in ($tmp)";
    }
    
    $tmp=$this->html->readRQn("autodelete");
    if ($tmp >0) {
        $sql.=" and autodelete='1'";
    }

    $tmp=$this->html->readRQ('types');
    if ($tmp!='') {
        $sql = "$sql and  d.type in ($tmp)";
    }
    
    $tmp=$this->html->readRQ("vacationid")*1;
    if ($tmp >0) {
        $sql = "$sql and  vacationid=$tmp";
    }
    $tmp=$this->html->readRQ("clientrequestid")*1;
    if ($tmp >0) {
        $sql = "$sql and  d.id in (select docid from docs2requests where clientrequestid=$tmp)";
    }
    $tmp=$this->html->readRQ("transactionid")*1;
    if ($tmp >0) {
        $sql = "$sql and  d.id in (select docid from docs2transactions where transactionid=$tmp)";
    }
    $tmp=$this->html->readRQ("docgroup")*1;
    if ($tmp >0) {
        $sql = "$sql and  docgroup=$tmp";
    }
    $tmp=$this->html->readRQ("nottype");
    if ($tmp >0) {
        $sql = "$sql and  type not in ($tmp)";
    }
    $tmp=$this->html->readRQ("notdocgroup");
    if ($tmp >0) {
        $sql = "$sql and  docgroup not in ($tmp)";
    }
    $tmp=$this->html->readRQ("parentid")*1;
    //if ($tmp >0){$sql = "$sql and  (parentid=$tmp or d.id in (select doc_id from docs2obj where ref_id=$tmp and ref_table='documents'))";}
    if ($tmp >0) {
        $sql = "$sql and  (d.id in (select doc_id from docs2obj where ref_id=$tmp and ref_table='documents'))";
    }
    $tmp=$this->html->readRQ("accountid")*1;
    if ($tmp > 0) {
        $sql = "$sql and  accountid=$tmp";
    }
    $tmp=$this->html->readRQ("contractid")*1;
    if ($tmp > 0) {
        $sql = "$sql and  contractid=$tmp";
    }
    $tmp=$this->html->readRQ("inc")*1;
    if ($tmp >0) {
        $sql = "$sql and  type in ($incdocs)";
    }
    $tmp=$this->html->readRQ("notinc")*1;
    if ($tmp >0) {
        $sql = "$sql and  type not in ($incdocs, 1602, 1652, 1658)";
    }
    $tmp=$this->html->readRQ("parentsonly")*1;
    if ($tmp >0) {
        $sql = "$sql and parentid=0 and d.id in (select dc.parentid from documents dc where dc.parentid>0)";
    }
    $tmp=$this->html->readRQ("own");
    if ($tmp <> '') {
        $sql = "$sql and  (executor=$tmp or initiator=$tmp or executor=0)";
    }

    $tmp=$this->html->readRQn("projectid");
    if ($tmp >0) {
        $sql = "$sql and d.id in (select docid from docs2partners where partnerid in (select partnerid from project2partner where projectid=$tmp))";
    }

    $tmp=$this->html->readRQn("ref_id");
    if ($tmp >0) {
        $ref_table=$this->html->readRQ("ref_table");
        $sql = "$sql and d.id in (select doc_id from docs2obj where ref_id=$tmp and ref_table='$ref_table')";
    }

    $tmp=$this->html->readRQ("executor")*1;
    if ($tmp >0) {
        $sql = "$sql and (executor=$tmp or d.id in (select da.docid from documentactions da where da.executor=$tmp))";
    }
    $tmp=$this->html->readRQ("creator")*1;
    if ($tmp >0) {
        $sql = "$sql and creator=$tmp ";
    }
    $tmp=$this->html->readRQ("creatorall")*1;
    if ($tmp >0) {
        $sql = "$sql and (creator=$tmp or d.id in (select da.docid from documentactions da where da.creator=$tmp))";
    }
    $tmp=$this->html->readRQ("currency")*1;
    if ($tmp >0) {
        $sql = "$sql and  currency=$tmp";
    }
    $tmp=$this->html->readRQ("amount")*1;
    if ($tmp >0) {
        $sql = "$sql and  amount=$tmp";
    }
    //$tmp=$this->html->readRQ("partnerslist");
    //if ($tmp !=''){$tmp=substr($tmp,0,-2);$sql = "$sql and d.id in (select doc_id from docs2obj where ref_table='partners' and ref_id in ($tmp))"; }
    
    
    $tmp=$this->html->readRQ("partnerslist");
    if ($tmp <> '') {
        //$idslist=substr($tmp,0,-2);
        $idslist=$tmp;
        if ($idslist!='') {
            $ids=explode(",", $idslist);
            foreach ($ids as $rid) {
                $rid=$rid*1;
                if ($rid>0) {
                    $sql = "$sql and d.id in (select doc_id from docs2obj where ref_table='partners' and ref_id=$rid)";
                }
            }
        }
        $_POST[partner]='';
        $idslist='';
    }
    
    $tmp=$this->html->readRQ("partner");
    if ($tmp <> '') {
        $sql = "$sql and d.id     in (select doc_id from docs2obj where ref_id=$tmp and ref_table='partners')";
    }
    if (($gid>3)) {
        $sql = "$sql and d.id not in (select doc_id from docs2obj where ref_table='partners' and ref_id in ($GLOBALS[hiddenpartnerids]))";
    }
    if ($GLOBALS[allowed_pids]!='') {
        $array_of_pids=$GLOBALS[allowed_pids];
        if ($GLOBALS[allowed_related_pids]!='') {
            //$sql = "$sql and ((d.id in (select doc_id from docs2obj where ref_table='partners' and ref_id in ($GLOBALS[allowed_pids],$GLOBALS[allowed_related_pids])) and d.docgroup not in ($secure_group)) or (d.id in (select doc_id from docs2obj where ref_table='partners' and ref_id in ($GLOBALS[allowed_pids])) and d.docgroup in ($secure_group)) or have_partners='f')";
            $sql = "$sql and (d.id in (select doc_id from docs2obj where ref_table='partners' and ref_id in ($GLOBALS[allowed_pids],$GLOBALS[allowed_related_pids])) or have_partners='f')";
            //$array_of_pids=array_merge($GLOBALS[allowed_related_pids],$array_of_pids);
        } else {
            $sql = "$sql and (d.id in (select doc_id from docs2obj where ref_table='partners' and ref_id in ($GLOBALS[allowed_pids])) or have_partners='f')";
        }
            //$sql = "$sql and (d.id in (select doc_id from docs2obj where ref_table='partners' and ref_id in ($array_of_pids)) or have_partners='f')";

        if ($type==1658) {
            $sql = "$sql and (d.executor=$uid or d.creator=$uid or d.id in (select a1.docid from documentactions a1 where a1.executor=$uid))";
        }
    }

    
    
    $tmp=$this->html->readRQ("initiator");
    if ($tmp <> '') {
        $sql = "$sql and d.initiator=$tmp";
    }
    $tmp=$this->html->readRQ("complete");
    if ($tmp != '') {
        $sql = "$sql and  complete='$tmp'";
    }
    $tmp=$this->html->readRQ("belong");
    if ($tmp == 'me') {
        $sql = "$sql and (d.executor=$uid or d.creator=$uid or d.id in (select a1.docid from documentactions a1 where a1.executor=$uid))";
    }
    $tmp=$this->html->readRQ("todo");
    if ($tmp != '') {
        $sql = "$sql and  (d.complete='f' and d.dateto<='$tomorrow' or d.id in (select a2.docid from documentactions a2 where a2.date<='$tomorrow' and a2.complete='f'))";
    }
    $tmp=$this->html->readRQ("expired");
    if ($tmp != '') {
        $sql = "$sql and  (d.complete='f' and d.dateto<='$today' or d.id in (select a2.docid from documentactions a2 where a2.date<='$today' and a2.complete='f'))";
    }
    $tmp=$this->html->readRQ("inwork");
    if ($tmp != '') {
        $sql = "$sql and  (d.complete='f' and d.dateto>'$today' or d.id in (select a2.docid from documentactions a2 where a2.date>'$today' and a2.complete='f'))";
    }
    $tmp=$this->dates->F_date($this->html->readRQ("df"));
    if ($tmp <> '') {
        $sql = "$sql and  date>='$tmp'";
    }
    $tmp=$this->dates->F_date($this->html->readRQ("dt"));
    if ($tmp <> '') {
        $sql = "$sql and  date<'$tmp'";
    }
    $tmp=$this->dates->F_date($this->html->readRQ("fdf"));
    if ($tmp <> '') {
        $sql = "$sql and  datefrom>='$tmp'";
    }
    $tmp=$this->dates->F_date($this->html->readRQ("fdt"));
    if ($tmp <> '') {
        $sql = "$sql and  datefrom<'$tmp'";
    }
    $tmp=$this->dates->F_date($this->html->readRQ("tdf"));
    if ($tmp <> '') {
        $sql = "$sql and  dateto>='$tmp'";
    }
    $tmp=$this->dates->F_date($this->html->readRQ("tdt"));
    if ($tmp <> '') {
        $sql = "$sql and  dateto<'$tmp'";
    }
    $tmp=$this->dates->F_date($this->html->readRQ("cdf"));
    if ($tmp <> '') {
        $sql = "$sql and  datecheck>='$tmp'";
    }
    $tmp=$this->dates->F_date($this->html->readRQ("cdt"));
    if ($tmp <> '') {
        $sql = "$sql and  datecheck<'$tmp'";
    }
    $tmp=$this->html->readRQ("value");
    if ($tmp <> '') {
        $sql = "$sql and  (lower(d.name) ~* lower('$tmp') or lower(d.descr) ~* lower('$tmp') or lower(d.addinfo) ~* lower('$tmp') or d.id in (select comm.refid from comments comm where comm.tablename='documents' and lower(comm.descr) ~* lower('$tmp')) or d.id in (select act.docid from documentactions act where lower(act.descr) ~* lower('$tmp')) or d.id in (select docid from docs2partners where partnerid in (select pt.id from partners pt where lower(pt.name) ~* lower('$tmp'))))";
    }

    $tmp=$this->html->readRQ("search_text");
    if ($tmp <> '') {
        $words=explode(' ', $tmp);
        foreach ($words as $word) {
            $sql_add.=" and lower(d.descr) ~* '$word'";
        }
        $sql = "$sql $sql_add";
    }
    
    $tmp=$this->html->readRQ("name");
    if ($tmp <> '') {
        $sql = "$sql and  (lower(d.name) ~* lower('$tmp') or lower(d.descr) ~* lower('$tmp') or lower(d.addinfo) ~* lower('$tmp'))";
    }
    
    $tmp=$this->html->readRQ("names");
    if ($tmp <> '') {
        if ($this->utils->contains(',', $tmp)) {
            $names=explode(',', $tmp);
            $sql.=" and(";
            $or='';
            $i=0;
            foreach ($names as $name) {
                $i++;
                if ($i>1) {
                    $or='or';
                }
                $sql.=" $or lower(d.name) ~* '$name'";
            }
            $sql.=") ";
        } else {
            $sql = "$sql and  (lower(d.name) ~* lower('$tmp') or lower(d.descr) ~* lower('$tmp') or lower(d.addinfo) ~* lower('$tmp'))";
        }
    }
    //if ($tmp == '222'){$sql = "$sql and  (lower(d.name) like lower('%$tmp%') or lower(d.descr) ~* lower('$tmp') or lower(d.addinfo) ~* lower('$tmp'))";}

    //if ($gid == 7){$sql = "$sql and  confidentlevel=1";}


    $sql1="select d.id, d.name, d.date, d.datefrom, d.datecheck, d.dateto, u2.username as executorname, d.executor, d.creator, d.qty,  substr(d.descr,0,44) as descr, d.uploads as files, d.block_download, d.amount, d.currency,
	CASE WHEN d.active='t' THEN $$<img src='".ASSETS_URI."/assets/img/custom/ok.gif'>$$
	ELSE $$<img src='".ASSETS_URI."/assets/img/custom/cancel.gif'>$$
	END as active,
	CASE WHEN d.complete='t' THEN $$<img src='".ASSETS_URI."/assets/img/custom/ok.png'>$$
	ELSE $$<img src='".ASSETS_URI."/assets/img/custom/warn.png'>$$
	END as completeimg, d.complete, t.name as typename, d.type, 0 as childs";

    $sql=" from documents d, listitems t, users u2 where t.id=d.type and u2.id=d.executor $sql";
    $sqltotal=$sql;
    $countrecs=$this->db->GetVal("select count(*)".$sqltotal);
    if ($countrecs>100) {
        $opt='';
    }
    //$out.= "Found:$countrecs limit:$limit<br>";
    $sql = "$sql order by d.name desc";
    $sql2=" limit $limit offset $offset;";
    $sql=$sql1.$sql.$sql2;
    
    //$GLOBALS['debug_message']=$sql;
    //echo $this->html->pre_display($sql);
    //if($this->db->GetVal("select count(*)".$sqltotal)==0){$out.= "<div id='info'>No $what.</div>"; return;}
    if (!($cur = pg_query($sql))) {
        $this->html->SQL_error($sql);
    }
    $rows=pg_num_rows($cur);
    $csv.=$this->data->csv($sql);
    //$csv.=$this->csv($sql);
    //HT_jsdynamic();


    $out.= "<div id='childs_'></div>";
    array($tmppost);
    array($tmpget);
    $tmppost=$_POST;
    $tmpget=$_GET;
    unset($_POST);
    unset($_GET);
    //$_GET[page]=$page;
    $fields=array('Rt.','id','name','date','type','descr','from','to','parties','files','qty','user','');
    //$sort=  array('','id','name','date','','','','type','','files','qty','');
    $out.=$this->html->tablehead($what, $qry, $order, 'no_addbutton', $fields, $sort);
    $nbrow=0;
    $i=$limit*$page;
    while ($row = pg_fetch_array($cur)) {
        if ($row[descr]=='') {
            $row[descr]="____";
        }
        $nbrow++;
        $i++;
        $r++;
        $no=sprintf("%03s", $row[id]);
        //$col_col = "";
        $col_col = "";
        if (($row[amount]>0)&&($row[currency]>0)) {
            $currency=$this->data->get_val('listitems', 'text1', $row[currency]);
        } else {
            $currency='';
        }
        if ($row[complete]!='t') {
            $complcntr=$this->db->GetVal("select count(id) from documentactions where complete='f' and docid=$row[id] and date<'$today'");
            $complcntr2=$this->db->GetVal("select count(id) from documents where complete='f' and parentid=$row[id] and dateto<'$today' and (docgroup=1501 or docgroup=1502)");
            if (($complcntr>0)||($complcntr2>0)) {
                $col_col = "roze";
            }
            $complcntr=$this->db->GetVal("select count(id) from documentactions where complete='f' and docid=$row[id] and date='$today'");
            if ($complcntr>0) {
                $col_col = "yellow";
            }
            if (strtotime($row[datecheck])<=strtotime($today)) {
                $col_col = "orange";
            }
            if (strtotime($row[dateto])<strtotime($today)) {
                $col_col = "red";
            }
        } else {
            $complcntr=$this->db->GetVal("select count(id) from documentactions where complete='f' and docid=$row[id] and date<'$today'");
            if ($complcntr>0) {
                $col_col = "red";
            }
            $complcntr=$this->db->GetVal("select count(id) from documentactions where complete='f' and docid=$row[id] and date='$today'");
            if ($complcntr>0) {
                $col_col = "orange";
            }
        }

        if (($row[executor]==$uid)||($row[creator]==$uid)||($access['main_admin'])) {
            $sw="<a href='?csrf=$GLOBALS[csrf]&act=save&what=boolean&field=complete&ref_table=$what&ref_id=$row[id]'>$row[completeimg]</a>";
        } else {
            $sw="$row[completeimg]";
        }
        $text1=str_ireplace("\r", "", $row[text1]);
        $text1=str_ireplace("\n", "<br>", $text1);
        //$partnersnamelist=substr($this->utils->F_tostring($this->db->GetResults("select substr(p.name,0,7) as name from partners p, docs2obj d where d.doc_id=$row[id] and d.ref_id=p.id and d.ref_table='partners'")),0,-1);
        //$partnersidlist=substr($this->utils->F_tostring($this->db->GetResults("select p.id from partners p, docs2obj d where d.doc_id=$row[id] and d.ref_id=p.id and d.ref_table='partners'")),0,-1);
        
        $pids=$this->utils->F_toarray($this->db->GetResults("select p.id from partners p, docs2obj d where d.doc_id=$row[id] and d.ref_id=p.id and d.ref_table='partners'"));
        $partnersnamelist2=$this->data->obj_namelist('partners', $pids, 1, ',', 1, 5);
    
        //$out.= "\t<tr  class='$col_col' onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='$col_col'\">\n";

        $filler="";
        $plus="$filler<img id=\"image$row[id]\" onClick=\"SetNode('$row[id]')\" src=\"".ASSETS_URI."/assets/img/custom/closed.png\">";
        $arrjs.="$row[id],";
        if ($access[edit_processdata]) {
            $basket="<span class='docfield0' onClick=\"this.className='blackout';basket_additem('documents:$row[id]')\" style='height:8px; padding-bottom:2px;'>⊕</span>";
        } else {
            $basket="";
        }
        if ($row[complete]!='t') {
        } else {
            $col_col='d';
        }
        if ($actionlist!=0) {//create list of actions
            $nextincomplete='';
            $lastcomplete='';
            $tmp=$this->db->GetRow("select * from documentactions where docid=$row[id] and complete='t' order by date desc limit 1");
            if ($tmp[name]!='') {
                $executor=$this->db->GetVal("select username from users where id=$tmp[executor]");
                $lastcomplete=" Done:$tmp[name] on $tmp[date] by $executor";
            }
            $tmp=$this->db->GetRow("select * from documentactions where docid=$row[id] and complete='f' order by date asc limit 1");
            if ($tmp[name]!='') {
                $executor=$this->db->GetVal("select username from users where id=$tmp[executor]");
                $nextincomplete=" Next:$tmp[name] on $tmp[date] by $executor";
            }
            $line.="$lastcomplete|$nextincomplete";
        }
        $id=$row[id];
        $modalbtn=$this->html->show_hide('Document_Route'.$id, "?act=show&what=documentactions&plain=1&docid=$id&opt=&refid=$id&reference=&tablename=documents&refid=$id", 'inline');
        //$partnersnamelist2="<div class='faded'>$partnersnamelist ($partnersidlist)</div>";
        if ($row[files]<=5) {
            $row[files]='';
            $sql="select * from uploads where refid=$row[id] and tablename='documents' and active='t'";
            if (!($cur2 = pg_query($sql))) {
                $this->html->SQL_error($sql);
            }
            while ($row2 = pg_fetch_array($cur2)) {
                $file_type=explode('/', $row2[filetype]);
                if ($file_type[0]=='image') {
                    $icon='icon-picture';
                } else {
                    $icon='icon-file';
                }
                if ($row[block_download]=='f') {
                    $row[files].= "<a href='?act=details&what=uploads&id=$row2[id]' onMouseover=\"showhint('$row2[filename]', this, event, '200px');\"><i class='$icon'></i></a>";
                } else {
                    if (($access[main_admin])||($access[block_download])) {
                        $row[files].= "<a href='?act=details&what=uploads&id=$row2[id]' onMouseover=\"showhint('$row2[filename]', this, event, '200px');\"><i class='$icon'></i></a>";
                    }
                }
            }
        }
        if ($row[amount]>0) {
            $amount=" $currency $row[amount]";
        } else {
            $amount='';
        }
        //$partnersnamelist2=$this->html->pre_display($pids);

        $out.="<tr class='$col_col'>";
        //$out.= $this->html->edit_rec($what, $row[id], 'ved', $i);
        $out.="<td>$i</td>";
        $out.="<td>$modalbtn</td>";
        $out.= "<td id='$what:$row[id]' class='cart-selectable' reference='$what' amount='$row[amount]'>$row[id]</td>";
        $out.="<td>$row[name]$sw</td>";

        // $out.="<tr class='$col_col' style=''><td>$i</td><td>$modalbtn</td>
        // <td onClick=\"this.className='blackout';basket_additem('documents:$row[id]')\">$row[id]</td>
        // <td>$row[name]$sw</td>
        $out.="<td>$row[date]</td>
		<td>$row[typename]</td>
		<td>$row[descr] $amount</td>
		<td>$row[datefrom]</td>
		<td>$row[dateto]</td>
		
		<td>$partnersnamelist2</td>
		<td>$row[files]</td>
		<td>$row[qty]</td>
		<td>$row[executorname]</td>
		";
        $out.=$this->html->HT_editicons($what, $row[id]);
        $out.= "\t</tr>\n";
        $totals[0]+=1;
        $row[descr]=str_replace(array("\n","\r","\t"), array(" "," "," "), $row[descr]);
        $csv.="$row[name]\t$row[date]\t$frompartner\t$client\t$type\t$row[descr]\t$amount\t$received\t$approved\n";
        if ($allids) {
            $allids.=','.$what.':'.$row[id];
        } else {
            $allids.=$what.':'.$row[id];
        }
        $this->livestatus(str_replace("\"", "'", $this->html->draw_progress($r/$rows*100)));
        //$this->livestatus(str_replace("\"","'",($r/$rows*100)));
    }
    $this->livestatus('');
    $_POST=$tmppost;
    $_GET=$tmpget;
    include(FW_DIR.'/helpers/end_table.php');
}
