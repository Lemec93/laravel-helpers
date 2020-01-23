<?php

namespace Lemec93\Support\Traits;

trait TranslationTrait
{
    public function scopeWithTranslation($query)
    {
        if (class_exists($this->getTranslationClass())) {
            $query->with('translation');
        }
    }

    public function translation()
    {
        $lang = session('lang', 'en');

        return $this->hasOne($this->getTranslationClass())
            ->where('locale', $lang);
    }

    public function allTranslations()
    {
        return $this->hasMany($this->getTranslationClass());
    }

    public function getTranslationClass()
    {
        return get_class($this) . 'Translation';
    }
}