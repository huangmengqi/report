@extends('base.base')
@section('base')

    <style>
        *{margin:0;padding:0;}
        .container{border: 1px #ccc solid;width: 90%;height:100margin: 100px auto;}
        #form-table{width: 100%;margin: 0 auto;text-align: center;table-layout: fixed;}
        #form-table th{border-right: 1px #ccc solid;border-bottom: 1px #ccc solid;background: #F3F3F3;height:2rem;}
        #form-table td{border:0;}
        #form-table1{width: 100%;margin: 0 auto;text-align: center;table-layout: fixed;}
        #form-table1 th{border-right: 1px #ccc solid;border-bottom: 1px #ccc solid;background: #F3F3F3;}
        #form-table1 td{border:0;font-size:16px;}
        .fixed-tfoot tr td{background: #F3F3F3;}
        .fixed-thead,.fixed-tfoot{padding-right:17px;}
        .fixed-thead tr th,.fixed-tfoot tr td{height: 50px;font-size: 16px;text-align: center;}
        .scroll-box{width: 100%;height:100%;overflow: auto;overflow-x:hidden;}
        .scroll-box tr{width: 100%;height: 40px;line-height: 20px;}
        .scroll-box tr td{padding: 5px;}
        /*.scroll-box tr:nth-child(odd) td{background-color: #ECE9D8;}*/
        .tip{margin: 0 auto;text-align: center;color: red;line-height: 150%;font-size: 14px;}
        .head td{font-size:16px;font-weight:bold;padding:15px 15px;}
        .today td{
            color:#46c35f;
        }
        .total td{
            color:#E91E63;
            padding: 10px;
            border-bottom: 1px #ccc solid;
	        border-right: 1px #ccc solid;
	        border-left: 1px #ccc solid;
        }
        .red{
            background-color:#FF3333;
        }
        .red1{
            color:#FF3333;
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
                    Country Report
                </h3>
                <nav aria-label="breadcrumb"> 
                    <p class="card-description">
                        <a href="/admin/report/downloadfbjx" class="btn btn-sm btn-gradient-success btn-icon-text">Export Excel(All Data)</a>
                    </p>
                </nav>
                
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-lg-3">
                                <form class="forms-sample">
                                    <div class="form-group" style="width: 520px">
                                        
                                        <div class="input-group col-xs-3">
                                            <select class="form-control form-control-lg" name="appname" style="width: 150px;height: calc(2.875rem + 2px);margin-right: 20px">
                                                @foreach($gamelist as $k=>$v)
                                                    <option  @if($appname == $v->name) selected @endif value="{{$v->name}}">{{$v->name}}</option>
                                                @endforeach
                                            </select>
                                            <span class="input-group-append" >
                                                <button type="submit" class=" btn btn-sm btn-gradient-primary"><i class="mdi mdi-account-search btn-icon-prepend"></i>
                                                    搜索
                                                </button>
                                            </span>
                                            <!-- <span class="input-group-append" style="float: right">
                                                <button type="button" onclick="refresh()" class=" btn btn-sm btn-gradient-primary"><i class="mdi mdi-account-search btn-icon-prepend"></i>
                                                    数据更新
                                                </button>
                                            </span> -->
                                        </div>
                                    </div>
                                </form>

                            </div>
                            
                            
                            <!-- <table class="table table-bordered" > -->
                                <table id="form-table" cellpadding="0" border="0" cellspacing="0">
                                    <thead>
                                    <tr class="head">
                                        <th data-type="string">Ad Accout ID</th>
    									<th data-type="string">Ad Accout Name</th>
                                        <th data-type="num">Reporting Starts</th>
                                        <th data-type="num">Reporting Ends</th>
                                        <th data-type="num">Accout Name</th>
                                        <th data-type="num">Country</th>
                                        <th data-type="num">Impressions</th>
                                        <th data-type="num">Clicks</th>
                                        <th data-type="num">CTR</th>
                                        <th data-type="num">Installs</th>
                                        <th data-type="num">Cost per Results</th>
                                        <th data-type="num">Result Indicator</th>
                                        <th data-type="num">Amount spend</th>
                                    </tr>
                                    <!-- <tr class="total" id="totalRow"></tr> -->
                                    </thead>
                                </table>


                                <!-- <tbody class="tbody"> -->
                                <div class="scroll-box">
                                    <table id="form-table1" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    @foreach($list as $k=>$v)
                                        <tr>
                                            <td>{{$v->account_id}}</td>
                                            <td>{{$v->ad_account_name}}</td>
                                            <td>{{$v->report_start}}</td>
                                            <td>{{$v->report_end}}</td>
                                            <td>{{$v->account_name}}</td>
                                            <td>{{$v->country_code}}</td>
                                            <td>{{$v->impressions}}</td>
                                            <td>{{$v->clicks}}</td>
                                            <td>{{$v->cpr}}</td>
                                            <td>{{$v->install}}</td>
                                            <td>{{$v->cpi}}</td>
                                            <td>{{$v->result_indicator}}</td>
                                            <td>{{$v->spend }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        
        $(".calendar1").flatpickr({
           dateFormat:"Y-m-d",
           enableTime:false,
           maxDate: "today"
           //更多配置百度搜索flatpickr即可找到
       	});
       	$(".calendar2").flatpickr({
           dateFormat:"Y-m-d",
           enableTime:false,
           maxDate: "today"
           //更多配置百度搜索flatpickr即可找到
       	});
        
       

        function del(id){
            myConfirm("删除操作不可逆,是否继续?",function(){
                myRequest("/admin/administrator/del/"+id,"post",{},function(res){
                    layer.msg(res.msg)
                    setTimeout(function(){
                        window.location.reload();
                    },1500)
                });
            });
        }

        function download(appid,date){
            var data = {appid:appid,date:date,gamename:gamename};//参数
            myRequest("/admin/report/download","post",data,function(res){
                layer.msg(res.msg)
                setTimeout(function(){
                    window.location.reload();
                },500)
            });
        }
        (function () {
        var tbody = document.querySelector('#form-table1').tBodies[0];
        var th = document.querySelector('#form-table').tHead.rows[0].cells;
        var td = tbody.rows;
        for (var i = 0; i < th.length; i++) {
            th[i].flag = 1;
            th[i].onclick = function () {
                sort(this.getAttribute('data-type'), this.flag, this.cellIndex);
                this.flag = -this.flag;
            };
        };
        function sort(str, flag, n) {
            var arr = [];
            for (var i = 0; i < td.length; i++) {
                arr.push(td[i]);
            };
            arr.sort(function (a, b) {
                return method(str, a.cells[n].innerHTML, b.cells[n].innerHTML) * flag;
            });
            for (var i = 0; i < arr.length; i++) {
                tbody.appendChild(arr[i]);
            };
        };
        function method(str, a, b) {
            switch (str) {
                case 'num':
                    return b - a;
                    break;
                case 'string':
                    return a.localeCompare(b);
                    break;
                case 'roi':
                    return b.replace("%","")/100 - a.replace("%","")/100;
                    break;
                default:
                    return new Date(a.split('-').join('/')).getTime() - new Date(b.split('-').join('/')).getTime();
            };
        };
    })();
    </script>
    <!-- 数据总计js -->
<script type="text/javascript">
    $(document).ready(function(){ 
      var totalRow3=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(3)').each(function(){ 
        totalRow3 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow4=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(4)').each(function(){ 
        totalRow4 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow5=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(5)').each(function(){ 
        totalRow5 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow6=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(6)').each(function(){ 
        totalRow6 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow7=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(7)').each(function(){ 
        totalRow7 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow8=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(8)').each(function(){ 
        totalRow8 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow9=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(9)').each(function(){ 
        totalRow9 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow10=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(10)').each(function(){ 
        totalRow10+= parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow11=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(11)').each(function(){ 
        totalRow11 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow12=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(12)').each(function(){ 
        totalRow12 += parseFloat($(this).text()); 
      }); 
    }); 
      var totalRow13=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(13)').each(function(){ 
        totalRow13 += parseFloat($(this).text()); 
      }); 
    }); 
      
    $('#totalRow').append('<td>Total</td><td>--</td><td>--</td><td>'+totalRow3+'</td><td>'+(totalRow5/totalRow3).toFixed(2)+'</td><td>'+totalRow5.toFixed(2)+'</td><td>'+totalRow6.toFixed(2)+'</td><td>'+totalRow7.toFixed(2)+'</td><td>'+totalRow8.toFixed(2)+'</td><td>'+totalRow9.toFixed(2)+'</td><td>'+totalRow10.toFixed(2)+'</td><td>'+totalRow11.toFixed(2)+'</td><td>'+(totalRow11-totalRow5).toFixed(2)+'</td><td>'+((totalRow11/totalRow5)*100).toFixed(2)+'%'+'</td>'); 
    }); 
</script>
@endsection
