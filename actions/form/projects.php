<?php
//Edit lists
if ($act == 'edit') {
	$sql = "select * from $what WHERE id=$id";
	$res = $this->db->GetRow($sql);
} else {
	$sql = "select * from $what WHERE id=$refid";
	$res2 = $this->db->GetRow($sql);
	$res[active] = 't';
}

$form_opt['well_class'] = "span11 columns form-wrap";

$out .= $this->html->form_start($what, $id, '', $form_opt);
$out .= "<hr>";

$out .= $this->html->form_hidden('reflink', $reflink);
$out .= $this->html->form_hidden('id', $id);
$out .= $this->html->form_hidden('reference', $reference);
$out .= $this->html->form_hidden('refid', $refid);

$out .= $this->html->form_text('name', $res[name], 'Name', '', 0, 'span12');
$out .= $this->html->form_text('alias', $res[alias], 'Alias', '', 0, 'span12');
$out .= $this->html->form_textarea('descr', $res[descr], 'Descr', '', 0, '', 'span12');
$out .= $this->html->form_textarea('addinfo', $res[addinfo], 'Addinfo', '', 0, '', 'span12');

$out .= $this->html->form_confirmations();
$out .= $this->html->form_submit('Save');
$out .= $this->html->form_end();

$body .= $out;
