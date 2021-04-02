<?php echo $header; ?>
<!-- <?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?> -->
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
    <h1 style="background-image: url('view/image/product.png');"><?php echo $heading_title; ?></h1>
    <div class="buttons">
    <a onclick=" $('#form').submit();" class="button"><span><?php echo $button_insert; ?></span></a>
    <!-- <a onclick="$('#form').attr('action', '<?php echo $copy; ?>'); $('#form').submit();" class="button"><span><?php echo $button_copy; ?></span></a>-->
    <a onclick="location ='<?php echo $cancel;?>'" class="button"><span><?php echo $button_cancel; ?></span></a> 
    </div>
  </div>
  <div class="content">
  
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
  <div id="language<?php echo $language['language_id']; ?>">
 
          <table class="form" id="voucher_theme">
            <tr>
              <td><span class="required">*</span> <?php echo $entry_name; ?></td>
              <td><input type="text" name="voucher_theme_name" class="form-control"  value="<?php echo isset($name) ? $name: ''; ?>"" />
                <?php /*if (isset($error_name[$language['language_id']])) { ?><span class="error"><?php echo $error_name[$language['language_id']]; ?></span><?php } */?></td>
            </tr>
            <tr>
              <td><?php echo $entry_description; ?></td>
              <td>   <input type="text" name="voucher_theme_description" value="<?php echo isset($voucher['from_name']) ? $voucher['from_name'] : ''; ?>" placeholder="<?php   echo $entry_description; ?>" id="input-from-name" class="form-control" /></td>
            </tr>
            <!-- Image upload button -->
             </tr>
            <!-- Image upload button -->

            <tr>
              <td><?php echo $entry_image; ?></td>
              <td> <input type="hidden" name="image" value="<?php echo $image;?>" id="image"/>
           <img src="<?php echo isset($image1) ? $image1 :  $no_image; ?>" alt="" id="preview" class="image" onclick='image_upload("image" , "preview");' />
                    </td>
            
            </tr>
            
         <tfoot></tfoot>
           
          </table>
        </div>

    </form>
 
  </div>
</div>
<script type="text/javascript" src="view/javascript/jquery/ui/ui.draggable.js"></script>
<script type="text/javascript" src="view/javascript/jquery/ui/ui.resizable.js"></script>
<script type="text/javascript" src="view/javascript/jquery/ui/ui.dialog.js"></script>
<script type="text/javascript" src="view/javascript/jquery/ui/external/bgiframe/jquery.bgiframe.js"></script>
<script type="text/javascript">
var image_row = <?php echo 0; // $image_row; ?>;

function addImage() {
  // alert(123);
    html  = '<tbody id="image_row' + image_row + '">';
	html += '<tr>';
	html += '<td class="left"><input type="hidden" name="voucher_image[' + image_row + ']" value="" id="image' + image_row + '" /><img src="<?php echo $no_image; ?>" alt="" id="preview' + image_row + '" class="image" onclick="image_upload(\'image' + image_row + '\', \'preview' + image_row + '\');" /></td>';
	html += '<td class="left"><a onclick="$(\'#image_row' + image_row  + '\').remove();" class="button"><span><?php echo "remove"; ?></span></a></td>';
	html += '</tr>';
	html += '</tbody>';
	$('#voucher_theme tfoot').before(html);
	
	image_row++;
}
function image_upload(field, preview) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $_GET['token']; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '<?php echo "Select Image"; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $_GET['token']; ?>',
					type: 'POST',
					data: 'image=' + encodeURIComponent($('#' + field).attr('value')),
					dataType: 'text',
					success: function(data) {
						$('#' + preview).replaceWith('<img src="' + data + '" alt="" id="' + preview + '" class="image" onclick="image_upload(\'' + field + '\', \'' + preview + '\');" />');
					}
				});
			}
		},	
		bgiframe: false,
		width: 700,
		height: 400,
		resizable: false,
		modal: false
	});
};

//--></script>
<script type="text/javascript" src="view/javascript/jquery/ui/ui.datepicker.js"></script>
<script type="text/javascript"><!--
$(document).ready(function() {
	$('.date').datepicker({dateFormat: 'yy-mm-dd'});
});
//--></script>
<script type="text/javascript"><!--
$.tabs('#tabs a'); 
$.tabs('#languages a'); 
//--></script>
<?php echo $footer; ?>