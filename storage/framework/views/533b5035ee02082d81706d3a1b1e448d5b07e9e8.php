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
                    菜单
                </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">系统设置</a></li>
                        <li class="breadcrumb-item active" aria-current="page">菜单管理</li>
                    </ol>
                </nav>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">菜单列表</h4>
                            <p class="card-description">
                                <button type="button" class="btn btn-sm btn-gradient-success btn-icon-text" onclick="add()">
                                    <i class="mdi mdi-plus btn-icon-prepend"></i>
                                    添加菜单
                                </button>

                            </p>
                            <p class="card-description"><a href="/admin/index/url">url测试</a>
                            <p class="card-description"><a href="/admin/index/getfbjixiao">FB绩效拉取</a>
                            <p class="card-description"><a href="/admin/index/getadmobjixiao">Admob绩效拉取</a>
                            <p class="card-description"><a href="/admin/index/fb">获取fb</a>
                            <p class="card-description"><a href="/admin/index/iron">获取iron</a>
                            <p class="card-description"><a href="/admin/index/mp">获取mp</a>
                            <p class="card-description"><a href="/admin/index/lovin">获取applovin</a>
                            <p class="card-description"><a href="/admin/index/getadmobcode">获取admob code</a>
                            <p class="card-description"><a href="/admin/index/getam">获取admob</a>
                            <p class="card-description"><a href="/admin/index/cinstall">获取分国家安装数</a>
                            <p class="card-description"><a href="/admin/index/cspend">获取分国家花费</a>
                            <p class="card-description"><a href="/admin/index/cfb">获取分国家fb</a>
                            <p class="card-description"><a href="/admin/index/ciron">获取分国家iron</a>
                            <p class="card-description"><a href="/admin/index/cmp">获取分国家mp</a>
                            <p class="card-description"><a href="/admin/index/clovin">获取分国家applovin</a>
                            <p class="card-description"><a href="/admin/index/cam">获取分国家admob</a>

                            <p class="card-description"><a href="/admin/index/sendemail">发送邮件</a>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>菜单名称</th>
                                    <th>菜单链接</th>
                                    <th>所属角色</th>
                                    <th>创建时间</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php if(count($v->child)): ?><i class="mdi mdi-menu-down menu-switch" id="<?php echo e($v->id); ?>" state="on"></i>　<?php else: ?>　　<?php endif; ?>
                                            <i class="<?php echo e($v->icon); ?>">　</i><?php echo e($v->name); ?>

                                        </td>
                                        <td><?php echo e($v->url); ?></td>
                                        <td>
                                            <?php $__currentLoopData = $v->role; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kk=>$vv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <label class="badge badge-success"><?php echo e($vv->name); ?></label>
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
                                    <?php if(count($v->child)): ?>
                                        <?php $__currentLoopData = $v->child; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="pid-<?php echo e($v->id); ?>">
                                                <td>　　　　 <?php echo e($val->name); ?></td>
                                                <td><?php echo e($val->url); ?></td>
                                                <td>
                                                    <?php $__currentLoopData = $val->role; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kkk=>$vvv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <label class="badge badge-success"><?php echo e($vvv->name); ?></label>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </td>
                                                <td><?php echo e($val->created_at); ?></td>
                                                <td><?php echo e($v->updated_at); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-gradient-dark btn-icon-text" onclick="update(<?php echo e($val->id); ?>)">
                                                        修改
                                                        <i class="mdi mdi-file-check btn-icon-append"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-gradient-danger btn-icon-text" onclick="del(<?php echo e($val->id); ?>)">
                                                        <i class="mdi mdi-delete btn-icon-prepend"></i>
                                                        删除
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
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
                title: '添加菜单',
                shadeClose: true,
                shade: 0.8,
                area: ['70%', '90%'],
                content: '/admin/menu/add'
            });
        }
        function update(id){
            var page = layer.open({
                type: 2,
                title: '修改菜单',
                shadeClose: true,
                shade: 0.8,
                area: ['70%', '90%'],
                content: '/admin/menu/update/'+id
            });
        }
        function del(id){
            myConfirm("删除操作不可逆,是否继续?",function(){
                myRequest("/admin/menu/del/"+id,"post",{},function(res){
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