<?php
/**
 * 模型：评论管理
 * @copyright (c) Emlog All Rights Reserved
 * @version emlog-3.1.0
 * $Id: comment.php 682 2008-10-14 16:08:01Z emloog $
 */

class emComment {

	var $dbhd;

	function emComment($dbhandle)
	{
		$this->dbhd = $dbhandle;
	}

	/**
	 * 获取评论
	 *
	 * @param int $blogId
	 * @param int $page
	 * @return array $comment
	 */
	function getComments($blogId = null, $hide = null, $page = null, $uid = 1)
	{
		$andQuery = '1=1';
		$andQuery .= $blogId ? " and a.gid=$blogId" : '';
		$andQuery .= $hide ? " and a.hide='$hide'" : '';
		$condition = '';
		if($page)
		{
			$startId = ($page - 1) *15;
			$condition = "LIMIT $startId, 15";
		}
		if ($uid == 1)
		{
			$sql = "SELECT * FROM ".DB_PREFIX."comment as a where $andQuery ORDER BY a.cid DESC $condition";
		}else {
			$sql = "SELECT *,a.hide FROM ".DB_PREFIX."comment as a, ".DB_PREFIX."blog as b where $andQuery and a.gid=b.gid and b.author=$uid ORDER BY a.cid DESC $condition";
		}
		$ret = $this->dbhd->query($sql);
		$comments = array();
		while($row = $this->dbhd->fetch_array($ret))
		{
			$row['cname'] = htmlspecialchars($row['poster']);
			$row['mail'] = htmlspecialchars($row['mail']);
			$row['url'] = htmlspecialchars($row['url']);
			$row['content'] = htmlClean($row['comment']);
			$row['date'] = date("Y-m-d H:i",$row['date']);
			$row['reply'] = htmlClean($row['reply']);
			$row['hide'] = $row['hide'];
			$comments[] = $row;
		}
		return $comments;
	}
	function getOneComment($commentId)
	{
		$sql = "select * from ".DB_PREFIX."comment where cid=$commentId";
		$res = $this->dbhd->query($sql);
		$commentArray = $this->dbhd->fetch_array($res);
		$commentArray['comment'] = htmlClean(trim($commentArray['comment']));
		$commentArray['reply'] = htmlClean(trim($commentArray['reply']));
		$commentArray['poster'] = trim($commentArray['poster']);
		$commentArray['date'] = date("Y-m-d H:i",$commentArray['date']);
		return $commentArray;
	}
	/**
	 * 获取查询评论的数目
	 *
	 * @param int $blogId
	 * @return int $comNum
	 */
	function getCommentNum($blogId = null, $hide = null, $uid = 1)
	{
		$comNum = '';
		$andQuery = '1=1';
		$andQuery .= $blogId ? " and a.gid=$blogId" : '';
		$andQuery .= $hide ? " and a.hide='$hide'" : '';
		if ($uid == 1)
		{
			$sql = "SELECT a.cid FROM ".DB_PREFIX."comment as a where $andQuery";
		}else {
			$sql = "SELECT a.cid FROM ".DB_PREFIX."comment as a, ".DB_PREFIX."blog as b where $andQuery and a.gid=b.gid and b.author=$uid";
		}
		$res = $this->dbhd->query($sql);
		$comNum = $this->dbhd->num_rows($res);
		return $comNum;
	}
	/**
	 * 删除评论
	 *
	 * @param int $commentId
	 */
	function delComment($commentId)
	{
		$row = $this->dbhd->once_fetch_array("SELECT gid,hide FROM ".DB_PREFIX."comment WHERE cid=$commentId");
		$this->dbhd->query("DELETE FROM ".DB_PREFIX."comment where cid=$commentId");
		$blogId = intval($row['gid']);
		if($row['hide'] == 'n')
		{
			$this->dbhd->query("UPDATE ".DB_PREFIX."blog SET comnum=comnum-1 WHERE gid=$blogId");
		}
	}
	/**
	 * 显示/隐藏评论
	 *
	 * @param int $commentId
	 */
	function hideComment($commentId)
	{
		$row = $this->dbhd->once_fetch_array("SELECT gid,hide FROM ".DB_PREFIX."comment WHERE cid=$commentId");
		$blogId = intval($row['gid']);
		$isHide = $row['hide'];
		if($isHide == 'n')
		{
			$this->dbhd->query("UPDATE ".DB_PREFIX."blog SET comnum=comnum-1 WHERE gid=$blogId");
		}
		$this->dbhd->query("UPDATE ".DB_PREFIX."comment SET hide='y' WHERE cid=$commentId");
	}
	function showComment($commentId)
	{
		$row = $this->dbhd->once_fetch_array("SELECT gid,hide FROM ".DB_PREFIX."comment WHERE cid=$commentId");
		$blogId = intval($row['gid']);
		$isHide = $row['hide'];
		if($isHide == 'y')
		{
			$this->dbhd->query("UPDATE ".DB_PREFIX."blog SET comnum=comnum+1 WHERE gid=$blogId");
		}
		$this->dbhd->query("UPDATE ".DB_PREFIX."comment SET hide='n' WHERE cid=$commentId");
	}

