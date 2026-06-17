<?php
include_once("bg.php");
include_once("config.php");
include_once("security.php");
ob_start();
ensure_session_started();
require_role(array('ADMINISTRATOR'), 'deladmin.php');
require_master_verification('deladmin.php');
?>
<?php
if(isset($_GET['deleteconfirm']))
{
	$admno = trim($_GET['admno']);

	mysqli_begin_transaction(db_conn());
	$allOk = true;

	$allOk = $allOk && db_prepared_execute("DELETE FROM stud_id WHERE adm_no = ?", "s", array($admno));
	$allOk = $allOk && db_prepared_execute("DELETE FROM stud_adm WHERE adm_no = ?", "s", array($admno));
	$allOk = $allOk && db_prepared_execute("DELETE FROM extra1 WHERE adm_no = ?", "s", array($admno));
	$allOk = $allOk && db_prepared_execute("DELETE FROM extra2 WHERE adm_no = ?", "s", array($admno));
	$allOk = $allOk && db_prepared_execute("DELETE FROM extra3 WHERE adm_no = ?", "s", array($admno));

	if ($allOk) {
		mysqli_commit(db_conn());
		echo "</br></br></br></br></br></br></br></br>";
		echo "<center><h3>"."Deleted Successfully"."</h3></center>";
		echo '<center><input type="button" style="background-color: #365884;color: white;padding: 14px 20px;margin: 4px 0;border: 2px solid #365884;cursor: pointer;width: 15%;font-size: 14px;border-radius:4px;" value="Goto Home" onclick="window.location =\'dashboard.php\'" />';
	} else {
		mysqli_rollback(db_conn());
		echo "<center><h3>"."Failed to Delete/ Adminssion Number not Exists"."</h3></center>";
		echo '<p><center><input type="button" style="background-color: #365884;color: white;padding: 14px 20px;margin: 4px 0;border: 2px solid #365884;cursor: pointer;width: 15%;font-size: 14px;border-radius:4px;" value="Retry" onclick="window.location =\'enroll_num_chang.php\'" /></p>';
		echo '<p><center><input type="button" style="background-color: #365884;color: white;padding: 14px 20px;margin: 4px 0;border: 2px solid #365884;cursor: pointer;width: 15%;font-size: 14px;border-radius:4px;" value="Goto Home" onclick="window.location =\'dashboard.php\'" /></p>';
	}
}
else
{
echo "</br></br></br></br></br></br></br></br>";
echo "<center><h3>"."Unauthorized Entry"."</h3></center>";
echo '<p><center><input type="button" style="background-color: #365884;color: white;padding: 14px 20px;margin: 4px 0;border: 2px solid #365884;cursor: pointer;width: 20%;font-size: 14px;border-radius:4px;" value="Goto Main Page" onclick="window.location =\'dashboard.php\'" /></p>';
}
ob_end_flush();

?>
<?php
unset($_SESSION['master_verified']);
?>