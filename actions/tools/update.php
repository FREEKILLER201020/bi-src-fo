<?php
if (!$access['main_admin']) { //Show error if not admin
    $this->html->error('');
}
//$this->data->writeconfig('update_version', 0);
$update_version=$this->data->readconfig('update_version')*1;
if($update_version==0){
    $this->data->writeconfig('update_version', 1);
    echo $this->html->message("Initial","");
}
$update_version++;
//echo $this->html->message("Running update $update_version","");
$update_version_fm=sprintf('%04d', $update_version);
$update_file=APP_DIR.DS.'updates'.DS."update_".$update_version_fm. '.php';
if (file_exists($update_file)) {
    require $update_file;
}else{
    echo $this->html->message("Up to date","");
    //echo $this->html->message("No $update_file found","Warning");
}