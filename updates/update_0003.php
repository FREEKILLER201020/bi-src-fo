<?php
if (!$access['main_admin']) {
	//Show error if not admin
	$this->html->error('');
}
echo $this->html->message("Running update $update_version", "");

$this->livestatus('DONE');
$this->data->writeconfig('update_version', $update_version);

//////===============================================\\\\\\\\

// Create Table for Documents
$sql="CREATE TABLE documents
(
  id serial NOT NULL,
  name text,
  date date,
  datefrom date,
  dateto date,
  datecheck date,
  type integer DEFAULT 0,
  creator integer DEFAULT 0,
  initiator integer DEFAULT 0,
  executor integer DEFAULT 0,
  active boolean DEFAULT true,
  complete boolean DEFAULT false,
  qty integer DEFAULT 0,
  priority integer DEFAULT 0,
  confidentlevel integer DEFAULT 0,
  descr text,
  addinfo text,
  amount double precision DEFAULT 0,
  accountid integer DEFAULT 0,
  transactionid integer DEFAULT 0,
  contractid integer DEFAULT 0,
  uploads integer DEFAULT 0,
  currency integer DEFAULT 0,
  parentid integer DEFAULT 0,
  docgroup integer DEFAULT 1500,
  vacationid integer DEFAULT 0,
  block_download boolean DEFAULT false,
  autodelete boolean DEFAULT false,
  have_partners boolean DEFAULT false,
  amount_vatable double precision DEFAULT 0,
  amount_vat double precision DEFAULT 0,
  CONSTRAINT documents_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=TRUE
);
ALTER TABLE documents
  OWNER TO postgres;

-- Index: documents_type

-- DROP INDEX documents_type;

CREATE INDEX documents_type
  ON documents
  USING btree
  (type);

-- Table: documentactions

-- DROP TABLE documentactions;

CREATE TABLE documentactions
(
  id serial NOT NULL,
  name text,
  date date,
  type integer DEFAULT 0,
  creator integer DEFAULT 0,
  executor integer DEFAULT 0,
  docid integer,
  active boolean DEFAULT true,
  complete boolean DEFAULT false,
  qty double precision DEFAULT 0,
  descr text,
  CONSTRAINT documentactions_pkey PRIMARY KEY (id),
  CONSTRAINT fk_documents_action FOREIGN KEY (docid)
      REFERENCES documents (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=TRUE
);
ALTER TABLE documentactions
  OWNER TO postgres;

-- Index: documentactions_docid

-- DROP INDEX documentactions_docid;

CREATE INDEX documentactions_docid
  ON documentactions
  USING btree
  (docid);

-- Table: docs2obj

-- DROP TABLE docs2obj;

CREATE TABLE docs2obj
(
  id serial NOT NULL,
  doc_id integer NOT NULL DEFAULT 0,
  ref_id integer NOT NULL DEFAULT 0,
  ref_table text DEFAULT ''::text,
  type_id integer DEFAULT 0,
  CONSTRAINT p_docs2obj_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE docs2obj
  OWNER TO postgres;

-- DROP TABLE docs2groups;

CREATE TABLE docs2groups
(
  docid integer DEFAULT 0,
  groupid integer DEFAULT 0,
  CONSTRAINT fk_d2g_documents FOREIGN KEY (docid)
      REFERENCES documents (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_d2g_groups FOREIGN KEY (groupid)
      REFERENCES groups (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=TRUE
);
ALTER TABLE docs2groups
  OWNER TO postgres;

-- DROP TABLE partner2obj;

CREATE TABLE partner2obj
(
  id serial NOT NULL,
  partner_id integer NOT NULL DEFAULT 0,
  ref_id integer NOT NULL DEFAULT 0,
  ref_table text DEFAULT ''::text,
  type_id integer DEFAULT 0,
  CONSTRAINT p_partner2obj_pkey PRIMARY KEY (id),
  CONSTRAINT fk_partner2obj_partners FOREIGN KEY (partner_id)
      REFERENCES partners (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE partner2obj
  OWNER TO postgres;";
$this->db->getVal($sql);


$access_arr = [
	'view_documents',
	'edit_documents',
	'view_docs2obj',
	'edit_docs2obj',
	'view_documentactions',
	'edit_documentactions'
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
