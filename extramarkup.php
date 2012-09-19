<?php if (!defined('PmWiki')) exit();
/**
 * extramarkup - contains markup additions that can be useful
 *
 * @author Tamara Temple <tamara@tamaratemple.com>
 * Time-stamp: <2012-09-19 13:35:03 tamara>
 * @version 20120320
 * @copyright 2012 Tamara Temple
 * @package pmwiki-stuff
 **/

/* **************************************
 * MARKUP CODE AND EXPRESSIONS FOR PMWIKI
 * ************************************** */

// Do the LaTeX thang

Markup('latex','inline',
       '/{{LaTeX}}/',
       Keep('<span class="texhtml" style="font-family:cmr10, LMRoman10-Regular, Times, serif;">L<span style="text-transform: uppercase; font-size: 70%; margin-left: -0.36em; vertical-align: 0.3em; line-height: 0; margin-right: -0.15em;">a</span>T<span style="text-transform: uppercase; margin-left: -0.1667em; vertical-align: -0.5ex; line-height: 0; margin-right: -0.125em;">e</span>X</span>'));


// nice markup for CC licenses
//
// Usage: (:cc-license [type=(by|by-nc|by-nc-sa)] [title=<name of work>] [work=<URL of work>] [author=<name of author>] [authorurl=<link to author's contact page> ] [permissions=<link to more permissions page>]:)
//
// Defaults:
//   type: by-nc-sa
//   title: $WikiTitle
//   work: $ScriptDirUrl
//   author: Anonymous
//   authorurl: blank
//   permissions: $ScriptDirUrl?n=Site.Permissions
//
Markup('creativecommonslicenses', /* unique markup identifier */
       '<links',		  /* don't want pmwiki to reprocess urls in text */
       '/\\(:cc-license\\s*?(.*?):\\)/ei', /* regex for markup recognition */
       "cc_license_markup('$1')");
function cc_license_markup($parameters='')
{
  $parameters=stripslashes($parameters);
  @sms('parameters: ',$parameters,__FILE__,__LINE__,__FUNCTION__,__CLASS__);
  @sms('parsed parameters: ',ParseArgs($parameters),__FILE__,__LINE__,__FUNCTION__,__CLASS__);
  global $WikiTitle, $ScriptUrl;
  /* defaults */
  $license_strings = array('by' => 'Attribution',
			   'by-nc' => 'Attribution NonCommercial',
			   'by-nc-sa' => 'Attribution NonCommercial ShareAlike');
  $defaults = array('type'=>'by-nc-sa',
		    'title'=>$WikiTitle,
		    'work'=>$ScriptUrl,
		    'author'=>'Anonymous',
		    'authorurl'=>'',
		    'permissions'=>"{$ScriptUrl}?n=Site.Permissions");
  $LicenseFmt = "<a rel='license' href='\$CCL_URL'><img alt='\$CCL_LogoAlt' style='border-width:0' src='\$CCL_LogoImg' /></a> <span xmlns:dct='http://purl.org/dc/terms/' href='http://purl.org/dc/dcmitype/Text' property='dct:title' rel='dct:type'><a href='\$CCL_Work'>\$CCL_Title</a></span> by <a xmlns:cc='http://creativecommons.org/ns#' href='\$CCL_AuthorUrl' property='cc:attributionName' rel='cc:attributionURL'>\$CCL_Author</a> is licensed under a <a rel='license' href='\$CCL_URL'>Creative Commons \$CCL_string 3.0 Unported License</a>. Permissions beyond the scope of this license may be available at <a xmlns:cc='http://creativecommons.org/ns#' href='\$CCL_Permissions' rel='cc:morePermissions'>Permissions</a>.";
  
  $args = array_merge($defaults, ParseArgs($parameters));
  unset($args['#']);

  $replacements = array();
  $replacements['$CCL_Title'] = $args['title'];
  $replacements['$CCL_Work'] = $args['work'];
  // NOTE: more precise replacement must preced less precise replacement
  $replacements['$CCL_AuthorUrl'] = $args['authorurl'];
  $replacements['$CCL_Author'] = $args['author'];
  $replacements['$CCL_Permissions'] = $args['permissions'];
  $replacements['$CCL_string'] = (isset($license_strings[$args['type']])?$license_strings[$args['type']]:'Unknown');
  
  $replacements['$CCL_URL'] = "http://creativecommons.org/licenses/{$args['type']}/3.0/";
  $replacements['$CCL_LogoImg'] = "http://i.creativecommons.org/l/{$args['type']}/3.0/80x15.png";
  $replacements['$CCL_LogoAlt'] = "Creative Commons Licence";
  @sms('args: ',$replacements,__FILE__,__LINE__,__FUNCTION__,__CLASS__);

  $return_text = str_replace(array_keys($replacements),array_values($replacements),$LicenseFmt);
  @sms('return text: ',$return_text,__FILE__,__LINE__,__FUNCTION__,__CLASS__);
  return Keep($return_text);
}

