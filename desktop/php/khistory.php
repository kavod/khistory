<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('khistory');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
								<a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un copieur}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
foreach ($eqLogics as $eqLogic) {
	$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
	echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '" style="' . $opacity .'"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
}
		    ?>
           </ul>
       </div>
   </div>

   <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
  <legend><i class="fa fa-cog"></i>  {{Gestion}}</legend>
  <div class="eqLogicThumbnailContainer">
	    <div class="cursor eqLogicAction" data-action="add" style="text-align: center; background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
	      <i class="fa fa-plus-circle" style="font-size : 7em;color:#94ca02;"></i>
	      <br>
	      <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02">{{Ajouter un copieur}}</span>
	    </div>
      <div class="cursor eqLogicAction" data-action="gotoPluginConf" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
      	<i class="fa fa-wrench" style="font-size : 6em;color:#767676;"></i>
    		<br />
    		<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Configuration}}</span>
  		</div>
  </div>
  <legend><i class="fa fa-table"></i> {{Mes copieurs}}</legend>
<div class="eqLogicThumbnailContainer">
    <?php
foreach ($eqLogics as $eqLogic) {
	$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
	echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="text-align: center; background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
	echo '<img src="' . $eqLogic->getImage() . '" height="105" width="95" />';
	echo "<br>";
	echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;">' . $eqLogic->getHumanName(true, true) . '</span>';
	echo '</div>';
}
?>
</div>
</div>

<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
	<a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
  <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
  <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
    <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
    <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
  </ul>
  <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
    <div role="tabpanel" class="tab-pane active" id="eqlogictab">
      <br/>
    <form class="form-horizontal" enctype="multipart/form-data">
        <fieldset id="form_khistory">
            <div class="form-group">
                <label class="col-sm-3 control-label">{{Nom du copieur}}</label>
                <div class="col-sm-3">
                    <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom du copieur}}"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" >{{Objet parent}}</label>
                <div class="col-sm-3">
                    <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                        <option value="">{{Aucun}}</option>
                        <?php
foreach (object::all() as $object) {
	echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
}
?>
                   </select>
               </div>
           </div>
	   <div class="form-group">
                <label class="col-sm-3 control-label">{{Catégorie}}</label>
                <div class="col-sm-9">
                 <?php
                    foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                    echo '<label class="checkbox-inline">';
                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                    echo '</label>';
                    }
                  ?>
               </div>
           </div>
		<div class="form-group">
			<label class="col-sm-3 control-label"></label>
			<div class="col-sm-9">
				<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
				<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
			</div>
		</div>

		<div class="form-group">
			 <label class="col-sm-3 control-label">{{Type de source}}</label>
			 <div class="col-sm-3">
				 <select id="sel_src" class="eqLogicAttr form-control"  data-l1key="configuration" data-l2key="type_src">
					 <option value="cmd">{{Commande}}</option>
					 <option value="file">{{Fichier serveur}}</option>
			 	 </select>
			 </div>
		</div>
		<div class="form-group khisto_cmd_src">
				<label class="col-sm-3 control-label">{{Commande source}}</label>
				<div class="col-sm-3">
						<div class="input-group">
							<input disabled class="eqLogicAttr form-control input-sm" type="text" data-l1key="configuration" data-l2key="cmd_src" />
							<span class="input-group-btn">
								<a class="btn btn-default btn-sm listCmdInfo btn-success" data-input="cmd_src">
									<i class="fa fa-list-alt"></i>
								</a>
							</span>
						</div>
				</div>
		</div>
		<div class="form-group khisto_file_src">
			<label class="col-sm-3 control-label">{{Fichier source}}</label>
			<div class="col-sm-3">
				<div class="input-group">
					<input disabled class="eqLogicAttr form-control input-sm" type="text" data-l1key="configuration" data-l2key="filename_src" />
					<span class="input-group-btn">
						<a class="btn btn-default btn-sm uploadFile btn-success" data-input="filename_src">
							<i class="fa fa-upload"></i>
						</a>
					</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			 <label class="col-sm-3 control-label">{{Type de destination}}</label>
			 <div class="col-sm-3">
				 <select id="sel_dst" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="type_dest">
					 <option value="cmd">{{Commande}}</option>
					 <option value="file">{{Fichier serveur}}</option>
			 	 </select>
				 <a href="" id="download_dst">{{Télécharger}}</a>
			 </div>
		</div>
		<div class="form-group khisto_cmd_dst">
				<label class="col-sm-3 control-label">{{Commande destination}}</label>
				<div class="col-sm-3">
						<div class="input-group">
							<input disabled class="eqLogicAttr form-control input-sm" type="text" data-l1key="configuration" data-l2key="cmd_dst" />
							<span class="input-group-btn">
								<a class="btn btn-default btn-sm listCmdInfo btn-danger" data-input="cmd_dst">
									<i class="fa fa-list-alt"></i>
								</a>
							</span>
						</div>
				</div>
				<label class="col-sm-3"><font color="red">{{ATTENTION: l'historique de cette commande risque d'être supprimé}}</font></label>
		</div>
		<div class="form-group khisto_cmd_dst">
			<label class="col-sm-3 control-label"></label>
			<div class="col-sm-9">
			 <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="delete_dst" />{{Vider historique destination avant de copier}}</label>
			</div>
		</div>
		<div class="form-group khisto_file_dst">
			<label class="col-sm-3 control-label">{{Nom du fichier destination}}</label>
			<div class="col-sm-3">
				<div class="input-group">
					<input class="eqLogicAttr form-control" type="text" data-l1key="configuration" data-l2key="filename_dst" />
				</div>
			</div>
		</div>
		<div class="form-group">
			 <label class="col-sm-3 control-label">{{Période}}</label>
			 <div class="col-sm-2">
				 <input class="eqLogicAttr form-control in-datepicker" type="text" data-l1key="configuration" data-l2key="date_from" placeholder="{{Vide <=> indéfini}}" />
			 </div>
			 <label class="col-sm-1 control-label">
				 {{à}}
			 </label>
			 <div class="col-sm-2">
				 <input class="eqLogicAttr form-control in-datepicker" type="text" data-l1key="configuration" data-l2key="date_to" placeholder="{{Vide <=> indéfini}}" />
			 </div>
		</div>
	</fieldset>
</form>
</div>
      <div role="tabpanel" class="tab-pane" id="commandtab">
<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{Commandes}}</a><br/><br/>
<table id="table_cmd" class="table table-bordered table-condensed">
    <thead>
        <tr>
            <th>{{Nom}}</th><th>{{Options}}</th><th>{{Action}}</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>
</div>

</div>
</div>
<?php include_file('desktop', 'khistory', 'js', 'khistory');?>
<?php include_file('core', 'plugin.template', 'js');?>
