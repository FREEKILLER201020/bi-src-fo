<?php
if (!$access['main_admin']) { //Show error if not admin
    $this->html->error('');
}
echo $this->html->message("Running update $update_version","");

$this->livestatus('DONE');
$this->data->writeconfig('update_version', $update_version);

//////===============================================\\\\\\\\
$list_id=7;
$sql="SELECT \"add_new_list\" ($list_id, 'Project Stages','stage',array['Planned','In progress','Completed']);";
$this->db->getVal($sql);


//////===============================================\\\\\\\\

$update_version++;

$update_version_fm=sprintf('%04d', $update_version);
$update_file=APP_DIR.DS.'updates'.DS."update_".$update_version_fm. '.php';
if (file_exists($update_file)) {
    require $update_file;
}else{
    echo $this->html->message("Up to date","");
}


