
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
                    应用列表
                </h3>
                <nav aria-label="breadcrumb">

                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">运营报表</a></li>
                        <li class="breadcrumb-item active" aria-current="page">应用列表</li>
                    </ol>
                </nav>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            
                            <p class="card-description">
                                <button type="button" class="btn btn-sm btn-gradient-success btn-icon-text" onclick="add()">
                                    <i class="mdi mdi-plus btn-icon-prepend"></i>
                                    添加应用
                                </button>
                            </p>

                                <form class="forms-sample">
                                    <div class="form-group" style="width: 400px">
                                        <div class="input-group col-xs-3">
                                            <input type="text" style="width: 110px;height: calc(2.875rem + 2px);margin-right: 20px"  class="form-control required calendar1" <?php if($app): ?> value="<?php echo e($app); ?>" <?php endif; ?> name="app" placeholder="请输入应用名称">
                                            <input type="text" style="width: 110px;height: calc(2.875rem + 2px);margin-right: 20px"  class="form-control required calendar1" <?php if($operator): ?> value="<?php echo e($operator); ?>" <?php endif; ?> name="operator" placeholder="请输入投放人员">
                                            <span class="input-group-append" >
                                                <button type="submit" class="btn btn-sm btn-gradient-primary"><i class="mdi mdi-account-search btn-icon-prepend"></i>
                                                    搜索
                                                </button>
                                            </span>   

                                        </div>
                                        
                                    </div>

                                </form>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>应用名称</th>
                                    <th>添加时间</th>
                                    <th>投放人员</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($v->name); ?></td>
                                        <td><?php echo date('Y-m-d',$v->add_time); ?></td>
                                        <td><?php echo e($v->operator); ?></td>
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
                            <div class="box-footer clearfix">
                                总共 <b><?php echo e($list->total()); ?></b> 条,分为<b><?php echo e($list->lastPage()); ?></b>页
                                <?php echo $list->links(); ?>

                            </div>
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
                title: '添加应用',
                shadeClose: true,
                shade: 0.8,
                area: ['70%', '90%'],
                content: '/admin/report/appadd'
            });
        }

        function update(id){
            var page = layer.open({
                type: 2,
                title: '修改应用',
                shadeClose: true,
                shade: 0.8,
                area: ['70%', '90%'],
                content: '/admin/report/appupdate/'+id
            });
        }

        function del(id){
            myConfirm("删除操作不可逆,是否继续?",function(){
                myRequest("/admin/report/appdel/"+id,"post",{},function(res){
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