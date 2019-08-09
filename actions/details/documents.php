<?php
if ($what == 'documents') {
        $this->project->update_document($id);
        $res=$this->db->GetRow("select * from $what where id=$id $sql");
        $res[id]=$res[id]*1;
        $typename=$this->db->GetVal("select name from listitems where id=$res[type]");

                $pdocname=$this->utils->F_tostring($this->db->GetResults("select '<a href=\"?act=details&what=documents&id='||id||'\">'||name||'</a>' from documents where id=$res[parentid]"));
        $cdosc=$this->utils->F_tostring($this->db->GetResults("select '<a href=\"?act=details&what=documents&id='||id||'\">'||name||'</a>' from documents where parentid=$id"));

        $executor=$this->db->GetVal("select username from users where id=$res[executor]");
        $user=$this->db->GetVal("select username from users where id=$res[creator]");

        
        
        //$tmp="?csrf=$GLOBALS[csrf]&act=delete&table=docs2transactions&transactionid='||t.id||'&docid='||d.docid||'";

        
    if ($this->data->table_exists('projects')) {
        $prname=$this->data->rev_docs2obj($id, 'projects');
    }
        $tasks=$this->data->rev_docs2obj($id, 'tasks');
    if ($this->data->table_exists('clientrequests')) {
        $qname=$this->data->rev_docs2obj($id, 'clientrequests');
    }
        //$loans=$this->data->rev_docs2obj($id, 'loans');
    if ($this->data->table_exists('addendums')) {
        $addname=$this->data->rev_docs2obj($id, 'addendums');
    }
        //$tname=$this->data->rev_docs2obj($id, 'transactions');
        $pdname=$this->data->rev_docs2obj($id, 'documents');
        $pnames=$this->data->rev_docs2obj($id, 'partners');
        
        $cdname=$this->data->docs2obj($id, $what);
        
        $status=(($res[complete]=='t')||($res[complete]==''))?'Complete':'Incomplete';
        
        $link="Not linked text:[url=?act=show&table=partners]text[/url] after link";
        $res[addinfo]=$this->utils->linkalize($res[addinfo]);
        
        $out.="<h1>$res[name] ($typename)</h1>\n";
        $out.=$this->data->details_bar($what, $id);
        $out.= "<div class='level2'>";
        $out.= "<table class='table table-morecondensed table-notfull'>";
        $out.= "<tr><td align=right valign=top>Created: $accessn</td><td><b>$res[date]</b></td></tr>";
        $out.= "<tr><td align=right valign=top>Created by:</td><td><b>$user </b></td></tr>";
        $out.= "<tr><td align=right valign=top>Document Dated:</td><td><b>$res[datefrom] </b></td></tr>";
        $out.= "<tr><td align=right valign=top>Expiration:</td><td><b>$res[dateto]</b></td></tr>";
        $out.= "<tr><td align=right valign=top>Check date:</td><td><b>$res[datecheck] </b></td></tr>";
        $out.= "<tr><td align=right valign=top>Executor:</td><td><b>$executor </b></td></tr>";
        $out.= "<tr><td align=right valign=top>Status:</td><td><b>$status </b></td></tr>";

        

        //if($this->data->table_exists('clientrequests'))$out.= "<tr><td align=right valign=top>Link to Client Request:</td><td><b>$qname </b></td></tr>";
        //$out.= "<tr><td align=right valign=top>Link to Loan:</td><td><b>$loans </b></td></tr>";
        //if($this->data->table_exists('addendums'))$out.= "<tr><td align=right valign=top>Link to Addendums:</td><td><b>$addname </b></td></tr>";
        $out.= "<tr><td align=right valign=top>Link to tasks:</td><td><b>$tasks </b></td></tr>";
        //if($this->data->table_exists('transactions'))$out.= "<tr><td align=right valign=top>Link to transaction:</td><td><b>$tname</b></td></tr>";
        if(($this->data->table_exists('projects')&&($access[view_projects])))$out.= "<tr><td align=right valign=top>Link to projects:</td><td><b>$prname</b></td></tr>";
        //if(($this->data->table_exists('projects'))&&($this->data->table_exists('docs2projects'))&&($access[view_projects]))$out.= "<tr><td align=right valign=top>Link to projects:</td><td><b>$prname</b></td></tr>";
        $out.= "<tr><td align=right valign=top>Parent Docs:</td><td><b>$pdname </b></td></tr>";
        $out.= "<tr><td align=right valign=top>Child Docs:</td><td><b>$cdname </b></td></tr>";
        

        //$out.= "<tr><td align=right valign=top>Partners:</td><td><b>$pnames </b></td></tr>";
        $out.= "</table>";
        $out.= "</div>";

    if ($res[descr]) {
        $out.="Description:<br><pre>$res[descr]</pre>";
    }
    if ($res[addinfo]) {
        $out.="Add. info.:<br><pre>$res[addinfo]</pre>";
    }
        
        $sql = "SELECT * FROM useralerts WHERE refid=$id and tablename='$what' and userid=$uid and wasread='0'";
        $res2=$this->db->GetRow($sql);
    if ($uid==$res2[userid]) {
        $sql = "SELECT id FROM useralerts WHERE refid=$id and tablename='$what' and userid=$uid and wasread='0' and confirm='1'";
        $count=$this->db->GetVal($sql)*1;
        $dummy=$this->db->GetVal("update useralerts set readdate=now(), readtime=now(), wasread='1' where refid=$id and tablename='$what' and userid=$uid and wasread='0' and confirm='0'");
        $userfrom=$this->db->GetVal("select username from users where id=$res2[fromuserid]");
        if ($count>0) {
            $confirm="Requeres manual confirmation. Press <a onclick=\"leavecomment('?csrf=$GLOBALS[csrf]&act=save&what=sw&field=confirm&table=useralerts&id=$count')\">[here]</a> to confirm.";
        } else {
            $confirm="";
        }
        $isnotified="<font color=FF0000>This record had an alert for you set by $userfrom. $confirm</font>";
    }
        $_POST[noexport]=1;
        $_POST[title]="Parties";
        $_POST[doc_id]=$id;
        $_POST[ref_table]='partners';
        $_POST[reffinfo]="&ref_table=partners&doc_id=$id";
        $out.=$this->show('docs2obj');
        
        $_POST[title]="";
        $_POST[docid]=$id;
        $_POST[tablename]=$what;
        $_POST[refid]=$id;
        $_POST[reffinfo]="&tablename=$what&refid=$id";
        //$out.=$this->show('warnings');
        
        $_POST[title]="Document Route";
        //$out.=$this->show('documentactions');
        $_POST[parentid]=$id;
        
        $_POST[title]="Dependant Documents";
        array($tmparray);
        $tmparray=$_POST;
        $out.=$this->show('documents');
        $out.=$this->show_docs2obj($id, $what);
        
        $_POST[noadd]='';
        $_POST=$tmparray;
        unset($_POST[title]);
        $out.=$this->show('uploads');
        
        $data=array(
            'tablename'=>$what,
            'refid'=>$id,
        );
        $data_json=json_encode($data);
        //$out.=$this->html->pre_display($data_json,'$data_json');
        $out.= $this->html->dropzoneJS($data_json, "Drop file here");
    if ($GLOBALS[access][view_debug]) {
        $out.= $this->html->pre_display($data, 'data_json');
    }
        
        $out.=$this->show('comments');
        
        $_POST[ref_table]=$what;
        $_POST[ref_id]=$id;

        
        
        $_POST[reference]="$what";
        $_POST[tablename]=$what;
        $_POST[refid]=$id;
        $_POST[reffinfo]="&tablename=$what&refid=$id";
        //$out.=$this->show('approvals');
        //$out.=$this->show('contacts');
        $out.=$this->show('schedules');
        $_POST[title]="Document Access";
        $_POST[tablename]="$what";
        $out.=$this->show('tableaccess');
        //$out.=$this->report('db_changes');
}
    
$body.=$out;
