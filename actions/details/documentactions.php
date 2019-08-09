<?php
if ($what == 'documentactions'){
		$res=$this->db->GetRow("select * from $what where id=$id");
		$typename=$this->db->GetVal("select name from listitems where id=$res[type]");
		$docname=$this->db->GetVal("select name from documents where id=$res[docid]");
		$out.="<div class='title2'>$res[name] document No. $docname </div>\n";
		$out.= "Dated: <b>$res[date]</b><br>";
		$out.= "Expiration: <b>$res[expdate]</b><br>";
		$out.= "Controller: <b>$res[controller]</b><br>";
		$out.= "Comments:<pre>$res[descr]</pre><br>";

		$_POST[tablename]=$what;
		$_POST[refid]=$id;
		$_POST[reffinfo]="&tablename=$what&refid=$id";
		$out.=$this->show('comments');
		//$out.=$this->show('uploads');
	}
$body.=$out;
