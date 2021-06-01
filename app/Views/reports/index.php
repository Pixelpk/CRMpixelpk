<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1> <?php echo app_lang('reports'); ?></h1>
            
        </div>
        <br>
        <div class="page-body container-fluid">
            <form action="javascript:void(0);" method="POST" id="report-form">
              <div class="row">
                <div class="col-md-6 offset-md-2">
                    <label for="project_id">Select Project</label>
                    <select class="custom-select form-control" id="project_id" name="project_id">
                      <option value="" selected>Select Project</option>
                        <?php foreach($projects as $project):?>
                            <option  value="<?php  echo $project->id;?>"><?php echo $project->title;?></option>
                        <?php endforeach; ?>
                       </select>
                </div>
                <!-- <div class="col">
                    <label>Select TeamMember</label>
                    <select class="custom-select form-control" id="project_id" name="project_id">
                      <option selected>Open this select menu</option>
                      <option value="1">One</option>
                      <option value="2">Two</option>
                      <option value="3">Three</option>
                    </select>
                    <small>Leave Blank for all team member</small>
                </div>
                 --><div class="col-md-2">
                    <button type="submit" id="form-sbmit-btn" class="btn btn-primary" style="margin-top: 25px">View Report</button>
                </div>
              </div>
                </form>
            <hr style="margin-bottom: 0px;">
            <div id="project-detail" style="display: none;">
                <div class="card">
                  <div class="card-header text-center">
                    <h3 id="project-name" style="font-weight: bold; color:#dc3545;"></h3>
                    <p id="project-cost" style="font-weight: bold;"></p>
                    <p id="project-earn"></p>
                    <p id="project-hours"></p>
                  
                  </div>
                  <div class="card-body">
                    <div class="row" id="members">
                      
                  </div>
                  
                </div>
               
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
       $('#form-sbmit-btn').on('click', function(e){
            e.preventDefault();
           var project_id = $("#project_id").val();
           if(project_id != ''){
            $.ajax({
               url: "<?php echo base_url()?>/index.php/reports/detail",
               type: "POST",
               data: {
                project_id : project_id
                
                },
               success: function(data) {
                console.log(data);
                var data = JSON.parse(data);
                //console.log(data);
                $('#project-detail').css('display','block');
                $('#project-name').text(data.project_detail['title']);
                $('#project-cost').text('Client is paying us : ' + data.project_detail['price']);
                $('#project-earn').text('Pixelpk Paid to Dev ' + totalProfitWithHourPay);
                
                //PerHourPay is Cost  & PerHourRate is Actual Cost
                $('#members').empty();
                var projectCost = data.project_detail['price'];
                var totalWorkingHours = 0;
                var totalProjectPerHourPayCost = 0;
                var totalProjectPerHourRateCost = 0;
                var members = 0;
                jQuery.each(data.members, function(key, value){
                    var perHourPay = (value['per_hour_pay'] != null) ? value['per_hour_pay'] : 0;
                    var perHourRate = (value['per_hour_rate'] != null) ? value['per_hour_rate'] : 0;
                    ++members;
                    var total_hours = 0;
                    jQuery.each(value['time'], function(k, v){
                        if(v['status'] != 'open'){
                            var start_time = Date.parse(v['start_time']);
                            var end_time = Date.parse(v['end_time']);
                            if(start_time < end_time){
                                var seconds = Math.floor((end_time - (start_time)) / 1000);
                                var minutes = Math.floor(seconds / 60);
                                var hours = Math.floor(minutes / 60);
                                hours = (hours == 0) ? 1 : hours;
                                console.log(hours);
                                total_hours += hours;
                                totalWorkingHours += hours;
                            }
                        }
                    });
                    var totalPerHourPayCost = perHourPay * total_hours;
                    totalProjectPerHourPayCost += totalPerHourPayCost;
                    var totalPerHourRateCost = perHourRate * total_hours;
                    totalProjectPerHourRateCost += totalPerHourRateCost;
                    var profitWithHourPay = data.project_detail['price'] - totalPerHourPayCost;
                    var profitWithHourRate = data.project_detail['price'] - totalPerHourRateCost;
                    $('#members').append('<div class="col-sm-6"><div class="card" style="border: 1px solid #EEF1F9"><div class="card-body"><h5 class="card-title">'+value['first_name']+' '+value['last_name']+'</h5><p class="card-text">Total Working Hours On Project: '+total_hours+'</p><p class="card-text">Per Hour Pay Cost: '+perHourPay*total_hours+'</p><p>Profit Lose With Per Hour Pay Cost : '+profitWithHourPay+'</p><p class="card-text">Per Hour Rate Cost : '+perHourRate * total_hours+'</p><p>Profit Lose With Per Hour Rate Cost : '+profitWithHourRate+'</p></div></div></div>');
                });  
                //total project Report
                        console.log(members);
                    var totalProfitWithHourRate = projectCost - totalProjectPerHourRateCost;
                    var totalProfitWithHourPay = projectCost - totalProjectPerHourPayCost;
                    var perResourceOfficeRent = 60;
                    var OfficeExpense = totalWorkingHours + perResourceOfficeRent;
                    var pixelcost = totalProfitWithHourPay + OfficeExpense;
                    $('#project-earn').text('Project cost beared by pixelpk : ' + totalProfitWithHourPay);
                    $('#project-hours').text('Total Hours invested by Pixelpk :  ' + totalWorkingHours);
                    
                $('#members').append('<div class="col-sm-12"><div class="card" style="border: 1px solid #EEF1F9"><div class="card-body"><h5 class="card-title text-center">Overall Project Report</h5><p class="card-text">Total Working Hours On Project: '+totalWorkingHours+'</p><p class="card-text">Per Hour Pay Total Cost: '+totalProjectPerHourPayCost+'</p><p>Total Profit Lose With Per Hour Pay Cost : '+totalProfitWithHourPay+'</p><p class="card-text">Per Hour Rate Total Cost : '+totalProjectPerHourRateCost+' </p><p>Total Profit Lose With Per Hour Rate Cost : '+totalProfitWithHourRate+'</p></div></div></div>');
               },
               
               error: function(data) {
                console.log(data);
                  alert('Something is wrong');
               },
            });
           }
            

       });
           });
</script>