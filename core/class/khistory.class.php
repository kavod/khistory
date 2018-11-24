<?php

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

/* * ***************************Includes********************************* */
error_reporting(-1);
ini_set('display_errors', 'On');

define('KHISTORY_FILES_DIRECTORY',dirname(__FILE__).'/../resources/impoexpo');
define('KHISTORY_KEY_DATETIME','datetime:history:private');
define('KHISTORY_KEY_VALUE','value:history:private');
define('KHISTORY_DATE_FORMAT','%Y-%m-%d %H-%i-%s');

class khistory extends eqLogic {
    /*     * *************************Attributs****************************** */
    /*private static $_client = null;
    private static $_device = null;*/


    /*     * ***********************Methode static*************************** */
    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {

      }
     */


   public static function dependancy_info() {
   		return 'ok';
   	}

    public static function dependancy_install() {
  	}

		public static function health() {
		}

    protected static function write_file($filename,$input)
    {
      if (strpos($filename,'/')) {
        log::add(__CLASS__,'debug',sprintf('Fichier %s interdit',$filename));
        return false;
      }
      $filename = dirname(__FILE__).'/../../resources/impoexpo/'.$filename.'_dst';
      if (!file_exists($filename) || is_writable($filename)) {
        if ($handle = fopen($filename, 'w')) {
          try {
            if (!(fwrite($handle,"datetime;value\n")===false))
            {
              foreach($input as $line)
              {
                $str = $line->getDatetime().','.$line->getValue()."\n";
                if (fwrite($handle, $str)===false)
                  break;
              }
            }
          } finally {
            fclose($handle);
          }
        } else {
            log::add(
              __CLASS__,
              'debug',
              sprintf(
                'File %s cannot be open in write mode',
                $filename
              )
            );
        }
      } else {
          log::add(
            __CLASS__,
            'debug',
            sprintf(
              'File %s not writable',
              $filename
            )
          );
      }
    }

    public static function read_file($filename)
    {
      /*if (strpos($filename,'/'))
        return false;

      $filename = dirname(__FILE__).'/../../resources/impoexpo/'.$filename;*/
      if (is_readable($filename))
      {
        $handle = fopen($filename, 'r');
        if ($handle) {
          try {
            $str = fgets($handle,1024); // ignore header line
            $result = array();
            while($str = fgets($handle,1024))
            {
              $tmp = explode(',',$str);
              $history = new history();
              $history->setValue($tmp[1]);
              $history->setDatetime($tmp[0]);
              $history->setTableName('history');
              $result[] = $history;
            }
            fclose($handle);
            return $result;
          } catch(Exception $e) {
            fclose($handle);
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            die(print_r(error_get_last(),true));
          }
          die('exception');
        }
        die('not fopen '.$filename . ' '.print_r(error_get_last(),true));
      }
      die('not readable');
      return false;
    }

    public static function filter_date($data,$date_from=null,$date_to=null)
    {
      if (!isset($date_from) && !isset($date_to))
        return $data;
      else
      {
        $datetime_from = DateTime::createFromFormat(
          KHISTORY_DATE_FORMAT,
          (!isset($date_from)) ? '1970-01-01 00:00:00' : $date_from
        );
        $datetime_to = DateTime::createFromFormat(
          KHISTORY_DATE_FORMAT,
          (!isset($date_from)) ? '2100-12-31 23:59:59' : $date_to
        );
        $out = array();
        for ($i=0;$i<count($data);$i++)
        {
          $date = DateTime::createFromFormat(KHISTORY_DATE_FORMAT, $data[$i]->getDatetime());
          if ($date >= $datetime_from && $date <= $datetime_to)
            $out[] = $data[$i];
        }
      }
      return $out;
    }

    protected static function copy_history($src_cmd_id,$dst_cmd_id,$date_from=null,$date_to=null)
    {
      if ($date_from == '') $date_from = null;
      if ($date_to == '') $date_to = null;

      $src_history = cmd::byId($src_cmd_id)->getHistory($date_from,$date_to);
      foreach($src_history as $histo)
      {
        $new_histo = $histo;
        $new_histo->setCmd_id($dst_cmd_id);
        $new_histo->save();
      }
    }

