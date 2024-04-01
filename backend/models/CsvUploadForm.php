<?php

namespace backend\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm : アップロードのフォームの背後にあるモデル
 */
class CsvUploadForm extends Model
{
    /**
     * @var UploadedFile file 属性
     */
    public $file;
    public $file_name;

    /**
     * @return array 検証規則
     */
    public function rules()
    {
        return [
            [['file'], 'file'],
        ];
    }


    public function getFileName() {
        return $this->file_name;
    }

    public function setFileName($val) {
        return $this->file_name = $val;
    }
}
