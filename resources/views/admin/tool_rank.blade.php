@extends('base.base')
@section('base')

    <style>
        *{margin:0;padding:0;}
        .container{border: 1px #ccc solid;width: 90%;height:100;margin: 100px auto;}
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
                    Googl Play工具类应用免费下载排行榜
                </h3>
                <nav aria-label="breadcrumb">
                    <p class="card-description">
                        <a onclick="uploadxls()" class="btn btn-sm btn-gradient-success btn-icon-text">上传表格</a>
                    </p>
                    <!-- <p class="card-description">
                        <a href="/admin/report/apprank" class="btn btn-sm btn-gradient-success btn-icon-text">更新</a>
                    </p> -->
                </nav>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">

                            <div class="col-lg-3">
                                <form class="forms-sample">
                                    <div class="form-group" style="width: 1000px">
                                        <div class="input-group col-xs-3">

                                            <input type="text" style="width: 110px;height: calc(2.875rem + 2px);margin-right: 20px"  class="form-control required calendar1"  value="{{$today}}" name="today" placeholder="按时间搜索">
                                            
                                        
                                            <select class="form-control form-control-lg" name="qushi" style="width: 150px;height: calc(2.875rem + 2px);margin-right: 20px">
                                                    <option value="">按排名趋势</option>
                                                    <option value="up">上升</option>
                                                    <option value="down">下降</option>
                                            </select>
                                            <!-- <select class="form-control form-control-lg" name="category" style="width: 150px;height: calc(2.875rem + 2px);margin-right: 20px">
                                                <option value="">按分类搜索</option>
                                                @foreach($catedata as $k=>$v)
                                                    <option  @if($category) value="{{$category}}" @endif >{{$v->category}}</option>
                                                @endforeach
                                            </select> -->
                                            <select class="form-control form-control-lg" name="rankcontinu" style="width: 150px;height: calc(2.875rem + 2px);margin-right: 20px">
                                                    <option value="">按新进榜单</option>
                                                    <option value="one">今日新进榜单</option>
                                                    <!-- <option value="three">连续三天出现</option>
                                                    <option value="twoup">排名连续两天上升</option>
                                                    <option value="threeup">排名连续三天上升</option> -->
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
                            
                              <table id="form-table" cellpadding="0" border="0" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>排名</th>
                                    <th>日期</th>
                                    <th>应用名称</th>
                                    <th>开发公司</th>
                                    <th>变化形式</th>
                                    <th data-type="num">变化数</th>
                                    <th>分类</th>
                                    <th data-type="num">评分</th>
                                    <th>上架时间</th>
                                    <th>更新时间</th>
                                </tr>
                                <!-- <tr id='totalRow'></tr> -->
                                </thead>
                                </table>

                                <table id="form-table1" cellpadding="0" cellspacing="0">
                                <tbody>
                                @foreach($list as $k=>$v)
                                    <tr>
                                        <td>{{ $v->range }}</td>
                                        <td>{{ $v->date }}</td>
                                        <td>{{ $v->app_name }}</td>
                                        <td>{{ $v->company }}</td>

                                        @if($v->range_change == '下降') 
                                            <td class="red">@php echo $v->range_change;@endphp</td>
                                        @elseif($v->range_change == '上升')
                                            <td class="green">@php echo $v->range_change;@endphp</td>
                                        @else
                                            <td>@php echo '不变';@endphp</td>
                                        @endif

                                        @if($v->range_change == '下降') 
                                            <td class="red">@php echo $v->range_change_num;@endphp</td>
                                        @elseif($v->range_change == '上升')
                                            <td class="green">@php echo $v->range_change_num;@endphp</td>
                                        @else
                                            <td>@php echo $v->range_change_num;@endphp</td>
                                        @endif

                                        <td>{{ $v->category }}</td>
                                        <td>{{ $v->star }}</td>
                                        <td>{{ $v->create_date }}</td>
                                        <td>{{ $v->update_date }}</td>
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

        function uploadxls(){
            layer.open({
                type: 2,
                title: '上传表格',
                shadeClose: true,
                shade: 0.8,
                area: ['50%', '70%'],
                content: '/admin/report/uploadtoolxls'
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
      var totalRow2=0 
      $('#form-table1 tr').each(function(){ 
        $(this).find('td:eq(2)').each(function(){ 
        totalRow2 += parseFloat($(this).text()); 
      }); 
    }); 
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
    $('#totalRow').append('<td>Total</td><td>--</td><td>'+totalRow2.toFixed(0)+'</td><td>'+totalRow3.toFixed(0)+'</td><td>'+totalRow4.toFixed(0)+'</td><td>'+totalRow5.toFixed(0)+'</td><td>'+totalRow6.toFixed(0)+'</td><td>'+totalRow7.toFixed(0)+'</td>'); 
    }); 
</script>
@endsection
