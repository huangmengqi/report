
<!-- 引入样式 -->
<link rel="stylesheet" type="text/css" href="//raw.githack.com/hnzzmsf/layui-formSelects/master/dist/formSelects-v4.css"/>
<!-- <link rel="stylesheet" href="//res.layui.com/layui/dist/css/layui.css"  media="all"> -->
<!-- 引入jquery依赖 -->
<script src="//unpkg.com/jquery@3.3.1/dist/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<!-- 引入组件 -->
<script src="//raw.githack.com/hnzzmsf/layui-formSelects/master/dist/formSelects-v4.js" type="text/javascript" charset="utf-8"></script>
<?php $__env->startSection('base'); ?>
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
                    Admob Ecpm Comparison
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
                                            <input type="text" style="width: 50px;height: calc(2.875rem + 2px);margin-right: 20px;flex: 0.5"  class="form-control required calendar1" <?php if($today): ?> value="<?php echo e($today); ?>" <?php endif; ?> name="today" >
                                            <input type="text" style="width: 50px;height: calc(2.875rem + 2px);margin-right: 20px;flex: 0.5"  class="form-control required calendar1" <?php if($time2): ?> value="<?php echo e($time2); ?>" <?php endif; ?> name="time2" >
                                            <select style="width: 50px;height: calc(2.875rem + 2px);margin-left: 20px" xm-select-height="38px" name="game" xm-select-placeholder="请选择应用" xm-select-search xm-select="selectId">
                                                <?php $__currentLoopData = $gamelisttotal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option  <?php if(in_array($v->id,$gameid)): ?> selected <?php endif; ?> value="<?php echo e($v->id); ?>"><?php echo e($v->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <!-- <option value="1" disabled="disabled">北京</option>
                                                <option value="2" selected="selected">上海</option>
                                                <option value="3">广州</option>
                                                <option value="4" selected="selected">深圳</option>
                                                <option value="5">天津</option> -->
                                            </select>
                                            <select style="width: 50px;height: calc(2.875rem + 2px);margin-left: 20px" xm-select-height="38px" name="country" xm-select-placeholder="请选择国家" xm-select-search xm-select="selectId1">
                                                <?php $__currentLoopData = $countrylist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option  <?php if(in_array($value->code_2,$countryid)): ?> selected <?php endif; ?> value="<?php echo e($value->code_2); ?>"><?php echo e($value->code_2); ?></option>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <select class="form-control form-control-lg" name="top" style="width: 100px;height: calc(2.875rem + 2px);margin-right: 20px">
                                                <option value="">请选择排名范围--空</option>
                                                <option  <?php if($top=='10'): ?> selected <?php endif; ?> value="10">默认前10条，不选请选空</option>
                                                <option  <?php if($top=='30'): ?> selected <?php endif; ?> value="30">top30</option>
                                                <option  <?php if($top=='50'): ?> selected <?php endif; ?> value="50">top50</option>
                                            </select>
                                            <span style="margin-left: 20px;" class="input-group-append">
                                                <button  type="submit" class="btn btn-sm btn-gradient-primary"><i class="mdi mdi-account-search btn-icon-prepend" οnclick="submit();" id="crop"></i>
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
                                        <th>Country</th>
                                        <?php $__currentLoopData = $gamelist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k1=>$v1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <th  data-type="num" colspan="3"><?php echo e($v1); ?></th>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                    <!-- <tr class="total" id="totalRow"></tr> -->
                                    </thead>
                                </table>


                                <!-- <tbody class="tbody"> -->
                                <div class="scroll-box">
                                    <table id="form-table1" cellpadding="0" cellspacing="0">
                                    <tbody>                                       
                                             
                                        <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k2=>$v2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                          <?php  $i=0; ?>
                                        <tr>   
                                            
                                                <td> <?php echo e($countryid[$k2]); ?> </td>
                                            
                                                
                                                <?php for($j=0;$j<count($v2);$j++): ?>
                                                    
                                                <?php
                                                    if($v2[$j]->name==$gamelist[$i]){ 
                                                    $a=1;$i=$i+1;
                                                    }else{
                                                    $a=0;
                                                    $j=$j-1;                                                    
                                                    $i=$i+1;
                                                }
                                                ?>
                                                 <?php if($a==1): ?>
                                                <td>
                                                    <?php echo e($v2[$j]->am_revenue); ?>

                                                    
                                                </td>
                                                <td><span style="color: green"><?php echo e(isset($v2[$j]->am_cpm) ?$v2[$j]->am_cpm : '0.00'); ?></span></td>
                                                <td>
                                                    <span style="color: green">
                                                    <?php 
                                                        if($v2[$j]->install_count!=0&&$v2[$j]->cost!=0){
                                                            echo round($v2[$j]->cost/$v2[$j]->install_count,2); 
                                                        }else{
                                                            echo 0.00; 
                                                        }
                                                    ?>
                                                    </span>
                                                </td>
                                                    
                                                    <?php endif; ?>
                                                    <?php if($a==0): ?>
                                                        
                                                       <td>0.00</td>
                                                       <td>0.00</td>
                                                       <td>0.00</td>
                                                    
                                                    <?php endif; ?>

                                                    

                                                       
                                                <?php endfor; ?>
                                            
                                            
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script type="text/javascript">
        formSelects.render('selectId');
         formSelects.render('selectId1');
    </script>
    <script>

        function submit(){
         var crop=document.getElementById('crop');
         crop.disabled=true;
         document.fm.submit();
         setTimeout("crop.disabled=false;",3000); //代码核心在这里，3秒还原按钮代码
        }

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('base.base', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>