Markup('dw-user','inline',
       '/\\(:dw-user\\s*?(.*?):\\)/ei',
       "dw_user_link('$1')");

function dw_user_link($params='')
{
  $old_er = error_reporting(-1);
  $old_de = ini_get('display_errors');
  ini_set('display_errors',true);
  $old_dse = ini_get('display_startup_errors');
  ini_set('display_startup_errors',true);

  $params=stripslashes($params);
  @sms('dw-user params:',$params,__FILE__,__LINE__,__FUNCTION__,__CLASS__);

  $defaults=array('name'=>'unknown',
		  'type'=>'user'
		  );
  $DWOptions=array(
		   'user'=>array(
				 'image'=>'user.png',
				 'width'=>'17',
				 'height'=>'17',
				 'alt'=>'[personal profile]'
				 ),
		   'community'=>array(
				      'image'=>'community.png',
				      'width'=>'16',
				      'height'=>'16',
				      'alt'=>'[community profile]'
				      )
		   );
  $DWLinkFmt = "<span style='white-space: nowrap;'><a href='http://\$DWName.dreamwidth.org/profile'><img src='http://www.dreamwidth.org/img/silk/identity/\$DWImage' alt='\$DWAlt ' width='\$DWWidth' height='\$DWHeight' style='vertical-align: text-bottom; border: 0; padding-right: 1px;' /></a><a href='http://\$DWName.dreamwidth.org/'><b>\$DWName</b></a></span>";
  $args = ParseArgs($params);
  @sms('dw-user args:',$args,__FILE__,__LINE__,__FUNCTION__,__CLASS__);
  if (isset($args[''])) {
      // non-parameterized arguments, i.e. (:dw-user name [type]:)
      $args['name'] = $args[''][0];
      $args['type'] = (isset($args[''][1]))?$args[''][1]:$defaults['type'];
  }
  @sms('dw-user args post parameterized check:',$args,__FILE__,__LINE__,__FUNCTION__,__CLASS__);
  $args = array_merge($defaults,$args);
  @sms('dw-user args post-merge:',$args,__FILE__,__LINE__,__FUNCTION__,__CLASS__);
  $replacements = array();
  $replacements['$DWName'] = $args['name'];
  $replacements['$DWImage'] = $DWOptions[$args['type']]['image'];
  $replacements['$DWWidth'] = $DWOptions[$args['type']]['width'];
  $replacements['$DWHeight'] = $DWOptions[$args['type']]['height'];
  $replacements['$DWAlt'] = $DWOptions[$args['type']]['alt'];
  @sms('dw-user replacements:',$replacements,__FILE__,__LINE__,__FUNCTION__,__CLASS__);
  $return_text = str_replace(array_keys($replacements),array_values($replacements),$DWLinkFmt);
  @sms('dw-user return text:',$return_text,__FILE__,__LINE__,__FUNCTION__,__CLASS__);

  error_reporting($old_er);
  ini_set('display_errors',$old_de);
  ini_set('display_startup_errors',$old_dse);

  return Keep($return_text);
}