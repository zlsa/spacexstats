<?php
use Illuminate\Database\Seeder;
use SpaceXStats\Models\NotificationType;

class NotificationTypesTableSeeder extends Seeder {
    public function run() {
        NotificationType::insert(array(
            array('name' => 'newMission'),
            array('name' => 'launchTimeChange'),
            array('name' => 'tMinus24HoursEmail'),
            array('name' => 'tMinus3HoursEmail'),
            array('name' => 'tMinus1HourEmail'),
            array('name' => 'newsSummaries'),
            array('name' => 'tMinus24HoursSMS'),
            array('name' => 'tMinus3HoursSMS'),
            array('name' => 'tMinus1HourSMS')
        ));
    }
}