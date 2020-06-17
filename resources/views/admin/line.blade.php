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
            Trend Line Chart
        </h3>
       
    </div>

    <div>
        <h3 style="text-align:center;padding-bottom: 2px">Total Report</h3>
        <div class="col-lg-3" style="float: right">
                <form class="forms-sample">
                    <div class="form-group" style="width:100%">
                        <div class="input-group col-xs-3">
                            <input type="text" style="width: 25%;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar1" name="time1" @if($time1) value="{{$time1}}" @endif placeholder="开始时间">
                            <input type="text" style="width: 25%;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar2" name="time2" @if($time2) value="{{$time2}}" @endif placeholder="结束时间">
                            <select class="form-control form-control-lg" name="appid" style="width: 15%;height: calc(2.875rem + 2px);margin-right: 20px">
                                @foreach($gamelist as $k=>$v)
                                    <option  @if($appid==$v->id) selected @endif value="{{$v->id}}">{{$v->name}}</option>
                                @endforeach
                            </select>
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
    <div id="total" style="width: 100%;height:400px;"></div>
    <div style="padding-top: 20px">
        <h3 style="text-align:center;">App Report----{{$appname}}</h3>
        <div id="main" style="width: 100%;height:300px;padding-top:10px"></div>
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
        // 基于准备好的dom，初始化echarts实例
        var total = echarts.init(document.getElementById('total'));

        option1 = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data:['Profit','Admob','FAN','Spend','Revenue','Roi']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: @php echo $datearray;@endphp
            },
            yAxis: {
                type: 'value',
                        
                        

            },
            series: [
                {
                    name:'Profit',
                    type:'line',
                    data:@php echo $profit;@endphp
                },
                {
                    name:'Admob',
                    type:'line',
                    data:@php echo $am;@endphp
                },
                {
                    name:'FAN',
                    type:'line',
                    data:@php echo $fb;@endphp
                },
                {
                    name:'Spend',
                    type:'line',
                    data:@php echo $spend;@endphp
                },
                {
                    name:'Revenue',
                    type:'line',
                    data:@php echo $revenue;@endphp
                },
                {
                    name:'Roi',
                    type:'line',
                    data:@php echo $roi;@endphp
                }
            ]
        };

        // 使用刚指定的配置项和数据显示图表。
        total.setOption(option1);
    </script>




    
    <script type="text/javascript">
       
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('main'));

        option = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data:['Profit','Admob','FAN','Spend','Revenue','Roi']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: @php echo $datearray;@endphp
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name:'Profit',
                    type:'line',
                    data:@php echo $appprofit;@endphp
                },
                {
                    name:'Admob',
                    type:'line',
                    data:@php echo $appam;@endphp
                },
                {
                    name:'FAN',
                    type:'line',
                    data:@php echo $appfb;@endphp
                },
                {
                    name:'Spend',
                    type:'line',
                    data:@php echo $appspend;@endphp
                },
                {
                    name:'Revenue',
                    type:'line',
                    data:@php echo $apprevenue;@endphp
                },
                {
                    name:'Roi',
                    type:'line',
                    data:@php echo $approi;@endphp
                }
            ]
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    </script>
    



</div>
@endsection


