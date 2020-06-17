
@extends('base.base')
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
@section('base')

<!-- 内容区域 -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">

                            <!-- <form class="forms-sample" id="form" method="POST" action="/admin/report/uploadxls" enctype="multipart/form-data"> -->
                            <form action="/admin/report/uploadadmob" method='post' enctype="multipart/form-data">
                            	@csrf
                                <input type="text" style="width: 210px;height: calc(2.875rem + 2px);margin-right: 20px"  class="form-control required calendar1"  value="选择日期" name="date" required>
                                <br>
                                <select required class="form-control form-control-lg" name="appid" style="width: 210px;height: calc(2.875rem + 2px);margin-right: 20px">
                                    <option value="">Select App</option>
                                    @foreach($gamelist as $k=>$v)
                                        <option  value="{{$v->id}}"  class="red1">{{$v->name}}</option>
                                    @endforeach
                                </select>
                                <br>

                                <div class="form-group">
                                    <label for="file">选择文件</label>
                                    <input id="file" type="file" class="form-control" name="file" required>    
                                </div>


                                <button type="submit" class="btn btn-sm btn-gradient-primary btn-icon-text">
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
            myRequest("/admin/report/uploadadmob","post",data,function(res){
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
    @endsection