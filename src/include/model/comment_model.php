<?php
/**
 * 评论管理
 * @copyright (c) Emlog All Rights Reserved
 * $Id$
 */

class Comment_Model {
	/**
	 * 内部数据对象
	 * @var MySql
	 */
	private $db;

	function __construct()
	{
		$this->db = MySql::getInstance();
	}

	/**
	 * 获取评论
	 *
	 * @param int $spot 0：前台 1：后台
	 * @param int $blogId
	 * @param string $hide
	 * @param int $page
	 * @return array
	 */
	function getComments($spot = 0, $blogId = null, $hide = null, $page = null)
	{
		$andQuery = '1=1';
		$andQuery .= $blogId ? " and a.gid=$blogId" : '';
		$andQuery .= $hide ? " and a.hide='$hide'" : '';
		$condition = '';
		if($page)
		{
			$perpage_num = Option::get('admin_perpage_num');
			$startId = ($page - 1) * $perpage_num;
			$condition = "LIMIT $startId, ".$perpage_num;
		}
		if($spot == 0)
		{
			$sql = "SELECT * FROM ".DB_PREFIX."comment as a where $andQuery ORDER BY a.cid DESC $condition";
		}else{
			$andQuery .= ROLE != 'admin' ? ' and b.author='.UID : '';
			$sql = "SELECT *,a.hide,a.date FROM ".DB_PREFIX."comment as a, ".DB_PREFIX."blog as b where $andQuery and a.gid=b.gid ORDER BY a.cid DESC $condition";
		}
		$ret = $this->db->query($sql);
		$comments = array();
		while($row = $this->db->fetch_array($ret))
		{
			$row['cname'] = htmlspecialchars($row['poster']);
			$row['mail'] = htmlspecialchars($row['mail']);
			$row['url'] = htmlspecialchars($row['url']);
			$row['content'] = htmlClean($row['comment']);
			$row['date'] = smartDate($row['date']);
			$row['reply'] = htmlClean($row['reply']);
			//$row['hide'];
			//$row['title'];
			//$row['gid'];
			$comments[] = $row;
		}
		return $comments;
	}
	function getOneComment($commentId)
	{
		$timezone = Option::get('timezone');
		$sql = "select * from ".DB_PREFIX."comment where cid=$commentId";
		$res = $this->db->query($sql);
		$commentArray = $this->db->fetch_array($res);
		$commentArray['comment'] = htmlClean(trim($commentArray['comment']));
		$commentArray['reply'] = htmlClean(trim($commentArray['reply']));
		$commentArray['poster'] = trim($commentArray['poster']);
		$commentArray['date'] = gmdate("Y-m-d H:i",$commentArray['date'] + $timezone * 3600);
		return $commentArray;
	}
	/**
	 * 获取查询评论的数目
	 *
	 * @param int $blogId
	 * @return int $comNum
	 */
	function getCommentNum($blogId = null, $hide = null)
	{
		$comNum = '';
		$andQuery = '1=1';
		$andQuery .= $blogId ? " and a.gid=$blogId" : '';
		$andQuery .= $hide ? " and a.hide='$hide'" : '';
		if (ROLE == 'admin')
		{
			$sql = "SELECT a.cid FROM ".DB_PREFIX."comment as a where $andQuery";
		}else {
			$sql = "SELECT a.cid FROM ".DB_PREFIX."comment as a, ".DB_PREFIX."blog as b where $andQuery and a.gid=b.gid and b.author=".UID;
		}
		$res = $this->db->query($sql);
		$comNum = $this->db->num_rows($res);
		return $comNum;
	}
	/**
	 * 删除评论
	 *
	 * @param int $commentId
	 */
	function delComment($commentId)
	{
		$row = $this->db->once_fetch_array("SELECT gid,hide FROM ".DB_PREFIX."comment WHERE cid=$commentId");
		$this->db->query("DELETE FROM ".DB_PREFIX."comment where cid=$commentId");
		$blogId = intval($row['gid']);
		if($row['hide'] == 'n')
		{
			$this->db->query("UPDATE ".DB_PREFIX."blog SET comnum=comnum-1 WHERE gid=$blogId");
		}
	}
	/**
	 * 显示/隐藏评论
	 *
	 * @param int $commentId
	 */
	function hideComment($commentId)
	{
		$row = $this->db->once_fetch_array("SELECT gid,hide FROM ".DB_PREFIX."comment WHERE cid=$commentId");
		$blogId = intval($row['gid']);
		$isHide = $row['hide'];
		if($isHide == 'n')
		{
			$this->db->query("UPDATE ".DB_PREFIX."blog SET comnum=comnum-1 WHERE gid=$blogId");
		}
		$this->db->query("UPDATE ".DB_PREFIX."comment SET hide='y' WHERE cid=$commentId");
	}
	function showComment($commentId)
	{
		$row = $this->db->once_fetch_array("SELECT gid,hide FROM ".DB_PREFIX."comment WHERE cid=$commentId");
		$blogId = intval($row['gid']);
		$isHide = $row['hide'];
		if($isHide == 'y')
		{
			$this->db->query("UPDATE ".DB_PREFIX."blog SET comnum=comnum+1 WHERE gid=$blogId");
		}
		$this->db->query("UPDATE ".DB_PREFIX."comment SET hide='n' WHERE cid=$commentId");
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
		$this->db->query($sql);
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
				foreach($comments as $val)
				{
					$this->delComment($val);
				}
				break;
			case 'hidecom':
				foreach($comments as $val)
				{
					$this->hideComment($val);
				}
				break;
			case 'showcom':
				foreach($comments as $val)
				{
					$this->showComment($val);
				}
				break;
		}
	}

	function addComment($name, $content, $mail, $url, $imgcode, $blogId) 
	{
			$ipaddr = getIp();
			$utctimestamp = time();
			$ischkcomment = Option::get('ischkcomment');
			$sql = 'INSERT INTO '.DB_PREFIX."comment (date,poster,gid,comment,reply,mail,url,hide,ip)
					VALUES ('$utctimestamp','$name','$blogId','$content','','$mail','$url','$ischkcomment','$ipaddr')";
			$ret = $this->db->query($sql);
			$CACHE = Cache::getInstance();
			if ($ischkcomment == 'n') {
				$this->db->query('UPDATE '.DB_PREFIX."blog SET comnum = comnum + 1 WHERE gid='$blogId'");
				$CACHE->updateCache(array('sta', 'comment'));
                doAction('comment_saved');
                emMsg('评论发表成功', Url::log($blogId).'#comment', true);
			} else {
		        $CACHE->updateCache('sta');
		        doAction('comment_saved');
		        emMsg('评论发表成功，请等待管理员审核', Url::log($blogId));
			}
	}

	function isCommentExist($blogId, $name, $content)
	{
		$query = $this->db->query("SELECT cid FROM ".DB_PREFIX."comment WHERE gid=$blogId AND poster='$name' AND comment='$content'");
		$result = $this->db->num_rows($query);
		if ($result > 0)
		{
			return true;
		}else {
			return false;
		}
	}

	function isLogCanComment($blogId)
	{
		$query = $this->db->query("SELECT allow_remark FROM ".DB_PREFIX."blog WHERE gid=$blogId");
		$show_remark = $this->db->fetch_array($query);
		if ($show_remark['allow_remark'] == 'n' || $show_remark === false)
		{
			return false;
		}else {
			return true;
		}
	}

	function setCommentCookie($name,$mail,$url)
	{
		$cookietime = time() + 31536000;
		setcookie('commentposter',$name,$cookietime);
		setcookie('postermail',$mail,$cookietime);
		setcookie('posterurl',$url,$cookietime);
	}

}
