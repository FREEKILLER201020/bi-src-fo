<?php
if ($what == 'documentactions') {
                $type = ($this->html->readRQ("type")*1);
                $active = ($this->html->readRQ("active")*1);
                $opt=$this->html->readRQ("opt");
                $filter = $this->html->readRQ("filter");
    if ($active>0) {
        $sql.=" and active='1'";
    }
                //$tmp=$this->html->readRQ("own");
                //if ($tmp <> ''){$sql = "$sql and  (executor=$tmp or initiator=$tmp or executor=0)";}
                $tmp=$this->html->readRQ("complete");
    if ($tmp == '0') {
        $sql = "$sql and  complete='f'";
    }
    if ($tmp == '1') {
        $sql = "$sql and  complete='t'";
    }
                //$tmp=$this->html->readRQ("belong");
                //if ($tmp == 'me'){$sql = "$sql and executor=$uid";}
                $tmp=$this->html->readRQ("todo");
    if ($tmp != '') {
        $sql = "$sql and  complete='f' and date<='$tomorrow'";
    }
                $tmp=$this->html->readRQ("complete");
    if ($tmp == 'no') {
        $sql = "$sql and  complete='f'";
    }
                $tmp=$this->dates->F_date($this->html->readRQ("df"));
    if ($tmp <> '') {
        $sql = "$sql and  date>='$tmp'";
    }
                $tmp=$this->dates->F_date($this->html->readRQ("dt"));
    if ($tmp <> '') {
        $sql = "$sql and  date<'$tmp'";
    }
                $tmp=$this->html->readRQ("docid");
    if ($tmp <> '') {
        $sql = "$sql and  docid='$tmp'";
    } else {
        $sql = "$sql and  docid='0'";
    }
                $tmp=$this->html->readRQ("value");
    if ($tmp <> '') {
        $sql = "$sql and  descr ~* '$tmp'";
    }
                $tmp=$this->html->readRQ("search_text");
    if ($tmp <> '') {
        $words=explode(' ', $tmp);
        foreach ($words as $word) {
            $sql_add.=" and lower(descr) ~* '$word'";
        }
        $sql = "$sql $sql_add";
    }
                
                $tmp=($this->html->readRQ("usecomplete"))*1;
    if ($tmp <> '') {
        $tmp=($this->html->readRQ("complete"))*1;
        if ($tmp <> '') {
            $sql = "$sql and  complete='1'";
        } else {
            $sql = "$sql and  complete='0'";
        }
    }
                $today=$this->dates->F_date('', 1);
                $sql1="select d.id, d.name, d.date, u2.username as executor, d.qty, substr(d.descr,0,50) as descr, (select count(*) from uploads c where tablename='$what' and refid=d.id) as files,
					CASE WHEN d.active='t' THEN $$<img src='".ASSETS_URI."/assets/img/custom/ok.gif'>$$
					ELSE $$<img src='".ASSETS_URI."/assets/img/custom/cancel.gif'>$$
					END as active,
					CASE WHEN d.complete='t' THEN $$<img src='".ASSETS_URI."/assets/img/custom/ok.png'>$$
					ELSE $$<img src='".ASSETS_URI."/assets/img/custom/warn.png'>$$
					END as completeimg, d.complete, t.name as typename, d.type";

                $sql=" from documentactions d, listitems t, users u2 where t.id=d.type and u2.id=d.executor $sql";
                $sqltotal=$sql;
                $sql = "$sql order by d.date, d.active desc, d.id";
                $sql2=" limit $limit offset $offset;";
                $sql=$sql1.$sql.$sql2;
                //$out.= "$sql";
                //if($this->db->GetVal("select count(*)".$sqltotal)==0){$out.= "<div id='info'>No $what.</div>"; return;}
    if (!($cur = pg_query($sql))) {
        $this->html->SQL_error($sql);
    }
    $rows=pg_num_rows($cur);
    $csv.=$this->data->csv($sql);
                //HT_jsdynamic();
                $nbrow=0;
                $i=$limit*$page;
                $out.="<table>";
    while ($row = pg_fetch_array($cur)) {
        $nbrow++;
        $i=$i+1;
        $no=sprintf("%03s", $row[id]);
        $col_col = "";

        $text1=str_ireplace("\r", "", $row[text1]);
        $text1=str_ireplace("\n", "<br>", $text1);
        //$out.= "\t<tr  class='$col_col' onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='$col_col'\">\n";

        $level++;
        $filler="";
        if ($row[descr]=='') {
            $row[descr]="____";
        }
        $plus="$filler<img src=\"".ASSETS_URI."/assets/img/custom/list.png\">";
        if ($row[complete]!='t') {
            $col_col='';
        } else {
            $col_col='d';
        }
        if ($row[complete]!='t') {
            if (strtotime($row[date])==strtotime($today)) {
                $col_col = "orange";
            }
            if (strtotime($row[date])<strtotime($today)) {
                $col_col = "red";
            }
        }
        $line="$row[name]<a href='?csrf=$GLOBALS[csrf]&act=save&what=boolean&field=complete&ref_table=$what&ref_id=$row[id]'>$row[completeimg]</a></td><td>";
        $line.="
						$row[date]</td><td style='min-width:300px;'>
						$row[descr]</td><td>";
        $line.="<img src='".ASSETS_URI."/assets/img/custom/user.png' >:$row[executor]|";
        if ($row[qty]>0) {
            $line.="<img src='".ASSETS_URI."/assets/img/custom/qty.png'>:$row[qty]|";
        }
        if ($row[files]>0) {
            $line.="<img src='".ASSETS_URI."/assets/img/custom/file.png'>:$row[files]|";
        }
        $line.="";
        $fastdel="";
        if ($access['main_admin']) {
            $fastdel="<img src='".ASSETS_URI."/assets/img/custom/skip.png'><a href='?csrf=$GLOBALS[csrf]&act=delete&table=$what&id=$row[id]'><img src='".ASSETS_URI."/assets/img/custom/delsm.png'></a>";
        }

        $out.= "
						<tr class=\"$col_col\">
						<td>$plus</td><td>$line</td>
						<td><a href='?act=details&what=$what&id=$row[id]'><i class='icon-eye-open withpointer'></i></a></td>
						<td><a href='?act=edit&what=$what&id=$row[id]'><i class='icon-pencil withpointer'></i></a></td>
						<td><i class='icon-trash withpointer' onclick=\"confirmation('?csrf=$GLOBALS[csrf]&act=delete&table=$what&id=$row[id]')\"></i></td>
						<td>$fastdel</td>
						</td>
						</tr>
						";
        $level--;
        //$out.= $this->html->HT_editicons($what, $row[id]);
        $totals[0]+=1;
    }
                $out.="</table>";
                
                $arrjs=substr($arrjs, 0, -1);
                $totals=$this->utils->F_toarray($this->db->GetResults("select count(*)".$sqltotal));
    if ($dynamic>0) {
        $nav=$this->html->HT_ajaxpager($totals[0], $orgqry, "$titleorig.");
    } else {
        $nav=$this->html->HT_pager($totals[0], $orgqry);
    }
    if (($opt=='')&&($totals[0]>0)) {
        //$out.= "<img src='".ASSETS_URI."/assets/img/custom/addsm.png'  style=\"opacity:.3;filter:alpha(opacity=3)\" onclick=\"SetAllNodesOpen('$arrjs')\"><img src='".ASSETS_URI."/assets/img/custom/delsm.png'  style=\"opacity:$opacity2;filter:alpha(opacity=$opacity)\" onclick=\"SetAllNodesClosed('$arrjs')\">";
        $out.= "$nav";
    }
}
            
$body.=$out;
