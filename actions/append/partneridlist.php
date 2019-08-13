<?php
if ($what == 'partneridlist'){
	// if ($GLOBALS[allowed_pids]!='') {
	// 	if ($GLOBALS[allowed_related_pids]!='') {
	// 		$sql = "$sql and  id in ($GLOBALS[allowed_pids],$GLOBALS[allowed_related_pids])";
	// 	}else{
	//     	$sql = "$sql and  id in ($GLOBALS[allowed_pids])";
	// 	}
	// }


	$sql="SELECT id, name FROM partners WHERE lower(name) like lower('%$value%') $sql ORDER by name";
	$response=$this->html->htlist('partner',$sql,$id,'Select Partner',"onchange='itemid=this.options[this.selectedIndex].value;
	itemname=this.options[this.selectedIndex].text;
	document.getElementById(\"partnerslist\").innerHTML+=itemid+\", \";
	document.getElementById(\"partnersnamelist\").innerHTML+=itemname+\", \";'");
	$out.= "$response";
}

$body.=$out;
