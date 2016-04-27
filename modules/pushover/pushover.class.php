<?php
/**
* Pushover service integration
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 13:03:10 [Mar 13, 2016])
*/
//
//
class pushover extends module {
/**
* pushover
*
* Module class constructor
*
* @access private
*/
function pushover() {
  $this->name="pushover";
  $this->title="Pushover";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();
 $out['CKEY']=$this->config['CKEY'];
 $out['LEVEL']=(int)$this->config['LEVEL'];
 $out['TOKEN']=$this->config['TOKEN'];
 $out['PREFIX']=$this->config['PREFIX'];

 $out['DISABLED']=$this->config['DISABLED'];


 if ($this->view_mode=='update_settings') {

   global $ckey;
   $this->config['CKEY']=$ckey;

   global $level;
   $this->config['LEVEL']=(int)$level;

   global $token;
   $this->config['TOKEN']=$token;

   global $prefix;
   $this->config['PREFIX']=$prefix;


   global $disabled;
   $this->config['DISABLED']=$disabled;

   $this->saveConfig();
   $this->redirect("?ok=1");
 }

 if ($_GET['ok']) {
  $out['OK']=1;
 }
 
}

/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}

 function processSubscription($event, $details='') {
  $this->getConfig();
  if ($event=='SAY') {
    $level=$details['level'];
    $message=$details['message'];
    

   $consumerKey = $this->config['CKEY'];
   if (!$consumerKey && defined('SETTINGS_PUSHOVER_USER_KEY')) {
    $consumerKey    = SETTINGS_PUSHOVER_USER_KEY;
   }

   $token = $this->config['TOKEN'];
   if (!$token && defined('SETTINGS_PUSHOVER_API_TOKEN')) {
    $token    = SETTINGS_PUSHOVER_API_TOKEN;
   }


   $consumerLevel = (int)$this->config['LEVEL'];
   $device_id = trim($this->config['DEVICE_ID']);
   $prefix = trim($this->config['PREFIX']);



   if ($consumerKey == '')
   return 0;

    if (!$this->config['DISABLED'] && $level>=$consumerLevel)
    {

          if ($prefix) {
           $message=$prefix.' '.$message;
          }

          curl_setopt_array($ch = curl_init(), array(
           CURLOPT_URL => "https://api.pushover.net/1/messages.json",
           CURLOPT_RETURNTRANSFER => 1,
           CURLOPT_SSL_VERIFYPEER => FALSE, 
           CURLOPT_SSL_VERIFYHOST => 2,
           CURLOPT_POSTFIELDS => array(
           "token" => $token,
           "user" => $consumerKey,
           "message" => $message,
          )));
          $res=curl_exec($ch);
          curl_close($ch);


    }
  }
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  subscribeToEvent($this->name, 'SAY');
  parent::install();
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgTWFyIDEzLCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