    protected function setDataDst($data)
    {
      switch ($this->getConfiguration('type_dest'))
      {
        case 'cmd':
          $dst_cmd_id = $this->getCmdId('dst');
          if (!$dst_cmd_id)
            return false;
          foreach($data as $histo)
          {
            $new_histo = $histo;
            $new_histo->setCmd_id($dst_cmd_id);
            log::add(
              __CLASS__,
              'debug',
              print_r($new_histo,true)
            );
            $new_histo->save();
          }
          log::add(
            __CLASS__,
            'debug',
            sprintf(
              '%d entrées copiées dans commande %d',
              count($data),
              $dst_cmd_id
            )
          );
          return true;
          break;
        case 'file':
          $id = $this->getId();
          self::write_file($id,$data);
          break;
      }
    }

    protected function getDataSource()
    {
      $date_from = $this->getConfiguration('date_from');
      if ($date_from == '') $date_from = null;
      $date_to = $this->getConfiguration('date_to');
      if ($date_to == '') $date_to = null;

      switch ($this->getConfiguration('type_src'))
      {
        case 'cmd':
          $src_cmd_id = $this->getCmdId('src');
          if (!$src_cmd_id)
            return false;
          $result = cmd::byId($src_cmd_id)->getHistory($date_from,$date_to);
          log::add(
            __CLASS__,
            'debug',
            sprintf(
              '%d entrées trouvées dans commande %d',
              count($result),
              $src_cmd_id
            )
          );
          return $result;
          break;

        case 'file':
          $data = self::read_file($this->getFilePath('src'));
          $result = self::filter_date($data,$date_from,$date_to);
          log::add(
            __CLASS__,
            'debug',
            sprintf(
              '%d entrées trouvées dans fichier %d_src',
              count($result),
              $this->getId()
            )
          );
          if (count($result)<10)
          log::add(
            __CLASS__,
            'debug',
            print_r($result,true)
          );
          return $result;
          break;
      }
      return false;
    }

    protected function cleanDst()
    {
      if ($this->getConfiguration('type_dest')!='cmd')
        return false;

      $dst_cmd_id = $this->getCmdId('dst');
      if (!$dst_cmd_id)
        return false;

      if($this->getConfiguration('delete_dst')) {
        log::add(__CLASS__, 'info', 'Historique de '.$dst_cmd_id.' vidé');
        cmd::byId($dst_cmd_id)->emptyHistory();
        return true;
      }
      return false;
    }

    protected function klog()
    {
      $str = 'Copie de %s vers %s';
      switch ($this->getConfiguration('type_src'))
      {
        case 'cmd':
          $src_cmd_id = $this->getCmdId('src');
          if (!$src_cmd_id)
            return false;
          $str1 = sprintf('la commande %d',$src_cmd_id);
          break;
        case 'file':
          $id = $this->getId();
          $str1 = sprintf('le fichier %d_%s',$id,'src');
          break;
        default:
          log::add(__CLASS__, 'error', sprintf('Type source %s inconnu',$this->getConfiguration('type_src')));
          return false;
      }
      switch ($this->getConfiguration('type_dest'))
      {
        case 'cmd':
          $dst_cmd_id = $this->getCmdId('dst');
          if (!$dst_cmd_id)
            return false;
          $str2 = sprintf('la commande %d',$dst_cmd_id);
          break;
        case 'file':
          $id = $this->getId();
          $str2 = sprintf('le fichier %d_%s',$id,'dst');
          break;
        default:
          log::add(__CLASS__, 'error', sprintf('Type dst %s inconnu',$this->getConfiguration('type_dest')));
          return false;
      }
      log::add(__CLASS__, 'info', sprintf($str,$str1,$str2));
    }

    public function kcopy()
    {
      $data = $this->getDataSource();
      $this->klog();
      $this->cleanDst();
      $this->setDataDst($data);
    }

