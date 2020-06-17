
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
                    FB Spend Report
                </h3>
                <nav aria-label="breadcrumb"> 
                    <p class="card-description">
                        <a href="/admin/report/downloadadjx" class="btn btn-sm btn-gradient-success btn-icon-text">Export Excel(All Data)</a>
                    </p>
                </nav>
                
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-lg-3">
                                <form class="forms-sample">
                                    <?php echo csrf_field(); ?>
                                    <div class="form-group" style="width: 520px">
                                        
                                        <div class="input-group col-xs-3">

                                            <select class="form-control form-control-lg" name="month" style="width: 100px;height: calc(2.875rem + 2px);margin-right: 20px" >
                                                <option value="Month Search">Month Search</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                            </select>
                                            <br>
                                            <select class="form-control form-control-lg" name="company" style="width: 100px;height: calc(2.875rem + 2px);margin-right: 20px" >
                                                <option value="Company Search">Company Search</option>
                                                <option value="PY">PY</option>
                                                <option value="MAD">MAD</option>
                                                <option value="MS">MS</option>
                                                <option value="PM">PM</option>
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
                            
                            
                            <!-- <table class="table table-bordered" > -->
                                <table id="form-table" cellpadding="0" border="0" cellspacing="0">
                                    <thead>
                                    <tr class="head">
                                        <th data-type="string">Company</th>
                                        <th data-type="string">Month</th>
                                        <th data-type="num">Account ID</th>
                                        <th data-type="num">FB Spend</th>
                                        <th data-type="num">Own Spend</th>
                                        <th data-type="num">Difference</th>
                                        
                                    </tr>
                                    <!-- <tr class="total" id="totalRow"></tr> -->
                                    </thead>
                                </table>


                                <!-- <tbody class="tbody"> -->
                                <div class="scroll-box">
                                    <table id="form-table1" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    <?php $__currentLoopData = $spenddata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($v->company); ?></td>
                                            <td><?php echo e($v->month); ?></td>
                                            <td><?php echo e($v->act_id); ?></td>
                                            <td><?php echo e($v->fb_spend); ?></td>
                                            <td><?php echo e($v->own_spend); ?></td>
                                            <td><?php echo e($v->difference); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="box-footer clearfix">
                                总共 <b><?php echo e($spenddata->total()); ?></b> 条,分为<b><?php echo e($spenddata->lastPage()); ?></b>页
                                <?php echo $spenddata->links(); ?>

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('base.base', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>