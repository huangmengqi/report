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
                            <!-- <h4 class="card-title">请填写管理员信息</h4> -->

                            <form class="forms-sample" id="form">
                                
                                <div class="form-group">
                                    <label for="nickname">*应用名称</label>
                                    <input type="text" required class="form-control required" name="name" placeholder="请输入应用名称">
                                </div>

                                <div class="form-group">
                                    <label for="nickname">Upltv ID</label>
                                    <input type="text"  class="form-control" name="upltv_id" placeholder="请输入ADX应用名称">
                                </div>

                                <div class="form-group">
                                    <label for="nickname">ADX应用名称</label>
                                    <input type="text" class="form-control" name="adx_appname" placeholder="请输入ADX应用名称">
                                </div>

                                <div class="form-group">
                                    <label for="account">*FaceBook Read Ads Account</label>
                                    <input type="text" required class="form-control required" name="account" placeholder="账号列表，多个账号请用英文逗号分隔">
                                </div>

                                <div class="form-group">
                                    <label for="account">*App Id</label>
                                    <input type="text"  required class="form-control required" name="fb_app_id" placeholder="App Id，fb变现数据使用">
                                </div>

                                <div class="form-group">
                                    <label for="account">*Admob Acount Id</label>
                                    <input type="text"  required class="form-control required" name="am_account_id" placeholder="Admob Acount Id，admob变现数据使用">
                                </div>

                                <div class="form-group">
                                    <label for="account">*Admob App Name</label>
                                    <input type="text"  required class="form-control required" name="am_app_name" placeholder="Admob App Name，admob变现数据使用">
                                </div>

                                <div class="form-group">
                                    <label for="account">*Ironsource App Key</label>
                                    <input type="text"  required class="form-control required" name="is_app_key" placeholder="Ironsource App Key，ironsource变现数据使用">
                                </div>

                                <div class="form-group">
                                    <label for="account">*Aplovin Package Name</label>
                                    <input type="text"  required class="form-control required" name="al_package_name" placeholder="Aplovin Package Name，applovin变现数据使用">
                                </div>
                                
                                <div class="form-group">
                                    <label for="account">*Mobpub App Id</label>
                                    <input type="text"  required class="form-control required" name="mp_app_id" placeholder="Mobpub App Id，Mobpub变现数据使用">
                                </div>

                                <div class="form-group">
                                    <label for="account">Monitor Phone Number</label>
                                    <input type="text" class="form-control" name="monitor_phone_number" placeholder="Monitor Phone Number，预警使用,多个账号请用英文逗号分隔">
                                </div>

                                <div class="form-group">
                                    <label for="account">App alarm mailbox(example@mobchang.com)</label>
                                    <input type="text"  class="form-control" name="bussiness_email" placeholder="App alarm mailbox，产品状态提示,公司企业邮箱">
                                </div>

                                <div class="form-group">
                                    <label for="account">Monitor Business mailbox(example@mobchang.com)</label>
                                    <input type="text"  class="form-control" name="alarm_email" placeholder="Monitor Business mailbox，预警使用,公司企业邮箱">
                                </div>

                                <div class="form-group">
                                    <label for="account">Monitor Install</label>
                                    <input type="number" min="0" class="form-control" name="monitor_install" placeholder="Monitor Install，预警使用,量级设置">
                                </div>

                                <div class="form-group">
                                    <label for="account">*Monitor Cpi</label>
                                    <input type="number" min="0" class="form-control" name="monitor_cpi" placeholder="Monitor Cpi，预警使用,CPI设置">
                                </div>


                                <div class="form-group">
                                    <label for="account">*Monitor Profit</label>
                                    <input type="number" min="0" class="form-control" name="monitor_profit" placeholder="Monitor Profit，预警使用，填写正整数">
                                </div>


                                <div class="form-group">
                                    <p>上架状态：</p>
                                    <!-- shelf对应app表中的status字段 -->
                                    <label><input name="shelf" value="1" type="radio"  checked /><span></span>上架</label>
                                    <label><input name="shelf" value="0" type="radio" /><span></span>下架</label>
                                </div>

                                <div class="form-group">
                                    <p>产品类别：</p>
                                    <label><input name="cate" value="1" type="radio"  checked /><span></span>自研</label>
                                    <label><input name="cate" value="0" type="radio" /><span></span>外单</label>
                                </div>

                                <div class="form-group">
                                    <p>是否为游戏：</p>
                                    <label><input name="is_game" value="1" type="radio"  checked /><span></span>是</label>
                                    <label><input name="is_game" value="0" type="radio" /><span></span>否</label>
                                </div>

                                <div class="form-group">
                                    <p>是否有FAN变现：</p>
                                    <label><input name="is_fan" value="1" type="radio"  checked /><span></span>是</label>
                                    <label><input name="is_fan" value="0" type="radio" /><span></span>否</label>
                                </div>

                                <div class="form-group">
                                    <label for="account">运营人员：</label>
                                    <input type="text" class="form-control" name="operator" placeholder="Operator，运营人员">
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
            myRequest("/admin/report/appadd","post",data,function(res){
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