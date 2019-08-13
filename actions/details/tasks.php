<?php
//Details tasks
$res = $this->db->GetRow("select * from $what where id=$id");
//$partner = $this->data->detalize('partners', $res[partner_id]);

$date = $this->html->readRQd('date', 1);
$out .= "<h1>$res[name]</h1>";
$out .= $this->data->details_bar($what, $id);

$out .= "<table class='table table-morecondensed table-notfull'>";
$out .= "<tr><td class='mr'><b>Date: </b></td><td class='mt'>".$this->dates->F_date($res[date])."</td></tr>";
$out .= "<tr><td class='mr'><b>Date from: </b></td><td class='mt'>".$this->dates->F_date($res[date_from])."</td></tr>";
$out .= "<tr><td class='mr'><b>Date to: </b></td><td class='mt'>".$this->dates->F_date($res[date_to])."</td></tr>";
$out .= "<tr><td class='mr'><b>Date check: </b></td><td class='mt'>".$this->dates->F_date($res[date_check])."</td></tr>";
$out .= "<tr><td class='mr'><b>Stage id: </b></td><td class='mt'>$res[stage_id]</td></tr>";
$out .= "<tr><td class='mr'><b>Descr: </b></td><td class='mt'>$res[descr]</td></tr>";
$out .= "<tr><td class='mr'><b>Link to project: </b></td><td class='mt'>".$this->data->detalize('projects',$res[project_id])."</td></tr>";
if ($res[parent_id] != 0) {
	$out .= "<tr><td class='mr'><b>Link to parent: </b></td><td class='mt'>".$this->data->detalize('tasks',$res[parent_id])."</td></tr>";
}
$out .= "</table>";

if ($res[descr]) {
	$out .= "Description:<br><pre>$res[descr]</pre>";
}

$dname = $this->data->docs2obj($id, $what);
$out .= "<b>Documents:</b> $dname<br>";
$out .= $this->show_docs2obj($id, $what);

unset($_POST[title]);
$_POST[tablename] = $what;
$_POST[refid] = $id;
$_POST[task_id] = $id;
$_POST[reffinfo] = "&tablename=$what&refid=$id";
$out .= $this->show('tasks');
$out .= $this->show('schedules');
$out .= $this->show('comments');
$out .= $this->report('posts');
$out .= $this->report('db_changes');
$body .= $out;
