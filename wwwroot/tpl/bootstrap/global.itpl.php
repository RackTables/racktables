<?php

/**
 * Template for: CSS inline
 * Usage: interface-lib.php (printPageHeaders())
 */
$this->setInMemoryTemplate("HeaderCssInline","<style type=\"text/css\">" . "\n" . "{{code}}" . "\n</style>\n");

/**
 * Template for: CSS include
 * Usage: interface-lib.php (printPageHeaders())
 */
$this->setInMemoryTemplate("HeaderCssInclude","<link rel=stylesheet type='text/css' href='?module=chrome&uri={{path}}' />\n");


/**
 * Template for: JS inline
 * Usage: interface-lib.php (printPageHeaders())
 */
$this->setInMemoryTemplate("HeaderJsInline",'<script type="text/javascript">' . "\n" . "{{code}}" . "\n</script>\n");

/**
 * Template for: JS include
 * Usage: interface-lib.php (printPageHeaders())
*/
$this->setInMemoryTemplate("HeaderJsInclude","<link rel=stylesheet type='text/css' href='?module=chrome&uri={{path}}' />\n");

/**
 * Template for: CellFilterPortlet in case there are no tags
 * Usage: interface.php (renderCellFilterPortlet())
 */
$this->setInMemoryTemplate("CellFilterNoTags","<tr><td colspan=2 class='tagbox sparenetwork'>(nothing is tagged yet)</td></tr>");

/**
 * Template for: CellFilterPortlet in case there are no predicates
 * Usage: interface.php (renderCellFilterPortlet())
 */
$this->setInMemoryTemplate("CellFilterNoPredicates","<tr><td colspan=2 class='tagbox sparenetwork'>(no predicates to show)</td></tr>");

/**
 * Template for: CelLFilterPortlet in case you want extra-tags.
 * Usage: interface.php (renderCellFilterPortlet())
 */
$this->setInMemoryTemplate("CellFilterExtraText","<tr><td colspan=2><textarea name=cfe {{Class}}>{{Extratext}}</textarea></td></tr>");

/**
 * Template for: Tablist, the currently active Tab
 * Usage: interface.php (showTabs)
 */
$this->setInMemoryTemplate("TabActive","<li><a class=current href='index.php?page={{Page}}&tab={{Tab}}{{Args}}'>{{Title}}</a></li>");

/**
 * Template for: Tablist, currently inactive tabs
 * Usage: interface.php (showTabs)
 */
$this->setInMemoryTemplate("TabInactive","<li><a class=std href='index.php?page={{Page}}&tab={{Tab}}{{Args}}'>{{Title}}</a></li>");

/**
 * Template for: Tablist, tabs with warnings
 * Usage: interface.php (showTabs)
 */
$this->setInMemoryTemplate("TabAttention","<li><a class=attn href='index.php?page={{Page}}&tab={{Tab}}{{Args}}'>{{Title}}</a></li>");

/**
 * Template for: Part of the path  in the PathAndSearch module
 * Usage: interface.php (showPathAndSearch)
 */
$this->setInMemoryTemplate("PathLink"," : <a href='index.php?{{Params}}{{AnchorTail}}'>{{Name}}</a>");

/**
 * Template for spacer in CellFilterPortlet
 * UsagE: interface.php (renderCellFilterPortlet)
 */
$this->setInMemoryTemplate("CellFilterSpacer","<tr><td colspan=2 class=tagbox><hr></td></tr>\n");

/**
 * Template for ObjectLogs when no log exists
 * Usage: interface.php (allObjectLogs)
**/
$this->setInMemoryTemplate("NoObjectLogFound","<center><h2>No logs exist</h2></center>");

/**
 * Template for Search when nothing was found
 * Usage: interface.php (renderSearchResults)
**/
$this->setInMemoryTemplate("NoSearchItemFound","<center><h2>Nothing found for {{Terms}}</h2></center>");


