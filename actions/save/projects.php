<?php
//Save lists
$id = $this->html->readRQn('id');
$name = $this->html->readRQ('name');
$alias = $this->html->readRQ('alias');
$descr = $this->html->readRQ('descr');
$addinfo = $this->html->readRQ('addinfo');

$vals = array(
	'name' => $name,
	'alias' => $alias,
	'descr' => $descr,
	'addinfo' => $addinfo,
);
echo $this->html->pre_display($_POST, 'Post');
echo $this->html->pre_display($vals, 'Vals');exit;
if ($id == 0) {$id = $this->db->insert_db($what, $vals);} else { $id = $this->db->update_db($what, $id, $vals);}
$body .= $out;
