@extends('base.base')
@section('base')
<div class="main-panel">
    <div class="page-header">
        <h3 class="page-title">
            <a href="javascript:location.replace(location.href);" title="refresh">
                <span class="page-title-icon bg-gradient-primary text-white mr-2">
                    <i style="color:white"class="mdi mdi-cached"></i>
                </span>
            </a>
            App Trend Line Chart
        </h3>
       
    </div>
    <div>
        <div class="col-lg-3" style="float: right">
                <form class="forms-sample">
                    <div class="form-group" style="width:100%">
                        <div class="input-group col-xs-3">
                            <input type="text" style="width: 25%;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar1" name="time1" @if($time1) value="{{$time1}}" @endif placeholder="开始时间">
                            <input type="text" style="width: 25%;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar2" name="time2" @if($time2) value="{{$time2}}" @endif placeholder="结束时间">
                            <span class="input-group-append" >
                                <button type="submit" class=" btn btn-sm btn-gradient-primary"><i class="mdi mdi-account-search btn-icon-prepend"></i>
                                    搜索
                                </button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
    </div>

    <script src='/assets/js/echarts.js'></script>

    <input id="array1" value="{{json_encode($apptotalprofit,TRUE)}}" type="hidden">


    @foreach($apptotalprofit as $k=>$v)
        <div style="padding-top: 20px">
            <input id="array" value="{{json_encode($v,TRUE)}}" type="hidden">
            @foreach($gamelist as $k1 => $v1)
                @if($k1==$k) <h3 style="text-align:center;">App Line Chart---{{$v1->name}}</h3>  @endif
            @endforeach
            <div id="main_{{$k}}" style="width: 100%;height:300px;padding-top:10px"></div>
        </div>
    @endforeach

</div>

<script type="text/javascript">
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
        $(document).ready(function(){
            var obj = JSON.parse(document.getElementById('array1').value);
            for(var i=0;i<obj.length;i++){
                // 基于准备好的dom，初始化echarts实例
                var barChart_show = echarts.init(document.getElementById('main_'+i));
                 // 指定图表的配置项和数据              
                var bar_option_show = {
                    color: ['#3398DB'],
                    tooltip: {
                        trigger: 'axis',
                    },
                    legend: {
                        data:['Profit']
                    },
                    grid: {  
                        bottom:'10%'//显示数据距离X轴的高度
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: @php echo $datearray;@endphp
                    },
                    
                    yAxis: {
                        type: 'value',
                    },
                    series: [{
                        name: 'Profit',
                        type: 'line',
                        data:obj[i]
                    }]
                };
                // 使用刚指定的配置项和数据显示图表。
                barChart_show.setOption(bar_option_show);

            }
        }) 
    </script>
@endsection


