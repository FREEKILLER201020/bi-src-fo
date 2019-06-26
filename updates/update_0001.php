<?php
if (!$access['main_admin']) { //Show error if not admin
    $this->html->error('');
}

$test_new=$this->data->get_val('users', 'id', 3)*1;
if ($test_new==3) {
    echo $this->html->message("System is already initialized","");
    exit;
}




// Add initial access items
$this->livestatus('Add access');
echo "<br><br><br><br>";
$access_arr=[

    'main_delete',
    'edit_webcam',
    'edit_dropzone',
    'edit_documents_save',
    'view_countries',
    'report_favorites',
    'edit_shoppingcart',
    'edit_processdata',

    'report_myprofile',
    'edit_myprofile',
    'edit_save_file',
    'act_run',

    'edit_signups',
    'edit_signup',

    'report_special',
    'report_my_sfuff',

    'edit_special',
    'edit_send_message',



    'view_partners',
    'edit_partners',

    'view_projects',
    'edit_projects',

    'view_tasks',
    'edit_tasks',

    'edit_message_history',

    'report_test',
    'report_active_users',
    'report_test_imports',
    'view_file_content',

    'edit_send_announcement',
    'report_recent_imports',
    'report_account_transactions_pdf',
    'view_schedules',
    'view_comments',
    'edit_schedules',
    'edit_comments',
    'report_posts',
    'edit_posts',
    'view_rates',
    'edit_rates',
];

foreach ($access_arr as $access_item) {
    $_POST[item]=$access_item;
    $this->tools('addaccessitems');
    if ($access_item!='main_delete') {
        $accid=$this->db->getval("SELECT id from accessitems where name='$access_item' order by id asc limit 1");
        $sql="UPDATE accesslevel set access='1' where groupid=3 and accessid=$accid";
        //echo "$sql<br>";
        if ($accid>0) {
            $cur= $this->db->GetVal($sql);
        }
    }
    echo "<br>";
}

// Add new users
$new_users[]=['username'=>'api'];
$new_users[]=['username'=>'alex'];


$new_users[]=[
    'username'=>'lukyanov',
    'firstname'=>'Andrey',
    'surname'=>'Lukyanov',
    'email'=>'Lukyanov@suekag.com'
];
$new_users[]=[
    'username'=>'kirill',
    'firstname'=>'Kirill',
    'surname'=>'Burchenko',
    'email'=>'Sova@suekag.com'
];


