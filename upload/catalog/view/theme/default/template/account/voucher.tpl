<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content">
  <div class="top">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center">
      <h1><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="middle">
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="edit">
      <b style="margin-bottom: 2px; display: block;"><?php echo $text_your_details; ?></b>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
        <table>
          <tr>
            <td width="150"><span class="required">*</span> <?php echo $entry_to_name; ?></td>
            <td><input type="text" name="to_name" value="<?php  echo $to_name; ?>" />
              <?php if ($error_to_name) { ?>
              <span class="error"><?php  echo $error_to_name; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_to_email ; ?></td>
            <td><input type="text" name="to_email" value="<?php  echo $to_email; ?>" />
             <?php if ($error_to_email) { ?>
              <span class="error"><?php  echo $error_to_email; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo  $entry_from_name; ?></td>
            <td><input type="text" name="from_name" value="<?php echo $from_name; ?>" />
             <?php if ($error_from_name) { ?>
              <span class="error"><?php echo $error_from_name; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_from_email; ?></td>
            <td><input type="text" name="from_email" value="<?php echo $from_email; ?>" />
              <?php if ($error_from_email) { ?>
              <span class="error"><?php  echo $error_from_email; ?></span>
              <?php } ?></td> 
          </tr>
          <tr>
            <td><?php echo $entry_theme; ?></td>
            <td>
         
            <?php foreach($voucher_themes as $vouchertheme): ?>
            <?php if( $voucher_theme_id == $vouchertheme['voucher_theme_id'] ) { ?>
              <input type="radio"  name="voucher_theme_id" value="<?php echo $vouchertheme['voucher_theme_id'];?>" checked="checked">&nbsp;<?php echo $vouchertheme['name'];?>  <br>
            <?php } else { ?>
              <input type="radio"  name="voucher_theme_id" value="<?php echo $vouchertheme['voucher_theme_id'];?>">&nbsp;<?php echo $vouchertheme['name'];?>  <br>
            <?php } ?>
            <?php  endforeach; ?>
            <?php if ($error_theme) { ?>
              <span class="error"><?php  echo $error_theme; ?></span>
              <?php } ?></td> 
          </tr>
          <tr>
            <td><?php echo $entry_message; ?></td>
            <td><input type="text" name="message" value="<?php echo $message; ?>" /> </td>
          </tr>
          <tr>
            <td><?php echo $entry_amount; ?> <?php echo $help_amount; ?></td>
            <td><input type="text" name="amount" value="<?php echo $amount; ?>" />
             
            <?php if ($error_amount) { ?>
              <span class="error"><?php  echo $error_amount; ?></span>
              <?php } ?></td> 
          </tr>
          <tr>
            <td colspan=3><input type="checkbox" name="agree" />&nbsp;<?php echo $text_agree; ?></td>
          </tr>
        </table>
        <input type="hidden" name="config_voucher_min" value="<?php echo $config_voucher_min; ?>" />
        <input type="hidden" name="config_voucher_max" value="<?php echo $config_voucher_max; ?>" />
      </div>
      
      <div class="buttons">
        <table>
          <tr>
            <td align="left"><a onclick="location = '<?php echo str_replace('&', '&amp;', $back); ?>'" class="button"><span><?php echo $button_back; ?></span></a></td>
            <td align="right"><a onclick="$('#edit').submit();" class="button"><span><?php echo $button_continue; ?></span></a></td>
          </tr>
        </table>
      </div>
    </form>
  </div>
  <div class="bottom">
    <div class="left"></div>
    <div class="right"></div>
    <div class="center"></div>
  </div>
</div>
<?php echo $footer; ?> 