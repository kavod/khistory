
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.
 */
function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
    tr += '</td>';

    tr += '<td>';
    tr += '<span>';
    tr += '<input type="checkbox" class="cmdAttr" data-size="mini" data-label-text="{{Visible}}" data-l1key="isVisible" />{{Visible}}';
    tr += '<br />';
    tr += '<input type="checkbox" class="cmdAttr" data-size="mini" data-label-text="{{Historiser}}" data-l1key="isHistorized" />{{Historiser}}';
    tr += '</span>';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}

function hideshow(src_dst,type)
{
  if (type == 'cmd')
  {
    kshow(src_dst,'cmd');
    khide(src_dst,'file');
  }
  else {
    khide(src_dst,'cmd');
    kshow(src_dst,'file');
  }
}

function khide(src_dst,type)
{
  //console.log("khide("+src_dst+","+type+")");
  $(".khisto_"+type+"_"+src_dst+" input[type='checkbox']").prop('checked',false);
  $(".khisto_"+type+"_"+src_dst+" input[type='text']").val('');
  $(".khisto_"+type+"_"+src_dst).hide();
}

function kshow(src_dst,type)
{
  //console.log("kshow("+src_dst+","+type+")");
  $(".khisto_"+type+"_"+src_dst).show();
}

function modal_upload(id,input_field) {
  modal_url = 'index.php?v=d&modal=modal.khistory_upload&plugin=khistory&id='+id+'&input_field='+input_field;

  $('#md_modal').dialog({title: "{{Charger un fichier source}}"});
  $('#md_modal').load(modal_url).dialog('open');
}

$('document').ready(function() {
  hideshow('src',$("#sel_src").val());
  hideshow('dst',$("#sel_dst").val());
  $('#sel_src').on('change',function() {
    hideshow('src',$("#sel_src").val());
  });

  $('#sel_dst').on('change',function() {
    hideshow('dst',$("#sel_dst").val());
  });
  $("#form_khistory").delegate(".listCmdInfo", 'click', function () {
      var el = $(this).closest('.input-group').find('.input-sm[data-l2key=' + $(this).attr('data-input') + ']');
      jeedom.cmd.getSelectModal({cmd: {type: 'info',isHistorized: 1}}, function (result) {
          el.val('');
          el.atCaret('insert', result.human);
      });
  });
  $("#form_khistory").delegate(".uploadFile", 'click', function () {
      var id = $("#form_khistory input[data-l1key='id']").val();
      var el = $(this).closest('.input-group').find('.input-sm[data-l2key=' + $(this).attr('data-input') + ']');
      /*jeedom.cmd.getSelectModal({cmd: {type: 'info',isHistorized: 1}}, function (result) {
          el.val('');
          el.atCaret('insert', result.human);
      });*/
      modal_upload(id,$(this).attr('data-input'));
  });

  $(".in-datepicker").datepicker();


  $("#download_dst").on('click',function(e) {
    var id = $("#form_khistory input[data-l1key='id']").val();
    e.preventDefault()
    window.location.href = 'plugins/khistory/core/ajax/khistory.ajax.php?action=download&id=' + id +'&jeedom_token='+JEEDOM_AJAX_TOKEN;
  });

});
