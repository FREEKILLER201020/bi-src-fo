<?php
//Save projects
$id = $this->html->readRQn('id');

$name = $this->html->readRQ('name');

$date_from = $this->html->readRQd('date_from', 1);
$date_to = $this->html->readRQd('date_to');
$date_check = $this->html->readRQd('date_check');
$active = $this->html->readRQn('active');
$stage_id = $this->html->readRQn('stage_id');
$category_id = $this->html->readRQn('category_id');
$descr = $this->html->readRQ('descr');

if ($name == '') {
	$name = $this->data->get_new_name($what, $GLOBALS[today], '', 'PRJ-');
}

if ($date_to == '') {
	$date_to = $this->dates->F_dateadd_month($date_from, 3);
}

if ($date_check == '') {
	$date_check = $this->dates->F_dateadd($date_to, -7);
}

if ($this->dates->is_later($date_check, $date_to)) {
	$this->html->error('Date check is later than comletion date');
}

if ($this->dates->is_later($date_from, $date_to)) {
	$this->html->error('Date from is later than comletion date');
}

$vals = array(
	'name' => $name,
	'date_from' => $date_from,
	'date_to' => $date_to,
	'date_check' => $date_check,
	'active' => $active,
	'stage_id' => $stage_id,
	'category_id' => $category_id,
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
