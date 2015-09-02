<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-velocitycreditcard" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-velocitycreditcard" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-merchant"><?php echo $entry_identitytoken; ?></label>
            <div class="col-sm-10">
                <textarea name="velocitycreditcard_identitytoken" placeholder="<?php echo $entry_identitytoken; ?>" id="input-merchant" class="form-control"><?php echo $velocitycreditcard_identitytoken; ?></textarea>
              <?php if ($error_identitytoken) { ?>
              <div class="text-danger"><?php echo $error_identitytoken; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-merchant"><?php echo $entry_workflowid; ?></label>
            <div class="col-sm-10">
              <input type="text" name="velocitycreditcard_workflowid" value="<?php echo $velocitycreditcard_workflowid; ?>" placeholder="<?php echo $entry_workflowid; ?>" id="input-merchant" class="form-control" />
              <?php if ($error_workflowid) { ?>
              <div class="text-danger"><?php echo $error_workflowid; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-key"><?php echo $entry_applicationprofileid; ?></label>
            <div class="col-sm-10">
              <input type="text" name="velocitycreditcard_applicationprofileid" value="<?php echo $velocitycreditcard_applicationprofileid; ?>" placeholder="<?php echo $entry_applicationprofileid; ?>" id="input-key" class="form-control" />
              <?php if ($error_applicationprofileid) { ?>
              <div class="text-danger"><?php echo $error_applicationprofileid; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-key"><?php echo $entry_merchantprofileid; ?></label>
            <div class="col-sm-10">
              <input type="text" name="velocitycreditcard_merchantprofileid" value="<?php echo $velocitycreditcard_merchantprofileid; ?>" placeholder="<?php echo $entry_merchantprofileid; ?>" id="input-key" class="form-control" />
              <?php if ($error_merchantprofileid) { ?>
              <div class="text-danger"><?php echo $error_merchantprofileid; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_test; ?></label>
            <div class="col-sm-10">
              <label class="radio-inline">
                <?php if ($velocitycreditcard_test) { ?>
                <input type="radio" name="velocitycreditcard_test" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <?php } else { ?>
                <input type="radio" name="velocitycreditcard_test" value="1" />
                <?php echo $text_yes; ?>
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if (!$velocitycreditcard_test) { ?>
                <input type="radio" name="velocitycreditcard_test" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="velocitycreditcard_test" value="0" />
                <?php echo $text_no; ?>
                <?php } ?>
              </label>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="velocitycreditcard_status" id="input-status" class="form-control">
                <?php if ($velocitycreditcard_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>