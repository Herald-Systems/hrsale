<?php
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\SystemModel;
use App\Models\ConstantsModel;
use App\Models\TrackgoalsModel;
//$encrypter = \Config\Services::encrypter();
$SystemModel = new SystemModel();
$RolesModel = new RolesModel();
$UsersModel = new UsersModel();
$ConstantsModel = new ConstantsModel();
$TrackgoalsModel = new TrackgoalsModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
if($user_info['user_type'] == 'staff'){
	$all_goals_not_started = $TrackgoalsModel->where('company_id',$user_info['company_id'])->where('goal_status',0)->orderBy('tracking_id', 'ASC')->findAll();
	$all_goals_inprogress = $TrackgoalsModel->where('company_id',$user_info['company_id'])->where('goal_status',1)->orderBy('tracking_id', 'ASC')->findAll();
	$all_goals_completed = $TrackgoalsModel->where('company_id',$user_info['company_id'])->where('goal_status',2)->orderBy('tracking_id', 'ASC')->findAll();
} else {
	$all_goals_not_started = $TrackgoalsModel->where('company_id',$usession['sup_user_id'])->where('goal_status',0)->orderBy('tracking_id', 'ASC')->findAll();
	$all_goals_inprogress = $TrackgoalsModel->where('company_id',$usession['sup_user_id'])->where('goal_status',1)->orderBy('tracking_id', 'ASC')->findAll();
	$all_goals_completed = $TrackgoalsModel->where('company_id',$usession['sup_user_id'])->where('goal_status',2)->orderBy('tracking_id', 'ASC')->findAll();
}
?>
<?php
$events_date = date('Y-m');
?>
<script type="text/javascript">
$(document).ready(function(){
	
	/* initialize the calendar
	-----------------------------------------------------------------*/
	$('#calendar_hr').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay,listWeek'
		},
		/*views: {
			listDay: { buttonText: 'list day' },
			listWeek: { buttonText: 'list week' }
		  },*/
		//defaultView: 'agendaWeek',
		themeSystem: 'bootstrap4',
		/*bootstrapFontAwesome: {
		  close: ' ion ion-md-close',
		  prev: ' ion ion-ios-arrow-back scaleX--1-rtl',
		  next: ' ion ion-ios-arrow-forward scaleX--1-rtl',
		  prevYear: ' ion ion-ios-arrow-dropleft-circle scaleX--1-rtl',
		  nextYear: ' ion ion-ios-arrow-dropright-circle scaleX--1-rtl'
		},*/
		  
		eventRender: function(event, element) {
		element.attr('title',event.title).tooltip();
		element.attr('href', event.urllink);
		
		},
		dayClick: function(date, jsEvent, view) {
        date_last_clicked = $(this);
			var event_date = date.format();
			$('#exact_date').val(event_date);
			var eventInfo = $("#module-opt");
            var mousex = jsEvent.pageX + 20; //Get X coodrinates
            var mousey = jsEvent.pageY + 20; //Get Y coordinates
            var tipWidth = eventInfo.width(); //Find width of tooltip
            var tipHeight = eventInfo.height(); //Find height of tooltip

            //Distance of element from the right edge of viewport
            var tipVisX = $(window).width() - (mousex + tipWidth);
            //Distance of element from the bottom of viewport
            var tipVisY = $(window).height() - (mousey + tipHeight);

            if (tipVisX < 20) { //If tooltip exceeds the X coordinate of viewport
                mousex = jsEvent.pageX - tipWidth - 20;
            } if (tipVisY < 20) { //If tooltip exceeds the Y coordinate of viewport
                mousey = jsEvent.pageY - tipHeight - 20;
            }
            //Absolute position the tooltip according to mouse position
            eventInfo.css({ top: mousey, left: mousex });
            eventInfo.show(); //Show tool tip
		},
		defaultDate: '<?php echo $events_date;?>',
		eventLimit: true, // allow "more" link when too many events
		navLinks: true, // can click day/week names to navigate views
		selectable: true,
		events: [
			<?php foreach($all_goals_not_started as $goals_not_started):?>
			{
				event_id: '<?php echo $goals_not_started['tracking_id']?>',
				unq: '0',
				title: '<?php echo $goals_not_started['subject']?>',
				start: '<?php echo $goals_not_started['start_date']?>',
				end: '<?php echo $goals_not_started['end_date']?>',
				urllink: '<?php echo site_url().'erp/goal-details/'.uencode($goals_not_started['tracking_id']);?>',
				color: '#f4c22b !important'
			},
			<?php endforeach;?>
			<?php foreach($all_goals_inprogress as $goals_inprogress):?>
			{
				event_id: '<?php echo $goals_inprogress['tracking_id']?>',
				unq: '0',
				title: '<?php echo $goals_inprogress['subject']?>',
				start: '<?php echo $goals_inprogress['start_date']?>',
				end: '<?php echo $goals_inprogress['end_date']?>',
				urllink: '<?php echo site_url().'erp/goal-details/'.uencode($goals_inprogress['tracking_id']);?>',
				color: '#7267EF !important'
			},
			<?php endforeach;?>
			<?php foreach($all_goals_completed as $goals_completed):?>
			{
				event_id: '<?php echo $goals_completed['tracking_id']?>',
				unq: '0',
				title: '<?php echo $goals_completed['subject']?>',
				start: '<?php echo $goals_completed['start_date']?>',
				end: '<?php echo $goals_completed['end_date']?>',
				urllink: '<?php echo site_url().'erp/goal-details/'.uencode($goals_completed['tracking_id']);?>',
				color: '#f44236 !important'
			},
			<?php endforeach;?>
		]
	});	
	/* initialize the external events
	-----------------------------------------------------------------*/

	$('#external-events .fc-event').each(function() {

		// Different colors for events
        $(this).css({'backgroundColor': $(this).data('color'), 'borderColor': $(this).data('color')});

		// store data so the calendar knows to render an event upon drop
		$(this).data('event', {
			title: $.trim($(this).text()), // use the element's text as the event title
			color: $(this).data('color'),
			stick: true // maintain when user navigates (see docs on the renderEvent method)
		});

	});


});
</script>