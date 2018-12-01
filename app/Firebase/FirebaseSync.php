<?php
declare(strict_types=1);
/**
 * Cada modulo que for usar, precisa chamar essa trait.
 * ChatGroup:
 * Date: 30/11/18
 * Time: 08:28
 */

namespace CodeShopping\Firebase;


use Kreait\Firebase;
use Kreait\Firebase\Database\Reference;


trait FirebaseSync
{
    public static function bootFirebaseSync()
    {
        static::created(function ($model) {
            $model->syncFbCreate();
        });

        static::updated(function ($model){
            $model->syncFbUpdate();
        });

        static::deleted(function ($model) {
            $model->stncFb->remove();
        });

    }

    protected function syncFbCreate()
    {
        $this->syncFbSet();
    }

    protected  function syncFbUpdate()
    {
        $this->syncFbSet();
    }

    protected function syncFbSet()
    {
        $this->getModelReference()->update($this->toArray());
    }

    protected function syncFbRemove()
    {
        $this->getModelReference()->remove();
    }

    /**
     * Referencia Base
     */
    protected function getModelReference(): Reference
    {
        // Retorna a tabela do banco de dados Ex: chat_groups/1
        $path = $this->getTable() . '/' . $this->getKey();
        return $this->getFirebaseDatabase()->getReference($path);
    }


    /**
     * Conecta ao Banco de dados do Firebase
     *
     * @return Firebase\Database
     */
    protected function getFirebaseDatabase(): Firebase\Database
    {
        $firebase = app(Firebase::class);
        return $firebase->getDatabase();
    }
}