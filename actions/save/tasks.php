<?php
//Save tasks
$id = $this->html->readRQn('id');
// $user_id = $this->html->readRQn('user_id');
$project_id = $this->html->readRQn('project_id');
$parent_id = $this->html->readRQn('parent_id');
$name = $this->html->readRQ('name');
// $date = $this->html->readRQ('date');
$date_from = $this->html->readRQ('date_from');
$date_to = $this->html->readRQ('date_to');
$date_check = $this->html->readRQ('date_check');
$active = $this->html->readRQn('active');
$stage_id = $this->html->readRQn('stage_id');
$descr = $this->html->readRQ('descr');

if ($name == '') {
	$name = $this->data->get_new_name($what, $GLOBALS[today], '', 'TSK-');
}

if ($date_from == '') {
	$date_from = $GLOBALS[today];
}

if ($date_to == '') {
	$date_to = $this->dates->F_dateadd_month($date_from, 3);
}

if ($date_check == '') {
	$date_check = $this->dates->F_dateadd($date_to, -7);
}
// Дата проверки не может быть позже даты окончания
if ($this->dates->is_later($date_check, $date_to)) {
	$this->html->error('Date check is later than comletion date');
}
// Дата начала не может быть позже даты окончания
if ($this->dates->is_later($date_from, $date_to)) {
	$this->html->error('Date from is later than comletion date');
}

$vals = array(
	// 'user_id' => $user_id,
	'project_id' => $project_id,
	'parent_id' => $parent_id,
	'name' => $name,
	// 'date' => $date,
	'date_from' => $date_from,
	'date_to' => $date_to,
	'date_check' => $date_check,
	'active' => $active,
	'stage_id' => $stage_id,
	'descr' => $descr,
);
if ($id == 0) {
	$vals[date] = $GLOBALS[today];
	$vals[user_id] = $GLOBALS[uid];

}
// echo $this->html->pre_display($_POST, 'Post');
// echo $this->html->pre_display($vals, 'Vals');exit;
if ($id == 0) {$id = $this->db->insert_db($what, $vals);} else { $id = $this->db->update_db($what, $id, $vals);}
$body .= $out;
