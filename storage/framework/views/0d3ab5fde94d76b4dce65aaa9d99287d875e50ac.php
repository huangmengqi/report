
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
                    配置Access Token
                </h3>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Token名称</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr id="1">
                                        <td>ADPMD-install/spend(获取Facebook安装和花费数据)</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-gradient-dark btn-icon-text" onclick="update(1)">
                                                修改
                                                <i class="mdi mdi-file-check btn-icon-append"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr id="2">
                                        <td>ADPMD-fan-used(获取Facebook变现数据)</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-gradient-dark btn-icon-text" onclick="update(2)">
                                                修改
                                                <i class="mdi mdi-file-check btn-icon-append"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr id="3">
                                        <td>Close-Campaign(亏损关闭campaign)</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-gradient-dark btn-icon-text" onclick="update(3)">
                                                修改
                                                <i class="mdi mdi-file-check btn-icon-append"></i>
                                        </td>
                                    </tr>
                                    <tr id="4">
                                        <td>ADPMD-page(page状态监控)</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-gradient-dark btn-icon-text" onclick="update(4)">
                                                修改
                                                <i class="mdi mdi-file-check btn-icon-append"></i>
                                            </button>
                                        </td>
                                    </tr>
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
                title: '添加应用',
                shadeClose: true,
                shade: 0.8,
                area: ['70%', '90%'],
                content: '/admin/report/envadd'
            });
        }

        function update(id){
            var page = layer.open({
                type: 2,
                title: '修改token信息',
                shadeClose: true,
                shade: 0.8,
                area: ['70%', '90%'],
                content: '/admin/report/envupdate/'+id
            });
        }

        function del(id){
            myConfirm("删除操作不可逆,是否继续?",function(){
                myRequest("/admin/report/envdel/"+id,"post",{},function(res){
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