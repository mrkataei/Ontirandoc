<?php 
/*
 نمایش و مدیریت لیست درخواستهای ارسال شده توسط کاربر
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-31
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTasks.class.php");
include_once("classes/ProjectTasksSecurity.class.php");
HTMLBegin();
$NumberOfRec = 20;
 $k=0;
$PageNumber = 0;
if(isset($_REQUEST["PageNumber"]))
{
	$FromRec = $_REQUEST["PageNumber"]*$NumberOfRec;
	$PageNumber = $_REQUEST["PageNumber"];
}
else
{
	$FromRec = 0; 
}
if(isset($_REQUEST["SearchAction"])) 
{
	$OrderByFieldName = "CreateDate";
	$OrderType = "DESC";
	if(isset($_REQUEST["OrderByFieldName"]))
	{
		$OrderByFieldName = $_REQUEST["OrderByFieldName"];
		$OrderType = $_REQUEST["OrderType"];
	}
	$ProjectID=htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8');
} 
else
{ 
	$OrderByFieldName = "CreateDate";
	$OrderType = "DESC";
	$ProjectID='';
}

$res = manage_ProjectTasks::GetUserRequestedTasks($ProjectID, $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->ProjectTaskID]) && $res[$k]->CanRemoveByCaller) 
	{
		manage_ProjectTasks::Remove($res[$k]->ProjectTaskID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_ProjectTasks::GetUserRequestedTasks($ProjectID, $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 
?>
<form id="SearchForm" name="SearchForm" method=post> 
<input type="hidden" name="PageNumber" id="PageNumber" value="0">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
<input type="hidden" name="SearchAction" id="SearchAction" value="1"> 
<br>
<table width="90%" align="center" border="1" cellspacing="0">
<tr id='SearchTr'>
<td>
<table width="100%" align="center" border="0" cellspacing="0">
<tr>
	<td width="1%" nowrap>
 پروژه مربوطه
	</td>
	<td nowrap>
	<select name="Item_ProjectID" id="Item_ProjectID" onchange='javascript: document.SearchForm.submit();'>
	<option value=0>-
	<? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.projects", "ProjectID", "title", "title"); ?>	</select> 
	</td>
</tr>
</table>
</td>
</tr>
</table>
<? 
if(isset($_REQUEST["SearchAction"])) 
{ 
?>
<script>
		document.SearchForm.Item_ProjectID.value='<? echo htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8'); ?>';
</script>
<?
}
?>
<table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="9">
	لیست درخواستهای ارسال شما از سوی شما
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td width=1%><a href="javascript: Sort('ProjectID', 'ASC');">پروژه مربوطه</a></td>
	<td><a href="javascript: Sort('title', 'ASC');">عنوان</a></td>
	<td width=1% nowrap><a href="javascript: Sort('TaskStatus', 'ASC');">وضعیت</a></td>	
	<td nowrap width=1%><a href="javascript: Sort('CreatorDate', 'ASC');">زمان ایجاد</a></td>
	<td nowrap width=1%>
	سایر مشخصات
	</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	if($res[$k]->CanRemoveByCaller)
		echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectTaskID."\">";
	else
		echo "&nbsp;";
	echo "</td>";
	echo "<td>".($k+$FromRec+1)."</td>";
	echo "	<td>";
	echo "<a target=\"_blank\" href=\"NewProjectTasks.php?UpdateID=".$res[$k]->ProjectTaskID."\">";
	echo "<img src='images/edit.gif' title='ویرایش'>";
	echo "</a></td>";
	echo "	<td nowrap>&nbsp;".$res[$k]->ProjectID_Desc."</td>";
	echo "	<td>".htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td nowrap>&nbsp;".$res[$k]->TaskStatus_Desc."</td>";
	echo "	<td nowrap>".$res[$k]->CreateDate_Shamsi."</td>";
	echo "<td nowrap>";
	echo "<a target=\"_blank\" href='ManageProjectTaskAssignedUsers.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
	echo "<img src='images/members.gif' border='0' title='کاربران منتسب به کار'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectTaskActivities.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
	echo "<img src='images/activity.gif' border='0' title='اقدامات'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectTaskComments.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
	echo "<img src='images/comment.gif' border='0' title='یادداشتها'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectTaskDocuments.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
	echo "<img src='images/document.gif' border='0' title='اسناد کارها'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectTaskRequisites.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
	echo "<img src='images/chain.gif' border='0' title='پیشنیازها'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectTaskHistory.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
	echo "<img src='images/history.gif' border='0' title='تاریخچه'>";
	echo "</a>  ";
	echo "</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="9" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
<tr bgcolor="#cccccc"><td colspan="9" align="right">
<?
for($k=0; $k<manage_ProjectTasks::GetUserRequestedTasksCount($ProjectID)/$NumberOfRec; $k++)
{
	if($PageNumber!=$k)
		echo "<a href='javascript: ShowPage(".($k).")'>";
	echo ($k+1);
	if($PageNumber!=$k)
		echo "</a>";
	echo " ";
}
?>
</td></tr>
</table>
</form>
<form target="_blank" method="post" action="NewProjectTasks.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.SearchForm.submit();
}
function ShowPage(PageNumber)
{
	SearchForm.PageNumber.value=PageNumber; 
	SearchForm.submit();
}
function Sort(OrderByFieldName, OrderType)
{
	SearchForm.OrderByFieldName.value=OrderByFieldName; 
	SearchForm.OrderType.value=OrderType; 
	SearchForm.submit();
}
</script>
</html>
