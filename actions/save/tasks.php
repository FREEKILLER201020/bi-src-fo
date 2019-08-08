<?php
//Save tasks
$id = $this->html->readRQn('id');
$user_id = $this->html->readRQn('user_id');
$project_id = $this->html->readRQn('project_id');
$parent_id = $this->html->readRQn('parent_id');
$name = $this->html->readRQ('name');
$date = $this->html->readRQ('date');
$date_from = $this->html->readRQ('date_from');
$date_to = $this->html->readRQ('date_to');
$date_check = $this->html->readRQ('date_check');
$active = $this->html->readRQn('active');
$stage_id = $this->html->readRQn('stage_id');
$descr = $this->html->readRQ('descr');

$vals = array(
	'user_id' => $user_id,
	'project_id' => $project_id,
	'parent_id' => $parent_id,
	'name' => $name,
	'date' => $date,
	'date_from' => $date_from,
	'date_to' => $date_to,
	'date_check' => $date_check,
	'active' => $active,
	'stage_id' => $stage_id,
	'descr' => $descr,
);
echo $this->html->pre_display($_POST, 'Post');
echo $this->html->pre_display($vals, 'Vals');exit;
if ($id == 0) {$id = $this->db->insert_db($what, $vals);} else { $id = $this->db->update_db($what, $id, $vals);}
$body .= $out;
