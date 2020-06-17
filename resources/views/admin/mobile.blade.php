<!DOCTYPE html>
<html>

<head>
    <title>移动端报表</title>
    <link href="css/fixed_table_rc.css" type="text/css" rel="stylesheet" media="all" />
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/sortable_table.js" type="text/javascript"></script>
    <script src="js/fixed_table_rc.js" type="text/javascript"></script>






 <link rel="stylesheet" href="/assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="/assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" type="text/css" href="/assets/wangEditor/dist/css/wangEditor.min.css">
  <!-- endinject -->
  <!-- inject:css -->
  
  <!-- endinject -->
  <link rel="shortcut icon" href="/assets/images/favicon.png" />

  {{--datetimer--}}
  <link rel="stylesheet" id=cal_style type="text/css" href="/assets/datetimer/dist/flatpickr.min.css">
 

  <link href="https://unpkg.com/bootstrap-table@1.15.3/dist/bootstrap-table.min.css" rel="stylesheet">

 



    <style>
        html, body {
            font-family: Arial,鈥嬧€媠ans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        div.container {
            padding: 40px 120px; /*x8*/
        }
        
        .ft_container table tr th {
            background-color: #DBEAF9;
            
        }
        .dwrapper tr th{
        height:30px;
        text-align: center;
}
    </style>
    <script>
    $(function () {
    /*    $('#fixed_hdr2').fxdHdrCol({
            fixedCols: 2,
            width: "100%",
            height: 400,
            colModal: [
            { width: 50, align: 'center' },
            { width: 110, align: 'center' },
            { width: 170, align: 'left' },
            { width: 250, align: 'left' },
            { width: 100, align: 'left' },
            { width: 70, align: 'left' },
            { width: 100, align: 'left' },
            { width: 100, align: 'center' },
            { width: 90, align: 'left' },
            { width: 400, align: 'left' }
            ]
        });                    */                          
        $('#fixed_hdr3').fxdHdrCol({
            width: "100%",//x2
            height: 1050,//x2.5
            colModal: [{width: 52, align: 'center'},
            {width: 52, align: 'center'},
            {width: 52, align: 'center'},
            {width: 52, align: 'center'},
            {width: 52, align: 'center'},
            {width: 52, align: 'center'}
            ],
            sort: true
        }); 
    });
    </script>
</head>
<body>

<style>

    .red{
        background-color:#FF3333;
    }
    .green{
        background-color:#99FF33;
    }

    .red1{
        color:#FF3333;
    }

    
    .scroll-box{width: 100%;height: 350px;overflow: auto;overflow-x: scroll;}
    .scroll-box tr{width: 100%;height: 30px;}
    .scroll-box tr td{padding: 5px;}
    

</style>




<div>
<h2>Report</h2>
<div class="card-body">
    <div class="col-lg-3" >
                <form class="forms-sample">
                    <div class="form-group" style="width:20rem">
                        
                        <div class="input-group col-xs-3">
                            <input type="text" style="width: 6rem;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar1" @if($time1) value="{{$time1}}" @endif name="time1" placeholder="开始时间">
                            <input type="text" style="width: 6rem;height: calc(2.875rem );margin-right: 10px"  class="form-control required calendar2" @if($time2) value="{{$time2}}" @endif name="time2" placeholder="结束时间">
                            <select class="form-control form-control-lg" name="appid" style="width: 12rem;height: calc(2.875rem);margin-right:10px">
                                    <option value="">Select App</option>
                                @foreach($gamelist as $k=>$v)
                                    @if($v->status == 0)
                                    <option  @if($appid==$v->id) selected @endif value="{{$v->id}}" class="red1">{{$v->name}}(下架)</option>
                                    @else
                                    <option  @if($appid==$v->id) selected @endif value="{{$v->id}}">{{$v->name}}</option>
                                    @endif
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


 
<div class="dwrapper">
    <div class="scroll-box">

    <table id="fixed_hdr3">
        
    <thead>
    <tr><th>Date</th><th>Name</th><th>Profit</th><th>Spent</th><th>Revenue</th><th>ROI</th></tr>    

    <tbody>
        
        @php $spe=0; $rev=0; $ro=0; $sum=0;@endphp

        @foreach($list as $k=>$v)
        <tr>
            @if($appid) <td>@php echo date('Y-m-d',$v->date);@endphp</td> @else <td>@php echo date('m-d',strtotime($time1)).'~'.date('m-d',strtotime($time2));@endphp</td> @endif
            <td>{{$v->name}}</td>
            @php 
                $total = $v->fb_ad_network_revenue+$v->is_revenue+$v->mp_revenue+$v->al_revenue+$v->am_revenue; 
                
            @endphp
            <td>@php echo round($total-$v->cost,2); @endphp</td>
            
            <td>{{isset($v->cost) ?$v->cost : '0.00'}}</td>
            
            <td>
                @php echo $total; @endphp
            </td>
            @php 
                if($total!=0&&$v->cost!=0){
                    $roi = round($total/$v->cost,2)*100;
                }else{
                    $roi = 0; 
                } 
            @endphp 
            @if($roi <= 100) 
                <td class="red">@php echo $roi.'%';@endphp</td>
            @else
                <td class="green">@php echo $roi.'%';@endphp</td>
            @endif

            @php  $spe=$v->cost+$spe; @endphp
            @php  $rev=$total+$rev; @endphp
            @php  $ro=$roi+$ro; $sum=$sum+1;@endphp
        </tr>

        @endforeach
        @php $pro=$rev-$spe; @endphp 
        <tbody>  
            <tr><th>Total</th><th>---</th><th>@php echo $pro; @endphp</th><th>@php echo $spe; @endphp</th><th>@php echo $rev; @endphp</th><th>@php echo round($ro/$sum,2).'%'; @endphp</th></tr>
        </tbody>
           
    </tbody>

    </thead>
  
    </table>

</div>
    </div>
            <div class="box-footer clearfix">
                总共 <b>{{ $list->total()  }}</b> 条,分为<b>{{ $list->lastPage() }}</b>页
                {!! $list->links() !!}
            </div>
</div>
</div>
</div>
<!--
<script type="text/javascript"> 
     var url = window.location.pathname;
     var pcurl="/mobile";
     if(/AppleWebKit.*Mobile/i.test(navigator.userAgent)==false || (/MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/.test(navigator.userAgent))==false){ 
         if(window.location.href.indexOf("?mobile")<0){ 
             try{ 
              if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)==false){ 
                  window.location.href=pcurl;
              } 
         }catch(e){}
      }
    }
</script>
-->
</body>

</html>