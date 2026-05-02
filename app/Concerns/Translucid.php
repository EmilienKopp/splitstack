<?php

namespace App\Concerns;

use App\Events\TranslucidCreated;
use App\Events\TranslucidDeleted;
use App\Events\TranslucidUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

trait Translucid
{
    use Notifiable;

    protected static function bootTranslucid()
    {
        self::created(function (Model $model) {
            event(new TranslucidCreated($model));
        });

        self::updated(function (Model $model) {
            event(new TranslucidUpdated($model));
        });

        self::deleted(function (Model $model) {
            event(new TranslucidDeleted($model));
        });
    }
}
