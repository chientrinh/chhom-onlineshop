<?php

namespace backend\models;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

/**
 * UploadForm : アップロードのフォームの背後にあるモデル
 */
class CsvUploadMultiForm extends Model
{
    /**
     * @var UploadedFile file 属性
     */
    public $csvFiles;
    public $success_count = 0;
    public $error_count = 0;
    /**
     * @return array 検証規則
     */
    public function rules()
    {
        return [
            [['csvFiles'], 'file', 'skipOnEmpty' => false],
        ];
    }
    
    public function upload()
    {
        foreach ($this->csvFiles as $file) {
            if($file->extension == 'csv') {
                $file->saveAs(Yii::getAlias(sprintf('@runtime/%s.%s',$file->baseName, $file->extension)));
                $this->success_count++;
            } else {
                $this->error_count++;
            }
        }
        if($this->error_count > 0)
            return false;
        
        return true;
    }



    public function getFileNames() {
        return ArrayHelper::getColumn($this->csvFiles, 'name');
    }

}
