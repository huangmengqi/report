
<?php $__env->startSection('base'); ?>
    <style type="text/css">
        .red{
            background-color:#FF3333;
        }
        .green{
            background-color:#99FF33;
        }
    </style>
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
                    Page列表
                </h3>
                <!-- <nav aria-label="breadcrumb">

                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">运营报表</a></li>
                        <li class="breadcrumb-item active" aria-current="page">应用列表</li>
                    </ol>
                </nav> -->
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            
                            <p class="card-description">
                                <button type="button" class="btn btn-sm btn-gradient-success btn-icon-text" onclick="add()">
                                    <i class="mdi mdi-plus btn-icon-prepend"></i>
                                    添加Page
                                </button>
                            </p>

                                <form class="forms-sample">
                                    <div class="form-group" style="width: 400px">
                                        <div class="input-group col-xs-3">
                                            <input type="text" style="width: 110px;height: calc(2.875rem + 2px);margin-right: 20px"  class="form-control required calendar1" <?php if($page_name): ?> value="<?php echo e($page_name); ?>" <?php endif; ?> name="page_name" placeholder="请输入Page名称">
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
                                    <th>Page名称</th>
                                    <th>Page Id</th>
                                    <th>状态</th>
                                    <th>添加时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($v->page_name); ?></td>
                                        <td><?php echo e($v->page_id); ?></td>
                                        <?php if($v->status == '0'): ?> 
                                            <td class="red">Unpublished</td>
                                        <?php else: ?>
                                            <td class="green">Published</td>
                                        <?php endif; ?>
                                        <td><?php echo date('Y-m-d',$v->create_time); ?></td>
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
                title: '添加Page',
                shadeClose: true,
                shade: 0.8,
                area: ['30%', '60%'],
                content: '/admin/report/urladd'
            });
        }

        function update(id){
            var page = layer.open({
                type: 2,
                title: '修改Page',
                shadeClose: true,
                shade: 0.8,
                area: ['30%', '60%'],
                content: '/admin/report/urlupdate/'+id
            });
        }

        function del(id){
            myConfirm("删除操作不可逆,是否继续?",function(){
                myRequest("/admin/report/urldel/"+id,"post",{},function(res){
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