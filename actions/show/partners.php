<?php
//Show partners
if($sortby==''){$sortby="id asc";}

$tmp=$this->html->readRQcsv('ids');
if ($tmp!=''){$sql.=" and id in ($tmp)";}

$tmp=$this->html->readRQn('list_id');
if ($tmp>0){$sql.=" and list_id=$tmp";}

$sql1="select *";
$sql=" from $what a where id>0 $sql";
$sqltotal=$sql;
$sql = "$sql order by $sortby";
$sql2=" limit $limit offset $offset;";
$sql=$sql1.$sql.$sql2;
//$out.= $sql;
$fields=array('id','name','type','email','mobile','country',' ');
//$sort= $fields;
$out=$this->html->tablehead($what,$qry, $order, 'no_addbutton', $fields,$sort);

if (!($cur = pg_query($sql))) {$this->html->HT_Error( pg_last_error()."<br><b>".$sql."</b>" );}
$rows=pg_num_rows($cur);if($rows>0)$csv.=$this->data->csv($sql);
while ($row = pg_fetch_array($cur)) {
    $i++;
    $class='';
    $type=$this->data->get_name('listitems',$row[type_id]);
    $country=$this->data->get_name('listitems',$row[country_id]);
    if($row[id]==0)$class='d';
    $out.= "<tr class='$class'>";
    //$out.= $this->html->edit_rec($what,$row[id],'ved',$i);

    $out.= "<td>$i</td>";
    $out.= "<td id='$what:$row[id]' class='cart-selectable' reference='$what'>$row[id]</td>";
    $out.= "<td onMouseover=\"showhint('$row[descr]', this, event, '400px');\">$row[name]</td>";
    $out.= "<td>$type</td>";
    $out.= "<td>$row[email]</td>";
    $out.= "<td>$row[mobile]</td>";
    $out.= "<td>$country</td>";

    $out.=$this->html->HT_editicons($what, $row[id]);
    $out.= "</tr>";
    $totals[2]+=$row[qty];
    if ($allids) $allids.=','.$what.':'.$row[id]; else $allids.=$what.':'.$row[id];
    $this->livestatus(str_replace("\"","'",$this->html->draw_progress($i/$rows*100)));
}
$this->livestatus('');
include(FW_DIR.'/helpers/end_table.php');
