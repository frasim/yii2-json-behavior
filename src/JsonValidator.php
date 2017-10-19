<?php
namespace paulzi\jsonBehavior;

use yii\base\InvalidParamException;
use yii\db\BaseActiveRecord;
use yii\validators\Validator;

class JsonValidator extends Validator
{
    /**
     * @var bool
     */
    public $merge = false;
    
    /**
     * @var bool
     */
    public $onlyArray = true;

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (!$value instanceof JsonField) {
            try {
                $new = new JsonField($value);
                if ($this->merge) {
                    /** @var BaseActiveRecord $model */
                    $old = new JsonField($model->getOldAttribute($attribute), $this->onlyArray);
                    $new = new JsonField(array_merge($old->toArray(), $new->toArray()), $this->onlyArray);
                }
                $model->$attribute = $new;
            } catch (InvalidParamException $e) {
                $this->addError($model, $attribute, $e->getMessage());
                $model->$attribute = new JsonField();
            }
        }
    }
}
