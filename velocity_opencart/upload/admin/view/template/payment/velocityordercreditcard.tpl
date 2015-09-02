<h2><?php echo $text_Refund_heading; ?></h2>
<div class="alert alert-success" id="response" style="display:none;"></div>
<table class="table table-striped table-bordered">
  <tr>
	<td><?php echo $text_refund_amount; ?></td>
	<td><input name="refund_amount" type="text" width="10" id="refund_amount" /></td>
  </tr>
  <tr>
	<td><?php echo $text_refund_shipping; ?></td>
	<td><input type="checkbox" name="shipping" width="10" id="shipping" /></td>
  </tr>
 <tr>
	<td></td>
	<td><input type="button" class="btn btn-primary" name="process_refund" id="process_refund" value="Process Refund" /></td>
  </tr>
</table>
<script type="text/javascript"><!--
     $("#process_refund").click(function () {
      if (confirm('<?php echo $text_confirm_refund ?>')) {
        $.ajax({
          type: 'POST',
          dataType: 'json',
          data: {'order_id': '<?php echo $order_id; ?>', 'amount': $('#refund_amount').val(), 'shipping': $('#shipping').is(':checked')},
          url: 'index.php?route=payment/velocitycreditcard/refund&token=<?php echo $token; ?>',
          beforeSend: function(xhr) {
             $("#process_refund").addClass('disabled');
             $("#process_refund").val('Processing Refund...');
          },
          complete: function() {
             $("#process_refund").removeClass('disabled');
             $("#process_refund").val('Process Refund');
             
             $('#refund_amount').val('');
             $('#shipping').attr('checked', false);
          },
          success: function (data) {
             if (data['success'] != '') {
                $('#response').text(data['success']);
                $('#response').removeClass('alert-danger').addClass('alert-success');
             }
             if (data['error'] != '') {
                $('#response').text(data['error']);
                $('#response').removeClass('alert-success').addClass('alert-danger');
             }
             $('#response').css('display', 'block');
          },
          error: function(data) {
            console.log(data); 
          }
        });
      }
    });
//--></script>