	/**
	 * 回复评论
	 *
	 * @param int $commentId
	 * @param string $reply
	 */
	function replyComment($commentId, $reply)
	{
		$sql="UPDATE ".DB_PREFIX."comment SET reply='$reply' where cid=$commentId ";
		$this->dbhd->query($sql);
	}

	/**
	 * 批量处理评论
	 *
	 * @param string $action
	 * @param array $comments
	 */
	function batchComment($action, $comments)
	{
		switch ($action)
		{
			case 'delcom':
				foreach($comments as $key=>$val)
				{
					$this->delComment($key);
				}
				break;
			case 'hidecom':
				foreach($comments as $key=>$val)
				{
					$this->hideComment($key);
				}
				break;
			case 'showcom':
				foreach($comments as $key=>$val)
				{
					$this->showComment($key);
				}
				break;
		}
	}

	/**
	 * 添加评论
	 *
	 * @param unknown_type $name
	 * @param unknown_type $content
	 * @param unknown_type $mail
	 * @param unknown_type $url
	 * @param unknown_type $imgcode
	 * @param unknown_type $comment_code
	 * @param unknown_type $ischkcomment
	 * @param unknown_type $localdate
	 * @param unknown_type $blogId
	 */
	function addComment($name, $content, $mail, $url, $imgcode, $comment_code, $ischkcomment, $localdate, $blogId)
	{
		if( $comment_code == 'y' )
		{
			session_start();
		}
		if ($url && strncasecmp($url,'http://',7))//0 if they are equal
		{
			$url = 'http://'.$url;
		}
		$this->setCommentCookie($name,$mail,$url,$localdate);
		if($this->isLogCanComment($blogId) === false)
		{
			emMsg('发表评论失败：该日志已关闭评论','javascript:history.back(-1);');
		}elseif ($this->isCommentExist($blogId, $name, $content) === true){
			emMsg('发表评论失败：已存在相同内容评论','javascript:history.back(-1);');
		}elseif (preg_match("/['<>,#|;\/\$\\&\r\t()%@+?^]/",$name) || strlen($name) > 20 || strlen($name) == 0){
			emMsg('发表评论失败：姓名不符合规范','javascript:history.back(-1);');
		} elseif ($mail != '' && !checkMail($mail)) {
			emMsg('发表评论失败：邮件地址不符合规范', 'javascript:history.back(-1);');
		} elseif (strlen($content) == '' || strlen($content) > 2000) {
			emMsg('发表评论失败：内容不符合规范','javascript:history.back(-1);');
		} elseif ($imgcode == '' && $comment_code == 'y') {
			emMsg('发表评论失败：验证码不能为空','javascript:history.back(-1);');
		} elseif ($comment_code == 'y' && $imgcode != $_SESSION['code']) {
			emMsg('发表评论失败：验证码错误','javascript:history.back(-1);');
		} else {
			$sql = "INSERT INTO ".DB_PREFIX."comment (date,poster,gid,comment,reply,mail,url,hide) VALUES ('$localdate','$name','$blogId','$content','','$mail','$url','$ischkcomment')";
			$ret = $this->dbhd->query($sql);
			if ($ischkcomment == 'n')
			{
				$this->dbhd->query("UPDATE ".DB_PREFIX."blog SET comnum = comnum + 1 WHERE gid='$blogId'");
				return 0;
			} else {
				return 1;
			}
		}
	}

	function isCommentExist($blogId, $name, $content)
	{
		$query = $this->dbhd->query("SELECT cid FROM ".DB_PREFIX."comment WHERE gid=$blogId AND poster='$name' AND comment='$content'");
		$result = $this->dbhd->num_rows($query);
		if ($result > 0)
		{
			return true;
		}else {
			return false;
		}
	}

	function isLogCanComment($blogId)
	{
		$query = $this->dbhd->query("SELECT allow_remark FROM ".DB_PREFIX."blog WHERE gid=$blogId");
		$show_remark = $this->dbhd->fetch_array($query);
		if ($show_remark['allow_remark'] == 'n')
		{
			return false;
		}else {
			return true;
		}
	}

	function setCommentCookie($name,$mail,$url,$localdate)
	{
		$cookietime = $localdate + 31536000;
		setcookie('commentposter',$name,$cookietime);
		setcookie('postermail',$mail,$cookietime);
		setcookie('posterurl',$url,$cookietime);
	}

}

?>
