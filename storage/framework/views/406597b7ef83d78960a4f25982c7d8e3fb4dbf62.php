<?php $__env->startSection('base'); ?>
    <!-- 内容区域 -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">
                    <a href="javascript:location.replace(location.href);" title="refresh">
                        <span class="page-title-icon bg-gradient-primary text-white mr-2">
                            <i style="color:white"class="mdi mdi-cached"></i>
                        </span>
                    </a>
                    权限
                </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">系统设置</a></li>
                        <li class="breadcrumb-item active" aria-current="page">权限管理</li>
                    </ol>
                </nav>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">权限列表</h4>
                            <p class="card-description">
                                <button type="button" class="btn btn-sm btn-gradient-success btn-icon-text" onclick="add()">
                                    <i class="mdi mdi-plus btn-icon-prepend"></i>
                                    添加权限
                                </button>
                            </p>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="10%">权限名</th>
                                    <th width="35%">路由</th>
                                    <th width="15%">创建时间</th>
                                    <th width="15%">更新时间</th>
                                    <th width="20%">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($v->id); ?></td>
                                        <td><?php echo e($v->name); ?></td>
                                        <td style="word-wrap:break-word;word-break:break-all">
                                            <?php $__currentLoopData = $v->route; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <label class="badge badge-success"><?php echo e($j); ?></label>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </td>
                                        <td><?php echo e($v->created_at); ?></td>
                                        <td><?php echo e($v->updated_at); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-gradient-dark btn-icon-text" onclick="update(<?php echo e($v->id); ?>)">
                                                修改
                                                <i class="mdi mdi-file-check btn-icon-append"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-gradient-danger btn-icon-text" onclick="del(<?php echo e($v->id); ?>)">
                                                <i class="mdi mdi-delete btn-icon-prepend"></i>
                                                删除
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>

        function add(){
            layer.open({
                type: 2,
                title: '添加权限',
                shadeClose: true,
                shade: 0.8,
                area: ['70%', '90%'],
                content: '/admin/permission/add'
            });
        }

        function update(id){
            var page = layer.open({
                type: 2,
                title: '修改权限',
                shadeClose: true,
                shade: 0.8,
                area: ['70%', '90%'],
                content: '/admin/permission/update/'+id
            });
        }

        function del(id){
            myConfirm("删除操作不可逆,是否继续?",function(){
                myRequest("/admin/permission/del/"+id,"post",{},function(res){
                    layer.msg(res.msg)
                    setTimeout(function(){
                        window.location.reload();
                    },1500)
                });
            });
        }

    </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('base.base', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>