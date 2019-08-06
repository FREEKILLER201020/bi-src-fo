<?php
//Edit projects
if ($act == 'edit') {
	$sql = "select * from $what WHERE id=$id";
	$res = $this->db->GetRow($sql);
} else {
	$sql = "select * from $what WHERE id=$refid";
	$res2 = $this->db->GetRow($sql);
	$res[active] = 't';
	//$res[stage_id]=700;
}

$form_opt['well_class'] = "span11 columns form-wrap";

$out .= $this->html->form_start($what, $id, '', $form_opt);
$out .= "<hr>";

$out .= $this->html->form_hidden('reflink', $reflink);
$out .= $this->html->form_hidden('id', $id);
$out .= $this->html->form_hidden('reference', $reference);
$out .= $this->html->form_hidden('refid', $refid);

// $user_id = $this->data->listitems('user_id', $res[user_id], 'user', 'span12');
// $out .= "<label>User</label>$user_id";
$out .= $this->html->form_text('name', $res[name], 'Name', '', 0, 'span12');
$out .= $this->html->form_date('date', $res[date], 'Date', '', 0, 'span12');
$out .= $this->html->form_date('date_from', $res[date_from], 'Date from', '', 0, 'span12');
$out .= $this->html->form_date('date_to', $res[date_to], 'Date to', '', 0, 'span12');
$out .= $this->html->form_date('date_check', $res[date_check], 'Date check', '', 0, 'span12');
$out .= $this->html->form_chekbox('active', $res[active], 'Active', '', 0, 'span12');

// $stage_id = $this->data->listitems('stage_id', $res[stage_id], 'stage', 'span12','Select from list');
// $out .= "<label>Stage</label>$stage_id";

$sql = "SELECT id, name FROM listitems WHERE list_id=7  ORDER by id";
$out .= "<label>Stage</label>$category_id";
$out .= $this->html->htlist('stage_id', $sql, $res[stage_id], 'Select Stage', "", '', 'span12');

// $category_id = $this->data->listitems('category_id', $res[category_id], 'category', 'span12');

$sql = "SELECT id, name FROM listitems WHERE list_id=8  ORDER by id";
$out .= "<label>Category</label>$category_id";
$out .= $this->html->htlist('category_id', $sql, $res[category_id], 'Select Category', "", '', 'span12');

$out .= $this->html->form_textarea('descr', $res[descr], 'Descr', '', 0, '', 'span12');
$out .= $this->html->form_confirmations();
$out .= $this->html->form_submit('Save');
$out .= $this->html->form_end();

$body .= $out;
