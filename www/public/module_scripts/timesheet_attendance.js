$(document).ready(function () {

    document.getElementById('department_id').addEventListener('change', function (ev) {
        debugger
        if (!!ev.target.value)
        $url = main_url + "timesheet/attendance_list?department_id="+ev.target.value;
        else
            $url = main_url + "timesheet/attendance_list";
		$('#xin_table').dataTable({
			"bDestroy": true,
			"ajax": {
				url: $url,
				type: 'GET'
			},
			"language": {
				"lengthMenu": dt_lengthMenu,
				"zeroRecords": dt_zeroRecords,
				"info": dt_info,
				"infoEmpty": dt_infoEmpty,
				"infoFiltered": dt_infoFiltered,
				"search": dt_search,
				"paginate": {
					"first": dt_first,
					"previous": dt_previous,
					"next": dt_next,
					"last": dt_last
				},
			},
			"fnDrawCallback": function (settings) {
				$('[data-toggle="tooltip"]').tooltip();
			}
		});
    });

    var xin_table = $('#xin_table').dataTable({
        "bDestroy": true,
        "ajax": {
            url: main_url + "timesheet/attendance_list",
            type: 'GET'
        },
        "language": {
            "lengthMenu": dt_lengthMenu,
            "zeroRecords": dt_zeroRecords,
            "info": dt_info,
            "infoEmpty": dt_infoEmpty,
            "infoFiltered": dt_infoFiltered,
            "search": dt_search,
            "paginate": {
                "first": dt_first,
                "previous": dt_previous,
                "next": dt_next,
                "last": dt_last
            },
        },
        "fnDrawCallback": function (settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    $('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
    $('[data-plugin="select_hrm"]').select2({width: '100%'});
});
