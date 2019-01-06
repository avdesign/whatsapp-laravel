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
    protected static $OPERATION_CREATE = 1;
    protected static $OPERATION_UPDATE = 2;


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

        /**
         * Sincronizando membros de grupo no Firebase conteudo=5387
         * Versão instalada 3.0.0 - Laravel 5.7
         * https://github.com/fico7489/laravel-pivot
        */

        /** Verifica se existe o mêtodo na class no model */
        if (method_exists(__CLASS__, 'pivotAttached')) {
            static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttribute) {
                //dd($model, $relationName, $pivotIds, $pivotIdsAttribute);
                $model->syncPivotAttached($model, $relationName, $pivotIds, $pivotIdsAttribute);
            });
        }

        if (method_exists(__CLASS__, 'pivotAttached')) {
            static::pivotDetached(function ($model, $relationName, $pivotIds) {
                $model->syncPivotDetached($model, $relationName, $pivotIds);
            });
        }


    }

    protected function syncFbCreate()
    {
        $this->syncFbSet(self::$OPERATION_CREATE);
    }

    protected  function syncFbUpdate()
    {
        $this->syncFbSet(self::$OPERATION_UPDATE);
    }

    protected function syncFbSet($operation = null)
    {
        $data = $this->toArray();
        $this->setTimestamps($data, $operation);
        $this->getModelReference()->update($this->toArray());
    }

    protected function setTimestamps(&$data, $operation = null)
    {
        if ($operation == self::$OPERATION_CREATE) {
            $data['created_at'] = ['.sv' => 'timestamp'];
            $data['updated_at'] = ['.sv' => 'timestamp'];
        }
        if ($operation == self::$OPERATION_UPDATE) {
            if (isset($data['created_at'])) {
                unset($data['created_at']);
            }
            $data['updated_at'] = ['.sv' => 'timestamp'];
        }
    }

    protected function syncFbRemove()
    {
        $this->getModelReference()->remove();
    }

    /**
     * Confirma se todos os métodos foram implementados.
     *
     * @param $model
     * @param $relationName
     * @param $pivotIds
     * @param $pivotIdsAttribute
     * @throws \Exception
     */
    protected function syncPivotAttached($model, $relationName, $pivotIds, $pivotIdsAttribute)
    {
        throw new \Exception('syncPivotAttached: Não Implentado');
    }

    /**
     * @param $model
     * @param $relationName
     * @param $pivotIds
     * @throws \Exception
     */
    protected function syncPivotDetached($model, $relationName, $pivotIds)
    {
        throw new \Exception('syncPivotDetached: Não Implentado');
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