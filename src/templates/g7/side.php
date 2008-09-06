<?php 
if(!defined('EMLOG_ROOT')) {exit('error!');}
?>
<div id="sidebar_tag">
<?php foreach($tag_cache as $value): ?>
<span style="font-size:<?php echo $value['fontsize'];?>pt; height:30px;"><a href="./?tag=<?php echo $value['tagurl'];?>"><?php echo $value['tagname'];?></a></span>&nbsp;
<?php endforeach; ?>
		
</div>

<div id="sidebar">
        <div id="sidebar_1">
<ul>

<li class="pagenav"><h2 onclick="showhidediv('blogger')">个人档</h2>
<ul id="blogger">
	<p><?php echo $photo;?></p>
	<p><b><?php echo $name;?></b>
	<div id="bloggerdes"><?php echo $blogger_des; ?></div>
	<?php if(ISLOGIN === true): ?>
	<a href="javascript:void(0);" onclick="showhidediv('modbdes','bdes')">
	<img src="<?php echo $em_tpldir; ?>/images/modify.gif" align="absmiddle" alt="修改我的状态"/></a></li>
	<li id='modbdes' style="display:none;">
	<textarea name="bdes" class="input" id="bdes" style="overflow-y: hidden;width:170px;height:50px;"><?php echo $blogger_des; ?></textarea>
	<br />
	<a href="javascript:void(0);" onclick="postinfo('./adm/blogger.php?action=modintro&flg=1','bdes','bloggerdes');">提交</a>
	<a href="javascript:void(0);" onclick="showhidediv('modbdes')">取消</a>
	<?php endif; ?>
		</ul>
</li>

<li class="categories"><h2 onclick="showhidediv('cal')">日历</h2>
		<ul id="cal">
			<div id="calendar"></div>
		</ul>
</li>
<script>sendinfo('<?php echo $calendar_url;?>','calendar');</script>

<?php if($index_twnum>0): ?>
<li class="r_comments"><h2 onclick="showhidediv('twitter')">Twitter</h2>
<ul id="twitter">
<?php  
if(isset($tw_cache) && is_array($tw_cache)) :
	$morebt = count($tw_cache)>$index_twnum?"<li id=\"twdate\"><a href=\"javascript:void(0);\" onclick=\"sendinfo('twitter.php?p=2','twitter')\">较早的&raquo;</a></li>":'';
	foreach (array_slice($tw_cache,0,$index_twnum) as $value):
		$delbt = ISLOGIN === true?"<a href=\"javascript:void(0);\" onclick=\"isdel('{$value['id']}','twitter')\">删除</a>":'';
		$value['date'] = smartyDate($localdate,$value['date']);
?>
<li> <?php echo $value['content'];?> <?php echo $delbt;?><br><span><?php echo $value['date'];?></span></li>
<?php endforeach; ?>
<?php echo $morebt;?>
<?php endif; ?>
</ul>
<?php if(ISLOGIN === true): ?>
<ul>
<li><a href="javascript:void(0);" onclick="showhidediv('addtw','tw')">我要唠叨</a></li>
<li id='addtw' style="display: none;">
<textarea name="tw" id="tw" style="overflow-y: hidden;width:190p;height:60px;"></textarea><br />
<a href="javascript:void(0);" onclick="postinfo('./twitter.php?action=add','tw','twitter');">提交</a>
<a href="javascript:void(0);" onclick="showhidediv('addtw')">取消</a>
</li>
</ul>
<?php endif; ?>
<?php endif; ?>
<?php if($ismusic): ?>
<li class="some"><h2 onclick="showhidediv('music')">音乐</h2>
<ul id="music">
<?php echo $musicdes;?><object type="application/x-shockwave-flash" data="./images/player.swf?son=<?php echo $musicurl; ?><?php echo $autoplay;?>&autoreplay=1" width="150" height="20"><param name="movie" value="./images/player.swf?son=<?php echo $musicurl; ?><?php echo $autoplay;?>&autoreplay=1" /></object>
</p>
</ul>
</li>
<?php endif; ?>

<li class="r_comments"><h2 onclick="showhidediv('comm')">评论</h2>
<ul id="comm">
<?php foreach($com_cache as $value): ?>
<li><?php echo $value['name'];?>
<?php if($value['reply']): ?>
	<a href="<?php echo $value['url']; ?>" title="博主回复：<?php echo $value['reply']; ?>">
	<img src="<?php echo $em_tpldir; ?>/images/comment.png" align="absmiddle"/>
	</a>
<?php endif;?>
<br /><a href="<?php echo $value['url'];?>"><?php echo $value['content'];?></a></li>
<?php endforeach; ?>
</ul>
</li>

<li class="statistics"><h2 onclick="showhidediv('sta')">统计</h2>
		<ul id="sta">
		<li>日志数量：<?php echo $sta_cache['lognum'];?></li>
		<li>评论数量：<?php echo $sta_cache['comnum'];?></li>
		<li>引用数量：<?php echo $sta_cache['tbnum'];?></li>
		<li>今日访问：<?php echo $sta_cache['day_view_count'];?></li>
		<li>总访问量：<?php echo $sta_cache['view_count'];?></li>
		</ul>
</li>
<?php
if(ISLOGIN === false):
	$login_code=='y'?
	$ckcode = "验证码:<br />
				<input name=\"imgcode\" type=\"text\" class=\"INPUT\" size=\"5\">&nbsp&nbsp\n
				<img src=\"./lib/C_checkcode.php\" align=\"absmiddle\"></td></tr>\n":
	$ckcode = '';
?> 
<li class="random"><h2 onclick="showhidediv('loginfm','user')" >登录</h2>
<ul id="loginfm" style="display: none;">
<form name="f" method="post" action="index.php?action=login" id="commentform">
<li>
用户名:<br>
<input name="user" id="user" type="text"><br />
密  码:<br>
<input name="pw" type="password"><br>
<?php echo $ckcode;?> <br>
<input type="submit" value=" 登录">
</li>
</form>
</ul>
<?php
else:
?>
<li class="random"><h2 onclick="showhidediv('loginfm')" >管理</h2>
<ul id="loginfm">
	<li><a href="./adm/add_log.php">写日志</a></li>
	<li><a href="./adm/">管理中心</a></li>
	<li><a href="./index.php?action=logout">退出</a></li>
	</ul>
<?php endif; ?>
<?php echo $exarea;?>
		</div>

<div id="sidebar_2">
<ul>

<li class="archives"><h2 onclick="showhidediv('dang')">存档</h2>
		<ul id="dang">
<?php foreach($dang_cache as $value): ?>
		<li><a href="<?php echo $value['url'];?>"><?php echo $value['record'];?>(<?php echo $value['lognum'];?>)</a></li>
<?php endforeach; ?>	
		</ul>
</li>

<li class="random"><h2 onclick="showhidediv('links')">友情链接</h2>
		<ul id="links">
<?php foreach($link_cache as $value): ?>     	
		<li><a href="<?php echo $value['url'];?>" title="<?php echo $value['des'];?>" target="_blank"><?php echo $value['link'];?></a></li>
<?php endforeach; ?>	
		</ul>
</li>

<li class="feed"><h2>feed</h2>
        <ul>
			<p><a href="./rss.php">all feed</a></p>
        </ul>
</li>
</ul>
  </div>

</div> 
</div> 
</ul>
</div>			