foreach ($new_users as $vals) {
    unset($user_vals);
    $user_vals=[
        'username'=>$vals[username],
        'email'=>$vals[email],
        'firstname'=>$vals[firstname],
        'surname'=>$vals[surname],
        'regdate'=>$GLOBALS[today],
        'active'=>1,
    ];
    $user_id=$this->db->insert_db('users', $user_vals);
    $this->db->getval("INSERT into user_group (groupid,userid) VALUES (3,$user_id)");
}
$this->db->getval("update users set
password='2b4c7c52a61c494ce05cbef3589eabca',
password_hash='sha256:1000:7LmfqDi7Hdtc3tc6ETYU66CKJN+zkM3u:aGCemxu31vR1PMaR1nBKlI4Q3EHyZnvR',
token_hash='47c4-742d7269' where id>2; --Pass1234");


//To soft reset:
//delete from signups;delete from users where id>3;

// Add API to use api
$vals=[
    'key' => 'ea363b-74638b-62008b-3d565f-81f147',
    'date' => '17.03.2019',
    'exp_date' => '17.03.2020',
    'active' => 't',
    'functions' => 'scan_dir',
    'user_id' => 3
];
$id=$this->db->insert_db('apis', $vals);


//Add initial menue
$this->livestatus('Add Menu');
//========29.07.2017
//$this->data->add_menu('Documents',['All'=>'?act=show&what=documents']);
$this->data->add_menu('Data', ['Business'=>'#']);
$this->data->add_menu('Data', ['Info'=>'#']);
$this->data->add_menu('Tools', ['Search'=>'#']);

$this->data->add_menu('Business', ['Partners'=>'?act=show&what=partners']);
$this->data->add_menu('Business', ['Projects'=>'?act=show&what=projects']);
$this->data->add_menu('Business', ['Tasks'=>'?act=show&what=tasks']);


$this->data->add_menu('Info', ['Lists'=>'?act=show&what=lists']);

$this->data->add_menu('Search', ['partners'=>'?act=search&what=partners']);
$this->data->add_menu('Search', ['Projects'=>'?act=search&what=projects']);
$this->data->add_menu('Search', ['Tasks'=>'?act=search&what=tasks']);

$this->data->add_menu('Reports', ['Books'=>'?act=report&what=books']);

$this->data->add_menu('New', ['Partner'=>'?act=add&what=partners']);
$this->data->add_menu('New', ['Partner Import'=>'?act=add&what=import_partner']);
$this->data->add_menu('New', ['Projects'=>'?act=add&what=projects']);


$this->livestatus('Copy Data from CSV');
$csv_dir=APP_DIR.DS.'data';

$csv_file=$csv_dir.DS."lists.csv";
if(file_exists($csv_file)){
    $file_content=file_get_contents($csv_file);
    echo $this->html->pre_display($file_content,"lists.csv");

    $sql="COPY lists(id,name,alias)
    FROM '$csv_file' DELIMITER ';' CSV HEADER;";
    echo $this->html->pre_display($sql, "SQL:lists");
    $this->db->getval($sql);
}else{
    $this->html->error("No file $csv_file");
}

$csv_file=$csv_dir.DS."listitems.csv";
if(file_exists($csv_file)){
    $sql="COPY listitems(id,name,alias,list_id,qty,default_value,addinfo,text1,num1)
    FROM '$csv_file' DELIMITER ';' CSV HEADER;";
    echo $this->html->pre_display($sql, "SQL:listitems");
    $this->db->getval($sql);
}else{
    $this->html->error("No file $csv_file");
}

$csv_file=$csv_dir.DS."listitems_ext2.csv";
if(file_exists($csv_file)){
    $sql="COPY listitems(id,name,alias,list_id,qty,default_value,values,descr,addinfo,text1,text2,num1,num2)
    FROM '$csv_file' DELIMITER ',' CSV HEADER;";
    echo $this->html->pre_display($sql, "SQL:listitems");
    $this->db->getval($sql);
}else{
    $this->html->error("No file $csv_file");
}

// SET DateStyle = 'German';
// select date, currency, rate from rates where date>='01.01.2018' and currency<610 order by date, currency;

$csv_file=$csv_dir.DS."rates.csv";
if(file_exists($csv_file)){
    $sql="COPY rates(date,currency,rate)
    FROM '$csv_file' DELIMITER ',' CSV HEADER;";
    echo $this->html->pre_display($sql, "SQL:rates");
    $this->db->getval($sql);
}else{
    $this->html->error("No file $csv_file");
}

$this->livestatus('Add partners');
$vals_array[]=[
    'name' => 'SUEK AG',
    'surname' => '',
    'type_id' => 201,
    'active' => 1,
    'physical' => 0,
    'email' => 'test@gmail.com',
    'address' => "Wassergasse 7\n9000 St. Gallen",
    'country_id' => 10063,
    'mobile' => '+35799350000',
    'descr' => 'Test1'
];
$vals_array[]=[
    'name' => 'Alexander',
    'surname' => 'Titov',
    'type_id' => 202,
    'active' => 1,
    'physical' => 1,
    'email' => 'test@gmail.com',
    'address' => 'Maximos Plaza, Limassol',
    'country_id' => 10018,
    'mobile' => '+35799350000',
    'descr' => 'Test1'
];

$vals_array[]=[
    'name' => 'Kirill',
    'surname' => 'Burchenko',
    'type_id' => 202,
    'active' => 1,
    'physical' => 1,
    'email' => 'test@gmail.com',
    'address' => 'Maximos Plaza, Limassol',
    'country_id' => 10018,
    'mobile' => '+35799350000',
    'descr' => 'Test1'
];

$vals_array[]=[
    'name' => 'Mikhail',
    'surname' => 'Burchenko',
    'type_id' => 202,
    'active' => 1,
    'physical' => 1,
    'email' => 'test@gmail.com',
    'address' => 'Maximos Plaza, Limassol',
    'country_id' => 10018,
    'mobile' => '+35799350000',
    'descr' => 'Test1'
];


$vals_array[]=[
    'name' => 'Generic Bank',
    'surname' => '',
    'type_id' => 204,
    'active' => 1,
    'physical' => 0,
    'email' => 'test@gmail.com',
    'address' => 'St',
    'country_id' => 10063,
    'mobile' => '+35799350000',
    'descr' => 'Test1'
];

foreach ($vals_array as $vals) {
    echo "Insert partners $vals[name]<br>";
    $id=$this->db->insert_db('partners', $vals);
}


// clean up var:
unset($vals_array);

//copy access
$_POST[group_id_from]=2;
$_POST[group_id_tos]='3';
// $_POST[group_id_tos]='3,4,7';
//$this->tools('copy_access');

$this->livestatus('DONE');
$this->data->writeconfig('update_version', $update_version);
$update_version++;

$update_version_fm=sprintf('%04d', $update_version);
$update_file=APP_DIR.DS.'updates'.DS."update_".$update_version_fm. '.php';
if (file_exists($update_file)) {
    require $update_file;
}else{
    echo $this->html->message("Up to date","");
}