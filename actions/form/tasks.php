<?php
//Edit tasks
// echo $this->html->pre_display($_GET, "get");
if ($act == 'edit') {
	$sql = "select * from $what WHERE id=$id";
	$res = $this->db->GetRow($sql);
} else {
	// echo $this->html->pre_display($what, "project");
	if ($tablename == 'projects') {
		$sql = "select * from projects WHERE id=$refid";
		$project = $this->db->GetRow($sql);
		$res[project_id] = $project[id];
		// 	//echo $this->html->pre_display($project,"project");
	} else {
		$sql = "select * from tasks WHERE id=$refid";
		$parent_task = $this->db->GetRow($sql);
		$project_id = $parent_task[project_id];
		$sql = "select * from projects WHERE id=$project_id";
		$project = $this->db->GetRow($sql);
		$res[project_id] = $project[id];
		$res[parent_id] = $refid;

	}
	$res[active] = 't';

}
// echo $this->html->pre_display($project, "project");
// echo $this->html->pre_display($parent_task, "parent_task");
$form_opt['well_class'] = "span11 columns form-wrap";
$form_opt['title'] = "Task of $tablename $project[name]";

$out .= $this->html->form_start($what, $id, '', $form_opt);
$out .= "<hr>";

$out .= $this->html->form_hidden('reflink', $reflink);
$out .= $this->html->form_hidden('id', $id);
$out .= $this->html->form_hidden('reference', $reference);
$out .= $this->html->form_hidden('refid', $refid);
$out .= $this->html->form_hidden('project_id', $res['project_id']);
$out .= $this->html->form_hidden('parent_id', $res['parent_id']);

// $user_id = $this->data->listitems('user_id', $res[user_id], 'user', 'span12');
// $out .= "<label>User</label>$user_id";

// $project_id = $this->data->listitems('project_id', $res[project_id], 'project', 'span12');
// $out .= "<label>Project</label>$project_id";

// $parent_id = $this->data->listitems('parent_id', $res[parent_id], 'parent', 'span12');
// $out .= "<label>Parent</label>$parent_id";
$out .= $this->html->form_text('name', $res[name], 'Name', '', 0, 'span12');
// $out .= $this->html->form_date('date', $res[date], 'Date', '', 0, 'span12');
$out .= $this->html->form_date('date_from', $res[date_from], 'Date from', '', 0, 'span12');
$out .= $this->html->form_date('date_check', $res[date_check], 'Date check', '', 0, 'span12');
$out .= $this->html->form_date('date_to', $res[date_to], 'Date to', '', 0, 'span12');
$out .= "<label>Stage</label>";
$sql = "SELECT id, name FROM listitems WHERE list_id=7  ORDER by id";
$out .= $this->html->htlist('stage_id', $sql, $res[stage_id], 'Select Stage', "", '', 'span12');
$out .= $this->html->form_chekbox('active', $res[active], 'Active', '', 0, 'span12');
$out .= $this->html->form_textarea('descr', $res[descr], 'Descr', '', 0, '', 'span12');

$out .= $this->html->form_confirmations();
$out .= $this->html->form_submit('Save');
$out .= $this->html->form_end();

$body .= $out;
