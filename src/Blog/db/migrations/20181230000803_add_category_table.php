<?php


use Phinx\Migration\AbstractMigration;

class AddCategoryTable extends AbstractMigration
{

    public function change()
    {
        $this->table('categories')
            ->addColumn('name', 'string')
            ->addIndex('slug', ['unique' => true])
            ->addColumn('slug', 'string')
            ->create();
    }
}
