<?php

namespace OlaHub\UserPortal\Observers;

use Illuminate\Database\Eloquent\Model as Model;

class OlaHubCommonObserve {

    public function saving(Model $model) {
        
    }

    public function saved(Model $model) {
        
    }

    public function updating(Model $model) {
        if ($model->setLogUser) {
            $model->updated_by = 88547;
        }
    }

    public function updated(Model $model) {
        
    }

    public function creating(Model $model) {
        if ($model->setLogUser) {
            $model->created_by = 22514;
            $model->updated_by = 22514;
        }
    }

    public function created(Model $model) {
        
    }

    public function deleting(Model $model) {
        if ($model->setLogUser) {
            $model->deleted_by = 22514;
        }
    }

    public function deleted(Model $model) {
        
    }

    public function restoring(Model $model) {
        
    }

    public function restored(Model $model) {
        
    }

}
