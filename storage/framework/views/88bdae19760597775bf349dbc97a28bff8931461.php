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
                    角色
                </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">系统设置</a></li>
                        <li class="breadcrumb-item active" aria-current="page">角色管理</li>
                    </ol>
                </nav>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">角色列表</h4>
                            <p class="card-description">
                                <button type="button" class="btn btn-sm btn-gradient-success btn-icon-text" onclick="add()">
                                    <i class="mdi mdi-plus btn-icon-prepend"></i>
                                    添加角色
                                </button>
                            </p>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>角色名称</th>
                                    <th>角色描述</th>
                                    <th>创建时间</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($v->name); ?></td>
                                        <td><?php echo e($v->des); ?></td>
                                        <td><?php echo e($v->created_at); ?></td>
                                        <td><?php echo e($v->updated_at); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-gradient-dark btn-icon-text" onclick="update(<?php echo e($v->id); ?>)">
                                                修改
                                                <i class="mdi mdi-file-check btn-icon-append"></i>
                                            </button>
                                            <button <?php if($v->id == 1): ?> disabled <?php endif; ?> type="button" class="btn btn-sm btn-gradient-danger btn-icon-text" onclick="del(<?php echo e($v->id); ?>)">
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
            var page = layer.open({
                type: 2,
                title: '添加角色',
                shadeClose: true,
                shade: 0.8,
                area: ['70%', '90%'],
                content: '/admin/role/add'
            });
        }
        function update(id){
            var page = layer.open({
                type: 2,
                title: '修改角色',
                shadeClose: true,
                shade: 0.8,
                area: ['70%', '90%'],
                content: '/admin/role/update/'+id
            });
        }
        function del(id){
            myConfirm("删除操作不可逆,是否继续?",function(){
                myRequest("/admin/role/del/"+id,"post",{},function(res){
                    layer.msg(res.msg)
                    setTimeout(function(){
                        window.location.reload();
                    },1500)
                });
            });
        }

        $('.menu-switch').click(function(){
            id = $(this).attr('id');
            state = $(this).attr('state');
            console.log(id)
            console.log(state)
            if(state == "on"){
                $('.pid-'+id).hide();
                $(this).attr("state","off")
                $(this).removeClass('mdi-menu-down').addClass('mdi-menu-right');
            }else{
                $('.pid-'+id).show();
                $(this).attr("state","on")
                $(this).removeClass('mdi-menu-right').addClass('mdi-menu-down');
            }
        })
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('base.base', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>