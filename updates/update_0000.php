<?php
if (!$access['main_admin']) { //Show error if not admin
    $this->html->error('');
}
echo $this->html->message("Running update $update_version","");

$this->livestatus('DONE');
$this->data->writeconfig('update_version', $update_version);

//////===============================================\\\\\\\\




//////===============================================\\\\\\\\

$update_version++;

$update_version_fm=sprintf('%04d', $update_version);
$update_file=APP_DIR.DS.'updates'.DS."update_".$update_version_fm. '.php';
if (file_exists($update_file)) {
    require $update_file;
}else{
    echo $this->html->message("Up to date","");
}