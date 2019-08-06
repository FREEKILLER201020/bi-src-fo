<?php
//Show projects
if ($sortby == '') {$sortby = "id asc";}

$tmp = $this->html->readRQcsv('ids');
if ($tmp != '') {$sql .= " and id in ($tmp)";}

$tmp = $this->html->readRQn('list_id');
if ($tmp > 0) {$sql .= " and list_id=$tmp";}

$sql1 = "select *";
$sql = " from $what a where id>0 $sql";
$sqltotal = $sql;
$sql = "$sql order by $sortby";
$sql2 = " limit $limit offset $offset;";
$sql = $sql1 . $sql . $sql2;
//$out.= $sql;
$fields = array('id', 'user', 'name', 'date_from', 'date_to', 'category', 'stage', '');
//$sort= $fields;
$out = $this->html->tablehead($what, $qry, $order, 'no_addbutton', $fields, $sort);

if (!($cur = pg_query($sql))) {$this->html->HT_Error(pg_last_error() . "<br><b>" . $sql . "</b>");}
$rows = pg_num_rows($cur);if ($rows > 0) {
	$csv .= $this->data->csv($sql);
}

while ($row = pg_fetch_array($cur)) {
	$i++;
	$class = 'bold';
	//$type=$this->data->get_name('listitems',$row[type]);
	if ($row[stage_id] == 700) {
		$class = '';
	}
	if ($row[stage_id] == 702) {
		$class = 'd';
	}
	if ($this->dates->is_earlier($row[date_check], $GLOBALS['today'])) {
		$class = 'orange';
	}
	if ($this->dates->is_earlier($row[date_to], $GLOBALS['today'])) {
		$class = 'red';
	}
	$username = $this->data->username($row['user_id']);
	$category_name = $this->data->get_name('listitems', $row['category_id']);
	$stage_name = $this->data->get_name('listitems', $row['stage_id']);
	$out .= "<tr class='$class'>";
	// $out .= $this->html->edit_rec($what, $row[id], 'ved', $i);
	$out .= "<td>$i</td>";
	$out .= "<td id='$what:$row[id]' class='cart-selectable' reference='$what'>$row[id]</td>";
	$out .= "<td>$username</td>";
	$out .= "<td onMouseover=\"showhint('$row[descr]', this, event, '400px');\">$row[name]</td>";
	$out .= "<td>" . $this->dates->F_date($row[date_from]) . "</td>";
	$out .= "<td>" . $this->dates->F_date($row[date_to]) . "</td>";
	$out .= "<td>$category_name</td>";
	$out .= "<td>$stage_name</td>";
	$out .= $this->html->HT_editicons($what, $row[id]);
	$out .= "</tr>";
	$totals[2] += $row[qty];
	if ($allids) {
		$allids .= ',' . $what . ':' . $row[id];
	} else {
		$allids .= $what . ':' . $row[id];
	}

	$this->livestatus(str_replace("\"", "'", $this->html->draw_progress($i / $rows * 100)));
}
$this->livestatus('');
include FW_DIR . '/helpers/end_table.php';
