<?php
//show docs2obj
//foreach ($_POST as $key => $value) {$out.= $key . " => " . $value . "<br>\n";} $limit=30; //exit;
if ($sortby=='') {
    $sortby="type_id desc, id desc";
}

$tmp=($this->html->readRQn("user_id"));
if ($tmp>0) {
    $sql = "$sql and user_id=$tmp";
}

$tmp=($this->html->readRQn("ref_id"));
if ($tmp>0) {
    $sql = "$sql and ref_id=$tmp";
}

$tmp=($this->html->readRQn("type_id"));
if ($tmp>0) {
    $sql = "$sql and type_id=$tmp";
}

$tmp=($this->html->readRQ("ref_table"));
if ($tmp!='') {
    $sql = "$sql and ref_table='$tmp'";
}

$sql1="select *";
$sql=" from $what a where id>0 $sql";
$sqltotal=$sql;
$sql = "$sql order by $sortby";
$sql2=" limit $limit offset $offset;";
$sql=$sql1.$sql.$sql2;
//$out.= $sql;
$fields=array('#','User','Link','Type',' ');
//$sort= $fields;



if (!($cur = pg_query($sql))) {
    $this->html->SQL_error($sql);
}
$rows=pg_num_rows($cur);
if ($rows>0) {
    $csv.=$this->data->csv($sql);
    $sort='autosort';
} else {
    $sort='no_sort';
}
$out=$this->html->tablehead('', $qry, $order, $addbutton, $fields, $sort);
while ($row = pg_fetch_array($cur)) {
    $i++;
    $class='';

    $type=$this->data->get_name('listitems', $row[type_id]);

    $user=$this->data->username($row[user_id], 40);
    $object=$this->data->detalize($row[ref_table], $row[ref_id], 40);

    //$dell="<span  style=\"cursor: pointer; cursor: hand; \" onclick=\"confirmation('?csrf=$GLOBALS[csrf]&act=save&what=$what&partnerid=$row[partnerid]&clientid=$row[clientid]&action=unlink')\">[Unlink]</span>";
    //$dell="<i class='icon-resize-full tooltip-test addbtn' data-original-title='Unlink' onclick=\"confirmation('?csrf=$GLOBALS[csrf]&act=save&what=$what&partnerid=$row[partnerid]&clientid=$row[clientid]&action=unlink')\"></i>";
    
    $out.= "<td>$i</td>
	<td>$user</td>
	<td>$object</td>
	<td>$type</td>
	
	";
    //$out.= "<td>$dell</td>\t</tr>\n";

    $out.=$this->html->HT_editicons($what, $row[id]);
    $out.= "</tr>";
    //$csv.="$row[id]   $row[name]\t$row[descr]\n";
    $totals[2]+=$row[qty];
    if ($allids) {
        $allids.=','.$what.':'.$row[id];
    } else {
        $allids.=$what.':'.$row[id];
    }
    $this->livestatus(str_replace("\"", "'", $this->html->draw_progress($i/$rows*100)));
}
$this->livestatus('');
$out.=$this->html->tablefoot($i, $totals, $totalrecs);
$totals=$this->utils->F_toarray($this->db->GetResults("select count(*)".$sqltotal));
if ($dynamic>0) {
    $nav=$this->html->HT_ajaxpager($totals[0], $orgqry, "$titleorig.");
} else {
    $nav=$this->html->HT_pager($totals[0], $orgqry);
}
if ($i>5) {
    $nav.= $this->html->add_all_to_cart2($what);
}
if ($noexport=='') {
    $export= $this->utils->exportcsv($csv);
}
$body.= "$out $nav $export";
