<?php defined('EMLOG_ROOT') || exit('access denied!'); ?>
<?php if (isset($_GET['add_shortcut_suc'])): ?>
    <div class="alert alert-success">设置成功</div>
<?php endif ?>
    <div class="d-flex align-items-center mb-3">
        <div class="flex-shrink-0">
            <a class="mr-2" href="blogger.php">
                <img src="<?= User::getAvatar($user_cache[UID]['avatar']) ?>"
                     alt="avatar" class="img-fluid rounded-circle border border-mute border-3"
                     style="width: 56px; height: 56px;">
            </a>
        </div>
        <div class="flex-grow-1 ms-3">
            <div class="align-items-center mb-3">
                <p class="mb-0 m-2"><a class="mr-2" href="blogger.php"><?= $user_cache[UID]['name'] ?></a></p>
                <p class="mb-0 m-2 small"><?= $role_name ?></p>
            </div>
        </div>
    </div>
    <div class="row ml-1 mb-1"><?php doAction('adm_main_top') ?></div>
    <div class="row">
        <div class="col-lg-12 mb-3">
            <div class="card shadow mb-3">
                <div class="card-body">
                    <a href="./article.php?action=write" class="mr-2">写文章</a>
                    <a href="article.php" class="mr-2">文章</a>
                    <a href="article.php?draft=1" class="mr-2">草稿</a>
                    <?php foreach ($shortcut as $item): ?>
                        <a href="<?= $item['url'] ?>" class="mr-2"><?= $item['name'] ?></a>
                    <?php endforeach; ?>
                    <span class="text-gray-300 mr-2">|</span>
                    <a href="#" class="my-1" data-toggle="modal" data-target="#shortcutModal"><i class="icofont-plus"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card shadow mb-3">
                <h6 class="card-header">站点信息</h6>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="./article.php?checked=n">待审文章</a>
                            <a href="./article.php?checked=n"><span class="badge badge-danger badge-pill"><?= $sta_cache['checknum'] ?></span></a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="./comment.php?hide=y">待审评论</a>
                            <a href="./comment.php?hide=y"><span class="badge badge-warning badge-pill"><?= $sta_cache['hidecomnum'] ?></span></a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="./user.php">用户</a>
                            <a href="./user.php"><span class="badge badge-success badge-pill"><?= count($user_cache) ?></span></a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="./article.php">文章</a>
                            <a href="./article.php"><span class="badge badge-primary badge-pill"><?= $sta_cache['lognum'] ?></span></a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="./twitter.php?all=y">微语</a>
                            <a href="./twitter.php?all=y"><span class="badge badge-primary badge-pill"><?= $sta_cache['note_num'] ?></span></a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="./comment.php">评论</a>
                            <a href="./comment.php"><span class="badge badge-primary badge-pill"><?= $sta_cache['comnum_all'] ?></span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php if (User::isAdmin()): ?>
            <div class="col-lg-6 mb-3">
                <div class="card shadow mb-3">
                    <h6 class="card-header">软件信息</h6>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                PHP
                                <span class="small"><?= $php_ver ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                数据库
                                <span class="small">MySQL <?= $mysql_ver ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Web服务
                                <span class="small"><?= $server_app ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                操作系统
                                <span class="small"><?= $os ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                系统时区
                                <span class="small"><?= Option::get('timezone') ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>已注册</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php if (User::isAdmin()): ?>
    <div class="modal fade" id="shortcutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shortcutModalLabel">快捷入口</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="index.php?action=add_shortcut" method="post">
                    <div class="modal-body">
                        <?php foreach ($shortcutAll as $k => $v):
                            $checked = in_array($v, $shortcut) ? 'checked' : '';
                            ?>
                            <input type="checkbox" name="shortcut[]" id="shortcut-<?= $k ?>" value="<?= $v['name'] ?>||<?= $v['url'] ?>" <?= $checked ?>>
                            <label class="mr-2" for="shortcut-<?= $k ?>"><?= $v['name'] ?></label>
                        <?php endforeach; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-sm btn-success">保存</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
<?php endif ?>
<?php if (User::isAdmin()): ?>
    <div class="row">
        <?php doAction('adm_main_content') ?>
    </div>
<?php endif; ?>