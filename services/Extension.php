<?php

/*
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fecshop\services;

//use fecshop\models\mysqldb\cms\StaticBlock;
use Yii;
use fecshop\services\Service;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Extension extends Service
{
    public $numPerPage = 20;
    
    // install status
    const INSTALLED_STATUS = 1;
    const INSTALL_INIT_STATUS = 2;
    const UNINSTALLED_STATUS = 3;
    // status
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;
    // type
    const TYPE_INSTALL = 'online_installed';
    const TYPE_LOCAL_CREATED = 'local_created';
    
    protected $warnings;
    protected $_modelName = '\fecshop\models\mysqldb\Extension';

    protected $_model;
    
    
    public function init()
    {
        parent::init();
        list($this->_modelName, $this->_model) = Yii::mapGet($this->_modelName);
    }
    
    public function getTypeArr()
    {
        return [
            self::TYPE_INSTALL => Yii::$service->page->translate->__('Online Installed'),
            self::TYPE_LOCAL_CREATED => Yii::$service->page->translate->__('Local Created'),
        ];
    }
    
    public function isTypeLocalCreated($type)
    {
        return $type == self::TYPE_LOCAL_CREATED ? true : false;
    }
    
    public function getInstallStatusArr()
    {
        return [
            self::INSTALLED_STATUS => Yii::$service->page->translate->__('Installed'),
            self::INSTALL_INIT_STATUS => Yii::$service->page->translate->__('Install Init'),
            self::UNINSTALLED_STATUS => Yii::$service->page->translate->__('UNINSTALLED'),
        ];
    }
    
    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getByPrimaryKey($primaryKey)
    {
        if ($primaryKey) {
            $one = $this->_model->findOne($primaryKey);
            
            return $one;
        } else {
            
            return new $this->_modelName();
        }
    }
    /**
     * @param $extension_name | string ??? ????????????????????????
     * @return model
     */
    public function getByNamespace($extension_namespace)
    {
        return $this->_model->findOne(['namespace' => $extension_namespace]);
    }
    

    /*
     * example filter:
     * [
     * 		'numPerPage' 	=> 20,
     * 		'pageNum'		=> 1,
     * 		'orderBy'	=> ['_id' => SORT_DESC, 'sku' => SORT_ASC ],
            'where'			=> [
                ['>','price',1],
                ['<=','price',10]
     * 			['sku' => 'uk10001'],
     * 		],
     * 	'asArray' => true,
     * ]
     */
    public function coll($filter = '')
    {
        $query = $this->_model->find();
        $query = Yii::$service->helper->ar->getCollByFilter($query, $filter);
        $coll = $query->all();
        
        //var_dump($one);
        return [
            'coll' => $coll,
            'count'=> $query->limit(null)->offset(null)->count(),
        ];
    }

    /**
     * @param $one|array
     * save $data to cms model,then,add url rewrite info to system service urlrewrite.
     */
    public function save($one)
    {
        $currentDateTime = \fec\helpers\CDate::getCurrentDateTime();
        $primaryVal = isset($one[$this->getPrimaryKey()]) ? $one[$this->getPrimaryKey()] : '';
        
        if ($primaryVal) {
            $model = $this->_model->findOne($primaryVal);
            if (!$model) {
                Yii::$service->helper->errors->add('extension: {primaryKey} is not exist', ['primaryKey' => $this->getPrimaryKey()]);

                return;
            }
        } else {
            $model = new $this->_modelName();
            $model->created_at = time();
        }
        $model->updated_at = time();
        
        $primaryKey = $this->getPrimaryKey();
        $model      = Yii::$service->helper->ar->save($model, $one);
        $primaryVal = $model[$primaryKey];

        return true;
    }

    public function remove($ids)
    {
        if (!$ids) {
            Yii::$service->helper->errors->add('remove id is empty');

            return false;
        }
        if (is_array($ids) && !empty($ids)) {
            foreach ($ids as $id) {
                $model = $this->_model->findOne($id);
                $model->delete();
            }
        } else {
            $id = $ids;
            $model = $this->_model->findOne($id);
            $model->delete();
        }

        return true;
    }
    /**
     * @param $ids | array??? ??????id??????
     * ??????????????????
     */
    public function enableAddons($ids)
    {
        foreach ($ids as $id) {
            $model = $this->_model->findOne($id);
            $model->status = self::STATUS_ENABLE;
            $model->updated_at = time();
            if (!$model->save()) {
                
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * @param $ids | array??? ??????id??????
     * ??????????????????
     */
    public function disableAddons($ids)
    {
        foreach ($ids as $id) {
            $model = $this->_model->findOne($id);
            $model->status = self::STATUS_DISABLE;
            $model->updated_at = time();
            if (!$model->save()) {
                
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * @param $param | array, ????????????
     * ??????Gii????????????????????????????????????????????????????????????????????????????????????????????????????????????`???????????????`,
     * ??????????????????????????????
     */
    public function newLocalCreateInit($param)
    {
        $namespace = $param['namespaces'];
        //$package = $param['package'];
        //$name = $param['name'];
        //$config_file_path = $param['config_file_path'];
        //$version = $param['version'];
        if (!$namespace) {
            Yii::$service->helper->errors->add('namespace is empty');

            return false;
        }
        // ??????namespace ????????????
        $modelOne = $this->_model->findOne(['namespace' => $namespace]);
        if ($modelOne['id']) {
            Yii::$service->helper->errors->add('this namespace is exist');

            return false;
        }
        $config_file_path = '@addons/' . $param['package'] . '/' .  $param['addon_folder']  . '/config.php';
        $model = new $this->_modelName();
        $model['namespace'] = $param['namespaces'];
        $model['package'] = $param['package'];
        $model['folder'] = $param['addon_folder'];
        $model['name'] = $param['addon_name'];
        $model['version'] = '1.0.0';
        $model['config_file_path'] = $config_file_path;
        
        if (!$model->validate()) {
            $errors = $model->errors;
            Yii::$service->helper->errors->addByModelErrors($errors);
            return false;
        }
        $model->status = self::STATUS_ENABLE;
        $model->type = self::TYPE_LOCAL_CREATED;
        
        $model->created_at = time();
        $model->updated_at = time();
        $model->installed_status = self::INSTALLED_STATUS;
        $model->priority = 1;
        return $model->save();
    }
    
    // ????????????????????????????????????
    public function newInstallInit($param)
    {
        $namespace = $param['namespace'];
        //$package = $param['package'];
        //$name = $param['name'];
        //$config_file_path = $param['config_file_path'];
        //$version = $param['version'];
        if (!$namespace) {
            Yii::$service->helper->errors->add('namespace is empty');

            return false;
        }
        // ??????namespace ????????????
        $modelOne = $this->_model->findOne([
            'namespace' => $namespace,
            'installed_status' => self::INSTALLED_STATUS,
        ]);
        if ($modelOne['id']) {
            Yii::$service->helper->errors->add('this namespace is exist');

            return false;
        }
        $param['config_file_path'] = '@addons/' . $param['package'] . '/' .  $param['folder']  . '/config.php';
        // ??????????????????????????????????????????
        $model = $this->_model->findOne([
            'namespace' => $namespace,
            'package' => $param['package'],
            'folder' => $param['folder'],
        ]);
        if (!$model['id']) {
            $model = new $this->_modelName();
        }
        
        $model->attributes = $param;
        if (!$model->validate()) {
            $errors = $model->errors;
            Yii::$service->helper->errors->addByModelErrors($errors);
            return false;
        }
        $model->status = self::STATUS_DISABLE;
        $model->type = self::TYPE_INSTALL;
        
        $model->created_at = time();
        $model->updated_at = time();
        $model->installed_status = self::INSTALL_INIT_STATUS;
        $model->priority = 1;
        return $model->save();
    }
    
    // ??????????????????zip????????????????????????????????????????????????
    public function upgradeInit($param)
    {
        $namespace = $param['namespace'];
        if (!$namespace) {
            Yii::$service->helper->errors->add('namespace is empty');

            return false;
        }
        // ??????namespace ????????????
        $modelOne = $this->_model->findOne(['namespace' => $namespace]);
        if (!$modelOne['id']) {
            Yii::$service->helper->errors->add('this namespace is exist');

            return false;
        }
        if (!$this->isInstalledStatus($modelOne['installed_status'])) {
            Yii::$service->helper->errors->add('addon status is not install status');

            return false;
        }
        $param['config_file_path'] = '@addons/' . $param['package'] . '/' .  $param['folder']  . '/config.php';
        // $param['config_file_path'] = '@addons/' . $param['package'] . '/' .  $param['config_file_path'] ;
        //$model = new $this->_modelName();
        $modelOne->attributes = $param;
        if (!$modelOne->validate()) {
            $errors = $modelOne->errors;
            Yii::$service->helper->errors->addByModelErrors($errors);
            return false;
        }
        //$modelOne->status = self::STATUS_ENABLE;
        //$modelOne->created_at = time();
        $modelOne->updated_at = time();
        //$modelOne->installed_status = self::INSTALL_INIT_STATUS;
        //$modelOne->priority = 1;
        return $modelOne->save();
    }
    
    // ??????????????????????????????namespace
    public function getAllNamespaces()
    {
        $filter = [
            'asArray' => true,
            'fetchAll' => true,
        ];
        $data = $this->coll($filter);
        $arr = [];
        if (is_array($data['coll'])) {
            foreach ($data['coll'] as $one) {
                $namespace = $one['namespace'];
                if ($namespace) {
                    $arr[] = $namespace;
                }
            }
        }
        
        return $arr;
    }
    
    
    
    /**
     * @param $installed_status | int 
     * @return boolean ?????????????????????
     */
    public function isInstalledStatus($installed_status)
    {
        if ($installed_status == self::INSTALLED_STATUS) {
            
            return true;
        }
        
        return false;
    }
    // ??????????????????
    public function addWarning($info)
    {
        $this->warnings[] = $info;
        
        return true;
    }
    // ??????????????????
    public function getWarning()
    {
        return $this->warnings;
    }
    
    //protected $_installOb;
    /**
     * @param $installConfig | array
     * ?????????????????????
     * ?????????????????????????????????????????????????????????????????????????????????
     */
    public function installAddons($installConfig, $modelOne)
    {
        $installOb = Yii::createObject($installConfig);
        if (!$installOb->version) {
            Yii::$service->helper->errors->add("Extension Install Object must have property `version`");
            
            return false;
        }
        if (!($installOb instanceof \fecshop\services\extension\InstallInterface)) {
            Yii::$service->helper->errors->add("Extension install file must implements interface `\fecshop\services\extension\InstallInterface`");
            
            return false;
        }
        if (!$installOb->run()) {
            return false;
        }
        // ???????????????-????????????????????????
        $modelOne->installed_status = self::INSTALLED_STATUS;
        $modelOne->status = self::STATUS_ENABLE;
        $modelOne->installed_version = $installOb->version;
        $modelOne->updated_at = time();
        
        return $modelOne->save();
    }
    
    public function testInstallAddons($installConfig, $modelOne)
    {
        $installOb = Yii::createObject($installConfig);
        if (!$installOb->version) {
            Yii::$service->helper->errors->add("Extension Install Object must have property `version`");
            
            return false;
        }
        if (!($installOb instanceof \fecshop\services\extension\InstallInterface)) {
            Yii::$service->helper->errors->add("Extension install file must implements interface `\fecshop\services\extension\InstallInterface`");
            
            return false;
        }
        if (!$installOb->run()) {
            
            return false;
        }
        
        // ???????????????-????????????????????????
        $modelOne->installed_status = self::INSTALLED_STATUS;
        $modelOne->status = self::STATUS_ENABLE;
        $modelOne->installed_version = $installOb->version;
        $modelOne->updated_at = time();
        
        return $modelOne->save();
    }
    
    
    /**
     * @param $installConfig | array
     * ?????????????????????
     * ?????????????????????????????????????????????????????????????????????????????????
     */
    public function upgradeAddons($upgradeConfig, $modelOne)
    {
        $upgradeOb = Yii::createObject($upgradeConfig);
        if (!($upgradeOb instanceof \fecshop\services\extension\UpgradeInterface)) {
            Yii::$service->helper->errors->add("Extension upgrade file must implements interface `\fecshop\services\extension\UpgradeInterface`");
            
            return false;
        }
        $versions = $upgradeOb->versions;
        if (!empty($versions) && !is_array($versions)) {
            Yii::$service->helper->errors->add("Upgrade Object property `versions` must be array");
            
            return false;
        }
        $installed_version = $modelOne['installed_version'];
        $addon_remote_version = $modelOne['version'];
        
        $count = count($versions);
        for ($i = 0; $i < $count; $i++) {
            // ????????????????????? ?????? ????????????
            if (version_compare($installed_version, $versions[$i] ,'<')
                && version_compare($versions[$i],  $addon_remote_version,'<=')  // ????????????update???????????????????????????????????????version????????????????????????????????????????????????????????????????????????
            ) {
                // ?????????????????????????????????
                if (!$upgradeOb->run($versions[$i])) {
                    
                    return false;
                }
                // ???????????????????????????????????????
                $modelOne->installed_version = $versions[$i];
                $modelOne->updated_at = time();
                if (!$modelOne->save()) {
                    
                    return false;
                }
            }
        }
        
        // ??????????????????install_version???version????????????, ????????????????????????db?????????????????????
        $modelOne->installed_version = $modelOne['version'];
        $modelOne->updated_at = time();
        if (!$modelOne->save()) {
            
            return false;
        }
        
        return true;
    }
    
    public function testUpgradeAddons($upgradeConfig, $modelOne)
    {
        $upgradeOb = Yii::createObject($upgradeConfig);
        if (!($upgradeOb instanceof \fecshop\services\extension\UpgradeInterface)) {
            Yii::$service->helper->errors->add("Extension upgrade file must implements interface `\fecshop\services\extension\UpgradeInterface`");
            
            return false;
        }
        $versions = $upgradeOb->versions;
        if (!empty($versions) && !is_array($versions)) {
            Yii::$service->helper->errors->add("Upgrade Object property `versions` must be array");
            
            return false;
        }
        $installed_version = $modelOne['installed_version'];
        $addon_remote_version = $modelOne['version'];
        
        $count = count($versions);
        for ($i = 0; $i < $count; $i++) {
            // ????????????????????? ?????? ????????????
            
            if (version_compare($installed_version, $versions[$i] ,'<')
                && version_compare($versions[$i],  $addon_remote_version,'<=')  // ????????????update???????????????????????????????????????version????????????????????????????????????????????????????????????????????????
            ) {
                //echo $versions[$i];
                // ?????????????????????????????????
                if (!$upgradeOb->run($versions[$i])) {
                    
                    return false;
                }
                // ???????????????????????????????????????
                $modelOne->installed_version = $versions[$i];
                $modelOne->updated_at = time();
                if (!$modelOne->save()) {
                    
                    return false;
                }
            }
        }
        
        // ??????????????????install_version???version????????????, ????????????????????????db?????????????????????
        
        $modelOne->installed_version = $modelOne['version'];
        $modelOne->updated_at = time();
        if (!$modelOne->save()) {
            
            return false;
        }
        
        
        return true;
    }
    
    /**
     * @param $installConfig | array
     * ?????????????????????
     * ?????????????????????????????????????????????????????????????????????????????????
     */
    public function uninstallAddons($unstallConfig, $modelOne)
    {
        $uninstallOb = Yii::createObject($unstallConfig);
        if (!($uninstallOb instanceof \fecshop\services\extension\UninstallInterface)) {
            Yii::$service->helper->errors->add("Extension unstall file must implements interface `\fecshop\services\extension\UninstallInterface`");
            
            return false;
        }
        
        if (!$uninstallOb->run()) {
            
            return false;
        }
        // ??????extension???????????????
        return $modelOne->delete();
    }
    public function testUninstallAddons($unstallConfig, $modelOne)
    {
        $uninstallOb = Yii::createObject($unstallConfig);
        if (!($uninstallOb instanceof \fecshop\services\extension\UninstallInterface)) {
            Yii::$service->helper->errors->add("Extension unstall file must implements interface `\fecshop\services\extension\UninstallInterface`");
            
            return false;
        }
        
        if (!$uninstallOb->run()) {
            
            return false;
        }
        
        return true;
        // ??????extension???????????????
        //return $modelOne->delete();
    }
    
}
