@extends('base.base')

@section('base')
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
    <!-- 内容区域 -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">

                            <form class="forms-sample" id="form">
                                
                                <div class="form-group">
                                    <label for="page_name">*Page Name</label>
                                    <input type="text" required class="form-control required" name="page_name" placeholder="Page Name">
                                </div>

                                <div class="form-group">
                                    <label for="page_id">*Page Id</label>
                                    <input type="text" required class="form-control required" name="page_id" placeholder="Page Id">
                                </div>

                                <div class="form-group">
                                    <p>Page Status：</p>
                                    <label><input name="status" value="1" type="radio"  checked /><span></span>Published</label>
                                    <label><input name="status" value="0" type="radio" /><span></span>Unpublished</label>
                                </div>

                                <div class="form-group">
                                    <p>是否申诉：</p>
                                    <label><input name="is_verify" value="1" type="radio" /><span></span>已申诉</label>
                                    <label><input name="is_verify" value="0" type="radio" /><span></span>未申诉</label>
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
            myRequest("/admin/report/urladd","post",data,function(res){
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
@endsection