<?php
namespace SpaceXStats\Services;

use SpaceXStats\Library\Enums\MissionControlSubtype;
use SpaceXStats\Library\Enums\MissionControlType;
use SpaceXStats\Models\Object;

class DeltaVCalculator {

    const DELTAV_TO_DAY_CONVERSION_RATE     = 1000;
    const SECONDS_PER_DAY                   = 60 * 60 * 24;

    protected $object;
    protected $score = 0;

    protected $baseTypeScores = [
        MissionControlType::Image       => 50,
        MissionControlType::GIF         => 50,
        MissionControlType::Audio       => 10,
        MissionControlType::Video       => 10,
        MissionControlType::Document    => 10,
        MissionControlType::Tweet       => 10,
        MissionControlType::Article     => 20,
        MissionControlType::Comment     => 5,
        MissionControlType::Webpage     => 50,
        MissionControlType::Text        => 50
    ];

    protected $specialTypeMultiplier = [
        MissionControlSubtype::MissionPatch     => 2,
        MissionControlSubtype::Photo            => 1.1,
        MissionControlSubtype::LaunchVideo      => 2,
        MissionControlSubtype::PressKit         => 2,
        MissionControlSubtype::WeatherForecast  => 2
    ];

    protected $resourceQuality = [
        'multipliers' => [
            'perMegapixel' => 1,
            'perMinute' => 1
        ],
        'scores' => [
            'perPage' => 1
        ]
    ];

    protected $metadataScore = [
        'summary' => [
            'perCharacter' => 0.01
        ],
        'author' => [
            'perCharacter' => 0.1
        ],
        'attribution' => [
            'perCharacter' => 0.1
        ]
    ];

    protected $dateAccuracyMultiplier = [
        'year' => 1,
        'month' => 1.2,
        'date' => 1.5,
        'datetime' => 2,
    ];

    protected $dataSaverMultiplier = [
        'hasExternalUrl' => 3
    ];

    /**
     * Calculates the total deltaV value of a particular object
     *
     * @param   $object   SpaceXStats\Models\Object   The object to calculate the deltaV for.
     * @return  int                                     The total worth of the object in deltaV.
     */
    public function calculate(Object $object) {
        $this->object = $object;

        $this->typeRegime();
        $this->resourceQualityRegime();
        $this->metadataRegime();
        $this->dateAccuracyRegime();
        $this->dataSaverRegime();

        return round($this->score);
    }

    /**
     * For a given amount of deltaV, calculates the number of seconds mission control it is worth.
     *
     * @param   $deltaV   int   The input value of deltaV.
     * @return  int             The number of seconds of mission control the input deltaV value corresponds to.
     */
    public function toSeconds($deltaV) {
        // Currently 86.4 seconds per point
        $secondsPerPoint = self::SECONDS_PER_DAY / self::DELTAV_TO_DAY_CONVERSION_RATE;

        return (int) round($deltaV * $secondsPerPoint);
    }

    /**
     * @internal
     */
    private function typeRegime() {
        // The base score
        $this->score += $this->baseTypeScores[$this->object->type];

        // The special type multiplier
        if (array_key_exists($this->object->subtype, $this->specialTypeMultiplier)) {
            $this->score *= $this->specialTypeMultiplier[$this->object->subtype];
        }
    }

    /**
     * @internal
     */
    private function resourceQualityRegime() {
        $resourceQualityScore = 0;

        if ($this->object->type == MissionControlType::Image) {
            $resourceQualityScore = $this->megapixelSubscore();
        }

        if ($this->object->type == MissionControlType::GIF || $this->object->type == MissionControlType::Video) {
            $resourceQualityScore = $this->megapixelSubscore() * $this->minuteSubscore();
        }

        if ($this->object->type == MissionControlType::Audio) {
            $resourceQualityScore = $this->minuteSubscore();
        }

        if ($this->object->type == MissionControlType::Document) {
            $resourceQualityScore = $this->pageSubscore();
        }

        $this->score += $resourceQualityScore;
    }

    /**
     * @internal
     */
    private function metadataRegime() {
        $this->score += strlen($this->object->summary) * $this->metadataScore['summary']['perCharacter'];
        $this->score += strlen($this->object->author) * $this->metadataScore['author']['perCharacter'];
        $this->score += strlen($this->object->attribution) * $this->metadataScore['attribution']['perCharacter'];
    }

    /**
     * @internal
     */
    private function dateAccuracyRegime() {
        $year = substr($this->object->originated_at, 0, 4);
        $month = substr($this->object->originated_at, 5, 2);
        $day = substr($this->object->originated_at, 8, 2);
        $datetime = substr($this->object->originated_at, 11, 8);

        if ($datetime != '00:00:00') {
            $this->score *= $this->dateAccuracyMultiplier['datetime'];
        } elseif ($day != '00') {
            $this->score *= $this->dateAccuracyMultiplier['day'];
        } elseif ($month != '00') {
            $this->score *= $this->dateAccuracyMultiplier['month'];
        } elseif ($year != '0000') {
            $this->score *= $this->dateAccuracyMultiplier['year'];
        }
    }

    /**
     * @internal
     * @return int
     */
    private function dataSaverRegime() {
        if (!is_null($this->object->external_url)) {
            return $this->score * $this->dataSaverMultiplier['hasExternalUrl'];
        }
    }

    /**
     * @internal
     * @return mixed
     */
    private function megapixelSubscore() {
        $megapixels = ($this->object->dimension_width * $this->object->dimension_height) / 1000000;
        return $this->resourceQuality['multipliers']['perMegapixel'] * $megapixels;
    }

    /**
     * @internal
     * @return mixed
     */
    private function minuteSubscore() {
        return $this->resourceQuality['multipliers']['perMinute'] * ($this->object->duration / 60);
    }

    /**
     * @internal
     * @return mixed
     */
    private function pageSubscore() {
        return $this->resourceQuality['scores']['perPage'] * $this->object->page_count;
    }
}