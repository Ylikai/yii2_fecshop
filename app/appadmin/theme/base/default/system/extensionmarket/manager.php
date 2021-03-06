<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
use fec\helpers\CRequest;
/** 
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
?>

<form id="pagerForm" method="post" action="<?= \fec\helpers\CUrl::getCurrentUrl();  ?>">
	<?=  CRequest::getCsrfInputHtml();  ?>
	<?=  $pagerForm;  ?>
</form>

<div class="pageContent" style="background:#fff;">
	<div class="panelBar">
		<?= $toolBar; ?>
	</div>
    <div style="margin:100px 100px 10px 100px;">
	<?php if (is_array($addon_list)) : ?>
        <ul>
        <?php foreach ($addon_list as $addon_one): ?>
            <?php 
                $symbol = '';
                $val = '';
                $mb = 1024 * 1024;
                if ($addon_one['addon_info']['zip_size']  <  $mb) {
                    $symbol = 'KB';
                    $val = Yii::$service->helper->format->numberFormat($addon_one['addon_info']['zip_size'] / 1024);
                } else {
                    $symbol = 'MB';
                    $val = Yii::$service->helper->format->numberFormat($addon_one['addon_info']['zip_size'] / $mb);
                
                }
                $zip_size = $val. $symbol ;
                $top_version = $addon_one['addon_info']['version'];
                $canDelete = false;
            ?>
            <li class="addon_li">
                <div class="addon_d">
                    <img style="" src="<?= $addon_one['addon_info']['image'] ?>" />
                    <h2 style=""><?= $addon_one['addon_info']['name'] ?>
                    ( <?= $zip_size ?> ) </h2>
                </div>
                <div class="clear"></div>
                <div style="margin-top:60px;">
                <?php   
                    $isLocal = false;
                    $namespace = $addon_one['addon_info']['namespace'];
                    if (in_array($namespace, $localCreatedArr)):
                        $isLocal = true;
                ?>
                       <a title="?????????????????????????????????????????????????????????????????????????????????" class="not_publish" href="javascript:void(0)" addonName="<?= $addon_one['addon_info']['name'] ?>" rel="<?= $namespace ?>"  folderName="<?= $addon_one['addon_info']['folder'] ?>"  packageName="<?= $addon_one['addon_info']['package'] ?>">????????????</a>
                       <a title="????????????????????????????????????????????????????????????????????????" class="local_install_test" p_type="install" href="javascript:void(0)" addonName="<?= $addon_one['addon_info']['name'] ?>" rel="<?= $namespace ?>"  folderName="<?= $addon_one['addon_info']['folder'] ?>"  packageName="<?= $addon_one['addon_info']['package'] ?>">??????Test</a>
                       <a title="????????????????????????????????????????????????????????????????????????" class="local_install_test" p_type="upgrade" href="javascript:void(0)" addonName="<?= $addon_one['addon_info']['name'] ?>" rel="<?= $namespace ?>"  folderName="<?= $addon_one['addon_info']['folder'] ?>"  packageName="<?= $addon_one['addon_info']['package'] ?>">??????Test</a>
                       <a title="????????????????????????????????????????????????????????????????????????" class="local_install_test" p_type="uninstall"  href="javascript:void(0)" addonName="<?= $addon_one['addon_info']['name'] ?>" rel="<?= $namespace ?>"  folderName="<?= $addon_one['addon_info']['folder'] ?>"  packageName="<?= $addon_one['addon_info']['package'] ?>">??????Test</a>
                       
                <?php
                    elseif (in_array($namespace, $installed_extensions_namespace)):
                        $canDelete = true; 
                        if ( version_compare($versionArr[$namespace], $addon_one['addon_info']['version'] ,'<') ):
                        
                ?>
                            <a  title="???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????zip???????????????????????????????????????"
                            class="abutton-update" href="javascript:void(0)"  addonName="<?= $addon_one['addon_info']['name'] ?>" rel="<?= $namespace ?>" folderName="<?= $addon_one['addon_info']['folder'] ?>"  packageName="<?= $addon_one['addon_info']['package'] ?>">????????????</a>
                        
                        
                            <?php if (in_array($namespace, $can_hand_upgrade_extensions)): // ??????????????????  ?>
                                <a  title="???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????zip???????????????????????????????????????"
                                    class="abutton-hand-update" href="javascript:void(0)"  addonName="<?= $addon_one['addon_info']['name'] ?>" rel="<?= $namespace ?>" folderName="<?= $addon_one['addon_info']['folder'] ?>"  packageName="<?= $addon_one['addon_info']['package'] ?>">????????????</a>
                                
                            <?php endif; ?>
                        
                        
                        <?php else: ?>
                            
                            <a title="????????????????????????????????????????????????????????????????????????????????????????????????????????????" class="abutton-normal" href="javascript:void(0)">????????????</a>
                        <?php endif; ?>
                 
                    
                <?php else: ?>
                    <?php if (in_array($namespace, $can_hand_install_extensions)): ?>
                        <a title="?????????????????????????????????????????????" class="handbutton" href="javascript:void(0)" addonName="<?= $addon_one['addon_info']['name'] ?>" rel="<?= $namespace ?>"  folderName="<?= $addon_one['addon_info']['folder'] ?>"  packageName="<?= $addon_one['addon_info']['package'] ?>">????????????</a>
                    
                    <?php endif; ?>
                    <?php if ($top_version): ?>
                        <a title="?????????????????????????????????????????????" class="abutton" href="javascript:void(0)" addonName="<?= $addon_one['addon_info']['name'] ?>" rel="<?= $namespace ?>"  folderName="<?= $addon_one['addon_info']['folder'] ?>"  packageName="<?= $addon_one['addon_info']['package'] ?>">????????????</a>
                    <?php else: ?>
                        <a title="?????????????????????????????????????????????zip???????????????????????????????????????????????????" class="not_publish" href="javascript:void(0)" addonName="<?= $addon_one['addon_info']['name'] ?>" rel="<?= $namespace ?>"  folderName="<?= $addon_one['addon_info']['folder'] ?>"  packageName="<?= $addon_one['addon_info']['package'] ?>">?????????</a>
                    
                    <?php endif; ?>
                <?php endif; ?>
                    
                    <div class="version_info <?= $isLocal ? 'addon_local' : ''  ?>">
                        <?php if ($top_version): ?>
                        <div class="">????????????: <?= $top_version ?></div>
                        <?php endif; ?>
                        
                        <?php if($versionArr[$namespace]): ?>
                        <div style="margin-top:5px;">????????????: <?= $versionArr[$namespace] ?></div>
                        <?php endif; ?>
                    </div>
                    
                </div>
                <?php if($canDelete): ?>
                <a href="javascript:void(0)" class="removeAddon" title="????????????"  addonName="<?= $addon_one['addon_info']['name'] ?>" rel="<?= $namespace ?>" folderName="<?= $addon_one['addon_info']['folder'] ?>"  packageName="<?= $addon_one['addon_info']['package'] ?>">
                    <i class="fa fa-trash-o"></i>
                </a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    </div>
   
</div>

<style>
.addon_li h2{
    margin10px auto;line-height: 20px;height: 40px;overflow: hidden;
}
.removeAddon{
    display: block;
    bottom: 10px;
    position: absolute;
    right: 10px;
    font-size:18px;
    color:#999;
}
.removeAddon:hover{
    color:#333;
}

.version_info.addon_local{
        margin-top: 20px;
}

.version_info{
    float: right;
    margin-right: 10px;
}
.addon_li{
    width: 280px;
    height:440px;
    display: inline-block;
    margin: 10px;
    border: 1px solid #ccc;
    position: relative;
}
.addon_d{
    width:230px;
    margin:auto;
}
.addon_d img {
    width:230px;
}

.addon_d h2 {
    width:230px;
    display:block;
    margin:20px auto;
    text-align:center;
}

.not_publish{
    background: #f0ad4e !important;
    color:#fff;
    padding:5px 10px;
    white-space: pre;
    height: 15px;
    display: inline-block;
    margin-bottom: 10px;
}

.local_install_test{
        background: #5e72e4 !important;
    color: #fff;
    padding: 5px 10px;
    white-space: pre;
    height: 15px;
    display: inline-block;
    margin-bottom: 10px;
}



.abutton{
    background:#009688  !important;
    color:#fff;
    padding:5px 10px;
}

.abutton-update{
    background:#cc0000  !important;
    color:#fff;
    padding:5px 10px;
}

.abutton-hand-update{
    background:#cc0000  !important;
    color:#fff;
    padding:5px 10px;
}

.abutton-normal{
    background:#337ab7 !important;
    color:#fff;
    padding:5px 10px;
}


.abutton:hover{
    opacity:0.8
}



.handbutton{
    background:#5e72e4 !important;
    color:#fff;
    padding:5px 10px;
}

.handbutton:hover{
    opacity:0.8
}

.panelBar{border:none;}

</style>


<script>
    $(document).ready(function(){
        var isGuest = <?=  $guest ? 'true' : 'false' ?> ;
        if (isGuest) {
            $(".accountLogin").click();
            var url = "<?= Yii::$service->url->getUrl('system/extensionmarket/login') ?>";
            var title = "????????????";
            var dlgId = '1';
            var options = {"width": "700","height":"480","mask":true,"drawable":true};
            $.pdialog.open(url, dlgId, title, options);???
        }
        $(document).off("click").on("click",".abutton",function(){
            namespace = $(this).attr('rel');
            var packageName = $(this).attr('packageName');
            var addonName = $(this).attr('addonName');
            var folderName = $(this).attr('folderName');
            
            var url = "<?= Yii::$service->url->getUrl("system/extensionmarket/install"); ?>";
            url += '?namespace=' + namespace;
            url += '&packageName=' + packageName;
            url += '&folderName=' + folderName;
            url += '&addonName=' + encodeURIComponent(addonName);
            
            $.ajax({
                url: url,
                async: true,
                timeout: 800000,
                dataType: 'json', 
                type: 'get',
                success:function(data, textStatus){
                    
                    if(data.statusCode == 200){
                        //alert(data.statusCode);
                        message = data.message;
                        alertMsg.correct(message);
                        navTab.reloadFlag('page1');
                    } else if (data.statusCode == 300){
                        message = data.message;
                        alertMsg.error(message)
                    } else {
                        alertMsg.error(data);
                    }
                    //
                },
                error:function(data, textStatus){
                    if (data && data.responseText) {
                        console.log(data.responseText);
                        alertMsg.error(data.responseText);
                    } else {
                        alertMsg.error("???????????????????????????????????????????????????");
                    }
                    
                }
            });
            
            
            
        });
        // hand install
        $(document).on("click",".handbutton",function(){
            namespace = $(this).attr('rel');
            var packageName = $(this).attr('packageName');
            var addonName = $(this).attr('addonName');
            var folderName = $(this).attr('folderName');
            
            var url = "<?= Yii::$service->url->getUrl("system/extensionmarket/handinstall"); ?>";
            url += '?namespace=' + namespace;
            url += '&packageName=' + packageName;
            url += '&folderName=' + folderName;
            url += '&addonName=' + encodeURIComponent(addonName);
            
            $.ajax({
                url: url,
                async: true,
                timeout: 800000,
                dataType: 'json', 
                type: 'get',
                success:function(data, textStatus){
                    if(data.statusCode == 200){
                        //alert(data.statusCode);
                        message = data.message;
                        alertMsg.correct(message);
                        navTab.reloadFlag('page1');
                    } else if (data.statusCode == 300){
                        message = data.message;
                        alertMsg.error(message)
                    } else {
                        alertMsg.error(data);
                    }
                    //
                },
                error:function(data, textStatus){
                    if (data && data.responseText) {
                        console.log(data.responseText);
                        alertMsg.error(data.responseText);
                    } else {
                        alertMsg.error("???????????????????????????????????????????????????");
                    }
                    
                }
            });
            
        });
        
        
        $(document).on("click",".removeAddon",function(){
            var self = this;
            alertMsg.confirm("??????????????????????????????", {
                okCall: function(){
                    namespace = $(self).attr('rel');
                    var packageName = $(self).attr('packageName');
                    var addonName = $(self).attr('addonName');
                    var folderName = $(this).attr('folderName');
                    
                    var url = "<?= Yii::$service->url->getUrl("system/extensionmarket/uninstall"); ?>";
                    url += '?namespace=' + namespace;
                    url += '&packageName=' + packageName;
                    url += '&folderName=' + folderName;
                    url += '&addonName=' + encodeURIComponent(addonName);
                    
                    $.ajax({
                        url: url,
                        async: true,
                        timeout: 800000,
                        dataType: 'json', 
                        type: 'get',
                        success:function(data, textStatus){
                            
                            if(data.statusCode == 200){
                                //alert(data.statusCode);
                                message = data.message;
                                alertMsg.correct(message);
                                navTab.reloadFlag('page1');
                            } else if (data.statusCode == 300){
                                message = data.message;
                                alertMsg.error(message)
                            } else {
                                alertMsg.error(data);
                            }
                            //
                        },
                        error:function(data, textStatus){
                            if (data && data.responseText) {
                                console.log(data.responseText);
                                alertMsg.error(data.responseText);
                            } else {
                                alertMsg.error("???????????????????????????????????????????????????");
                            }
                        }
                    });
                },
                cancelCall : function() {
                     
                }
            });
            
        });
        
        $(document).on("click",".abutton-hand-update",function(){
            namespace = $(this).attr('rel');
            var packageName = $(this).attr('packageName');
            var addonName = $(this).attr('addonName');
            var folderName = $(this).attr('folderName');
            
            var url = "<?= Yii::$service->url->getUrl("system/extensionmarket/handupgrade"); ?>";
            url += '?namespace=' + namespace;
            url += '&packageName=' + packageName;
            url += '&folderName=' + folderName;
            url += '&addonName=' + encodeURIComponent(addonName);
            
            $.ajax({
                url: url,
                async: true,
                timeout: 800000,
                dataType: 'json', 
                type: 'get',
                success:function(data, textStatus){
                    
                    if(data.statusCode == 200){
                        //alert(data.statusCode);
                        message = data.message;
                        alertMsg.correct(message);
                        navTab.reloadFlag('page1');
                    } else if (data.statusCode == 300){
                        message = data.message;
                        alertMsg.error(message)
                    } else {
                        alertMsg.error(data);
                    }
                    //
                },
                error:function(data, textStatus){
                    if (data && data.responseText) {
                        console.log(data.responseText);
                        alertMsg.error(data.responseText);
                    } else {
                        alertMsg.error("???????????????????????????????????????????????????");
                    }
                }
            });
        });
        
        $(document).on("click",".abutton-update",function(){
            namespace = $(this).attr('rel');
            var packageName = $(this).attr('packageName');
            var addonName = $(this).attr('addonName');
            var folderName = $(this).attr('folderName');
            
            var url = "<?= Yii::$service->url->getUrl("system/extensionmarket/upgrade"); ?>";
            url += '?namespace=' + namespace;
            url += '&packageName=' + packageName;
            url += '&folderName=' + folderName;
            url += '&addonName=' + encodeURIComponent(addonName);
            
            $.ajax({
                url: url,
                async: true,
                timeout: 800000,
                dataType: 'json', 
                type: 'get',
                success:function(data, textStatus){
                    
                    if(data.statusCode == 200){
                        //alert(data.statusCode);
                        message = data.message;
                        alertMsg.correct(message);
                        navTab.reloadFlag('page1');
                    } else if (data.statusCode == 300){
                        message = data.message;
                        alertMsg.error(message)
                    } else {
                        alertMsg.error(data);
                    }
                    //
                },
                error:function(data, textStatus){
                    if (data && data.responseText) {
                        console.log(data.responseText);
                        alertMsg.error(data.responseText);
                    } else {
                        alertMsg.error("???????????????????????????????????????????????????");
                    }
                }
            });
        });
        
        
        $(document).on("click",".local_install_test",function(){
            namespace = $(this).attr('rel');
            var p_type = $(this).attr('p_type');
            var packageName = $(this).attr('packageName');
            var addonName = $(this).attr('addonName');
            var folderName = $(this).attr('folderName');
            
            var url = "<?= Yii::$service->url->getUrl("system/extensionmarket/administertest"); ?>";
            url += '?namespace=' + namespace;
            url += '&packageName=' + packageName;
            url += '&folderName=' + folderName;
            url += '&p_type=' + p_type;
            url += '&addonName=' + encodeURIComponent(addonName);
            
            $.ajax({
                url: url,
                async: true,
                timeout: 800000,
                dataType: 'json', 
                type: 'get',
                success:function(data, textStatus){
                    if(data.statusCode == 200){
                        //alert(data.statusCode);
                        message = data.message;
                        alertMsg.correct(message);
                        navTab.reloadFlag('page1');
                    } else if (data.statusCode == 300){
                        message = data.message;
                        alertMsg.error(message)
                    } else {
                        alertMsg.error(data);
                    }
                    //
                },
                error:function(data, textStatus){
                    if (data && data.responseText) {
                        console.log(data.responseText);
                        alertMsg.error(data.responseText);
                    } else {
                        alertMsg.error("???????????????????????????????????????????????????");
                    }
                }
            });
        });
        
    });


</script>
