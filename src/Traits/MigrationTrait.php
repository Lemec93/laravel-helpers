<?php

namespace Lemec93\Support\Traits;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait MigrationTrait
{
    public function addForeignKey($fromEntity, $toEntity, $needAddField = false)
    {
        Schema::table($this->getTableName($fromEntity), function (Blueprint $table) use ($toEntity, $needAddField) {
            $fieldName = Str::snake($toEntity) . '_id';

            if ($needAddField) {
                $table->unsignedBigInteger($fieldName);
            }

            $table->foreign($fieldName)
                ->references('id')
                ->on($this->getTableName($toEntity));
        });
    }

    public function addForeignKeyPersoNullable($fromEntity, $toEntity, $name, $needAddField = false)
    {
        Schema::table($this->getTableName($fromEntity), function (Blueprint $table) use ($toEntity, $name, $needAddField) {
            $fieldName = Str::snake($name) . '_id';

            if ($needAddField) {
                $table->unsignedBigInteger($fieldName)->nullable();
            }

            $table->foreign($fieldName)
                ->references('id')
                ->on($this->getTableName($toEntity));
        });
    }

    public function dropForeignKey($fromEntity, $toEntity, $needDropField = false)
    {
        $field = Str::snake($toEntity) . '_id';
        $table = $this->getTableName($fromEntity);

        if (Schema::hasColumn($table, $field)) {
            Schema::table($table, function (Blueprint $table) use ($field, $needDropField) {
                $table->dropForeign([$field]);

                if ($needDropField) {
                    $table->dropColumn([$field]);
                }
            });
        }
    }

    public function createBridgeTable($fromEntity, $toEntity)
    {
        $bridgeTableName = $this->getBridgeTable($fromEntity, $toEntity);

        Schema::create($bridgeTableName, function (Blueprint $table) {
            $table->increments('id');
        });

        $this->addForeignKey($bridgeTableName, $fromEntity, true);
        $this->addForeignKey($bridgeTableName, $toEntity, true);
    }

    public function createBridgePersoTable($fromEntity, $toEntity, $fields = [])
    {
        $bridgeTableName = $this->getBridgeTable($fromEntity, $toEntity);
        Schema::create($bridgeTableName, function (Blueprint $table) use ($fields) {
            $table->increments('id');
            //Modif pour mes champs
            $table->timestamps();
            if(isset($fields)) {
                print_r($fields);
                foreach ( $fields as $key => $f )
                {
                    if($f === 'i') {
                        $table->integer($key)->nullable();
                    } elseif ($f === 'I') {
                        $table->integer($key);
                    } elseif ($f === 's') {
                        $table->string($key)->nullable();
                    } elseif ($f === 'S') {
                        $table->string($key);
                    } elseif ($f === 'b') {
                        $table->boolean($key)->nullable();
                    } elseif ($f === 'B') {
                        $table->boolean($key);
                    } elseif ($f === 'F') {
                        $table->float($key);
                    } elseif ($f === 'f') {
                        $table->float($key)->nullable();
                    } elseif ($f === 'D') {
                        $table->decimal($key, 10, 2);
                    } elseif ($f === 'd') {
                        $table->decimal($key, 10, 2)->nullable();
                    } elseif ($f === 't') {
                        $table->timestamp($key)->nullable();
                    } elseif ($f === 'T') {
                        $table->timestamp($key);
                    } elseif ($f === 'j') {
                        $table->json($key)->nullable();
                    }
                }
            }
        });
        $this->addForeignKey($bridgeTableName, $fromEntity, true);
        $this->addForeignKey($bridgeTableName, $toEntity, true);
    }

    public function dropBridgeTable($fromEntity, $toEntity)
    {
        $bridgeTableName = $this->getBridgeTable($fromEntity, $toEntity);

        $this->dropForeignKey($bridgeTableName, $fromEntity, true);
        $this->dropForeignKey($bridgeTableName, $toEntity, true);

        Schema::drop($bridgeTableName);
    }

    protected function getBridgeTable($fromEntity, $toEntity)
    {
        $entities = [Str::snake($fromEntity), Str::snake($toEntity)];
        sort($entities, SORT_STRING);

        return implode('_', $entities);
    }

    protected function getTableName($entityName)
    {
        if (Schema::hasTable($entityName)) {
            return $entityName;
        }
        $entityName = Str::snake($entityName);

        return Str::plural($entityName);
    }
}
