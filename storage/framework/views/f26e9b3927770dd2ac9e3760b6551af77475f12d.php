

<style type="text/css">
p{ display: inline-block;}
label{ position: relative; padding:0 10px 0 25px; cursor: pointer;}
label input{ display: none;}
label span{ position: absolute; left: 0; top: 0; bottom: 0; width: 16px; height: 16px; margin: auto; border: 1px solid #ccc; border-radius: 100%;}
label span:after{ content: ''; position: absolute; top: 0; bottom: 0; left: 0; right: 0; width: 6px; height: 6px; margin: auto; border-radius: 100%; background: #fff;
-webkit-transform: scale(0); transform: scale(0); -webkit-transition: all .3s; transition: all .3s;}
label input:checked + span{ border-color: #1db0fc; background: #1db0fc;}
label input:checked + span:after{ -webkit-transform: scale(1); transform: scale(1);}
</style>
<?php $__env->startSection('base'); ?>

<!-- 内容区域 -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">

                            <!-- <form class="forms-sample" id="form" method="POST" action="/admin/report/uploadxls" enctype="multipart/form-data"> -->
                            <form action="/report/synchronizedataadd" method='post' enctype="multipart/form-data">
                            	<?php echo csrf_field(); ?>
                                <br>
                                <div class="form-group">
                                    <label for="file">选择应用：</label>
                                    <select required class="form-control form-control-lg" name="appid" style="width: 210px;height: calc(2.875rem + 2px);margin-right: 20px">
                                        <option value="">Select App</option>
                                        <?php $__currentLoopData = $gamelist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option  value="<?php echo e($v->id); ?>"  class="red1"><?php echo e($v->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select> 
                                </div>

                                
                                <br>

                                <button type="submit" class="btn btn-sm btn-gradient-primary btn-icon-text">
                                    <i class="mdi mdi-file-check btn-icon-prepend"></i>
                                    同步分国家花费数据
                                </button>
                                <button type="button" onclick="cancel()" class="btn btn-sm btn-gradient-warning btn-icon-text">
                                    <i class="mdi mdi-reload btn-icon-prepend"></i>
                                    取消
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
         function commit(){
            if(!checkForm()){
                return false;
            }
            var data = $("#form").serializeObject();
            myRequest("/report/synchronizedataadd","post",data,function(res){
                if(res.code == '200'){
                    layer.msg(res.msg)
                    setTimeout(function(){
                        parent.location.reload();
                    },1500)
                }else{
                    layer.msg(res.msg)
                }
            });
        }
        $(".calendar1").flatpickr({
           dateFormat:"Y-m-d",
           enableTime:false,
           maxDate: "today"
           //更多配置百度搜索flatpickr即可找到
        });
        function cancel() {
            parent.location.reload();
        }
    </script>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('base.base', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>