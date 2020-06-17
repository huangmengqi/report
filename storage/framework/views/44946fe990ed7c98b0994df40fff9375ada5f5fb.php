<?php $__env->startSection('base'); ?>
    <!-- 内容区域 -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">请修改菜单信息</h4>
                            
                            
                            
                            <form class="forms-sample" id="form">
                                <div class="form-group">
                                    <label for="exampleInputName1">菜单名称</label>
                                    <input type="text"  class="form-control required" name="name" placeholder="菜单名称" value="<?php echo e($res->name); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail3">菜单链接</label>
                                    <input type="text"  class="form-control required" name="url" placeholder="菜单链接" value="<?php echo e($res->url); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail3">菜单图标</label>
                                    <input type="text"  class="form-control" name="icon" placeholder="菜单图标对应class值,二级菜单留空即可" value="<?php echo e($res->icon); ?>">
                                    <p class="card-description">
                                        点击查看<a href="/icon" target="_blank">图标库</a>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail3">权重</label>
                                    <input type="text"  class="form-control required" name="sort" placeholder="权重 数字越大,排名越靠前" value="<?php echo e($res->sort); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword4">上级菜单</label>
                                    <select class="form-control required" name="pid" >
                                        <option value="0" <?php if($res->pid == 0): ?> selected <?php endif; ?>>顶级菜单</option>
                                        <?php $__currentLoopData = $parent_menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option <?php if($res->pid == $v->id): ?> selected <?php endif; ?> value="<?php echo e($v->id); ?>"><?php echo e($v->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="form-check col-md-1 col-sm-1" style="display: inline-block;">
                                        <label class="form-check-label" style="margin-left: 0">
                                            *选择角色
                                        </label>
                                    </div>
                                    <div class="form-check col-md-2 col-sm-2" style="display: inline-block;">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input all">
                                            全选
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                    <br>
                                    <?php $__currentLoopData = $role_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="form-check col-md-2 col-sm-2" style="display: inline-block;">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input role" value="<?php echo e($v->id); ?>" <?php if($v->checked): ?> checked <?php endif; ?>>
                                                <?php echo e($v->name); ?>

                                                <i class="input-helper"></i>
                                            </label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <button type="button" onclick="commit(<?php echo e($res->id); ?>)" class="btn btn-sm btn-gradient-primary btn-icon-text">
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
        $('.all').on("click",function(){
            if(this.checked) {
                $("input[type='checkbox']").prop('checked',true);
            }else {
                $("input[type='checkbox']").prop('checked',false);
            }
        });
        function commit(id){
            if(!checkForm()){
                return false;
            }
            var data = $("#form").serializeObject();
            var roles =  new Array();
            $('.role:checked').each(function(index){
                roles[index] = $(this).val();
            })
            data.role = roles;
            myRequest("/admin/menu/update/"+id,"post",data,function(res){
                layer.msg(res.msg)
                setTimeout(function(){
                    parent.location.reload();
                },1500)
            });
        }
        function cancel() {
            parent.location.reload();
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('base.base', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>