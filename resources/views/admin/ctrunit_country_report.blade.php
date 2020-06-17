@extends('base.base')
@section('base')
<script type="text/javascript" src="http://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="http://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="http://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
        .scroll-box{width: 100%;height: 550px;overflow: auto;overflow-x:hidden;}
        .scroll-box tr{width: 100%;height: 40px;line-height: 20px;}
        .scroll-box tr td{padding: 5px;}
        /*.scroll-box tr:nth-child(odd) td{background-color: #ECE9D8;}*/
        .tip{margin: 0 auto;text-align: center;color: red;line-height: 150%;font-size: 14px;}
        .head td{font-size:16px;font-weight:bold;padding:15px 15px;}
        .time1 td{
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
                    Country CTR Report
                </h3>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">

                            <div class="col-lg-3">
                                <form class="forms-sample">
                                    <div class="form-group" style="width: 1000px">
                                        <div class="input-group col-xs-3">
<input type="text"  readonly style="width: 10rem;height: calc(2.875rem );margin-right: 20px"  class="form-control" name="date" id="demo" value="{{$time1}}  {{$time2}}" data-time1="{{$time1}}" data-time2="{{$time2}}"/>
                            <script type="text/javascript">
                                var time_range = function (beginTime, endTime) {
                                var strb = beginTime.split (":");
                                if (strb.length != 2) {
                                      return false;
                                  }
                              
                                  var stre = endTime.split (":");
                                  if (stre.length != 2) {
                                      return false;
                                 }
                             
                                 var b = new Date ();
                                 var e = new Date ();
                                 var n = new Date ();
                             
                                 b.setHours (strb[0]);
                                 b.setMinutes (strb[1]);
                                 e.setHours (stre[0]);
                                 e.setMinutes (stre[1]);
                             
                                 if (n.getTime () - b.getTime () > 0 && n.getTime () - e.getTime () < 0) {
                                     return true;
                                 } else {
                                     // alert ("当前时间是：" + n.getHours () + ":" + n.getMinutes () + "，不在该时间范围内！");
                                     return false;
                                 }
                             }
                                console.log(time_range ("15:00", "24:00"));
                                if(time_range ("15:00", "24:00")){
                                    $('input[name="date"]').daterangepicker();
                                    var div = document.getElementById("demo");
                                    var start = div.dataset.time1;//获取data-appid的值
                                    var end = div.dataset.time2;//获取data-myname的值
                                    // alert(time1,time2);



                                    function cb(start, end) {
                                        $('#demo').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                                    }

                                    $('#demo').daterangepicker({
                                        "maxSpan": {
                                            "days": 7
                                        },
                                        ranges: {
                                            'Today': [moment(), moment()],
                                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                                        },
                                        "alwaysShowCalendars": true,
                                        "startDate": start,
                                        "endDate": end,
                                    }, function(start, end, label) {
                                      console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
                                    });
                                }else{
                                    $('input[name="date"]').daterangepicker();
                                    var div = document.getElementById("demo");
                                    var start = div.dataset.time1;//获取data-appid的值
                                    var end = div.dataset.time2;//获取data-myname的值
                                    $('#demo').daterangepicker({
                                        "maxSpan": {
                                            "days": 7
                                        },
                                        ranges: {
                                            'Today': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                            'Yesterday': [moment().subtract(2, 'days'), moment().subtract(2, 'days')],
                                            'Last 7 Days': [moment().subtract(7, 'days'),moment().subtract(1, 'days')],
                                            'Last 30 Days': [moment().subtract(30, 'days'), moment().subtract(1, 'days')],
                                            'This Month': [moment().startOf('month'), moment().subtract(1, 'days')],
                                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                                        },
                                        "alwaysShowCalendars": true,
                                        "startDate": start,
                                        "endDate": end,
                                        
                                    });
                                }
                                    </script>
                                            <select class="form-control form-control-lg" name="appid" style="width: 100px;height: calc(2.875rem + 2px);margin-right: 20px" >
                                                <option value="App Search">App Search</option>

                                                @foreach($gamelist as $k=>$v)
                                                    <option  @if($appid==$v->id) selected @endif value="{{$v->id}}">{{$v->name}}</option>
                                                @endforeach
                                            </select>
                                            <select class="form-control form-control-lg" name="unit_name" style="width: 100px;height: calc(2.875rem + 2px);margin-right: 20px" >
                                                <!-- <option value="App Search">Unit Name</option> -->
                                                <option  @if($unit_name === 'Native-sp-main') selected @endif value="Native-sp-main">Native-sp-main</option>
                                                <option  @if($unit_name == 'Interstitial') selected @endif value="Interstitial">Interstitial</option>
                                            </select>
                                            <select class="form-control form-control-lg" name="countryid" style="width: 100px;height: calc(2.875rem + 2px);margin-right: 20px">
                                                    <option value="">Country Search</option>
                                                    <option value="nanya">南亚次大陆</option>
                                                    <option value="beiou">北欧</option>
                                                    <option value="jialebi">加勒比</option>
                                                    <option value="lamei">拉美</option>
                                                    <option value="laomei">老美</option>
                                                    <option value="xiou">西欧</option>
                                                    <option value="nanou">南欧</option>
                                                    <option value="dongou">东欧</option>

                                                @foreach($countrylist as $k=>$v)
                                                    <option  @if($countryid==$v->code_2) selected @endif value="{{$v->code_2}}">{{$v->code_2}}({{$v->english}})</option>
                                                @endforeach
                                            </select>
                                            
                                            <span class="input-group-append" >
                                                <button type="submit" class="btn btn-sm btn-gradient-primary"><i class="mdi mdi-account-search btn-icon-prepend"></i>
                                                    搜索
                                                </button>
                                            </span>   

                                        </div>
                                        
                                    </div>

                                </form>
                            </div>
                            
                            <!-- <table class="table table-bordered" > -->
                                <table id="form-table" cellpadding="0" border="0" cellspacing="0">
                                    <thead>
                                    <tr class="head">
                                        <th>Date</th>
                                        <th data-type="string">Country Code</th>
                                        <th data-type="num">Unit Name</th>
                                        <th data-type="num">Request</th>
                                        <th data-type="num">Impressions</th>
                                        <th data-type="num">Clicks</th>
                                        <th data-type="roi">CTR</th>
                                       
                                    </tr>
                                    <tr class="total" id="totalRow"></tr>
                                    </thead>
                                </table>


                                <!-- <tbody class="tbody"> -->
                                <div class="scroll-box">
                                    <table id="form-table1" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    @foreach($list as $k=>$v)
                                        <tr>
                                            <td>@php echo date('Y-m-d',$v->date);@endphp</td>
                                            <td>{{$v->country_code}}</td>
                                            <td>{{$v->unit_name}}</td>
                                            <td>{{$v->request}}</td>
                                            <td>{{$v->impressions}}</td>
                                            <td>{{$v->clicks}}</td>
                                            @php 
                                                $ctr = $v->ctr*100;
                                                $request = $v->request; 
                                            @endphp 
                                            @if($ctr >= 15 && $request > 100) 
                                                <td class="red">@php echo $ctr.'%';@endphp</td>
                                            @else
                                                <td class="green">@php echo $ctr.'%';@endphp</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" value='{{ $list->total()}}' name="total" id="total" />  
                            <div class="box-footer clearfix">
                                总共 <b>{{ $list->total()}}</b> 条,分为<b>{{ $list->lastPage() }}</b>页
                                {!! $list->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        

       

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
    var total = document.getElementById("total").value;
    console.log(total)
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
      
    $('#totalRow').append('<td>Total</td><td>--</td><td>--</td><td>'+totalRow3+'</td><td>'+totalRow4+'</td><td>'+totalRow5+'</td><td>'+((totalRow5/totalRow4)*100).toFixed(2)+'%'+'</td>'); 
    }); 
</script>


@endsection