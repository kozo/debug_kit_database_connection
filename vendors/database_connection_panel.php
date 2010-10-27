<?php
class DatabaseConnectionPanel extends DebugPanel {
    var $plugin = 'DebugKitDatabaseConnection';
    var $title = 'Connections';
    
    var $controller;

    function startup(&$controller) {
        $this->controller = $controller;
    }

    function beforeRender(&$controller) {
        
        // 接続先一覧を取得
        $dbConfigs = $this->_getDbConnectInfo();
        
        $controller->set(compact('dbConfigs'));
    }    
    
    /**
     * データベースの接続先情報を取得する
     * 
     * @access private
     * @author sakuragawa
     */
    private function _getDbConnectInfo(){
        $dbConfigInfo = array();
        $dbConfig = new DATABASE_CONFIG();
        
        // 読み込まれているModel分ループ
        foreach($this->controller->modelNames as $key=>$val)
        {
            APP::import('Model', $this->controller->modelNames[$key]);
            $model = new $this->controller->modelNames[$key];
            $useDbConfig = $model->useDbConfig;
            
            if(!isset($dbConfig->{$useDbConfig})){
                // 定義されてない
                unset($model);
                continue;
            }
            
            // 接続設定
            $one = $dbConfig->{$useDbConfig};
            
            // 必要な分のみ取り出し
            $buf['driver'] = $one['driver'];
            $buf['host'] = $one['host'];
            $buf['database'] = $one['database'];
            if(count($dbConfigInfo) != 0){
                // 初回以外
                if(!in_array($buf, $dbConfigInfo)){
                    $dbConfigInfo[$useDbConfig] = $buf;
                }
            }else{
                // 初回
                $dbConfigInfo[$useDbConfig] = $buf;
            }
            
            unset($model);
        }
        
        return $dbConfigInfo;
    }
}
?>