/**
 * The four error messages.
 * Usage: interface.php (showMessageOrError)
 */
$this->setInMemoryTemplate("MessageNeutral","<div class=msg_neutral><p class=bg_primary> {{Message}} </p></div>");
$this->setInMemoryTemplate("MessageSuccess","<div class=msg_success><p class=bg_success> {{Message}} </p></div>");
$this->setInMemoryTemplate("MessageError","<div class=msg_error><p class=bg_danger> {{Message}} </p></div>");
$this->setInMemoryTemplate("MessageWarning","<div class=msg_warning><p class=bg_warning> {{Message}} </p></div>");

/**
 * Text to render when no CellList elements are available.
 * Usage: interface.php (renderEmptyResults)
 **/
$this->setInMemoryTemplate("EmptyResults","<p>Please set a filter to display the corresponging {{Name}} <br> <a href=\"{{ShowAll}}\">Show all {{Name}}{{Suffix}}</a></p>");


$this->setInMemoryTemplate("GetImageHrefDoInput", "<input type=image name=submit class=icon src='{{SrcPath}}' border=0 {{TabIndex}} {{Title}}>");
$this->setInMemoryTemplate("GetImageHrefNoInput", "<img src={{SrcPath}} width={{ImgWidth}} height={{ImgHeight}} border=0 {{Title}} >");


/**
*	getRenderedIPv4NetCapacity: 
*	Usage: interface.php, interface-lib.php
*
**/
$this->setInMemoryTemplate("RenderedIPv4NetCapacityAddrc", "<img width='{{width}}' height=10 border=0 title='{{title2}}' src='?module=progressbar4&px1={{px1}}" . 
															"&px2={{px2}}&px3={{px3}}'><small class='title'>{{title}}</small>");
$this->setInMemoryTemplate("RenderedIPv4NetCapacityReturn","<div class=\"{{class}}\" id=\"{{div_id}}\"> {{textVal}}</div>");

/**
*	getRenderedIPv6NetCapacity: 
*	Usage: interface.php, interface-lib.php
*
**/
$this->setInMemoryTemplate("RenderedIPv6NetCapacity","<div class=\"{{class}}\" id=\"{{div_id}}\"> {{addrc}}{{cnt}}{{mult}}{{what}}  </div>");

/**
*	renderNetVLAN: 
*	Usage: interface.php, interface-lib.php
*
**/
$this->setInMemoryTemplate("RenderNetVLAN","<div class='vlan'><strong><small>{{noun}}</small>{{link}} </strong></div>");

/**
*	MkA: 
*	Usage: functions.php
*
**/
$this->setInMemoryTemplate("MkAInMemory","<a href='{{link}}'>{{text}}</a>");


/**
*	formatVSPort: 
*	Usage: slb2-interface.php
*
**/
$this->setInMemoryTemplate("formatVSPortInMemory"," <span title={{name}}>{{srv}}</span>");

/**
*	FormatVSIP: 
*	Usage:  slb2-interface.php
*
**/
$this->setInMemoryTemplate("FormatVSIPInMem","<a href='{{href}}'>{{fmt_ip}}</a>");

/**
*	GetSelect: 
*	Usage:  slb-interface.php
*
**/
$this->setInMemoryTemplate("GetSelectInLine","<input type=hidden name={{selectName}} id={{selectName}} value={{keyValue}}>{{value}}");

/**
*	RenderNewEntityTags: 
*	Usage:  interface.php
*
**/
$this->setInMemoryTemplate("RenderNewEntityTags_empty","No tags defined");
$this->setInMemoryTemplate("RenderNewEntityTags","<div class=tagselector><table border=0 align=center cellspacing=0 class='tagtree'>\n{{checkbox}}\n</table></div>\n");

/**
 * FileSummaryDownloadLink
 * Usage: interface.php - renderFileSummary() to display the download link.
 */
