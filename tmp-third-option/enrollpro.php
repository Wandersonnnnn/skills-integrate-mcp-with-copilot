<?php

include_once("config.php");
include_once("bg.php");
include_once("security.php");
ob_start();
ensure_session_started();
require_role(array('ADMINISTRATOR', 'FACULTY', 'STUDENT'));

if(isset($_POST['enroll']))
{
   $admno = trim($_POST['admno']);
   $name = trim($_POST['name']);
   $actnam = trim($_POST['act_nam']);

   mysqli_begin_transaction(db_conn());

   $allOk = db_prepared_execute("INSERT INTO stud_id(adm_no) VALUES(?)", "s", array($admno));
   $allOk = $allOk && db_prepared_execute(
      "INSERT INTO stud_adm(adm_no,act_nam,name) VALUES(?,?,?)",
      "sss",
      array($admno, $actnam, $name)
   );
   $allOk = $allOk && db_prepared_execute(
      "UPDATE last_entry SET adm_no = ?, name = ? WHERE id = 1",
      "ss",
      array($admno, $name)
   );
   $allOk = $allOk && db_prepared_execute("INSERT INTO extra1(adm_no,name) VALUES(?,?)", "ss", array($admno, $name));
   $allOk = $allOk && db_prepared_execute("INSERT INTO extra2(adm_no,name) VALUES(?,?)", "ss", array($admno, $name));
   $allOk = $allOk && db_prepared_execute("INSERT INTO extra3(adm_no,name) VALUES(?,?)", "ss", array($admno, $name));
   $allOk = $allOk && db_prepared_execute("INSERT INTO extra_cirr(adm_no,act_nam) VALUES(?,?)", "ss", array($admno, $actnam));

   if($allOk)
   {
      mysqli_commit(db_conn());
      echo "</br></br></br></br></br></br></br></br>";
      echo "<center><h3>"."Enrolled Successfully"."</h3></center>";
      echo '<center><input type="button" style="background-color: #365884;color: white;padding: 12px 18px;margin: 6px 0; border: none;cursor: pointer;width: 20%;font-size: 14px;border-radius:4px;" value="Go Home" onclick="window.location =\'dashboard.php\'" />';
   }
   else 
   {
      mysqli_rollback(db_conn());
      echo "</br></br></br></br></br></br></br></br>";
      echo "<center><h3>"."Admission Number already Exists/ Invalid details"."</h3></center>";
      echo '<p><center><input type="button" style="background-color: #365884;color: white;padding: 12px 18px;margin: 6px 0; border: none;cursor: pointer;width: 20%;font-size: 14px;border-radius:4px;" value="Retry" onclick="window.location =\'enroll.php\'" /></p>';
      echo '<p><center><input type="button" style="width:100%;background-color: #365884;color: white;padding: 12px 18px;margin: 6px 0; border: none;cursor: pointer;width: 20%;font-size: 14px;border-radius:4px;" value="Goto Home" onclick="window.location =\'dashboard.php\'" /></p>';
   }
}
else
{
echo "</br></br></br></br></br></br></br></br>";
echo "<center><h3>"."Unauthorized Entry"."</h3></center>";
echo '<p><center><input type="button" style="background-color: #365884;color: white;padding: 12px 18px;margin: 6px 0; border: none;cursor: pointer;width: 20%;font-size: 14px;border-radius:4px;width:100%;" value="Goto Main Page" onclick="window.location =\'dashboard.php\'" /></p>';

}
ob_end_flush();

?>