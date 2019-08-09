<?php
if ($what == 'doctypelist3') {
    if ($value==0) {
        $response="No group selected";
    } else {
        if ($value!=1500) {
            $ids=$this->db->GetVal("select s.values from listitems s where s.id=$value");
            $sql="select t.id, t.name FROM listitems t WHERE t.list_id=16 and t.id in ($ids) ORDER by t.name";
        } else {
            $ids=substr($this->utils->F_tostring($this->db->GetResults("select values from listitems where list_id=15 and id!=1500 and values!=''")), 0, -1);
            $sql="select t.id, t.name FROM listitems t WHERE t.list_id=16 and t.id not in ($ids) ORDER by t.name";
        }
        
            $select="";
            $response=$this->html->htlist('type', $sql, $id, $select, "onchange='typeid=this.options[this.selectedIndex].value; typename=this.options[this.selectedIndex].text+\" \";'");
    }
    $out.= "$response";
}

if ($value==0) {
    $response="<span class='badge red'>No group selected<span>";
} else {
    $sql="select t.id, t.name FROM listitems t WHERE t.list_id=16 and t.num1=$value ORDER by t.name";
    $response=$this->html->htlist('type', $sql, $id, $select, "onchange='typeid=this.options[this.selectedIndex].value; typename=this.options[this.selectedIndex].text+\" \";'");
}
if ($value==1509) {
    $response="<span class='badge red'>Deactivated</span>";
}
$out.= "$response";
$body.=$out;
