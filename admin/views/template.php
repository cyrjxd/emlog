<?php defined('EMLOG_ROOT') || exit('access denied!'); ?>
<?php if (isset($_GET['activated'])): ?>
    <div class="alert alert-success">模板更换成功</div><?php endif ?>
<?php if (isset($_GET['activate_install'])): ?>
    <div class="alert alert-success">模板安装成功</div><?php endif ?>
<?php if (isset($_GET['activate_upgrade'])): ?>
    <div class="alert alert-success">模板更新成功</div><?php endif ?>
<?php if (isset($_GET['activate_del'])): ?>
    <div class="alert alert-success">删除模板成功</div><?php endif ?>
<?php if (isset($_GET['error_f'])): ?>
    <div class="alert alert-danger">删除失败，请检查模板文件权限</div><?php endif ?>
<?php if (!$nonce_template_data): ?>
    <div class="alert alert-danger">当前使用的模板(<?= $nonce_template ?>)已被删除或损坏，请选择其他模板。</div><?php endif ?>
<?php if (isset($_GET['error_a'])): ?>
    <div class="alert alert-danger">只支持zip压缩格式的模板包</div><?php endif ?>
<?php if (isset($_GET['error_b'])): ?>
    <div class="alert alert-danger">上传失败，模板目录(content/templates)不可写</div><?php endif ?>
<?php if (isset($_GET['error_d'])): ?>
    <div class="alert alert-danger">请选择一个zip格式的模板安装包</div><?php endif ?>
<?php if (isset($_GET['error_e'])): ?>
    <div class="alert alert-danger">安装失败，模板安装包不符合标准</div><?php endif ?>
<?php if (isset($_GET['error_f'])): ?>
    <div class="alert alert-danger">上传安装包大小超出PHP限制</div><?php endif ?>
<?php if (isset($_GET['error_c'])): ?>
    <div class="alert alert-danger">服务器PHP不支持zip模块</div><?php endif ?>
<?php if (isset($_GET['error_h'])): ?>
    <div class="alert alert-danger">更新失败，无法下载更新包，可能是服务器网络问题。</div><?php endif ?>
<?php if (isset($_GET['error_i'])): ?>
    <div class="alert alert-danger">您的emlog pro尚未注册</div><?php endif ?>

<div class="row app-list">
    <?php foreach ($templates as $key => $value): ?>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm" data-app-alias="<?= $value['tplfile'] ?>" data-app-version="<?= $value['version'] ?>">
                <div class="card-header <?php if ($nonce_template == $value['tplfile']) {
                    echo "bg-success text-white font-weight-bold";
                } ?>">
                    <?= $value['tplname'] ?>
                </div>
                <div class="card-body">
                    <a href="template.php?action=use&tpl=<?= $value['tplfile'] ?>&token=<?= LoginAuth::genToken() ?>">
                        <img class="card-img-top" src="<?= $value['preview'] ?>" alt="preview image">
                    </a>
                </div>
                <div class="card-footer">
                    <?php if ($value['version']): ?>
                        <div class="small">版本号：<?= $value['version'] ?></div>
                    <?php endif ?>
                    <?php if ($value['author']): ?>
                        <div class="small">开发者：<?= $value['author'] ?></div>
                    <?php endif ?>
                    <div class="small">
                        <?= $value['tpldes'] ?>
                        <a href="<?= $value['tplurl'] ?>" target="_blank">更多信息&rarr;</a>
                    </div>
                    <div class="card-text d-flex justify-content-between mt-3">
                        <span>
                        <?php if ($nonce_template !== $value['tplfile']): ?>
                            <a class="btn btn-success btn-sm" href="template.php?action=use&tpl=<?= $value['tplfile'] ?>&token=<?= LoginAuth::genToken() ?>">启用</a>
                        <?php endif; ?>
                            <span class="update-btn"></span>
                        </span>
                        <span>
                            <a class="btn btn-danger btn-sm" href="javascript: em_confirm('<?= $value['tplfile'] ?>', 'tpl', '<?= LoginAuth::genToken() ?>');">删除</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>