		public function getImgFilePath() {
		}

		public function getImage() {
			return 'plugins/khistory/plugin_info/khistory_icon.png';
		}

    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
			log::add('khistory','info','Add Copier');
      $logicalId = 'khistory_'.strtolower($this->getName());
      $eqLogic = self::byLogicalId($logicalId, __CLASS__);
      if (is_object($eqLogic)) {
        throw new Exception(__('Le nom du copieur est déjà utilisé', __FILE__));
      }
      $this->setLogicalId($logicalId);
    }

    public function postInsert() {

    }

    public function preSave() {
    }

		public function addCmd($id,$type,$subtype=NULL,$name = NULL,$isVisible=NULL,$isHistorized=NULL,$unit=NULL,$generic_type=NULL,$actionConfirm=NULL)
		{
			if (!isset($name)) $name = ucfirst($id);
			$cmd = $this->getCmd(null, $id);
			if (!is_object($cmd)) {
				$cmd = new kkasaCmd();
				$cmd->setName(__($name, __FILE__));
				if (isset($isVisible)) $cmd->setIsVisible($isVisible);
				if (isset($isHistorized)) $cmd->setIsHistorized($isHistorized);
				if (isset($generic_type)) $cmd->setDisplay('generic_type', $generic_type);
				if (isset($unit)) $cmd->setUnite($unit);
        if (isset($actionConfirm)) $cmd->setConfiguration('actionConfirm',$actionConfirm);
			}
			$cmd->setEqLogic_id($this->getId());
			$cmd->setLogicalId($id);
			$cmd->setType($type);
			if (isset($subtype)) $cmd->setSubType($subtype);
			$cmd->save();
		}

    public function download_dst()
    {
      if ($this->getConfiguration('type_dest')!='file')
        return false;

      header("Content-Type: application/csv");
      header(
        sprintf(
          "Content-disposition: attachment; filename=\"%s.csv\"",
          $this->getConfiguration('filename_dst')
        )
      );
      readfile(dirname(__FILE__).'/../../resources/impoexpo/'.$this->getId().'_dst');
      die();
    }

    public function postSave() {
      $this->addCmd('kcopy','action','other','{{Copier}}',0,0,null,null,1);
    }

    public function preUpdate() {
      $logicalId = 'khistory_'.strtolower($this->getName());

    }

    public function postUpdate() {

    }

    public function preRemove() {

    }

    public function postRemove() {

    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
		public function setInfo($cmd_name,$value)
		{
			/*$cmd = $this->getCmd(null,$cmd_name);
			$changed = $this->checkAndUpdateCmd($cmd_name, $value);
			log::add('kkasa','debug','set: '.$cmd->getName().' to '. $value);
			$cmd->event($value,null,0);
			return $changed;*/
		}

    public function getCmdId($src_dst)
    {
      if (!in_array($src_dst,array('src','dst')))
        return false;

      if(preg_match_all("/#([0-9]*)#/", $this->getConfiguration('cmd_'.$src_dst), $matches))
      {
        foreach ($matches[1] as $cmd_id) {
          return intval($cmd_id);
        }
      }

      return false;
    }

    public function getFilePath($src_dst)
    {
      return dirname(__FILE__).'/../../resources/impoexpo/'.$this->getId().'_'.$src_dst;
    }
}

class khistoryCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
			if ($this->getType() == 'info') {
				return;
			}
			if ($this->getType() == '') {
				return '';
			}
			$eqLogic = $this->getEqLogic();
			if ($this->getLogicalId() == 'kcopy') {
				$eqLogic->kcopy();
			} elseif ($this->getLogicalId() == 'download')
      {
        $eqLogic->download_dst();
      }


			/*if ($this->getLogicalId() == 'on') {
				$eqLogic->setState(1);
			}
			if ($this->getLogicalId() == 'off') {
				$eqLogic->setState(0);
			}*/


    }

    /*     * **********************Getteur Setteur*************************** */
}
