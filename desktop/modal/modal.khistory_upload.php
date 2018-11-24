<?php
error_reporting(-1);
ini_set('display_errors', 'On');
$khistory = khistory::byId(init('id'));
if (!is_object($khistory)) {
	throw new Exception('Impossible de trouver le copieur');
}
sendVarToJS('id', $khistory->getId());
sendVarToJS('input_field',init('input_field'));
?>
<div id="div_alertHistoryConfigure"></div>

<form class="form-horizontal">
  <fieldset id="fd_historyConfigure">
    <legend>{{Upload}}</legend>

    <div class="form-group link_type link_image display_mode display_mode_image">
      <label class="col-lg-4 control-label">{{Fichier local}}</label>
      <div class="col-lg-8">
        <span class="btn btn-default btn-file">
          <i class="fas fa-cloud-upload-alt"></i> {{Envoyer}}<input  id="bt_uploadFile" type="file" name="file" style="display: inline-block;">
        </span>
      </div>
    </div>
  </fieldset>
</form>
<script>
  $('#bt_uploadFile').fileupload({
    replaceFileInput: false,
    url: 'plugins/khistory/core/ajax/khistory.ajax.php?action=uploadFile&id=' + id+'&jeedom_token='+JEEDOM_AJAX_TOKEN,
    dataType: 'json',
    done: function (e, data) {
      if (data.result.state != 'ok') {
        $('#div_alertHistoryConfigure').showAlert({message: data.result.result, level: 'danger'});
        return;
      } else {
				var el = $('.input-sm[data-l2key=' + input_field + ']');
				el.val('');
				el.atCaret('insert', $('#bt_uploadFile').val().replace(/^.*[\\\/]/, ''));
        $('#fd_historyConfigure').closest("div.ui-dialog-content").dialog("close");
      }
    }
  });
</script>
