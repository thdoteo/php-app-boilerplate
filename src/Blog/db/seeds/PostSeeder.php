<?php


use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{

    public function run()
    {
        // Categories
        $data = [];
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 5; $i++) {
            $data[] = [
                'name' => $faker->catchPhrase,
                'slug' => $faker->slug
            ];
        }
        $this->table('categories')->insert($data)->save();

        // Posts
        $data = [];
        for ($i = 0; $i < 100; $i++) {
            $date = $faker->unixTime('now');
            $data[] = [
                'name' => $faker->catchPhrase,
                'category_id' => rand(1, 5),
                'slug' => $faker->slug,
                'content' => $faker->text(3000),
                'created_at' => $faker->date('Y-m-d H:i:s', $date),
                'updated_at' => $faker->date('Y-m-d H:i:s', $date),
                'published' => 1
            ];
        }
        $this->table('posts')->insert($data)->save();
    }
}
