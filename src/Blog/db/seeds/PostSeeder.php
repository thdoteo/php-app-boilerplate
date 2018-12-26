<?php


use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{

    public function run()
    {
        $data = [];
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 100; $i++) {
            $date = $faker->unixTime('now');
            $data[] = [
                'name' => $faker->catchPhrase,
                'slug' => $faker->slug,
                'content' => $faker->text(3000),
                'created_at' => $faker->date('Y-m-d H:i:s', $date),
                'updated_at' => $faker->date('Y-m-d H:i:s', $date)
            ];
        }

        $this->table('posts')
            ->insert($data)
            ->save();
    }
}
