<?php

use yii\db\Schema;
use yii\db\Migration;

class m140723_010511_filemanager_init extends Migration
{
    public function up()
    {
        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%files}}', [
            'id' => Schema::TYPE_PK,
            'author_id' => Schema::TYPE_INTEGER . ' DEFAULT 0',
            'editor_id' => Schema::TYPE_INTEGER . ' DEFAULT 0',
            'url' => Schema::TYPE_STRING . '(555) NULL',
            'thumbnail_url' => Schema::TYPE_STRING . '(555) NULL',
            'file_name' => Schema::TYPE_STRING . '(555) NULL',
            'remote_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'remote_type' => Schema::TYPE_STRING . '(45) NULL',
            'type' => Schema::TYPE_STRING . '(45) NULL',
            'title' => Schema::TYPE_STRING . '(45) NULL',
            'size' => Schema::TYPE_INTEGER . ' NULL',
            'width' => Schema::TYPE_INTEGER  . ' NULL',
            'height' => Schema::TYPE_INTEGER  . ' NULL',
            'created_at' => Schema::TYPE_TIMESTAMP  . ' DEFAULT NOW()',
            'updated_at' => Schema::TYPE_TIMESTAMP  . '',
            'date' => Schema::TYPE_DATETIME  . ' NULL',
            'date_gmt' => Schema::TYPE_DATETIME  . ' NULL',
            'update' => Schema::TYPE_DATETIME  . ' NULL',
            'update_gmt' => Schema::TYPE_DATETIME  . ' NULL',
            'deleted_by' => Schema::TYPE_INTEGER  . ' NULL',
            'deleted_at' => Schema::TYPE_DATETIME  . ' NULL',
            'deleted' => Schema::TYPE_BOOLEAN  . ' NULL',
        ], $tableOptions);

        $this->createTable('{{%files_metadata}}', [
            'id' => Schema::TYPE_PK,
            'file_id' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'key' => Schema::TYPE_STRING . '(45) NULL',
            'value' => Schema::TYPE_TEXT . ' NULL',
            'author_id' => Schema::TYPE_INTEGER . '(11) NULL',
            'editor_id' => Schema::TYPE_INTEGER . '(11) NULL',
            'created_at' => Schema::TYPE_TIMESTAMP  . ' DEFAULT NOW()',
            'updated_at' => Schema::TYPE_TIMESTAMP  . ' DEFAULT 0',
        ], $tableOptions);
        
        $this->addForeignKey('FK_files_metadata','{{%files_metadata}}','file_id','{{%files}}','id');
        $this->addForeignKey('FK_files_users','{{%files}}','user_id','{{%user}}','id');
        
    }

    public function down()
    {
        $this->dropForeignKey('FK_files_terms','{{%files_metadata}}');
        $this->dropForeignKey('FK_files_users','{{%files}}');
        $this->dropTable('{{%files}}');
        $this->dropTable('{{%files_metadata}}');   
    }
}