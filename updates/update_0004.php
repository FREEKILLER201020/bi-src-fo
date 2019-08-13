<?php
if (!$access['main_admin']) {
	//Show error if not admin
	$this->html->error('');
}
echo $this->html->message("Running update $update_version", "");

$this->livestatus('DONE');
$this->data->writeconfig('update_version', $update_version);

//////===============================================\\\\\\\\

// Update Partners
$sql="ALTER TABLE partners ADD COLUMN ru text default '';
ALTER TABLE partners ADD COLUMN en text default '';
ALTER TABLE partners ADD COLUMN synonyms text default '';


-- Table: users2obj

-- DROP TABLE users2obj;

CREATE TABLE users2obj
(
  id serial NOT NULL,
  user_id integer NOT NULL DEFAULT 0,
  ref_id integer NOT NULL DEFAULT 0,
  ref_table text DEFAULT ''::text,
  type_id integer DEFAULT 0,
  CONSTRAINT p_users2obj_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE users2obj
  OWNER TO postgres;

";
$this->db->getVal($sql);
$access_arr = [
  'view_partner2obj',
  'edit_partner2obj',
  'view_users2obj',
  'edit_users2obj'
];

foreach ($access_arr as $access_item) {
  $_POST[item] = $access_item;
  $this->tools('addaccessitems');
  if ($access_item != 'main_delete') {
    $accid = $this->db->getval("SELECT id from accessitems where name='$access_item' order by id asc limit 1");
    $sql = "UPDATE accesslevel set access='1' where groupid=3 and accessid=$accid";
    //echo "$sql<br>";
    if ($accid > 0) {
      $cur = $this->db->GetVal($sql);
    }
  }
  echo "<br>";
}
//////===============================================\\\\\\\\

$update_version++;

$update_version_fm = sprintf('%04d', $update_version);
$update_file = APP_DIR . DS . 'updates' . DS . "update_" . $update_version_fm . '.php';
if (file_exists($update_file)) {
	require $update_file;
} else {
	echo $this->html->message("Up to date", "");
}
