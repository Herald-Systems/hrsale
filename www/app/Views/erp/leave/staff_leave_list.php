<?php

use App\Models\DepartmentModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\SystemModel;
use App\Models\LeaveModel;
use App\Models\ConstantsModel;

//$encrypter = \Config\Services::encrypter();
$SystemModel = new SystemModel();
$RolesModel = new RolesModel();
$UsersModel = new UsersModel();
$LeaveModel = new LeaveModel();
$ConstantsModel = new ConstantsModel();
$DepartmentsModel = new DepartmentModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
if ($user_info['user_type'] == 'staff') {
    $leave_types = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'leave_type')->orderBy('constants_id', 'ASC')->findAll();
} else {
    $leave_types = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'leave_type')->orderBy('constants_id', 'ASC')->findAll();
}
if ($user_info['user_type'] == 'staff') {
    $leave_types = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'leave_type')->orderBy('constants_id', 'ASC')->findAll();
    $total_leave = $LeaveModel->where('employee_id', $usession['sup_user_id'])->countAllResults();
    $leave_pending = $LeaveModel->where('employee_id', $usession['sup_user_id'])->where('status', 1)->countAllResults();
    $total_accepted = $LeaveModel->where('employee_id', $usession['sup_user_id'])->where('status', 2)->countAllResults();
    $total_rejected = $LeaveModel->where('employee_id', $usession['sup_user_id'])->where('status', 3)->countAllResults();
} else {
    $leave_types = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'leave_type')->orderBy('constants_id', 'ASC')->findAll();
    $total_leave = $LeaveModel->where('company_id', $usession['sup_user_id'])->orderBy('leave_id', 'ASC')->countAllResults();
    $leave_pending = $LeaveModel->where('company_id', $usession['sup_user_id'])->where('status', 1)->countAllResults();
    $total_accepted = $LeaveModel->where('company_id', $usession['sup_user_id'])->where('status', 2)->countAllResults();
    $total_rejected = $LeaveModel->where('company_id', $usession['sup_user_id'])->where('status', 3)->countAllResults();
}
$departments = $DepartmentsModel->findAll();
?>
<?php if (in_array('leave2', staff_role_resource()) || in_array('leave_calendar', staff_role_resource()) || in_array('leave_type1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>

    <div id="smartwizard-2" class="border-bottom smartwizard-example sw-main sw-theme-default mt-2">
        <ul class="nav nav-tabs step-anchor">
            <?php if (in_array('leave2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                <li class="nav-item active"><a href="<?= site_url('erp/leave-list'); ?>" class="mb-3 nav-link"> <span
                                class="sw-done-icon feather icon-check-circle"></span> <span
                                class="sw-icon feather icon-plus-square"></span>
                        <?= lang('Dashboard.xin_manage_leaves'); ?>
                        <div class="text-muted small">
                            <?= lang('Main.xin_set_up'); ?>
                            <?= lang('Leave.left_leave'); ?>
                        </div>
                    </a></li>
            <?php } ?>
            <?php if (in_array('leave_type1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                <li class="nav-item clickable"><a href="<?= site_url('erp/leave-type'); ?>" class="mb-3 nav-link"> <span
                                class="sw-done-icon feather icon-check-circle"></span> <span
                                class="sw-icon fas fa-tasks"></span>
                        <?= lang('Leave.xin_leave_type'); ?>
                        <div class="text-muted small">
                            <?= lang('Main.xin_add'); ?>
                            <?= lang('Leave.xin_leave_type'); ?>
                        </div>
                    </a></li>
            <?php } ?>
            <?php if (in_array('leave_calendar', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                <li class="nav-item clickable"><a href="<?= site_url('erp/leave-calendar'); ?>" class="mb-3 nav-link">
                        <span class="sw-done-icon feather icon-check-circle"></span> <span
                                class="sw-icon feather icon-calendar"></span>
                        <?= lang('Dashboard.xin_acc_calendar'); ?>
                        <div class="text-muted small">
                            <?= lang('Leave.xin_leave_calendar'); ?>
                        </div>
                    </a></li>
            <?php } ?>
        </ul>
    </div>
    <hr class="border-light m-0 mb-3">
<?php } ?>
<div class="row">
    <div class="col-sm-3">
        <div class="card prod-p-card background-pattern">
            <div class="card-body">
                <div class="row align-items-center m-b-0">
                    <div class="col">
                        <h6 class="m-b-5">
                            <?= lang('Dashboard.xin_total_leaves'); ?>
                        </h6>
                        <h3 class="m-b-0">
                            <?= $total_leave; ?>
                        </h3>
                    </div>
                    <div class="col-auto"><i class="fas fa-money-bill-alt text-primary"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card prod-p-card bg-primary background-pattern-white">
            <div class="card-body">
                <div class="row align-items-center m-b-0">
                    <div class="col">
                        <h6 class="m-b-5 text-white">
                            <?= lang('Main.xin_approved'); ?>
                        </h6>
                        <h3 class="m-b-0 text-white">
                            <?= $total_accepted; ?>
                        </h3>
                    </div>
                    <div class="col-auto"><i class="fas fa-database text-white"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card prod-p-card bg-primary background-pattern-white">
            <div class="card-body">
                <div class="row align-items-center m-b-0">
                    <div class="col">
                        <h6 class="m-b-5 text-white">
                            <?= lang('Main.xin_rejected'); ?>
                        </h6>
                        <h3 class="m-b-0 text-white">
                            <?= $total_rejected; ?>
                        </h3>
                    </div>
                    <div class="col-auto"><i class="fas fa-dollar-sign text-white"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card prod-p-card background-pattern">
            <div class="card-body">
                <div class="row align-items-center m-b-0">
                    <div class="col">
                        <h6 class="m-b-5">
                            <?= lang('Main.xin_pending'); ?>
                        </h6>
                        <h3 class="m-b-0">
                            <?= $leave_pending; ?>
                        </h3>
                    </div>
                    <div class="col-auto"><i class="fas fa-tags text-primary"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row m-b-1 animated fadeInRight">
    <div class="col-md-12">
        <?php if (in_array('leave3', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <div id="add_form" class="collapse add-form <?php echo $get_animate; ?>" data-parent="#accordion" style="">
                <?php $attributes = array('name' => 'add_leave', 'id' => 'xin-form', 'autocomplete' => 'off'); ?>
                <?php $hidden = array('_user' => 1); ?>
                <?php echo form_open('erp/leave/add_leave', $attributes, $hidden); ?>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-2">
                            <div id="accordion">
                                <div class="card-header">
                                    <h5>
                                        <?= lang('Main.xin_add'); ?>
                                        <?= lang('Leave.left_leave'); ?>
                                    </h5>
                                    <div class="card-header-right"><a data-toggle="collapse" href="#add_form"
                                                                      aria-expanded="false"
                                                                      class="collapsed btn btn-sm waves-effect waves-light btn-primary m-0">
                                            <i data-feather="minus"></i>
                                            <?= lang('Main.xin_hide'); ?>
                                        </a></div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php if ($user_info['user_type'] == 'company') { ?>
                                            <?php $staff_info = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'staff')->findAll(); ?>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="employees" class="control-label">
                                                        <?= lang('Dashboard.dashboard_employee'); ?> <span
                                                                class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-control" name="employee_id"
                                                            data-plugin="select_hrm"
                                                            data-placeholder="<?= lang('Dashboard.dashboard_employee'); ?>">
                                                        <?php foreach ($staff_info as $staff) { ?>
                                                            <option value="<?= $staff['user_id'] ?>">
                                                                <?= $staff['first_name'] . ' ' . $staff['last_name'] ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-md-6">
                                            <div class="form-group" id="employee_ajax">
                                                <label for="leave_type" class="control-label">
                                                    Job <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input class="form-control"
                                                           placeholder="Job"
                                                           name="job" type="text" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" id="employee_ajax">
                                                <label for="leave_type" class="control-label">
                                                    Department <span
                                                            class="text-danger">*</span>
                                                </label>
                                                <select class="form-control" name="department_id" data-plugin="select_hrm"
                                                        data-placeholder="Department">
                                                    <option value=""></option>
                                                    <?php foreach ($departments as $department): ?>
                                                        <option value="<?= $department['department_id']; ?>">
                                                            <?= $department['department_name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" id="employee_ajax">
                                                <label for="leave_type" class="control-label">
                                                    <?= lang('Leave.xin_leave_type'); ?> <span
                                                            class="text-danger">*</span>
                                                </label>
                                                <select class="form-control" name="leave_type" data-plugin="select_hrm"
                                                        data-placeholder="<?= lang('Leave.xin_leave_type'); ?>">
                                                    <option value=""></option>
                                                    <?php foreach ($leave_types as $ileave_type): ?>
                                                        <option value="<?= $ileave_type['constants_id']; ?>">
                                                            <?= $ileave_type['category_name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="start_date">
                                                    Leave Start Date <span
                                                            class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input class="form-control date"
                                                           placeholder="<?= lang('Projects.xin_start_date'); ?>"
                                                           name="start_date" type="text" value="">
                                                    <div class="input-group-append"><span class="input-group-text"><i
                                                                    class="fas fa-calendar-alt"></i></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="end_date">
                                                    Leave End Date <span
                                                            class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input class="form-control date"
                                                           placeholder="<?= lang('Projects.xin_end_date'); ?>"
                                                           name="end_date" type="text" value="">
                                                    <div class="input-group-append"><span class="input-group-text"><i
                                                                    class="fas fa-calendar-alt"></i></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 pt-2">
                                            <div class="form-group mt-4">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input input-primary"
                                                           name="leave_half_day" id="leave_half_day" value="1">
                                                    <label class="custom-control-label" for="leave_half_day">
                                                        <?= lang('Employees.xin_hr_leave_half_day'); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="days">
                                                    No. of Days Leave <span
                                                            class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input class="form-control"
                                                           placeholder="No. of Days Leave"
                                                           name="days" type="number" min="0" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="end_date">
                                                    Resumption Date <span
                                                            class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input class="form-control date"
                                                           placeholder="Resumption Date"
                                                           name="resumption_date" type="text" value="">
                                                    <div class="input-group-append"><span class="input-group-text"><i
                                                                    class="fas fa-calendar-alt"></i></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pt-2">
                                            <div class="form-group mt-4">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input input-primary"
                                                           name="medical_certificate" id="medical_certificate"
                                                           value="1">
                                                    <label class="custom-control-label" for="medical_certificate">
                                                        Medical Certificate
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pt-2">
                                            <div class="form-group mt-4">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input input-primary"
                                                           name="advance_pay" id="advance_pay" value="1">
                                                    <label class="custom-control-label" for="advance_pay">
                                                        Advance Pay
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pt-2">
                                            <div class="form-group mt-4">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input input-primary"
                                                           name="supporting_document" id="supporting_document"
                                                           value="1">
                                                    <label class="custom-control-label" for="supporting_document">
                                                        Supporting Document
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pt-2">
                                            <div class="form-group mt-4">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input input-primary"
                                                           name="require_leave_fare" id="require_leave_fare"
                                                           value="1">
                                                    <label class="custom-control-label" for="require_leave_fare">
                                                        Do You Require a Leave Fare? <br/> (Recreation and Furlough
                                                        Leave
                                                        Only)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="description">
                                                    Leave To be Spent in (District/Province)
                                                </label>
                                                <textarea class="form-control textarea"
                                                          placeholder="Leave To be Spent in (District/Province)"
                                                          name="to_be_spent_in" rows="1"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="description">
                                                    Leave Address for Mail
                                                </label>
                                                <textarea class="form-control textarea"
                                                          placeholder="Leave Address for Mail"
                                                          name="leave_address_mail" rows="1"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-header bg-light">
                                    <h5 class="text-uppercase">Leave Travel Details</h5>
                                    <h6>Departure</h6>
                                </div>
                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="travel_departure_from">
                                                    From
                                                </label>
                                                <input class="form-control"
                                                       placeholder="From"
                                                       id="travel_departure_from"
                                                       name="travel_departure_from"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="travel_departure_to">
                                                    To
                                                </label>
                                                <input class="form-control"
                                                       placeholder="To"
                                                       id="travel_departure_to"
                                                       name="travel_departure_to"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="travel_departure_mode">
                                                    Mode of Travel
                                                </label>
                                                <input class="form-control"
                                                       placeholder="Mode of Travel"
                                                       id="travel_departure_mode"
                                                       name="travel_departure_mode"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="travel_departure_company">
                                                    Name of Company
                                                </label>
                                                <input class="form-control"
                                                       placeholder="Name of Company"
                                                       id="travel_departure_company"
                                                       name="travel_departure_company"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-header bg-light">
                                    <h5 class="text-uppercase">Leave Travel Details</h5>
                                    <h6>Returning</h6>
                                </div>
                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="travel_returning_from">
                                                    From
                                                </label>
                                                <input class="form-control"
                                                       placeholder="From"
                                                       id="travel_returning_from"
                                                       name="travel_returning_from"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="travel_returning_to">
                                                    To
                                                </label>
                                                <input class="form-control"
                                                       placeholder="To"
                                                       id="travel_returning_to"
                                                       name="travel_returning_to"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="travel_returning_mode">
                                                    Mode of Travel
                                                </label>
                                                <input class="form-control"
                                                       placeholder="Mode of Travel"
                                                       id="travel_returning_mode"
                                                       name="travel_returning_mode"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="travel_returning_company">
                                                    Name of Company
                                                </label>
                                                <input class="form-control"
                                                       placeholder="Name of Company"
                                                       id="travel_returning_company"
                                                       name="travel_returning_company"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-md-3 pt-2">
                                            <div class="form-group mt-4">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input input-primary"
                                                           name="is_travelling_time_required" id="is_travelling_time_required"
                                                           value="1">
                                                    <label class="custom-control-label" for="is_travelling_time_required">
                                                        Is Travelling Time Required
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label for="travelling_time_remarks">
                                                    If Yes, Please Give Details
                                                </label>
                                                <textarea class="form-control textarea"
                                                          placeholder="If Yes, Please Give Details "
                                                          id="travelling_time_remarks"
                                                          name="travelling_time_remarks" rows="1"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="declaration">
                                                    I declare that the statements made in this application are correct and that my home district/Province and my spouse’s home
                                                    District/Province for leave purposes is
                                                </label>
                                                <textarea class="form-control textarea"
                                                          placeholder="I declare that the statements made in this application are correct and that my home district/Province and my spouse’s home District/Province for leave purposes is"
                                                          id="declaration"
                                                          name="declaration" rows="2"></textarea>
                                            </div>
                                        </div>
<!--                                        <div class="col-md-12">-->
<!--                                            <div class="form-group">-->
<!--                                                <label for="summary">-->
<!--                                                    --><?php //= lang('Leave.xin_leave_reason'); ?><!-- <span-->
<!--                                                            class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <textarea class="form-control"-->
<!--                                                          placeholder="--><?php //= lang('Leave.xin_leave_reason'); ?><!--"-->
<!--                                                          name="reason" cols="30" rows="2" id="reason"></textarea>-->
<!--                                            </div>-->
<!--                                        </div>-->
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="reset" class="btn btn-light" href="#add_form" data-toggle="collapse"
                                            aria-expanded="false">
                                        <?= lang('Main.xin_reset'); ?>
                                    </button>
                                    &nbsp;
                                    <button type="submit" class="btn btn-primary">
                                        <?= lang('Main.xin_save'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>
                                    <?= lang('Leave.xin_leave_attachment'); ?>
                                </h5>
                            </div>
                            <div class="card-body py-2">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <fieldset class="form-group">
                                                <label for="attachment">
                                                    <?= lang('Main.xin_attachment'); ?>
                                                </label>
                                                <input type="file" class="form-control-file" id="attachment"
                                                       name="attachment">
                                                <small>
                                                    <?= lang('Leave.xin_leave_file_type'); ?>
                                                </small>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        <?php } ?>
        <div class="card user-profile-list <?php echo $get_animate; ?>">
            <div class="card-header">
                <h5>
                    <?= lang('Main.xin_list_all'); ?>
                    <?= lang('Leave.left_leave'); ?>
                </h5>
                <?php if (in_array('leave3', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                    <div class="card-header-right"><a data-toggle="collapse" href="#add_form" aria-expanded="false"
                                                      class="collapsed btn waves-effect waves-light btn-primary btn-sm m-0">
                            <i data-feather="plus"></i>
                            <?= lang('Main.xin_add_new'); ?>
                        </a></div>
                <?php } ?>
            </div>
            <div class="card-body">
                <div class="box-datatable table-responsive">
                    <table class="datatables-demo table table-striped table-bordered" id="xin_table">
                        <thead>
                        <tr>
                            <th><?= lang('Dashboard.dashboard_employee'); ?></th>
                            <th><?= lang('Leave.xin_leave_type'); ?></th>
                            <th><i class="fa fa-calendar"></i>
                                <?= lang('Leave.xin_leave_duration'); ?></th>
                            <th><?= lang('Leave.xin_leave_days'); ?></th>
                            <th><i class="fa fa-calendar"></i>
                                <?= lang('Leave.xin_applied_on'); ?></th>
                            <th><?= lang('Main.dashboard_xin_status'); ?></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h6>
                            <?= lang('Dashboard.xin_leave_status'); ?>
                        </h6>
                        <div class="row d-flex justify-content-center align-items-center">
                            <div class="col">
                                <div id="leave-status-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-12">
                <div class="row">
                    <div class="col-xl-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6>
                                    <?= lang('Leave.xin_leave_type_status'); ?>
                                </h6>
                                <div class="row d-flex justify-content-center align-items-center">
                                    <div class="col">
                                        <div id="leave-type-chart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
