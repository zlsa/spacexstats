<?php

namespace SpaceXStats\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use LukeNZ\Reddit\Reddit;
use SpaceXStats\Models\Question;

class QuestionUpdaterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reddit:questions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the questions table from the Reddit FAQ.';

    protected $faqPages = [
        'faq/spacexstats'
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $questions = new Collection();

        $reddit = new Reddit(Config::get('services.reddit.username'), Config::get('services.reddit.password'), Config::get('services.reddit.id'), Config::get('services.reddit.secret'));
        $reddit->setUserAgent("/u/ElongatedMuskrat by /u/EchoLogic, fetching Wiki pages daily");

        foreach($this->faqPages as $faqPage) {
            $wikipage = $reddit->subreddit('spacex')->wikiPage($faqPage);
            $contents = str_replace("\r\n", "", $wikipage->data->content_md);

            $rawQuestions = explode('###', $contents);

            $i = 0;
            foreach($rawQuestions as $rawQuestion) {
                if ($i != 0) {
                    $questionParts = explode('?', $rawQuestion);

                    // If the question does not exist already...
                    if ($questions->where('question', $questionParts[0])->count() == 0) {
                        $question = Question::create(array(
                            'question' => $questionParts[0],
                            'answer' => $questionParts[1]
                        ));

                        $questions->push($question);
                    }
                }
                $i++;
            }

        }

    }
}
