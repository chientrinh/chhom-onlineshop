<?php
namespace backend\models;

/**
 * $URL: https://tarax.toyouke.com/svn/MALL/backend/models/ImageForm.php $
 * $Id: ImageForm.php 2278 2016-03-20 08:39:05Z mori $
 */

use Yii;
use yii\web\UploadedFile;

class ImageForm extends \yii\base\Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'required'],
            [['imageFile'], 'image', 
             'extensions' => 'png, jpg',
             'maxHeight'  => 5000,
             'maxWidth'   => 5000,
             'maxSize'    => 5 * 1000 * 1000, // 5MB
            ],
        ];
    }
    
    /* @return string(fullpath of the saved file) or bool(false: save() failed) */
    public function upload()
    {
        if(! $this->validate())
            return false;

        $filename = sprintf('%s/%s.%s',
                            Yii::getAlias('@runtime'),
                            md5($this->imageFile->baseName), // to avoid Japanese character
                            $this->imageFile->extension);

        if(($tempName = $this->imageFile->tempName) && is_file($tempName))
            return $tempName;

        return false;
    }
}