$this->setInMemoryTemplate('FileSummaryDownloadLink',"<a href='?module=download&file_id={{Id}}' src=?module=chrome&uri=pix/download.png title='Download' height=16 width=16 ><img ></a>&nbsp;");
/**
*	ReportsCounters: 
*	ReportsMesseges: 
*	ReportsCustom:
*	ReportsMeters: 
*	Usage: interface.php
*
**/
$this->setInMemoryTemplate("ReportsCounter","<tr><td class=tdright>{{Header}}:</td><td class=tdleft>{{Data}}</td></tr>");
$this->setInMemoryTemplate("ReportsMessages","<tr class='msg_{{Class}}'><td class=tdright>{{Header}}:</td><td class=tdleft>{{Text}}</td></tr>");
$this->setInMemoryTemplate("ReportsCustom","<tr><td colspan=2>\n{{ItemCont}}\n</td></tr>");
$this->setInMemoryTemplate("ReportsMeters","<tr><td class=tdright>{{Title}}:</td><td class=tdcenter>\n{{ProgressBar}}\n<br><small>{{IsMax}}</small></td></tr>");


/**
*	GetProgressBar: 
*	Usage: interface-lib-php
*
**/
$this->setInMemoryTemplate("GetProgressBar","<img width=100 height=10 border=0 title='{{done}}%' src='{{src}}'>");


/**
*	NoVLANConfig: 
*	Usage:  interface.php
*
**/
$this->setInMemoryTemplate("NoVLANConfig","<center><h3>(no VLAN configuration exists)</h3></center>");

/**
*	StdCenterTableCell: 
*	Usage: interface.php
*
**/
$this->setInMemoryTemplate("StdCenterTableCell","<td class=tdcenter>{{Cont}}</td>");

/**
*	StdTableCell: 
*	Usage: interface.php
*
**/
$this->setInMemoryTemplate("StdTableCell","<td>{{cont}}</td>");

/**
*	StdTableRow: 
*	Usage: printObjectDetailsForRenderRack -> interface.php
*
**/
$this->setInMemoryTemplate("StdTableRow","<tr>{{cont}}</tr>");

/**
*	StdTableHead: 
*	Usage: portletRSPoolSrv -> interface.php
*
**/
$this->setInMemoryTemplate("StdTableHead","<th>{{Cont}}</th>");

/**
*	RenderTagStatsALink: 
*	Usage:  interface.php
*
**/
$this->setInMemoryTemplate("RenderTagStatsALink","<a href='index.php?page={{Pagerealm}}&cft[]={{TaginfoID}}'>{{Taginfo}}</a>");

/**
 * FileSummaryComment
 * Usage: as above
 */
$this->setInMemoryTemplate('FileSummaryComment','<div class="dashed commentblock">{{Comment}}</div>');


/**
*	RenderConfigVarName: 
*	Usage:  interface.php
*
**/
$this->setInMemoryTemplate("RenderConfigVarName",'<span class="varname">{{vname}}</span>\n<p class="vardescr">{{desAndIsDefined}}</p>');

/**
 * FileSummaryComment
 * Usage: as above
 */
$this->setInMemoryTemplate('FileLinksDefLink','<tr><td class=tdleft>{{Content}}</td></tr>');

/**
 * FileSummaryComment
 * Usage: as above
 */
$this->setInMemoryTemplate('FileLinksObjLink','<tr><td class=tdleft>{{Name}} : {{Link}} </td></tr>');

/**
 * FileSummaryComment
 * Usage: as above
 */
$this->setInMemoryTemplate('CellLink','<a href="{{Link}}">{{Title}}</a>');

/**
 * Serialized Tag
 * Usage: serializeTags (interface-lib.php)
 */
