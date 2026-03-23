<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LifeStyle\Models\LifeElement;
use Modules\LifeStyle\Models\LifeStyle;
use Modules\LifeStyle\Models\LifeTaskType;

class LifeStyleSeeder extends Seeder
{
    public function run(): void
    {
        $lifeStyles = [
            [
                'name' => 'Homemaker',
                'icon_key' => 'homemaker',
            ],
            [
                'name' => 'Working Woman',
                'icon_key' => 'working_woman',
            ],
            [
                'name' => 'Smart Mother',
                'icon_key' => 'smart_mother',
            ],
            [
                'name' => 'Student',
                'icon_key' => 'student',
            ],
            [
                'name' => 'Personal Planner',
                'icon_key' => 'personal_planner',
            ],
        ];

        foreach ($lifeStyles as $lifeStyle) {
            LifeStyle::firstOrCreate($lifeStyle);
        }

        //--------------------------------------------------------------------------------------------------
        
        $lifeElements = [
            [
                'name' => 'Religious practices',
                'suggested_tasks' => [
                    'Read morning adhkar',
                    'Read evening adhkar',
                    'Read quran',
                ],
                'task_types' => [
                    ['name' => 'Quran recitation', 'icon_key' => 'quran'],
                    ['name' => 'Adhkar', 'icon_key' => 'adhkar'],
                    ['name' => 'Prayer', 'icon_key' => 'prayer'],
                    ['name' => 'Charity', 'icon_key' => 'charity'],
                ],
                'icon_key' => 'religious_practices',
            ],
            [
                'name' => 'Manage expenses',
                'suggested_tasks' => [
                    'Set daily spending limit',
                    'Save a small amount today',
                ],
                'task_types' => [
                    ['name' => 'Housing', 'icon_key' => 'housing'],
                    ['name' => 'Food', 'icon_key' => 'food'],
                    ['name' => 'Self care', 'icon_key' => 'self_care'],
                    ['name' => 'Transport', 'icon_key' => 'transport'],
                    ['name' => 'Shopping', 'icon_key' => 'shopping'],
                    ['name' => 'Bills', 'icon_key' => 'bills'],
                ],
                'icon_key' => 'manage_expenses',
            ],
            [
                'name' => 'Daily achievements',
                'suggested_tasks' => [
                    'Complete 3 tasks from today\'s list',
                    'Try something new today',
                ],
                'task_types' => [
                    ['name' => 'Work', 'icon_key' => 'work'],
                    ['name' => 'Personal', 'icon_key' => 'personal'],
                    ['name' => 'Home', 'icon_key' => 'home'],
                    ['name' => 'Health', 'icon_key' => 'health'],
                    ['name' => 'Study', 'icon_key' => 'study'],
                ],
                'icon_key' => 'daily_achievements',
            ],
            [
                'name' => 'Life balance',
                'suggested_tasks' => [
                    'Avoid sugar for the day',
                    'Write how you feel today',
                ],
                'task_types' => [
                    ['name' => 'Self care', 'icon_key' => 'self_care'],
                    ['name' => 'Relaxation', 'icon_key' => 'relaxation'],
                    ['name' => 'Family time', 'icon_key' => 'family_time'],
                    ['name' => 'Hobby', 'icon_key' => 'hobby'],
                    ['name' => 'Physical activity', 'icon_key' => 'physical_activity'],
                    ['name' => 'My time', 'icon_key' => 'my_time'],
                ],
                'icon_key' => 'life_balance',
            ],
            [
                'name' => 'Child management',
                'suggested_tasks' => [
                    'Read a short story to your child',
                    'Teach child a good habit',
                ],
                'task_types' => [
                    ['name' => 'Morning routine', 'icon_key' => 'morning_routine'],
                    ['name' => 'Evening routine', 'icon_key' => 'evening_routine'],
                    ['name' => 'Study', 'icon_key' => 'study'],
                    ['name' => 'Health', 'icon_key' => 'health'],
                ],
                'icon_key' => 'child_management',
            ],
            [
                'name' => 'Personal development',
                'suggested_tasks' => [
                    'Learn a new word in another language',
                    'Learn a new language',
                ],
                'task_types' => [
                    ['name' => 'Skills', 'icon_key' => 'skills'],
                    ['name' => 'Reading', 'icon_key' => 'reading'],
                    ['name' => 'Language', 'icon_key' => 'language'],
                ],
                'icon_key' => 'personal_development',
            ],
        ];

        foreach ($lifeElements as $elementData) {
            $taskTypes = $elementData['task_types'] ?? [];
            unset($elementData['task_types']);

            $element = LifeElement::firstOrCreate([
                'user_id' => null,
                'name' => $elementData['name'],
            ], $elementData);

            foreach ($taskTypes as $typeData) {
                LifeTaskType::firstOrCreate([
                    'life_element_id' => $element->id,
                    'user_id' => null,
                    'name' => $typeData['name'],
                ], $typeData);
            }
        }
    }
}
