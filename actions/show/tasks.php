<?php
//Show tasks
if ($sortby == '') {$sortby = "id asc";}

$tmp = $this->html->readRQcsv('ids');
if ($tmp != '') {$sql .= " and id in ($tmp)";}

$tmp = $this->html->readRQn('project_id');
if ($tmp > 0) {$sql .= " and project_id=$tmp";}

$tmp = $this->html->readRQn('parent_id');
if ($tmp > 0) {$sql .= " and parent_id=$tmp";}

$tmp = $this->html->readRQn('task_id');
if ($tmp > 0) {$sql .= " and parent_id=$tmp";}

$sql1 = "select *";
$sql = " from $what a where id>0 $sql";
$sqltotal = $sql;
$sql = "$sql order by $sortby";
$sql2 = " limit $limit offset $offset;";
$sql = $sql1 . $sql . $sql2;
//$out.= $sql;
$fields = array('id', 'project', 'parent', 'name', 'date_from', 'date_to', 'stage', '');
//$sort= $fields;
$out = $this->html->tablehead($what, $qry, $order, 'no_addbutton', $fields, $sort);

if (!($cur = pg_query($sql))) {$this->html->HT_Error(pg_last_error() . "<br><b>" . $sql . "</b>");}
$rows = pg_num_rows($cur);if ($rows > 0) {
	$csv .= $this->data->csv($sql);
}

while ($row = pg_fetch_array($cur)) {
	$i++;
	$class = '';
	//$type=$this->data->get_name('listitems',$row[type]);
	if ($row[id] == 0) {
		$class = 'd';
	}
	$stage_name = $this->data->get_name('listitems', $row['stage_id']);
	// $project_id = $this->html->readRQn('id');
	$sql = "select * from projects WHERE id=$row[project_id]";
	$project = $this->db->GetRow($sql);

	$sql = "select * from tasks WHERE id=$row[parent_id]";
	$parent = $this->db->GetRow($sql);

	if ($parent[name] == "") {
		$parent[name] = "МЫ ЭТО ЕЩЕ НЕ СДЕЛАЛИ";
	}

	$out .= "<tr class='$class'>";
	$out .= "<td>$i</td>";
	// $out .= $this->html->edit_rec($what, $row[id], 'ved', $i);
	$out .= "<td id='$what:$row[id]' class='cart-selectable' reference='$what'>$row[id]</td>";
	$out .= "<td>$project[name]</td>";
	$out .= "<td>$parent[name]</td>";
	// $out .= "<td>$row[parent_id]</td>";
	$out .= "<td onMouseover=\"showhint('$row[descr]', this, event, '400px');\">$row[name]</td>";
	$out .= "<td>" . $this->dates->F_date($row[date_from]) . "</td>";
	$out .= "<td>" . $this->dates->F_date($row[date_to]) . "</td>";
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