$this->setInMemoryTemplate('SerializedTagLink','<a href="{{BaseUrl}}cft[]={{ID}}" class="{{Class}}" title="{{Title}}">{{Tag}}</a> {{Delimiter}}');
$this->setInMemoryTemplate('SerializedTag','<span class="{{Class}}" title="{{Title}}">{{Tag}}</span> {{Delimiter}}');

/**
 * FileSummaryComment
 * Usage: as above
 */
$this->setInMemoryTemplate('IPNetBacktraceLink','<a href="{{Link}}" title="View IP tree with this net as root">{{Title}}</a>');

/**
*	StdListElem: 
*	Usage:  renderVLANInfo -> interface.php
*
**/
$this->setInMemoryTemplate('StdListElem','<li>{{cont}}</li>');

/**
*	RenderedIPPortPair: 
*	Usage:  interface.php
*
**/
$this->setInMemoryTemplate('RenderedIPPortPair','<a href="{{href}}">{{ip}}</a>{{isPort}}');

/**
*	FormatLoggedSpan: 
*	Usage:  ajax-interface.php
*
**/
$this->setInMemoryTemplate("FormatLoggedSpan","<span {{class}} {title}}>{{text}}</span>");

/**
*	FullWidthTable: 
*	Usage:  printObjectDetailsForRenderRack -> interface.php
*
**/
$this->setInMemoryTemplate("FullWidthTable","<table width='100%' border='1'>{{cont}}</table>");

/**
*	ObjectFreeSolt: 
*	Usage:  printObjectDetailsForRenderRack -> interface.php
*
**/
$this->setInMemoryTemplate("ObjectFreeSolt","<td class='state_F'><div title='Free slot'>&nbsp;</div></td>");

/**
*	FormatPortLink: 
*	Usage:  formatPortLink -> interface.php
*
**/
$this->setInMemoryTemplate("FormatPortLink",'<a $additional href="{{href}}">{{text_items}}</a>');

/**
*	RenderSNMPPortFinder_NoExt: 
*	Usage:  renderSNMPPortFinder -> interface.php
*
**/
$this->setInMemoryTemplate("RenderSNMPPortFinder_NoExt","<div class=msg_error>The PHP SNMP extension is not loaded.  Cannot continue.</div>");

/**
*	GlobalPlaceholder: 
*	A very basic globalplaceholder to be filled with submodules
*	Usage:  serializeTags -> interface-lib.php
*
**/
$this->setInMemoryTemplate("GlobalPlaceholder","{{Cont}}");

/**
*	ETagsLine: 
*	Usage:  render8021QStatus -> interface.php
*
**/
$this->setInMemoryTemplate("ETagsLine","<br><small>{{cont}}</small>");

/**
*	PCodeLine: 
*	Usage:  renderFilesPortlet -> interface.php
*
**/
$this->setInMemoryTemplate("PCodeLine", "<tr><td colspan=2>{{pcode}}</td></tr>\n");

/**
*	EmptyTableCell: 
*	Usage:  renderIndex -> interface.php
*
**/
$this->setInMemoryTemplate("EmptyTableCell","<td>&nbsp;</td>");

/**
*	IndexItemMod': 
*	Usage: renderIndex -> interface.php
*
**/
$this->setInMemoryTemplate("IndexItemMod"," <td>\n<h1><a href='{{Href}}'>" .
		"{{PageName}}<br>\n{{Image}} </a></h1>\n</td>");

/**
*	TDLeftCell: 
*	Usage: renderObject -> interface.php
*
**/
$this->setInMemoryTemplate("TDLeftCell","<td class=tdleft {{rowspan}}>{{cont}}</td>");

/**
*	RoundBracketsMod: 
*	Usage:  renderObject -> interface.php
*
**/
$this->setInMemoryTemplate("RoundBracketsMod","({{cont}})");

/**
*	TbcLine: 
*	Usage:  Render8021QReport -> interface.php
*
**/
$this->setInMemoryTemplate("TbcLineMod","<tr class='state_A'><th>...</th><td colspan={{CountDomains}}>&nbsp;</td></tr>");

