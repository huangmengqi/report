
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
                            <!-- <h4 class="card-title">请填写管理员信息</h4> -->

                            <form class="forms-sample" id="form">
                               
                                <div class="form-group">
                                    <label for="nickname">*账户ID</label>
                                    <input type="text" required class="form-control required" name="act_id" placeholder="请输入需要清理的广告账户ID">
                                </div>

                                <div class="form-group">
                                    <label for="nickname">安装数</label>
                                    <input type="text"  value='0' class="form-control" name="install" placeholder="请输入需要清理的安装数标准，默认0">
                                </div>

                                <div class="form-group">
                                    <p>上架状态：</p>
                                    <label><input name="day" value="7" type="radio"  checked /><span></span>过去7天</label>
                                    <label><input name="day" value="14" type="radio" /><span></span>过去14天</label>
                                    <label><input name="day" value="30" type="radio" /><span></span>过去30天</label>
                                </div>

                                <button type="button" onclick="commit()" class="btn btn-sm btn-gradient-primary btn-icon-text">
                                    <i class="mdi mdi-file-check btn-icon-prepend"></i>
                                    提交
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
            myRequest("/report/actdeleteadd","post",data,function(res){
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
        function cancel() {
            parent.location.reload();
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('base.base', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>