/**
*	ExpirationsNoSection: 
*	Usage:  renderExpirations -> interface.php
*
**/
$this->setInMemoryTemplate("ExpirationsNoSection","<tr><td colspan=4>(none)</td></tr></table><br>\n");

/**
 *	IPv6Separators:
 *	Usage: renderIPv6Addresses -> interface.php 
 *
 **/
$this->setInMemoryTemplate("IPv6SeparatorPlain","<tr><td colspan=4 class=tdleft></td></tr>");
$this->setInMemoryTemplate("IPv6Separator","<tr class='tdleft {{Highlight}}'><td><a name='ip-{{FMT}}' href='{{Link'>{{FMT}}</a>" . 
												"</td><td><span class='rsvtext {{Editable}} id-{{FMT}} op-upd-ip-name'></span></td>" .
												"<td><span class='rsvtext {{Editable}} id-{{FMT}} op-upd-ip-comment'></span></td><td>&nbsp;</td></tr>");

/**
*	SmallElement: 
*	Usage:  renderCell -> interface.php
*
**/
$this->setInMemoryTemplate('SmallElement',"<small>{{Cont}}</small>");

/**
*	StrongElement: 
*	Usage:  getRenderedAlloc -> interface.php
*
**/
$this->setInMemoryTemplate('StrongElement',"<strong>{{Cont}}</strong>");

/**
*	StdOptionTemplate: 
*	Usage:  renderRackMultiSelect -> interface.php
*
**/
$this->setInMemoryTemplate('StdOptionTemplate',"<option value={{RackId}} {{IsSelected}}>{{RackName}}</option>\n");


/**
*	RenderedAlloc Templates: 
*	Usage:  getRenderedAlloc -> interface.php
*
**/
$this->setInMemoryTemplate("RenderedAllocTdIpNetInfo","<a name='ip-{{Dottequad}}' class='{{IpClass}}' {{IpTitle}} href='{{Href}}'>{{Title}}</a>");
$this->setInMemoryTemplate("RenderedAllocTdIpNoNetInfo","<span class='{{IpClass}}' {{IpTitle}}>{{Dottequad}}</span>");
$this->setInMemoryTemplate("RenderedAllocTdIp","<td class='tdleft'>{{Info}}{{Aac}} {{NiftyStr}}</td>");

$this->setInMemoryTemplate("RenderedAllocNetworkNoNetinfo","<td class='{{TdClass}} sparenetwork'>N/A</td>");
$this->setInMemoryTemplate("RenderedAllocNetworkNetinfo","<td class='{{TdClass}}'>{{InfoCell}}</td>");

$this->setInMemoryTemplate("RenderedAllocRoutedByOnly","<td class='{{TdClass}}'>&nbsp;</td>");

$this->setInMemoryTemplate("RenderedAllocPeers","<td class='{{TdClass}}'>{{Prefix}}{{Strong}}{{LocPeers}}</td>");
$this->setInMemoryTemplate("RenderedAllocLocPeers","{{Prefix}}<a href='{{href}}'>{{Osif}}{{LocPeer}</a>");

/**
*	RSPoolSrvPortlet Table Filed Templates: 
*	Usage:  portletRSPoolSrv -> slb_interface.php
*
**/
$this->setInMemoryTemplate("RSPoolSrvInservice","<td align=center>{{ImgCont}}</td>");
$this->setInMemoryTemplate("RSPoolSrvRsconfig","<td class=slbconf>{{Cont}}</td>");
$this->setInMemoryTemplate("RSPoolSrvDefault","<td class=tdleft>{{Cont}}</td>");

/**
*	StdSpan: 
*	Usage:  renderSLBTriplets2 ->  slb2_interface.php
*
**/
$this->setInMemoryTemplate("StdSpan","<span class='{{Class}}'>{{Cont}}</span>");

?>